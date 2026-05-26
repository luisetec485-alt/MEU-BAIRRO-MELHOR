<?php

require_once 'Conexao.php';

class CategoriaModel {

    // inserir categoria
    public function inserir($nome) {

        $conexao = Conexao::getConexao();

        // verificar se já existe
        $stmt = $conexao->prepare(
            "SELECT * FROM categorias WHERE nome = ?"
        );

        $stmt->execute([$nome]);

        if($stmt->rowCount() > 0){
            return "existe";
        }

        // inserir categoria
        $stmt = $conexao->prepare(
            "INSERT INTO categorias(nome)
             VALUES(?)"
        );

        $stmt->execute([$nome]);

        return "ok";
    }

    // listar categorias
    public function listarTodos() {

        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT * FROM categorias
             ORDER BY nome ASC"
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // buscar categoria por id
    public function buscarPorId($id) {

        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT * FROM categorias
             WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>