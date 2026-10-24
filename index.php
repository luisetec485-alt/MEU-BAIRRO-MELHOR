<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se o usuário está logado, vai para o dashboard
if (isset($_SESSION['id'])) {
    header("Location: view/dashboard.php");
} else {
    // Se não está logado, vai para o login
    header("Location: view/login.php");
}
exit;
?>