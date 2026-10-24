<?php require_once __DIR__.'/../model/Seguranca.php'; Seguranca::iniciarSessao(); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Redefinir senha - Meu Bairro Melhor</title>

    <link rel="stylesheet" href="../view/css/style.css?v=<?= time() ?>">
</head>

<body>

<div class="fixed-logo">
    <img src="../view/img/logo.png" alt="Logo Meu Bairro Melhor">
</div>

<section class="screen">

    <div class="dashboard-container max-width-small">

        <div class="welcome-box">

            <h2>Redefinir senha</h2>

            <p>
                Digite sua nova senha.
            </p>

            <form
                method="POST"
                action="../controller/routes.php?acao=redefinirSenha"
                class="form-moderno"
            >
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Seguranca::token(), ENT_QUOTES, 'UTF-8') ?>">

                <input
                    type="hidden"
                    name="token"
                    value="<?= htmlspecialchars($_GET['token']) ?>"
                >

                <div class="form-group">

                    <label for="senha">
                        Nova senha:
                    </label>

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="confirmarSenha">
                        Confirmar nova senha:
                    </label>

                    <input
                        type="password"
                        id="confirmarSenha"
                        name="confirmarSenha"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="btn-green btn-block"
                >
                    Redefinir senha
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

</body>
</html>