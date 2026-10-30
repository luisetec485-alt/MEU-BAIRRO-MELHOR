<?php
require_once 'Conexao.php';

class CategoriaModel {

    public function inserir($nome) {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare(
            "SELECT COUNT(*) FROM categorias WHERE nome = ?"
        );
        $stmt->execute([$nome]);

        if ((int) $stmt->fetchColumn() > 0) {
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

    public function listarComSubcategorias() {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare(
            "SELECT DISTINCT c.*
             FROM categorias c
             INNER JOIN subcategorias s
                ON s.categoriaid = c.id
             ORDER BY c.nome ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarComDenuncias($usuarioId = null) {
        $conexao = Conexao::getConexao();
        $sql = "SELECT DISTINCT c.*
                FROM categorias c
                INNER JOIN problemas p
                    ON p.categoriaid = c.id";
        $parametros = [];

        if ($usuarioId !== null) {
            $sql .= ' WHERE p.usuarioid = ?';
            $parametros[] = (int) $usuarioId;
        }

        $sql .= ' ORDER BY c.nome ASC';
        $stmt = $conexao->prepare($sql);
        $stmt->execute($parametros);

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