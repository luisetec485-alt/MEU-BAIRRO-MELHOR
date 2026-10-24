<?php
require_once __DIR__.'/../model/Conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit;
}

try {
    $conexao = Conexao::getConexao();
    $stmt = $conexao->prepare("UPDATE notificacoes SET lida = 1 WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['ok' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
?>