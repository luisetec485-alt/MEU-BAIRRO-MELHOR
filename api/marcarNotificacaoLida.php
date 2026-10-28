<?php
require_once __DIR__.'/../model/Conexao.php';
require_once __DIR__.'/../model/Seguranca.php';
Seguranca::iniciarSessao();

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Sessão expirada. Faça login novamente.']);
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit;
}

try {
    $conexao = Conexao::getConexao();
    $stmt = $conexao->prepare("UPDATE notificacoes SET lida = 1 WHERE id = ? AND usuarioid = ?");
    $stmt->execute([$id, $_SESSION['id']]);
    echo json_encode(['ok' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
?>