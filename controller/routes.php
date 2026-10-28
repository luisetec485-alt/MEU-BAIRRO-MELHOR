<?php
require_once __DIR__.'/UsuarioController.php';
require_once __DIR__.'/CategoriaController.php';
require_once __DIR__.'/SubcategoriaController.php';
require_once __DIR__.'/ProblemaController.php';
require_once __DIR__.'/RecuperacaoSenhaController.php';
require_once __DIR__.'/../model/Seguranca.php';
require_once __DIR__.'/../model/MensagemFlash.php';

Seguranca::iniciarSessao();

$acao = $_GET['acao'] ?? 'dashboard';

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && !Seguranca::validarToken($_POST['csrf_token'] ?? null)
) {
    $destinosCsrf = [
        'login' => '../view/login.php',
        'cadastrar' => '../view/cadastro.php',
        'recuperarSenha' => '../view/esqueceuSenha.php',
        'redefinirSenha' => '../view/login.php',
        'atualizar' => 'routes.php?acao=editar',
        'salvarCategoria' => 'routes.php?acao=formCategoria',
        'salvarSubcategoria' => 'routes.php?acao=formSubcategoria',
        'salvarProblema' => 'routes.php?acao=formProblema',
        'atualizarStatusEncaminhamento' => 'routes.php?acao=listarProblemas',
        'excluirProblema' => 'routes.php?acao=listarProblemas',
        'atualizarPrioridadeProblema' => 'routes.php?acao=listarProblemas',
        'adicionarObservacao' => 'routes.php?acao=listarProblemas',
        'excluir' => 'routes.php?acao=editar'
    ];
    $destino = $destinosCsrf[$acao] ?? '../view/login.php';
    MensagemFlash::redirecionar(
        $destino,
        'Sua solicitação expirou ou é inválida. Tente novamente.',
        'erro'
    );
}

$acoesPublicas = [
    'login',
    'recuperarSenha',
    'mostrarRedefinicao',
    'redefinirSenha',
    'cadastrar'
];

$acoesAdministrativas = [
    'painelAdmin',
    'formCategoria',
    'listarCategorias',
    'salvarCategoria',
    'formSubcategoria',
    'listarSubcategorias',
    'salvarSubcategoria',
    'relatorioUsuarios',
    'excluirProblema',
    'atualizarStatusEncaminhamento',
    'historicoProblema',
    'atualizarPrioridadeProblema',
    'adicionarObservacao'
];

$acoesExclusivasCidadao = ['formProblema', 'salvarProblema'];

if (!in_array($acao, $acoesPublicas, true) && !isset($_SESSION['id'])) {
    header('Location: ../view/login.php');
    exit;
}

// 3.2 - Funções de administrador: usuário comum recebe a página de acesso negado (403).
if (in_array($acao, $acoesAdministrativas, true) && !Seguranca::ehAdministrador()) {
    Seguranca::negarAcesso();
}

if (in_array($acao, $acoesExclusivasCidadao, true) && Seguranca::ehAdministrador()) {
    Seguranca::negarAcesso();
}

$usuarioController = new UsuarioController();
$categoriaController = new CategoriaController();
$subcategoriaController = new SubcategoriaController();
$problemaController = new ProblemaController();
$recuperacaoSenhaController = new RecuperacaoSenhaController();

switch ($acao) {

    case 'login':
        $usuarioController->login();
        break;

    case 'recuperarSenha':
        $recuperacaoSenhaController->solicitarRecuperacao();
        break;

    case 'mostrarRedefinicao':
        $recuperacaoSenhaController->mostrarRedefinicao();
        break;

    case 'redefinirSenha':
        $recuperacaoSenhaController->redefinirSenha();
        break;

    case 'cadastrar':
        $usuarioController->cadastrar();
        break;

    case 'editar':
        $usuarioController->mostrarEditar();
        break;

    case 'atualizar':
        $usuarioController->atualizar();
        break;

    case 'excluir':
        $usuarioController->excluir();
        break;

    case 'logout':
        $usuarioController->logout();
        break;

    case 'formCategoria':
        $categoriaController->abrirFormulario();
        break;

    case 'listarCategorias':
        $categoriaController->listar();
        break;

    case 'salvarCategoria':
        $categoriaController->salvar();
        break;

    case 'formSubcategoria':
        $subcategoriaController->abrirFormulario();
        break;

    case 'listarSubcategorias':
        $subcategoriaController->listar();
        break;

    case 'salvarSubcategoria':
        $subcategoriaController->salvar();
        break;

    case 'formProblema':
        $problemaController->abrirFormulario();
        break;

    case 'listarProblemas':
        $problemaController->listar();
        break;

    case 'historicoProblema':
        $problemaController->historico();
        break;

    case 'salvarProblema':
        $problemaController->salvar();
        break;

    case 'excluirProblema':
        $problemaController->excluir();
        break;

    case 'atualizarStatusEncaminhamento':
        $problemaController->atualizarStatusEncaminhamento();
        break;

    case 'atualizarPrioridadeProblema':
        $problemaController->atualizarPrioridade();
        break;

    case 'adicionarObservacao':
        $problemaController->adicionarObservacao();
        break;

    case 'dashboard':
        include __DIR__."/../view/dashboard.php";
        break;

    case 'painelAdmin':
        include __DIR__."/../view/adminDashboard.php";
        break;

    case 'relatorioUsuarios':
        include __DIR__."/../view/usuariosRelatorio.php";
        break;

    default:
        include __DIR__."/../view/dashboard.php";
        break;
}
?>