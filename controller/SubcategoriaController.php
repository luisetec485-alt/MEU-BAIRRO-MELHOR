<?php

require_once '../model/SubcategoriaModel.php';
require_once '../model/CategoriaModel.php';

class SubcategoriaController {

    // abrir formulário
    public function abrirFormulario() {

        $categoriaModel = new CategoriaModel();

        // buscar categorias para o select
        $categorias = $categoriaModel->listarTodos();

        require_once '../view/subcategoriaForm.php';
    }

    // salvar subcategoria
    public function salvar() {

        $nome = trim($_POST['nome'] ?? '');
        $categoriaId = $_POST['categoriaId'] ?? '';

        // validar vazio
        if(empty($nome) || empty($categoriaId)) {

            echo "
            <script>
                alert('Preencha todos os campos!');
                history.back();
            </script>
            ";

            return;
        }

        // validar tamanho
        if(strlen($nome) > 100){

            echo "
            <script>
                alert('Nome muito longo!');
                history.back();
            </script>
            ";

            return;
        }

        $model = new SubcategoriaModel();

        $model->inserir($nome, $categoriaId);

        header("Location: ../view/dashboard.php");
        exit;
    }

    // listar subcategorias
    public function listar() {

        $model = new SubcategoriaModel();

        $subcategorias = $model->listarTodos();

        require_once '../view/subcategoriaLista.php';
    }
}
?>