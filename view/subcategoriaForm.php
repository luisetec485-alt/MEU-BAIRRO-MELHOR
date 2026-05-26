<?php session_start(); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Subcategoria</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="content-box">

    <h2>Nova Subcategoria</h2>

    <form
        method="POST"
        action="../controller/routes.php?acao=salvarSubcategoria"
    >

        <label>Nome:</label>

        <input
            type="text"
            name="nome"
            maxlength="100"
            required
        >

        <br><br>

        <label>Categoria:</label>

        <select name="categoriaId" required>

            <option value="">
                Selecione
            </option>

            <?php foreach($categorias as $c): ?>

            <option value="<?= $c['id'] ?>">
                <?= $c['nome'] ?>
            </option>

            <?php endforeach; ?>

        </select>

        <br><br>

        <button type="submit" class="btn btn-green">
            Salvar
        </button>

    </form>

</div>

</body>
</html>