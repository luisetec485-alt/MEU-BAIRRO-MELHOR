<?php
require_once __DIR__ . '/../model/Seguranca.php';
Seguranca::exigirAdministrador();

$subcategorias = $subcategorias ?? [];
?>

<?php
$pageTitle = 'Subcategorias - Meu Bairro Melhor';
include __DIR__ . '/header.php';
?>

<section class="screen">
    <div class="dashboard-container max-width-large">
        <div class="welcome-box">

            <h2>Subcategorias</h2>
            <p>Subcategorias cadastradas no sistema.</p>

            <div class="table-responsive">
                <table class="tabela-dados">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Subcategoria</th>
                            <th>Categoria</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($subcategorias as $s): ?>
                        <tr>
                            <td>#<?= $s['id'] ?></td>
                            <td><?= htmlspecialchars($s['nome']) ?></td>
                            <td><?= htmlspecialchars($s['categoriaNome']) ?></td>
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
