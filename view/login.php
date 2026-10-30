<?php
require_once __DIR__.'/../model/Seguranca.php';
Seguranca::iniciarSessao();
$pageTitle = 'Login - Meu Bairro Melhor';
$bodyClass = 'auth-page';
include __DIR__ . '/header.php';
?>

<section class="screen">
    <div class="dashboard-container max-width-small">
        <div class="welcome-box">
            <a href="inicio.php" class="auth-back-link">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6M9 12h12"/></svg>
                Voltar
            </a>
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
<?php include __DIR__ . '/footer.php'; ?>