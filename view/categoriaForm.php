<?php
session_start();

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
    <title>Nova Categoria - Meu Bairro Melhor</title>
    <link rel="stylesheet" href="../view/css/style.css">
</head>
<body>

<div class="fixed-logo">
    <img src="img/logo.png" alt="Logo Meu Bairro Melhor">
</div>

<section class="screen">
    <div class="dashboard-container max-width-small">
        <div class="welcome-box">

            <h2>Nova Categoria</h2>
            <p>Cadastre uma nova categoria de problemas urbanos.</p>

            <form method="POST" action="../controller/routes.php?acao=salvarCategoria" class="form-moderno">

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

</body>
</html>