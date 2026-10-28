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

        $nome = trim((string) ($_POST['nome'] ?? ''));
        $categoriaid = filter_var($_POST['categoriaid'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($nome === '') {
            MensagemFlash::redirecionar(
                'routes.php?acao=formSubcategoria',
                'Digite o nome da subcategoria.',
                'erro'
            );
        }

        if (!$categoriaid) {
            MensagemFlash::redirecionar(
                'routes.php?acao=formSubcategoria',
                'Selecione a categoria principal da subcategoria.',
                'erro'
            );
        }

        if (mb_strlen($nome) > 100) {
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

        if ($model->existeNaCategoria($nome, $categoriaid)) {
            MensagemFlash::redirecionar(
                'routes.php?acao=formSubcategoria',
                'Essa subcategoria já existe nessa categoria.',
                'erro'
            );
        }

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