<?php session_start(); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Subcategorias</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="content-box">

    <h2>Subcategorias</h2>

    <table border="1" width="100%">

        <tr>
            <th>ID</th>
            <th>Subcategoria</th>
            <th>Categoria</th>
        </tr>

        <?php foreach($subcategorias as $s): ?>

        <tr>

            <td><?= $s['id'] ?></td>

            <td>
                <?= htmlspecialchars($s['nome']) ?>
            </td>

            <td>
                <?= htmlspecialchars($s['categoriaNome']) ?>
            </td>

        </tr>

        <?php endforeach; ?>

    </table>

</div>

</body>
</html>