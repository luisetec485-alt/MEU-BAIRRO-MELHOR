<?php
$pageTitle = $pageTitle ?? 'Problemas Registrados - Meu Bairro Melhor';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$inController = strpos($scriptName, '/controller/') !== false || strpos($scriptName, '/controller') === strlen($scriptName) - 10;
$cssHref = $inController ? '../view/css/style.css?v=' . time() : 'css/style.css?v=' . time();
$logoSrc = $inController ? '../view/img/logo.png' : 'img/logo.png';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars($cssHref, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
</head>
<body>

<div class="fixed-logo">
    <img src="<?= htmlspecialchars($logoSrc, ENT_QUOTES, 'UTF-8') ?>" alt="Logo Meu Bairro Melhor">
</div>