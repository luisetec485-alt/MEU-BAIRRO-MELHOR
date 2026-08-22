<?php

require_once __DIR__.'/../model/ProblemaModel.php';
require_once __DIR__.'/../model/CategoriaModel.php';
require_once __DIR__.'/../model/SubcategoriaModel.php';

class ProblemaController {

    public function abrirFormulario() {

        $categoriaModel = new CategoriaModel();
        $subcategoriaModel = new SubcategoriaModel();

        $categorias = $categoriaModel->listarTodos();
        $subcategorias = $subcategoriaModel->listarTodos();

        require_once '../view/problemaForm.php';
    }

    public function listar() {

        $problemaModel = new ProblemaModel();

        $problemas = $problemaModel->listarTodos();

        $categoriaModel = new CategoriaModel();
        $subcategoriaModel = new SubcategoriaModel();

        $categorias = $categoriaModel->listarTodos();
        $subcategorias = $subcategoriaModel->listarTodos();

        require_once __DIR__.'/../view/problemaLista.php';
    }

    public function salvar() {

        session_start();

        $titulo = $_POST['titulo'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $imagem = $_POST['imagem'] ?? '';
        $categoriaid = $_POST['categoriaid'] ?? '';
        $subcategoriaid = $_POST['subcategoriaid'] ?? '';
        $usuarioId = $_SESSION['id'];

        if(
            empty($titulo) ||
            empty($descricao) ||
            empty($categoriaid) ||
            empty($subcategoriaid)
        ) {

            echo "Preencha todos os campos.";
            return;
        }

        $model = new ProblemaModel();

        $model->inserir(
            $titulo,
            $descricao,
            $imagem,
            $categoriaid,
            $subcategoriaid,
            $usuarioId
        );

        header(
            'Location: routes.php?acao=listarProblemas'
        );
    }
}
?>