<?php
// 
require_once '../model/CategoriaModel.php';

class CategoriaController {

    public function abrirFormulario() {
        require_once '../view/categoriaForm.php';
    }

    public function salvar() {

        $nome = trim($_POST['nome'] ?? '');

        if(empty($nome)) {

            echo "
            <script>
                alert('Digite o nome da categoria!');
                history.back();
            </script>
            ";

            return;
        }

        if(strlen($nome) > 100){

            echo "
            <script>
                alert('Categoria muito longa!');
                history.back();
            </script>
            ";

            return;
        }

        $model = new CategoriaModel();

        $resultado = $model->inserir($nome);

        if($resultado == "existe") {

            echo "
            <script>
                alert('Categoria já cadastrada!');
                history.back();
            </script>
            ";

        } else {

            header("Location: ../view/dashboard.php");
            exit;
        }
    }

    public function listar() {

        $model = new CategoriaModel();

        $categorias = $model->listarTodos();

        require_once '../view/categoriaLista.php';
    }
}
?>