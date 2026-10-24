<?php
// 
require_once '../model/CategoriaModel.php';
require_once '../model/MensagemFlash.php';

class CategoriaController {

    public function abrirFormulario() {
        require_once '../view/categoriaForm.php';
    }

    public function salvar() {

        $nome = trim($_POST['nome'] ?? '');

        if (empty($nome)) {
            MensagemFlash::redirecionar(
                'routes.php?acao=formCategoria',
                'Digite o nome da categoria.',
                'erro'
            );
        }

        if (strlen($nome) > 100) {
            MensagemFlash::redirecionar(
                'routes.php?acao=formCategoria',
                'O nome da categoria deve ter no máximo 100 caracteres.',
                'erro'
            );
        }

        $model = new CategoriaModel();

        $resultado = $model->inserir($nome);

        if ($resultado === 'existe') {
            MensagemFlash::redirecionar(
                'routes.php?acao=formCategoria',
                'Essa categoria já está cadastrada.',
                'erro'
            );
        }

        MensagemFlash::redirecionar(
            'routes.php?acao=listarCategorias',
            'Categoria cadastrada com sucesso.'
        );
    }

    public function listar() {

        $model = new CategoriaModel();

        $categorias = $model->listarTodos();

        require_once '../view/categoriaLista.php';
    }
}
?>