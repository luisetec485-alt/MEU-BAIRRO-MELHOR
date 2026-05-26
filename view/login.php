<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="fixed-logo">
    <img src="logo.png" alt="Logo Meu Bairro Melhor">
</div>
<section class="screen" id="home">
    <div class="content-box">
        <h1 class="main-title">Login</h1><br><br>

        <form method="POST" action="../controller/routes.php?acao=login">
            <label>Email:</label>
            <input type="email" name="email" required><br><br>

            <label>Senha:</label>
            <input type="password" name="senha" required><br>

            <br><br>
            <button type="submit" class="btn btn-green">Entrar</button>
        </form>
        <br><br>

        <a href="cadastro.php" style="display: inline-block;margin-top: 15px;font-size: 14px;color: #5A2E1C;text-decoration: none;border-bottom: 1px solid transparent;transition: 0.3s;" onmouseover="this.style.borderBottom='1px solid #5A2E1C'" onmouseout="this.style.borderBottom='1px solid transparent'">
            Não tem conta? Cadastre-se
        </a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>