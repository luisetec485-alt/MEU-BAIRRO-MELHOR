<?php

require_once __DIR__.'/Conexao.php';

class MensagemModel {

    public function enviar($problemaid, $usuarioid, $mensagem) {

        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "INSERT INTO mensagens (problemaid, usuarioid, mensagem)
             VALUES (?, ?, ?)"
        );

        return $stmt->execute([$problemaid, $usuarioid, $mensagem]);
    }

    public function listarPorProblema($problemaid) {

        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT
                m.id,
                m.mensagem,
                m.dataenvio,
                m.usuarioid,
                l.nomeUsuario,
                l.nivelAcesso
            FROM mensagens m
            INNER JOIN login l ON m.usuarioid = l.idUsuario
            WHERE m.problemaid = ?
            ORDER BY m.dataenvio ASC"
        );

        $stmt->execute([$problemaid]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>