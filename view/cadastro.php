<?php
require_once __DIR__.'/../model/Seguranca.php';
Seguranca::iniciarSessao();
$pageTitle = 'Cadastro - Meu Bairro Melhor';
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
            <h2>Cadastro de Usuário</h2>
            <p>Crie sua conta para começar a ajudar seu bairro.</p>

            <form method="POST" action="../controller/routes.php?acao=cadastrar" class="form-moderno">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Seguranca::token(), ENT_QUOTES, 'UTF-8') ?>">
                
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" autocomplete="name" maxlength="50" required
                           oninvalid="this.setCustomValidity('Dado Requerido')"
                           oninput="this.setCustomValidity('')">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" autocomplete="email" maxlength="100" required
                           oninvalid="this.setCustomValidity('Dado Requerido')"
                           oninput="this.setCustomValidity('')">
                </div>

                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" autocomplete="new-password" maxlength="255" required
                           oninvalid="this.setCustomValidity('Dado Requerido')"
                           oninput="this.setCustomValidity('')">
                </div>

                <div class="form-group cadastro-politica">
                    <label>
                        <input type="checkbox" name="aceitaPolitica" value="1" required>
                        <span>Li e aceito a <a href="politicaPrivacidade.php" target="_blank" rel="noopener noreferrer">Política de Privacidade</a>.</span>
                    </label>
                </div>

                <button type="submit" class="btn-green btn-block">Cadastrar</button>
            </form>

            <a href="login.php" class="btn-secondary btn-block">Já tem conta? Faça login</a>
            
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>