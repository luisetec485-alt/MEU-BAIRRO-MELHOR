<?php session_start(); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Categoria</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="content-box">

    <h2>Nova Categoria</h2>

    <form
        method="POST"
        action="../controller/routes.php?acao=salvarCategoria"
    >

        <label>Nome da Categoria:</label>

        <input
            type="text"
            name="nome"
            maxlength="100"
            required
        >

        <br><br>

        <button type="submit" class="btn btn-green">
            Cadastrar
        </button>

    </form>

</div>

</body>
</html>