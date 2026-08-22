<?php
require_once 'Conexao.php';

class CategoriaModel {

    public function inserir($nome) {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare(
            "SELECT * FROM categorias WHERE nome = ?"
        );
        $stmt->execute([$nome]);

        if($stmt->rowCount() > 0){
            return "existe";
        }
        $stmt = $conexao->prepare(
            "INSERT INTO categorias(nome)
             VALUES(?)"
        );
        $stmt->execute([$nome]);
        return "ok";
    }

    public function listarTodos() {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare(
            "SELECT * FROM categorias
             ORDER BY nome ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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