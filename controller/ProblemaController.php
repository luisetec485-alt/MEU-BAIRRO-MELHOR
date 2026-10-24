<?php

require_once __DIR__.'/../model/ProblemaModel.php';
require_once __DIR__.'/../model/CategoriaModel.php';
require_once __DIR__.'/../model/SubcategoriaModel.php';
require_once __DIR__.'/../model/UsuarioModel.php';
require_once __DIR__.'/../model/AuxiliarEmail.php';
require_once __DIR__.'/../model/NotificacaoModel.php';

class ProblemaController {

    public function abrirFormulario() {

        $categoriaModel = new CategoriaModel();
        $subcategoriaModel = new SubcategoriaModel();

        $categorias = $categoriaModel->listarTodos();
        $subcategorias = $subcategoriaModel->listarTodos();

        require_once __DIR__.'/../view/problemaForm.php';
    }

    public function listar() {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $problemaModel = new ProblemaModel();

        $ordem = $_GET['ordem'] ?? 'data';
        $direcao = $_GET['direcao'] ?? 'DESC';

        $ehAdministrador = (int) ($_SESSION['nivelAcesso'] ?? 1) === 0;
        $problemas = $problemaModel->listarTodos(
            $ehAdministrador ? null : $_SESSION['id'],
            $ordem,
            $direcao
        );

        $categoriaModel = new CategoriaModel();
        $subcategoriaModel = new SubcategoriaModel();

        $categorias = $categoriaModel->listarTodos();
        $subcategorias = $subcategoriaModel->listarTodos();

        require_once __DIR__.'/../view/problemaLista.php';
    }

    public function salvar() {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id'])) {
            header("Location: ../view/login.php");
            exit;
        }

        $titulo = $_POST['titulo'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $bairro = $_POST['bairro'] ?? '';
        $categoriaid = $_POST['categoriaid'] ?? '';
        $subcategoriaid = $_POST['subcategoriaid'] ?? '';
        $latitude = $_POST['latitude'] ?? '';
        $longitude = $_POST['longitude'] ?? '';
        $prioridade = $_POST['prioridade'] ?? 'media';
        $usuarioId = $_SESSION['id'];
        $imagem = '';

        $categoriaid = filter_var($categoriaid, FILTER_VALIDATE_INT);
        $subcategoriaid = filter_var($subcategoriaid, FILTER_VALIDATE_INT);
        $latitude = filter_var($latitude, FILTER_VALIDATE_FLOAT);
        $longitude = filter_var($longitude, FILTER_VALIDATE_FLOAT);

        // Valida prioridade
        $prioridadesValidas = ['baixa', 'media', 'alta', 'critica'];
        if (!in_array($prioridade, $prioridadesValidas)) {
            $prioridade = 'media';
        }

        if (
            empty($titulo) ||
            empty($descricao) ||
            empty($categoriaid) ||
            empty($subcategoriaid) ||
            empty($latitude) ||
            empty($longitude) ||
            empty($prioridade)
        ) {
            echo "Preencha todos os campos e selecione a localização no mapa.";
            return;
        }

        if ($latitude === false || $longitude === false
            || $latitude < -90 || $latitude > 90
            || $longitude < -180 || $longitude > 180
        ) {
            echo "Informe coordenadas válidas para a localização.";
            return;
        }

        if (!$this->pontoEmFrancoDaRocha($latitude, $longitude)) {
            echo "A localização deve estar dentro de Franco da Rocha.";
            return;
        }

        $subcategoriaModel = new SubcategoriaModel();

        if (!(new CategoriaModel())->buscarPorId($categoriaid)) {
            echo "A categoria selecionada não existe.";
            return;
        }

        if (!$subcategoriaModel->pertenceACategoria($subcategoriaid, $categoriaid)) {
            echo "A subcategoria selecionada não pertence à categoria informada.";
            return;
        }

        /*
         * Upload de imagem ou vídeo
         */
        if (
            isset($_FILES['imagem']) &&
            $_FILES['imagem']['error'] === UPLOAD_ERR_OK &&
            !empty($_FILES['imagem']['name'])
        ) {

            $extensao = strtolower(
                pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION)
            );

            $extensoesPermitidas = [
                'jpg',
                'jpeg',
                'png',
                'mp4',
                'mov',
                'avi',
                'webm'
            ];
            $tiposPermitidos = [
                'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
                'mp4' => 'video/mp4', 'mov' => 'video/quicktime',
                'avi' => 'video/x-msvideo', 'webm' => 'video/webm'
            ];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $tipoMime = $finfo ? finfo_file($finfo, $_FILES['imagem']['tmp_name']) : false;
            if ($finfo) {
                finfo_close($finfo);
            }

            if (!in_array($extensao, $extensoesPermitidas, true)
                || $tipoMime !== $tiposPermitidos[$extensao]
            ) {
                echo "<script>
                        alert('Extensão inválida! Use apenas JPG, JPEG, PNG, MP4, MOV, AVI ou WEBM.');
                        history.back();
                      </script>";
                return;
            }

            $tipoArquivo = in_array(
                $extensao,
                ['jpg', 'jpeg', 'png']
            ) ? 'img' : 'video';

            $limiteBytes = $tipoArquivo === 'img'
                ? 10 * 1024 * 1024
                : 50 * 1024 * 1024;

            if ($_FILES['imagem']['size'] > $limiteBytes) {

                $mensagem = $tipoArquivo === 'img'
                    ? 'Imagem muito grande! O limite é de 10MB.'
                    : 'Vídeo muito grande! O limite é de 50MB.';

                echo "<script>
                        alert('{$mensagem}');
                        history.back();
                      </script>";
                return;
            }

            $pastaDestino = __DIR__
                . '/../view/'
                . $tipoArquivo
                . '/problemas/';

            if (!is_dir($pastaDestino)) {
                mkdir($pastaDestino, 0775, true);
            }

            $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $extensao;

            $destino = $pastaDestino . $nomeArquivo;

            if (!move_uploaded_file(
                $_FILES['imagem']['tmp_name'],
                $destino
            )) {
                echo "<script>
                        alert('Erro ao enviar o arquivo. Tente novamente.');
                        history.back();
                      </script>";
                return;
            }

            $imagem = $tipoArquivo
                . '/problemas/'
                . $nomeArquivo;
        }

