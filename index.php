<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['id'])) {
    header("Location: view/dashboard.php");
} else {
    header("Location: view/inicio.php");
}
exit;
?>
