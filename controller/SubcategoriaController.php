<?php

require_once '../model/SubcategoriaModel.php';
require_once '../model/CategoriaModel.php';

class SubcategoriaController {
    public function abrirFormulario() {
        $categoriaModel = new CategoriaModel();
        $categorias = $categoriaModel->listarTodos();
        require_once '../view/subcategoriaForm.php';
    }

    public function salvar() {

        $nome = trim($_POST['nome'] ?? '');
        $categoriaid = $_POST['categoriaid'] ?? '';
        if(empty($nome) || empty($categoriaid)) {

            echo "
            <script>
                alert('Preencha todos os campos!');
                history.back();
            </script>
            ";

            return;
        }

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
        $model->inserir($nome, $categoriaid);
        header("Location: ../view/dashboard.php");
        exit;
    }

    public function listar() {
        $model = new SubcategoriaModel();
        $subcategorias = $model->listarTodos();
        require_once '../view/subcategoriaLista.php';
    }
}
?>