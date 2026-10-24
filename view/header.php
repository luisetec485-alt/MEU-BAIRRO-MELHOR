<?php
require_once __DIR__ . '/../model/Seguranca.php';
require_once __DIR__ . '/../model/MensagemFlash.php';
Seguranca::iniciarSessao();

$pageTitle = $pageTitle ?? 'Problemas Registrados - Meu Bairro Melhor';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$inController = strpos($scriptName, '/controller/') !== false;
$cssHref = $inController ? '../view/css/style.css?v=' . time() : 'css/style.css?v=' . time();
$logoSrc = $inController ? '../view/img/logo.png' : 'img/logo.png';
$viewBase = $inController ? '../view/' : '';
$routeBase = '../controller/routes.php?acao=';
$nomeUsuario = trim((string) ($_SESSION['nomeUsuario'] ?? 'Usuário'));
$primeiroNome = explode(' ', $nomeUsuario)[0] ?: 'Usuário';
$usuarioAutenticado = isset($_SESSION['id']);
$ehAdministrador = (int) ($_SESSION['nivelAcesso'] ?? 1) === 0;
$bodyClass = $bodyClass ?? '';
$mensagemFlash = MensagemFlash::consumir();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars($cssHref, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
</head>
<body class="<?= htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8') ?>">

<header class="site-header">
    <a class="site-brand" href="<?= $routeBase ?>dashboard">
        <img src="<?= htmlspecialchars($logoSrc, ENT_QUOTES, 'UTF-8') ?>" alt="">
        <span>Meu Bairro Melhor</span>
    </a>

    <nav class="site-nav" aria-label="Menu principal">
        <?php if ($usuarioAutenticado): ?>
            <a href="<?= $routeBase ?>dashboard">Início</a>
            <a href="<?= $routeBase ?>listarProblemas">Denúncias</a>
            <a href="<?= $routeBase ?>formProblema">Registrar problema</a>
            <a href="<?= $routeBase ?>editar">Meu perfil</a>

            <?php if ($ehAdministrador): ?>
                <a href="<?= $routeBase ?>painelAdmin">Painel administrativo</a>
                <a href="<?= $routeBase ?>relatorioUsuarios">Usuários</a>
                <a href="<?= $routeBase ?>listarCategorias">Categorias</a>
                <a href="<?= $routeBase ?>listarSubcategorias">Subcategorias</a>
            <?php endif; ?>

            <a href="<?= htmlspecialchars($viewBase . 'politicaPrivacidade.php', ENT_QUOTES, 'UTF-8') ?>">Privacidade</a>
            <a class="site-nav-logout" href="<?= $routeBase ?>logout">
                Sair (<?= htmlspecialchars($primeiroNome, ENT_QUOTES, 'UTF-8') ?>)
            </a>
        <?php else: ?>
            <a href="<?= htmlspecialchars($viewBase . 'login.php', ENT_QUOTES, 'UTF-8') ?>">Entrar</a>
            <a href="<?= htmlspecialchars($viewBase . 'cadastro.php', ENT_QUOTES, 'UTF-8') ?>">Criar conta</a>
            <a href="<?= htmlspecialchars($viewBase . 'politicaPrivacidade.php', ENT_QUOTES, 'UTF-8') ?>">Privacidade</a>
        <?php endif; ?>
    </nav>
</header>
<?php if ($mensagemFlash): ?>
    <div class="flash-message flash-message-<?= htmlspecialchars($mensagemFlash['tipo'], ENT_QUOTES, 'UTF-8') ?>"
         role="<?= $mensagemFlash['tipo'] === 'erro' ? 'alert' : 'status' ?>"
         aria-live="<?= $mensagemFlash['tipo'] === 'erro' ? 'assertive' : 'polite' ?>">
        <?= htmlspecialchars($mensagemFlash['texto'], ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>