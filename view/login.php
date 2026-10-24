<?php require_once __DIR__.'/../model/Seguranca.php'; Seguranca::iniciarSessao(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Meu Bairro Melhor</title>
    <link rel="stylesheet" href="../view/css/style.css?v=<?= time() ?>">
    <style>
        .login-links {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .link-senha {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .link-senha:hover {
            color: #2563eb;
            text-decoration: underline;
            transform: translateX(2px);
        }
    </style>
</head>
<body class="auth-page">

<div class="fixed-logo">
    <img src="../view/img/logo.png" alt="Logo Meu Bairro Melhor">
</div>

<section class="screen">
    <div class="dashboard-container max-width-small">
        <div class="welcome-box">
            <h2>Login</h2>
            <p>Acesse sua conta para continuar.</p>

            <form method="POST" action="../controller/routes.php?acao=login" class="form-moderno">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Seguranca::token(), ENT_QUOTES, 'UTF-8') ?>">
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" autocomplete="email" required>
                </div>

                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" autocomplete="current-password" required>
                </div>

                <button type="submit" class="btn-green btn-block">Entrar</button>
            </form>

            <div class="login-links">
                <a href="esqueceuSenha.php" class="link-senha">Esqueceu sua senha?</a>
                <a href="cadastro.php" class="btn-secondary btn-block">Não tem conta? Cadastre-se</a>
            </div>

        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>