<?php
require_once __DIR__ . '/../model/Seguranca.php';
Seguranca::exigirAdministrador();

$categorias = $categorias ?? [];
?>

<?php
$pageTitle = 'Categorias - Meu Bairro Melhor';
include __DIR__ . '/header.php';
?>

<section class="screen">
    <div class="dashboard-container max-width-large">
        <div class="welcome-box">

            <h2>Categorias</h2>
            <p>Categorias cadastradas no sistema.</p>

            <div class="table-responsive">
                <table class="tabela-dados">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Categoria</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categorias as $c): ?>
                        <tr>
                            <td>#<?= $c['id'] ?></td>
                            <td><?= htmlspecialchars($c['nome']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <a href="../view/dashboard.php" class="btn-secondary btn-block">
                Voltar
            </a>

        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
