<?php

require_once __DIR__.'/../model/Conexao.php';
require_once __DIR__.'/../model/ProblemaModel.php';
require_once __DIR__.'/../model/MensagemModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Sessão expirada. Faça login novamente.']);
    exit;
}

$problemaid = filter_input(INPUT_GET, 'problemaid', FILTER_VALIDATE_INT);

if (!$problemaid) {
    http_response_code(400);
    echo json_encode(['error' => 'Denúncia inválida.']);
    exit;
}

$problemaModel = new ProblemaModel();
$problema = $problemaModel->buscarPorId($problemaid);

if (!$problema) {
    http_response_code(404);
    echo json_encode(['error' => 'Denúncia não encontrada.']);
    exit;
}

// Só o dono da denúncia ou um gestor (admin) podem ver a conversa.
$souAdmin = (int) ($_SESSION['nivelAcesso'] ?? 1) === 0;
$souDono = (int) $problema['usuarioid'] === (int) $_SESSION['id'];

if (!$souAdmin && !$souDono) {
    http_response_code(403);
    echo json_encode(['error' => 'Você não tem acesso a esta conversa.']);
    exit;
}

$mensagemModel = new MensagemModel();
$mensagens = $mensagemModel->listarPorProblema($problemaid);

echo json_encode($mensagens);
?>