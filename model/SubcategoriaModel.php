<?php

require_once 'Conexao.php';

class SubcategoriaModel {

    public function inserir($nome, $categoriaid) {

        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "INSERT INTO subcategorias(nome, categoriaid)
             VALUES(?, ?)"
        );

        $stmt->execute([$nome, $categoriaid]);

        return "ok";
    }

    public function listarTodos() {

        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT
                s.*,
                c.nome AS categoriaNome
             FROM subcategorias s
             INNER JOIN categorias c
                ON s.categoriaid = c.id
             ORDER BY s.nome ASC"
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorCategoria($categoriaid) {

        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT *
             FROM subcategorias
             WHERE categoriaid = ?
             ORDER BY nome ASC"
        );

        $stmt->execute([$categoriaid]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeNaCategoria($nome, $categoriaid) {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT COUNT(*) FROM subcategorias
             WHERE nome = ? AND categoriaid = ?"
        );

        $stmt->execute([$nome, $categoriaid]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function pertenceACategoria($subcategoriaid, $categoriaid) {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT id FROM subcategorias
             WHERE id = ? AND categoriaid = ?"
        );

        $stmt->execute([$subcategoriaid, $categoriaid]);

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {

        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT *
             FROM subcategorias
             WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>