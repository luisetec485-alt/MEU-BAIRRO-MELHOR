<?php

require_once 'Conexao.php';

class SubcategoriaModel {

    // inserir subcategoria
    public function inserir($nome, $categoriaId) {

        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "INSERT INTO subcategorias(nome, categoriaId)
             VALUES(?, ?)"
        );

        $stmt->execute([$nome, $categoriaId]);

        return "ok";
    }

    // listar subcategorias com nome da categoria
    public function listarTodos() {

        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT
                s.*,
                c.nome AS categoriaNome
             FROM subcategorias s
             INNER JOIN categorias c
                ON s.categoriaId = c.id
             ORDER BY s.nome ASC"
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // buscar subcategorias por categoria (para selects dinâmicos)
    public function buscarPorCategoria($categoriaId) {

        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT *
             FROM subcategorias
             WHERE categoriaId = ?
             ORDER BY nome ASC"
        );

        $stmt->execute([$categoriaId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // buscar subcategoria por id
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