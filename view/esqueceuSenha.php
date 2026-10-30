<?php
require_once __DIR__.'/../model/Seguranca.php';
Seguranca::iniciarSessao();
$pageTitle = 'Recuperar senha - Meu Bairro Melhor';
include __DIR__ . '/header.php';
?>

<section class="screen">

    <div class="dashboard-container max-width-small">

        <div class="welcome-box">

            <h2>Recuperar senha</h2>

            <p>
                Digite o e-mail cadastrado na sua conta.
            </p>

            <form
                method="POST"
                action="../controller/routes.php?acao=recuperarSenha"
                class="form-moderno"
            >
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Seguranca::token(), ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-group">

                    <label for="email">
                        E-mail:
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        autocomplete="email"
                        maxlength="100"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="btn-green btn-block"
                >
                    Recuperar senha
                </button>

            </form>

            <a
                href="login.php"
                class="btn-secondary btn-block"
            >
                Voltar para o login
            </a>

        </div>

    </div>

</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include __DIR__ . '/footer.php'; ?>