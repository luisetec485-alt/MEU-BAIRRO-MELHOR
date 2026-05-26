<?php

// Inclui os arquivos dos controllers
// __DIR__ garante o caminho correto independente de onde o script for chamado
require_once __DIR__.'/UsuarioController.php';
require_once __DIR__.'/CategoriaController.php';
require_once __DIR__.'/SubcategoriaController.php';
require_once __DIR__.'/ProblemaController.php';

// Captura a ação enviada pela URL (GET)
// Exemplo: routes.php?acao=listaCategorias
// Se não existir, usa 'dashboard' como padrão
$acao = $_GET['acao'] ?? 'dashboard';

// Instancia os controllers
$usuarioController = new UsuarioController();
$categoriaController = new CategoriaController();
$subcategoriaController = new SubcategoriaController();
$problemaController = new ProblemaController();

// Estrutura de decisão baseada na ação recebida
switch ($acao) {

    // USUÁRIO ---------------------------------------------------------------
    case 'login':
        // Chama o método login do UsuarioController
        $usuarioController->login();
        break;

    case 'cadastrar':
        // Chama o método cadastrar do UsuarioController
        $usuarioController->cadastrar();
        break;

    case 'editar':
        // Chama o método mostrarEditar do UsuarioController
        $usuarioController->mostrarEditar();
        break;

    case 'atualizar':
        // Chama o método atualizar do UsuarioController
        $usuarioController->atualizar();
        break;

    case 'excluir':
        // Chama o método excluir do UsuarioController
        $usuarioController->excluir();
        break;

    case 'logout':
        // Chama o método logout do UsuarioController
        $usuarioController->logout();
        break;

    // CATEGORIAS ---------------------------------------------------------------
    case 'formCategoria':
        // Carrega diretamente a view do formulário de categoria
        include __DIR__."/../view/categoriaForm.php";
        break;

    case 'listarCategorias':
        // Chama o método listar do CategoriaController
        $categoriaController->listar();
        break;

    case 'salvarCategoria':
        // Chama o método salvar do CategoriaController
        $categoriaController->salvar();
        break;

    // SUBCATEGORIAS ---------------------------------------------------------------
    case 'formSubcategoria':
        // Carrega diretamente a view do formulário de subcategoria
        include __DIR__."/../view/subcategoriaForm.php";
        break;

    case 'listarSubcategorias':
        // Chama o método listar do SubcategoriaController
        $subcategoriaController->listar();
        break;

    case 'salvarSubcategoria':
        // Chama o método salvar do SubcategoriaController
        $subcategoriaController->salvar();
        break;

    // PROBLEMAS ---------------------------------------------------------------
    case 'formProblema':
        // Carrega diretamente a view do formulário de problema
        include __DIR__."/../view/problemaForm.php";
        break;

    case 'listarProblemas':
        // Chama o método listar do ProblemaController
        $problemaController->listar();
        break;

    case 'salvarProblema':
        // Chama o método salvar do ProblemaController
        $problemaController->salvar();
        break;

    // DASHBOARD (HOME) ---------------------------------------------------------------
    case 'dashboard':
        // Carrega diretamente a view do dashboard
        include __DIR__."/../view/dashboard.php";
        break;

    default:
        // Caso a ação não exista, volta para o dashboard
        include __DIR__."/../view/dashboard.php";
        break;
}
?>