        $model = new ProblemaModel();

        $resultado = $model->inserir(
            $titulo,
            $descricao,
            $imagem,
            $categoriaid,
            $subcategoriaid,
            $usuarioId,
            $latitude,
            $longitude,
            $prioridade,
            $bairro
        );

        if (!$resultado) {
            echo "Erro ao registrar o problema.";
            return;
        }

        header(
            'Location: routes.php?acao=listarProblemas'
        );
        exit;
    }

    public function cancelar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id'])) {
            header("Location: ../view/login.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Método não permitido.";
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            echo "Denúncia inválida.";
            return;
        }

        $model = new ProblemaModel();

        if (!$model->cancelar($id, $_SESSION['id'])) {
            echo "A denúncia não existe, não pertence a você ou não pode mais ser cancelada.";
            return;
        }

        header("Location: routes.php?acao=listarProblemas");
        exit;
    }

    public function excluir() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id'])) {
            header("Location: ../view/login.php");
            exit;
        }

        if ((int) ($_SESSION['nivelAcesso'] ?? 1) !== 0) {
            http_response_code(403);
            echo "Apenas administradores podem excluir denúncias.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Método não permitido.";
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            echo "Denúncia inválida.";
            return;
        }

        $model = new ProblemaModel();

        if (!$model->excluir($id, $_SESSION['id'])) {
            echo "Não foi possível excluir a denúncia.";
            return;
        }

        header("Location: routes.php?acao=listarProblemas");
        exit;
    }

    public function atualizarStatus() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id'])) {
            header("Location: ../view/login.php");
            exit;
        }

        if ((int) ($_SESSION['nivelAcesso'] ?? 1) !== 0) {
            http_response_code(403);
            echo "Apenas administradores podem alterar o status.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Método não permitido.";
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';
        $statusPermitidos = ['aberta', 'em_analise', 'resolvida', 'cancelada'];

        if (!$id || !in_array($status, $statusPermitidos, true)) {
            echo "Dados inválidos.";
            return;
        }

        $model = new ProblemaModel();
        $problema = $model->buscarPorId($id);

        if (!$problema) {
            echo "Denúncia não encontrada.";
            return;
        }

        if (!$model->atualizarStatus($id, $status, $_SESSION['id'])) {
            echo "Não foi possível atualizar o status.";
            return;
        }

        // Notificação via E-mail
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->buscarPorId($problema['usuarioid']);
        if ($usuario) {
            $statusFormatado = ucfirst(str_replace('_', ' ', $status));
            $assunto = "Atualização na sua denúncia: " . $problema['codigoDenuncia'];
            $corpo = "
                <h2>Olá, {$usuario['nomeUsuario']}!</h2>
                <p>O status da sua denúncia <strong>{$problema['codigoDenuncia']}</strong> foi alterado para: <strong>{$statusFormatado}</strong>.</p>
                <p>Você pode acompanhar os detalhes no sistema.</p>
                <br>
                <p>Atenciosamente,<br>Equipe Meu Bairro Melhor</p>
            ";
            AuxiliarEmail::enviarEmail($usuario['Email'], $usuario['nomeUsuario'], $assunto, $corpo);
        }

        // Notificação no Sistema (Dashboard)
        $notifModel = new NotificacaoModel();
        $msgNotif = "O status da sua denúncia {$problema['codigoDenuncia']} foi alterado para " . ucfirst(str_replace('_', ' ', $status)) . ".";
        $notifModel->criar($problema['usuarioid'], $msgNotif);

        header("Location: routes.php?acao=listarProblemas");
        exit;
    }

    public function atualizarPrioridade() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id'])) {
            header("Location: ../view/login.php");
            exit;
        }

        if ((int) ($_SESSION['nivelAcesso'] ?? 1) !== 0) {
            http_response_code(403);
            echo "Apenas administradores podem alterar a prioridade.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Método não permitido.";
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $prioridade = $_POST['prioridade'] ?? '';
        $prioridadesPermitidas = ['baixa', 'media', 'alta', 'critica'];

        if (!$id || !in_array($prioridade, $prioridadesPermitidas, true)) {
            echo "Dados inválidos.";
            return;
        }

        $model = new ProblemaModel();

        if (!$model->atualizarPrioridade($id, $prioridade, $_SESSION['id'])) {
            echo "Não foi possível atualizar a prioridade.";
            return;
        }

        header("Location: routes.php?acao=listarProblemas");
        exit;
    }

    public function adicionarObservacao() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id'])) {
            header("Location: ../view/login.php");
            exit;
        }

        if ((int) ($_SESSION['nivelAcesso'] ?? 1) !== 0) {
            http_response_code(403);
            echo "Apenas administradores podem adicionar observações.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Método não permitido.";
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $observacoes = $_POST['observacoes'] ?? '';

        if (!$id || empty(trim($observacoes))) {
            echo "Dados inválidos.";
            return;
        }

        $observacoes = trim($observacoes);

        $model = new ProblemaModel();

        if (!$model->adicionarObservacao($id, $observacoes, $_SESSION['id'])) {
            echo "Não foi possível adicionar a observação.";
            return;
        }

        header("Location: routes.php?acao=listarProblemas");
        exit;
    }

    public function historico() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id'])) {
            header("Location: ../view/login.php");
            exit;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $model = new ProblemaModel();
        $problema = $id ? $model->buscarPorId($id) : false;

        if (!$problema) {
            http_response_code(404);
            echo "Denúncia não encontrada.";
            return;
        }

        $ehAdministrador = (int) ($_SESSION['nivelAcesso'] ?? 1) === 0;
        if (!$ehAdministrador && (int) $problema['usuarioid'] !== (int) $_SESSION['id']) {
            http_response_code(403);
            echo "Acesso negado.";
            return;
        }

        $historico = $model->listarHistorico($id);
        require_once __DIR__.'/../view/historicoProblema.php';
    }

    private function pontoEmFrancoDaRocha($latitude, $longitude) {
        $caminhoGeoJson = __DIR__ . '/../view/assets/geo/franco-da-rocha.geojson';
        $conteudo = file_get_contents($caminhoGeoJson);
        $dados = json_decode($conteudo, true);

        if (!is_array($dados['features'] ?? null)) {
            return false;
        }

        foreach ($dados['features'] as $feature) {
            $propriedades = $feature['properties'] ?? [];

            if (($propriedades['CD_MUN'] ?? '') !== '3516408') {
                continue;
            }

            $geometria = $feature['geometry'] ?? [];
            $poligonos = $geometria['coordinates'] ?? [];

            foreach ($poligonos as $poligono) {
                if ($this->pontoNoPoligono($longitude, $latitude, $poligono)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function pontoNoPoligono($longitude, $latitude, $poligono) {
        if (empty($poligono[0])) {
            return false;
        }

        if (!$this->pontoNoAnel($longitude, $latitude, $poligono[0])) {
            return false;
        }

        foreach (array_slice($poligono, 1) as $buraco) {
            if ($this->pontoNoAnel($longitude, $latitude, $buraco)) {
                return false;
            }
        }

        return true;
    }

    private function pontoNoAnel($longitude, $latitude, $anel) {
        $dentro = false;
        $quantidade = count($anel);

        for ($i = 0, $j = $quantidade - 1; $i < $quantidade; $j = $i++) {
            $longitudeI = (float) $anel[$i][0];
            $latitudeI = (float) $anel[$i][1];
            $longitudeJ = (float) $anel[$j][0];
            $latitudeJ = (float) $anel[$j][1];

            $intersecta = (($latitudeI > $latitude) !== ($latitudeJ > $latitude))
                && ($longitude < ($longitudeJ - $longitudeI)
                    * ($latitude - $latitudeI)
                    / ($latitudeJ - $latitudeI)
                    + $longitudeI);

            if ($intersecta) {
                $dentro = !$dentro;
            }
        }

        return $dentro;
    }
}
?>