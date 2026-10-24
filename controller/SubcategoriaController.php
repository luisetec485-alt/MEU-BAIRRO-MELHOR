<?php

require_once '../model/SubcategoriaModel.php';
require_once '../model/CategoriaModel.php';
require_once '../model/MensagemFlash.php';

class SubcategoriaController {
    public function abrirFormulario() {
        $categoriaModel = new CategoriaModel();
        $categorias = $categoriaModel->listarTodos();
        require_once '../view/subcategoriaForm.php';
    }

    public function salvar() {

        $nome = trim($_POST['nome'] ?? '');
        $categoriaid = $_POST['categoriaid'] ?? '';
        if (empty($nome) || empty($categoriaid)) {
            MensagemFlash::redirecionar(
                'routes.php?acao=formSubcategoria',
                'Preencha todos os campos da subcategoria.',
                'erro'
            );
        }

        if (strlen($nome) > 100) {
            MensagemFlash::redirecionar(
                'routes.php?acao=formSubcategoria',
                'O nome da subcategoria deve ter no máximo 100 caracteres.',
                'erro'
            );
        }

        $categoriaModel = new CategoriaModel();
        if (!$categoriaModel->buscarPorId($categoriaid)) {
            MensagemFlash::redirecionar(
                'routes.php?acao=formSubcategoria',
                'Selecione uma categoria válida.',
                'erro'
            );
        }

        $model = new SubcategoriaModel();
        $model->inserir($nome, $categoriaid);
        MensagemFlash::redirecionar(
            'routes.php?acao=listarSubcategorias',
            'Subcategoria cadastrada com sucesso.'
        );
    }

    public function listar() {
        $model = new SubcategoriaModel();
        $subcategorias = $model->listarTodos();
        require_once '../view/subcategoriaLista.php';
    }
}
?>