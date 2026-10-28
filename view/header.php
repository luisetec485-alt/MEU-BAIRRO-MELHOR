<?php
require_once __DIR__ . '/../model/Seguranca.php';
require_once __DIR__ . '/../model/MensagemFlash.php';
Seguranca::iniciarSessao();

$pageTitle = $pageTitle ?? 'Problemas Registrados - Meu Bairro Melhor';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$inController = strpos($scriptName, '/controller/') !== false;
$cssHref = $inController ? '../view/css/style.css?v=' . time() : 'css/style.css?v=' . time();
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
        <span>Meu Bairro Melhor</span>
    </a>

    <nav class="site-nav" aria-label="Menu principal">
        <?php if ($usuarioAutenticado): ?>
            <?php if (!$ehAdministrador): ?>
                <a href="<?= $routeBase ?>dashboard">Início</a>
            <?php endif; ?>
            <a href="<?= $routeBase ?>listarProblemas">Denúncias</a>
            <?php if (!$ehAdministrador): ?>
                <a href="<?= $routeBase ?>formProblema">Registrar problema</a>
            <?php endif; ?>

            <?php if ($ehAdministrador): ?>
                <a href="<?= $routeBase ?>painelAdmin">Painel administrativo</a>
                <a href="<?= $routeBase ?>relatorioUsuarios">Usuários</a>
                <details class="site-nav-dropdown">
                    <summary>Cadastros</summary>
                    <div class="site-nav-dropdown-menu">
                        <a href="<?= $routeBase ?>listarCategorias">Categorias</a>
                        <a href="<?= $routeBase ?>listarSubcategorias">Subcategorias</a>
                    </div>
                </details>
            <?php endif; ?>

            <details class="profile-menu">
                <summary>
                    <span class="profile-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" focusable="false"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 8a7 7 0 0 0-14 0h14Z"/></svg>
                    </span>
                    <span><?= htmlspecialchars($primeiroNome, ENT_QUOTES, 'UTF-8') ?></span>
                </summary>
                <div class="profile-menu-content">
                    <a href="<?= $routeBase ?>editar">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15.2 5.2 3.6 3.6M4 20l3.8-.8L19.7 7.3a2.1 2.1 0 0 0-3-3L4.8 16.2 4 20Z"/></svg>
                        Editar perfil
                    </a>
                    <a class="site-nav-logout" href="<?= $routeBase ?>logout">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 5H5v14h5M14 8l4 4-4 4m4-4H9"/></svg>
                        Sair da conta
                    </a>
                </div>
            </details>
        <?php else: ?>
            <a href="<?= htmlspecialchars($viewBase . 'login.php', ENT_QUOTES, 'UTF-8') ?>">Entrar</a>
            <a href="<?= htmlspecialchars($viewBase . 'cadastro.php', ENT_QUOTES, 'UTF-8') ?>">Criar conta</a>
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