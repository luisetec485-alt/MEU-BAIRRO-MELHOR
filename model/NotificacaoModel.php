<?php

require_once 'Conexao.php';

class NotificacaoModel
{
    public function criar($usuarioId, $mensagem)
    {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare("INSERT INTO notificacoes (usuarioid, mensagem) VALUES (?, ?)");
        return $stmt->execute([$usuarioId, $mensagem]);
    }

    public function buscarNaoLidas($usuarioId)
    {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare("SELECT * FROM notificacoes WHERE usuarioid = ? AND lida = 0 ORDER BY data_criacao ASC");
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarComoLida($id)
    {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare("UPDATE notificacoes SET lida = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>