<?php

require_once __DIR__.'/../model/Conexao.php';
require_once __DIR__.'/../model/ProblemaModel.php';
require_once __DIR__.'/../model/MensagemModel.php';
require_once __DIR__.'/../model/Seguranca.php';
Seguranca::iniciarSessao();

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Sessão expirada. Faça login novamente.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido.']);
    exit;
}

$problemaid = filter_input(INPUT_POST, 'problemaid', FILTER_VALIDATE_INT);
$mensagem = trim($_POST['mensagem'] ?? '');

if (!$problemaid) {
    http_response_code(400);
    echo json_encode(['error' => 'Denúncia inválida.']);
    exit;
}

if ($mensagem === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Escreva uma mensagem antes de enviar.']);
    exit;
}

if (mb_strlen($mensagem) > 2000) {
    http_response_code(400);
    echo json_encode(['error' => 'Mensagem muito longa (máximo de 2000 caracteres).']);
    exit;
}

$problemaModel = new ProblemaModel();
$problema = $problemaModel->buscarPorId($problemaid);

if (!$problema) {
    http_response_code(404);
    echo json_encode(['error' => 'Denúncia não encontrada.']);
    exit;
}

// Só o dono da denúncia ou um gestor (admin) podem escrever nesta conversa.
$souAdmin = (int) ($_SESSION['nivelAcesso'] ?? 1) === 0;
$souDono = (int) $problema['usuarioid'] === (int) $_SESSION['id'];

if (!$souAdmin && !$souDono) {
    http_response_code(403);
    echo json_encode(['error' => 'Você não tem acesso a esta conversa.']);
    exit;
}

$mensagemModel = new MensagemModel();
$enviou = $mensagemModel->enviar($problemaid, $_SESSION['id'], $mensagem);

if (!$enviou) {
    http_response_code(500);
    echo json_encode(['error' => 'Não foi possível enviar a mensagem.']);
    exit;
}

echo json_encode(['ok' => true]);
?>