<?php
require_once __DIR__ . '/model/Seguranca.php';
Seguranca::iniciarSessao();

if (isset($_SESSION['id'])) {
    header("Location: controller/routes.php?acao=dashboard");
} else {
    header("Location: view/inicio.php");
}
exit;
?>
