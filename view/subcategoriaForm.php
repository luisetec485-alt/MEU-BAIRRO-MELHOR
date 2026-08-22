<?php session_start(); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Subcategoria - Meu Bairro Melhor</title>
    <link rel="stylesheet" href="../view/css/style.css">
</head>
<body>

<div class="fixed-logo">
    <img src="img/logo.png" alt="Logo Meu Bairro Melhor">
</div>

<section class="screen">
    <div class="dashboard-container max-width-small">
        <div class="welcome-box">
            
            <h2>Nova Subcategoria</h2>
            <p>Cadastre uma subcategoria vinculada a uma categoria principal.</p>

            <form method="POST" action="../controller/routes.php?acao=salvarSubcategoria" class="form-moderno">

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

                <a href="../view/dashboard.php" class="btn-secondary btn-block">
                    Voltar
                </a>

            </form>

        </div>
    </div>
</section>

</body>
</html>