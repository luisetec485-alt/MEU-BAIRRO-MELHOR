<?php

require_once 'Conexao.php';

class RecuperacaoSenhaModel
{
    public function buscarUsuarioPorEmail($email)
    {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT idUsuario, nomeUsuario, Email
             FROM login
             WHERE Email = ?"
        );

        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvarToken($idUsuario, $tokenHash, $expiraEm)
    {
        $conexao = Conexao::getConexao();

        // Remove tokens antigos desse usuário
        $stmt = $conexao->prepare(
            "DELETE FROM recuperacao_senha
             WHERE idUsuario = ?"
        );

        $stmt->execute([$idUsuario]);

        // Salva o novo token
        $stmt = $conexao->prepare(
            "INSERT INTO recuperacao_senha
             (idUsuario, token_hash, expira_em)
             VALUES (?, ?, ?)"
        );

        return $stmt->execute([
            $idUsuario,
            $tokenHash,
            $expiraEm
        ]);
    }

    public function buscarTokenValido($tokenHash)
    {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT id, idUsuario
            FROM recuperacao_senha
            WHERE token_hash = ?
            AND expira_em > NOW()"
        );

        $stmt->execute([$tokenHash]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarSenha($idUsuario, $senhaHash)
    {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "UPDATE login
             SET Senha = ?
             WHERE idUsuario = ?"
        );

        return $stmt->execute([
            $senhaHash,
            $idUsuario
        ]);
    }

    public function excluirToken($id)
    {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "DELETE FROM recuperacao_senha
             WHERE id = ?"
        );

        return $stmt->execute([$id]);
    }
}

?>