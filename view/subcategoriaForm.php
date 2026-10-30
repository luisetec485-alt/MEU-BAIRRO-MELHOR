<?php 
require_once __DIR__.'/../model/Seguranca.php';
Seguranca::exigirAdministrador();
$categorias = $categorias ?? [];
?>

<?php
$pageTitle = 'Cadastrar Subcategoria - Meu Bairro Melhor';
include __DIR__ . '/header.php';
?>

<section class="screen">
    <div class="dashboard-container max-width-small">
        <div class="welcome-box">
            
            <h2>Nova Subcategoria</h2>
            <p>Cadastre uma subcategoria vinculada a uma categoria principal.</p>

            <form method="POST" action="../controller/routes.php?acao=salvarSubcategoria" class="form-moderno">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Seguranca::token(), ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-group">
                    <label for="nome">Nome da Subcategoria:</label>
                    <input 
                        type="text" 
                        id="nome"
                        name="nome" 
                        maxlength="100" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="categoriaid">Categoria Principal:</label>
                    <select id="categoriaid" name="categoriaid" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach($categorias as $c): ?>
                            <option value="<?= $c['id'] ?>">
                                <?= htmlspecialchars($c['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn-green">
                    Salvar Subcategoria
                </button>

                <a href="../controller/routes.php?acao=dashboard" class="btn-secondary btn-block">
                    Voltar
                </a>

            </form>

        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
