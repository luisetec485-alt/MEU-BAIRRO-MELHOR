<?php session_start(); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Categorias</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="content-box">

    <h2>Categorias</h2>

    <table border="1" width="100%">

        <tr>
            <th>ID</th>
            <th>Categoria</th>
        </tr>

        <?php foreach($categorias as $c): ?>

        <tr>
            <td><?= $c['id'] ?></td>

            <td>
                <?= htmlspecialchars($c['nome']) ?>
            </td>
        </tr>

        <?php endforeach; ?>

    </table>

</div>

</body>
</html>