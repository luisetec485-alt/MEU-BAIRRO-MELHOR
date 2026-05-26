<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="fixed-logo">
    <img src="logo.png" alt="Logo Meu Bairro Melhor">
</div>

<section class="screen" id="home">
    <div class="content-box">
        <h2>Cadastro de Usuário</h2>

        <form method="POST" action="../controller/routes.php?acao=cadastrar">
            <label>Nome:</label><br>
            <input type="text" name="nome" required
                   oninvalid="this.setCustomValidity('Dado Requirido')"
                   oninput="this.setCustomValidity('')"><br><br>

            <label>Email:</label><br>
            <input type="email" name="email" required
                   oninvalid="this.setCustomValidity('Dado Requirido')"
                   oninput="this.setCustomValidity('')"><br><br>

            <label>Senha:</label><br>
            <input type="password" name="senha" required
                   oninvalid="this.setCustomValidity('Dado Requirido')"
                   oninput="this.setCustomValidity('')"><br><br>

            <input type="submit" value="Cadastrar">
        </form>

        <br>
        <a href="login.php" style="display: inline-block;margin-top: 15px;font-size: 14px;color: #5A2E1C;text-decoration: none;border-bottom: 1px solid transparent;transition: 0.3s;" onmouseover="this.style.borderBottom='1px solid #5A2E1C'" onmouseout="this.style.borderBottom='1px solid transparent'">
            Já tem conta? Faça login
        </a>
    </div>
</section>

</body>
</html>