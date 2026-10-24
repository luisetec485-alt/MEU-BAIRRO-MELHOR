<?php
require_once __DIR__.'/../model/Seguranca.php';
Seguranca::exigirAdministrador();
?>

<?php
$pageTitle = 'Nova Categoria - Meu Bairro Melhor';
include __DIR__ . '/header.php';
?>

<section class="screen">
    <div class="dashboard-container max-width-small">
        <div class="welcome-box">

            <h2>Nova Categoria</h2>
            <p>Cadastre uma nova categoria de problemas urbanos.</p>

            <form method="POST" action="../controller/routes.php?acao=salvarCategoria" class="form-moderno">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Seguranca::token(), ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-group">
                    <label for="nome">Nome da Categoria:</label>
                    <input 
                        type="text" 
                        id="nome"
                        name="nome" 
                        maxlength="100" 
                        required
                    >
                </div>

                <button type="submit" class="btn-green">
                    Cadastrar Categoria
                </button>

                <a href="../view/dashboard.php" class="btn-secondary btn-block">
                    Voltar
                </a>

            </form>

        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
