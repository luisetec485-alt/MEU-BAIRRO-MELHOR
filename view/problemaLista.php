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
    <title>Problemas Registrados - Meu Bairro Melhor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="fixed-logo">
    <img src="logo.png" alt="Logo Meu Bairro Melhor">
</div>

<section class="screen">
    <div class="content-box" style="max-width: 1200px;">
        <h2>Problemas Registrados</h2>

        <!-- BUSCA -->
        <form method="GET" action="../controller/routes.php">
            <input type="hidden" name="acao" value="listarProblemas">
            <input type="text" name="busca" placeholder="Pesquisar problema..." 
                   value="<?= isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : '' ?>">
            <button type="submit" class="btn btn-green">Buscar</button>
        </form>

        <br>

        <table class="tabela-dados">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Categoria</th>
                    <th>Subcategoria</th>
                    <th>Usuário</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($problemas) && !empty($problemas)): ?>
                    <?php foreach($problemas as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['titulo']) ?></td>
                        <td><?= htmlspecialchars($p['categoriaNome']) ?></td>
                        <td><?= htmlspecialchars($p['subcategoriaNome']) ?></td>
                        <td><?= htmlspecialchars($p['nomeUsuario']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['dataProblema'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">
                            Nenhum problema cadastrado ainda.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <br>
        <a href="../view/dashboard.php" class="back-link">← Voltar ao Dashboard</a>
    </div>
</section>

</body>
</html>