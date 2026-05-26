<?php

require_once '../model/ProblemaModel.php';

class ProblemaController {

    // listar problemas
    public function listar() {

        $model = new ProblemaModel();

        $busca = $_GET['busca'] ?? '';

        if(!empty($busca)) {

            $problemas = $model->buscar($busca);

        } else {

            $problemas = $model->listarTodos();
        }

        require_once '../view/problemaLista.php';
    }

    // salvar problema
    public function salvar() {

        session_start();

        $titulo = $_POST['titulo'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $imagem = $_POST['imagem'] ?? '';
        $categoriaId = $_POST['categoriaId'] ?? '';
        $subcategoriaId = $_POST['subcategoriaId'] ?? '';
        $usuarioId = $_SESSION['id'];

        if(
            empty($titulo) ||
            empty($descricao) ||
            empty($categoriaId) ||
            empty($subcategoriaId)
        ) {

            echo "Preencha todos os campos.";
            return;
        }

        $model = new ProblemaModel();

        $model->inserir(
            $titulo,
            $descricao,
            $imagem,
            $categoriaId,
            $subcategoriaId,
            $usuarioId
        );

        header(
            'Location: routes.php?acao=listarProblemas'
        );
    }
}
?>