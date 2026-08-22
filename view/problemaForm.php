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
    <title>Registrar Problema - Meu Bairro Melhor</title>
    <link rel="stylesheet" href="../view/css/style.css">
</head>
<body>

<div class="fixed-logo">
    <img src="img/logo.png" alt="Logo Meu Bairro Melhor">
</div>

<section class="screen">
    <div class="dashboard-container max-width-medium">
        <div class="welcome-box">
            <h2>Registrar Novo Problema</h2>
            <p>Ajude a melhorar sua comunidade relatando um incidente urbano.</p>

            <form method="POST" action="../controller/routes.php?acao=salvarProblema" class="form-moderno">
                
                <div class="form-group">
                    <label for="titulo">Título do Problema:</label>
                    <input type="text" id="titulo" name="titulo" required maxlength="255" placeholder="Ex: Buraco na via principal">
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição detalhada:</label>
                    <textarea id="descricao" name="descricao" rows="4" required placeholder="Descreva os detalhes do problema encontrado..."></textarea>
                </div>

                <div class="form-group">
                    <label for="imagem">Imagem (URL):</label>
                    <input type="text" id="imagem" name="imagem" placeholder="">
                </div>

                <div class="form-group">
                    <label for="categoriaid">Categoria:</label>
                    <select id="categoriaid" name="categoriaid" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach($categorias as $categoria): ?>
                            <option value="<?= $categoria['id'] ?>">
                                <?= htmlspecialchars($categoria['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subcategoriaid">Subcategoria:</label>
                    <select id="subcategoriaid" name="subcategoriaid" required>
                        <option value="">Selecione uma subcategoria</option>
                        <?php foreach($subcategorias as $subcategoria): ?>
                            <option value="<?= $subcategoria['id'] ?>">
                                <?= htmlspecialchars($subcategoria['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn-green btn-block">Registrar Problema</button>
                <a href="../view/dashboard.php" class="btn-secondary btn-block">Voltar ao Dashboard</a>
            </form>
        </div>
    </div>
</section>