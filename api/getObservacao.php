<?php

require_once __DIR__.'/../model/Conexao.php';
require_once __DIR__.'/../model/ProblemaModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['id']) || (int) ($_SESSION['nivelAcesso'] ?? 1) !== 0) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

$model = new ProblemaModel();
$observacao = $model->obterObservacoes($id);

if ($observacao) {
    echo json_encode([
        'observacoes' => $observacao['observacoes'],
        'data_observacao' => $observacao['data_observacao']
    ]);
} else {
    echo json_encode([
        'observacoes' => null,
        'data_observacao' => null
    ]);
}
?>
