<?php

require_once __DIR__.'/../model/ProblemaModel.php';
require_once __DIR__.'/../model/CategoriaModel.php';
require_once __DIR__.'/../model/SubcategoriaModel.php';
require_once __DIR__.'/../model/UsuarioModel.php';
require_once __DIR__.'/../model/AuxiliarEmail.php';
require_once __DIR__.'/../model/NotificacaoModel.php';
require_once __DIR__.'/../model/MensagemFlash.php';

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
            MensagemFlash::redirecionar(
                'routes.php?acao=formProblema',
                'Preencha os campos obrigatórios e selecione a localização no mapa.',
                'erro'
            );
        }

        if ($latitude === false || $longitude === false
            || $latitude < -90 || $latitude > 90
            || $longitude < -180 || $longitude > 180
        ) {
            MensagemFlash::redirecionar(
                'routes.php?acao=formProblema',
                'Selecione uma localização válida no mapa.',
                'erro'
            );
        }

        if (!$this->pontoEmFrancoDaRocha($latitude, $longitude)) {
            MensagemFlash::redirecionar(
                'routes.php?acao=formProblema',
                'A localização selecionada deve estar dentro de Franco da Rocha.',
                'erro'
            );
        }

        $subcategoriaModel = new SubcategoriaModel();

        if (!(new CategoriaModel())->buscarPorId($categoriaid)) {
            MensagemFlash::redirecionar(
                'routes.php?acao=formProblema',
                'A categoria selecionada não está disponível. Escolha outra.',
                'erro'
            );
        }

        if (!$subcategoriaModel->pertenceACategoria($subcategoriaid, $categoriaid)) {
            MensagemFlash::redirecionar(
                'routes.php?acao=formProblema',
                'Escolha uma subcategoria pertencente à categoria selecionada.',
                'erro'
            );
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
                MensagemFlash::redirecionar(
                    'routes.php?acao=formProblema',
                    'O arquivo deve ser uma imagem ou vídeo em formato permitido (JPG, PNG, MP4, MOV, AVI ou WEBM).',
                    'erro'
                );
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

                MensagemFlash::redirecionar('routes.php?acao=formProblema', $mensagem, 'erro');
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
                MensagemFlash::redirecionar(
                    'routes.php?acao=formProblema',
                    'Não foi possível enviar o arquivo. Tente novamente.',
                    'erro'
                );
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
            MensagemFlash::redirecionar(
                'routes.php?acao=formProblema',
                'Não foi possível registrar o problema. Revise os dados e tente novamente.',
                'erro'
            );
        }

        MensagemFlash::redirecionar(
            'routes.php?acao=listarProblemas',
            'Problema registrado com sucesso.'
        );
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
            echo "Esta operação não aceita esse tipo de solicitação.";
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'Não foi possível identificar a denúncia.', 'erro');
        }

        $model = new ProblemaModel();

        if (!$model->cancelar($id, $_SESSION['id'])) {
            MensagemFlash::redirecionar(
                'routes.php?acao=listarProblemas',
                'A denúncia não pode ser cancelada. Verifique se ela pertence à sua conta e ainda está aberta.',
                'erro'
            );
        }

        MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'Denúncia cancelada com sucesso.');
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
            echo "Esta operação não aceita esse tipo de solicitação.";
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'Não foi possível identificar a denúncia.', 'erro');
        }

        $model = new ProblemaModel();

        if (!$model->excluir($id, $_SESSION['id'])) {
            MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'Não foi possível excluir a denúncia.', 'erro');
        }

        MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'Denúncia excluída com sucesso.');
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
            echo "Esta operação não aceita esse tipo de solicitação.";
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';
        $statusPermitidos = ['aberta', 'em_analise', 'resolvida', 'cancelada'];

        if (!$id || !in_array($status, $statusPermitidos, true)) {
            MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'O status informado é inválido.', 'erro');
        }

        $model = new ProblemaModel();
        $problema = $model->buscarPorId($id);

        if (!$problema) {
            MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'Denúncia não encontrada.', 'erro');
        }

        if (!$model->atualizarStatus($id, $status, $_SESSION['id'])) {
            MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'Não foi possível atualizar o status da denúncia.', 'erro');
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

        MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'Status da denúncia atualizado com sucesso.');
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
            echo "Esta operação não aceita esse tipo de solicitação.";
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $prioridade = $_POST['prioridade'] ?? '';
        $prioridadesPermitidas = ['baixa', 'media', 'alta', 'critica'];

        if (!$id || !in_array($prioridade, $prioridadesPermitidas, true)) {
            MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'A prioridade informada é inválida.', 'erro');
        }

        $model = new ProblemaModel();

        if (!$model->atualizarPrioridade($id, $prioridade, $_SESSION['id'])) {
            MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'Não foi possível atualizar a prioridade da denúncia.', 'erro');
        }

        MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'Prioridade da denúncia atualizada com sucesso.');
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
            echo "Esta operação não aceita esse tipo de solicitação.";
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $observacoes = $_POST['observacoes'] ?? '';

        if (!$id || empty(trim($observacoes))) {
            MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'Informe uma observação válida para a denúncia.', 'erro');
        }

        $observacoes = trim($observacoes);

        $model = new ProblemaModel();

        if (!$model->adicionarObservacao($id, $observacoes, $_SESSION['id'])) {
            MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'Não foi possível salvar a observação.', 'erro');
        }

        MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'Observação adicionada com sucesso.');
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
            MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'Denúncia não encontrada.', 'erro');
        }

        $ehAdministrador = (int) ($_SESSION['nivelAcesso'] ?? 1) === 0;
        if (!$ehAdministrador && (int) $problema['usuarioid'] !== (int) $_SESSION['id']) {
            MensagemFlash::redirecionar(
                'routes.php?acao=listarProblemas',
                'Você não tem permissão para consultar o histórico desta denúncia.',
                'erro'
            );
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