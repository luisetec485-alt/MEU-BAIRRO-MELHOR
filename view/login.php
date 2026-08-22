<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Meu Bairro Melhor</title>
    <link rel="stylesheet" href="../view/css/style.css">
</head>
<body>

<div class="fixed-logo">
    <img src="img/logo.png" alt="Logo Meu Bairro Melhor">
</div>

<section class="screen">
    <div class="dashboard-container max-width-small">
        <div class="welcome-box">
            <h2>Login</h2>
            <p>Acesse sua conta para continuar.</p>

            <form method="POST" action="../controller/routes.php?acao=login" class="form-moderno">
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>
                </div>

                <button type="submit" class="btn-green btn-block">Entrar</button>
            </form>

            <a href="cadastro.php" class="btn-secondary btn-block">Não tem conta? Cadastre-se</a>

        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>