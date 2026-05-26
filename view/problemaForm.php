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
    <title>Registrar Problema - Meu Bairro Melhor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="fixed-logo">
    <img src="logo.png" alt="Logo Meu Bairro Melhor">
</div>

<section class="screen">
    <div class="content-box">
        <h2>Registrar Novo Problema</h2>

        <form method="POST" action="../controller/routes.php?acao=salvarProblema">
            
            <div class="form-group">
                <label>Título do Problema:</label>
                <input type="text" name="titulo" required maxlength="255">
            </div>

            <div class="form-group">
                <label>Descrição:</label>
                <textarea name="descricao" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label>Imagem (URL):</label>
                <input type="text" name="imagem" placeholder="https://exemplo.com/imagem.jpg">
            </div>

            <div class="form-group">
                <label>Categoria:</label>
                <select name="categoriaId" required>
                    <option value="">Selecione uma categoria</option>
                    <?php foreach($categorias as $categoria): ?>
                        <option value="<?= $categoria['id'] ?>">
                            <?= htmlspecialchars($categoria['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Subcategoria:</label>
                <select name="subcategoriaId" required>
                    <option value="">Selecione uma subcategoria</option>
                    <?php foreach($subcategorias as $subcategoria): ?>
                        <option value="<?= $subcategoria['id'] ?>">
                            <?= htmlspecialchars($subcategoria['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-green">Registrar Problema</button>
            <a href="dashboard.php" class="back-link">Voltar ao Dashboard</a>
        </form>
    </div>
</section>

</body>
</html>