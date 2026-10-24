<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$subcategorias = $subcategorias ?? [];

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Subcategorias</title>
    <link rel="stylesheet" href="../view/css/style.css?v=<?= time() ?>">
</head>
<body>

<div class="fixed-logo">
    <img src="../view/img/logo.png" alt="Logo Meu Bairro Melhor">
</div>

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

</body>
</html>