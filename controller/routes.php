<?php
require_once __DIR__.'/UsuarioController.php';
require_once __DIR__.'/CategoriaController.php';
require_once __DIR__.'/SubcategoriaController.php';
require_once __DIR__.'/ProblemaController.php';

$acao = $_GET['acao'] ?? 'dashboard';

$usuarioController = new UsuarioController();
$categoriaController = new CategoriaController();
$subcategoriaController = new SubcategoriaController();
$problemaController = new ProblemaController();

switch ($acao) {

    case 'login':
        $usuarioController->login();
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

    case 'salvarProblema':
        $problemaController->salvar();
        break;

    case 'dashboard':
        include __DIR__."/../view/dashboard.php";
        break;

    case 'uploadFoto':
        $usuarioController->uploadFoto();
        break;

    default:
        include __DIR__."/../view/dashboard.php";
        break;
}
?>