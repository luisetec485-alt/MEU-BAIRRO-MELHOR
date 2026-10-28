<?php

require_once __DIR__.'/../model/ProblemaModel.php';
require_once __DIR__.'/../model/CategoriaModel.php';
require_once __DIR__.'/../model/SubcategoriaModel.php';
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

        // 1.6 - Filtros enviados pelo formulário (GET) da tela de denúncias.
        $filtros = $this->lerFiltrosDaUrl();

        // "ordem" vem como recentes, antigas, prioridade ou titulo.
        $ordens = [
            'recentes' => ['data', 'DESC'],
            'antigas' => ['data', 'ASC'],
            'prioridade' => ['prioridade', 'DESC'],
            'titulo' => ['titulo', 'ASC']
        ];
        [$ordem, $direcao] = $ordens[$filtros['ordem']];

        $ehAdministrador = (int) ($_SESSION['nivelAcesso'] ?? 1) === 0;
        $usuarioFiltro = $ehAdministrador ? null : $_SESSION['id'];

        $problemas = $problemaModel->filtrar($filtros, $usuarioFiltro, $ordem, $direcao);
        $bairros = $problemaModel->listarBairros($usuarioFiltro);

        $filtrosAtivos = $filtros['texto'] !== ''
            || $filtros['categoriaid']
            || $filtros['subcategoriaid']
            || $filtros['status'] !== ''
            || $filtros['prioridade'] !== ''
            || $filtros['bairro'] !== '';

        $categoriaModel = new CategoriaModel();
        $subcategoriaModel = new SubcategoriaModel();

        $categorias = $categoriaModel->listarTodos();
        $subcategorias = $subcategoriaModel->listarTodos();

        require_once __DIR__.'/../view/problemaLista.php';
    }

    /*
     * Lê e limpa os filtros da URL. Valores fora do esperado são ignorados,
     * para que a consulta nunca quebre por causa de um parâmetro digitado à mão.
     */
    private function lerFiltrosDaUrl() {
        $texto = trim((string) ($_GET['busca'] ?? ''));
        $bairro = trim((string) ($_GET['bairro'] ?? ''));
        $status = (string) ($_GET['status'] ?? '');
        $prioridade = (string) ($_GET['prioridade'] ?? '');
        $ordem = (string) ($_GET['ordem'] ?? 'recentes');

        return [
            'texto' => mb_substr($texto, 0, 100),
            'categoriaid' => filter_var($_GET['categoria'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0,
            'subcategoriaid' => filter_var($_GET['subcategoria'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0,
            'status' => in_array($status, ['aguardando_encaminhamento', 'em_analise', 'recusada', 'enviado'], true) ? $status : '',
            'prioridade' => in_array($prioridade, ['baixa', 'media', 'alta', 'critica'], true) ? $prioridade : '',
            'bairro' => mb_substr($bairro, 0, 100),
            'ordem' => in_array($ordem, ['recentes', 'antigas', 'prioridade', 'titulo'], true) ? $ordem : 'recentes'
        ];
    }

    public function salvar() {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id'])) {
            header("Location: ../view/login.php");
            exit;
        }

        $titulo = trim((string) ($_POST['titulo'] ?? ''));
        $descricao = trim((string) ($_POST['descricao'] ?? ''));
        $bairro = trim((string) ($_POST['bairro'] ?? ''));
        $categoriaid = $_POST['categoriaid'] ?? '';
        $subcategoriaid = $_POST['subcategoriaid'] ?? '';
        $latitude = $_POST['latitude'] ?? '';
        $longitude = $_POST['longitude'] ?? '';
        $prioridade = (string) ($_POST['prioridade'] ?? '');
        $rua = trim((string) ($_POST['rua'] ?? ''));
        $usuarioId = $_SESSION['id'];
        $imagens = [];

        $categoriaid = filter_var($categoriaid, FILTER_VALIDATE_INT);
        $subcategoriaid = filter_var($subcategoriaid, FILTER_VALIDATE_INT);
        $latitude = filter_var($latitude, FILTER_VALIDATE_FLOAT);
        $longitude = filter_var($longitude, FILTER_VALIDATE_FLOAT);

        // 4.1 - cada campo obrigatório é conferido no servidor com uma mensagem própria.
        // Espaços em branco não contam como preenchido.
        $destinoFormulario = 'routes.php?acao=formProblema';

        if ($titulo === '') {
            MensagemFlash::redirecionar($destinoFormulario, 'Preencha o título do problema.', 'erro');
        }

        if (mb_strlen($titulo) > 255) {
            MensagemFlash::redirecionar($destinoFormulario, 'O título deve ter no máximo 255 caracteres.', 'erro');
        }

        if ($descricao === '') {
            MensagemFlash::redirecionar($destinoFormulario, 'Descreva o problema encontrado.', 'erro');
        }

        if (mb_strlen($descricao) > 5000) {
            MensagemFlash::redirecionar($destinoFormulario, 'A descrição deve ter no máximo 5000 caracteres.', 'erro');
        }

        if (mb_strlen($bairro) > 100) {
            MensagemFlash::redirecionar($destinoFormulario, 'O nome do bairro deve ter no máximo 100 caracteres.', 'erro');
        }

        if (mb_strlen($rua) > 180) {
            MensagemFlash::redirecionar($destinoFormulario, 'O nome da rua deve ter no máximo 180 caracteres.', 'erro');
        }

        if (!$categoriaid) {
            MensagemFlash::redirecionar($destinoFormulario, 'Selecione a categoria do problema.', 'erro');
        }

        if (!$subcategoriaid) {
            MensagemFlash::redirecionar($destinoFormulario, 'Selecione a subcategoria do problema.', 'erro');
        }

        if (!in_array($prioridade, ['baixa', 'media', 'alta', 'critica'], true)) {
            MensagemFlash::redirecionar($destinoFormulario, 'Selecione a prioridade do problema.', 'erro');
        }

        if ($latitude === false || $longitude === false) {
            MensagemFlash::redirecionar($destinoFormulario, 'Marque no mapa o local do problema.', 'erro');
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
         * Upload de até 5 imagens e 1 vídeo.
         */
        if (isset($_FILES['imagem']) && is_array($_FILES['imagem']['name'])) {
            $arquivos = $_FILES['imagem'];
            $quantidadeImagens = 0;
            $quantidadeVideos = 0;
            $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'mp4', 'mov', 'avi', 'webm'];
            $tiposPermitidos = [
                'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
                'mp4' => 'video/mp4', 'mov' => 'video/quicktime',
                'avi' => 'video/x-msvideo', 'webm' => 'video/webm'
            ];

            foreach ($arquivos['name'] as $indice => $nomeOriginal) {
                if ($nomeOriginal === '') {
                    continue;
                }

                if ($arquivos['error'][$indice] !== UPLOAD_ERR_OK) {
                    MensagemFlash::redirecionar(
                        'routes.php?acao=formProblema',
                        'Um dos arquivos não pôde ser enviado. Tente novamente.',
                        'erro'
                    );
                }

                $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $tipoMime = $finfo ? finfo_file($finfo, $arquivos['tmp_name'][$indice]) : false;
                if ($finfo) {
                    finfo_close($finfo);
                }

                if (!isset($tiposPermitidos[$extensao])
                    || $tipoMime !== $tiposPermitidos[$extensao]
                ) {
                    MensagemFlash::redirecionar(
                        'routes.php?acao=formProblema',
                        'Os arquivos devem ser JPG, PNG, MP4, MOV, AVI ou WEBM.',
                        'erro'
                    );
                }

                $tipoArquivo = in_array($extensao, ['jpg', 'jpeg', 'png'], true) ? 'img' : 'video';
                if ($tipoArquivo === 'img') {
                    $quantidadeImagens++;
                } else {
                    $quantidadeVideos++;
                }

                if ($quantidadeImagens > 5 || $quantidadeVideos > 1) {
                    MensagemFlash::redirecionar(
                        'routes.php?acao=formProblema',
                        'É permitido anexar até 5 imagens e 1 vídeo por denúncia.',
                        'erro'
                    );
                }

                $limiteBytes = $tipoArquivo === 'img' ? 10 * 1024 * 1024 : 50 * 1024 * 1024;
                if ($arquivos['size'][$indice] > $limiteBytes) {
                    $mensagem = $tipoArquivo === 'img'
                        ? 'Cada imagem pode ter no máximo 10MB.'
                        : 'O vídeo pode ter no máximo 50MB.';
                    MensagemFlash::redirecionar('routes.php?acao=formProblema', $mensagem, 'erro');
                }

                $pastaDestino = __DIR__ . '/../view/' . $tipoArquivo . '/problemas/';
                if (!is_dir($pastaDestino)) {
                    mkdir($pastaDestino, 0775, true);
                }

                $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $extensao;
                if (!move_uploaded_file($arquivos['tmp_name'][$indice], $pastaDestino . $nomeArquivo)) {
                    MensagemFlash::redirecionar(
                        'routes.php?acao=formProblema',
                        'Não foi possível enviar um dos arquivos. Tente novamente.',
                        'erro'
                    );
                }

                $imagens[] = $tipoArquivo . '/problemas/' . $nomeArquivo;
            }
        }

        $model = new ProblemaModel();

        $resultado = $model->inserir(
            $titulo,
            $descricao,
            $imagens,
            $categoriaid,
            $subcategoriaid,
            $usuarioId,
            $latitude,
            $longitude,
            $prioridade,
            $bairro !== '' ? $bairro : null,
            $rua !== '' ? $rua : null
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

    public function atualizarStatusEncaminhamento() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['id'])) {
            header("Location: ../view/login.php");
            exit;
        }

        if ((int) ($_SESSION['nivelAcesso'] ?? 1) !== 0) {
            http_response_code(403);
            echo "Apenas administradores podem atualizar o encaminhamento.";
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
        $problema = $model->buscarPorId($id);

        if (!$problema) {
            MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'Denúncia não encontrada.', 'erro');
        }

        $status = (string) ($_POST['status'] ?? '');
        if (!$model->atualizarStatus($id, $status, $_SESSION['id'])) {
            MensagemFlash::redirecionar('routes.php?acao=listarProblemas', 'O status informado é inválido ou não pôde ser atualizado.', 'erro');
        }

        $rotulos = [
            'aguardando_encaminhamento' => 'Aguardando encaminhamento',
            'em_analise' => 'Em análise',
            'recusada' => 'Recusada',
            'enviado' => 'Enviado'
        ];
        MensagemFlash::redirecionar(
            'routes.php?acao=listarProblemas',
            'Status atualizado para "' . ($rotulos[$status] ?? $status) . '".'
        );
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