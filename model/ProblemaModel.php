<?php

require_once 'Conexao.php';

class ProblemaModel {

    public function inserir($titulo, $descricao, $imagem, $categoriaid, $subcategoriaid, $usuarioId) {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "INSERT INTO problemas (titulo, descricao, imagem, categoriaid, subcategoriaid, usuarioId)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->execute([$titulo, $descricao, $imagem, $categoriaid, $subcategoriaid, $usuarioId]);

        return "ok";
    }

    public function listarTodos() {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT
                p.*,
                c.nome AS categoriaNome,
                s.nome AS subcategoriaNome,
                l.nomeUsuario
            FROM problemas p
            INNER JOIN categorias c ON p.categoriaid = c.id
            INNER JOIN subcategorias s ON p.subcategoriaid = s.id
            INNER JOIN login l ON p.usuarioId = l.idUsuario
            ORDER BY p.dataproblema DESC"
        );

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare("SELECT * FROM problemas WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function excluir($id) {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare("DELETE FROM problemas WHERE id = ?");
        $stmt->execute([$id]);

        return "ok";
    }

    public function buscar($texto) {
        $conexao = Conexao::getConexao();

        $busca = "%".$texto."%";

        $stmt = $conexao->prepare(
            "SELECT
                p.*,
                c.nome AS categoriaNome,
                s.nome AS subcategoriaNome,
                l.nomeUsuario
            FROM problemas p
            INNER JOIN categorias c ON p.categoriaid = c.id
            INNER JOIN subcategorias s ON p.subcategoriaid = s.id
            INNER JOIN login l ON p.usuarioId = l.idUsuario
            WHERE p.titulo LIKE ?
                OR p.descricao LIKE ?
                OR c.nome LIKE ?
                OR s.nome LIKE ?
            ORDER BY p.dataproblema DESC"
        );

        $stmt->execute([$busca, $busca, $busca, $busca]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>