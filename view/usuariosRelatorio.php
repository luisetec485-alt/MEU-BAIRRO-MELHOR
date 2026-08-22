<?php 
session_start();
if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}

require_once '../model/Conexao.php';
$conexao = Conexao::getConexao();

$stmt = $conexao->prepare("
    SELECT l.idUsuario, l.nomeUsuario, l.Email, l.dataCadastro, p.endpasta 
    FROM login l
    LEFT JOIN perfis p ON l.idUsuario = p.usuarioId
    ORDER BY l.nomeUsuario ASC
");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Usuários - Meu Bairro Melhor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="fixed-logo">
    <img src="logo.png" alt="Logo Meu Bairro Melhor">
</div>

<section class="screen">
    <div class="content-box" style="max-width: 1100px; margin: 50px auto; padding: 30px; background: rgba(242, 232, 213, 0.95); border-radius: 15px;">
        <h2>Relatório de Membros da Comunidade</h2>

        <table class="tabela-dados" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background: #1F4D36; color: white;">
                    <th style="padding: 10px;">Foto</th>
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">Nome</th>
                    <th style="padding: 10px;">E-mail</th>
                    <th style="padding: 10px;">Membro Desde</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr style="border-bottom: 1px solid #5A2E1C; text-align: center;">
                    <td style="padding: 10px;">
                        <?php 

                            $foto = !empty($u['endpasta']) ? '../' . $u['endpasta'] : 'img/default-avatar.png'; 
                        ?>
                        <img src="<?= $foto ?>" width="50" height="50" style="border-radius: 50%; object-fit: cover; border: 2px solid #1F4D36;">
                    </td>
                    <td style="padding: 10px;"><?= $u['idUsuario'] ?></td>
                    <td style="padding: 10px;"><?= htmlspecialchars($u['nomeUsuario']) ?></td>
                    <td style="padding: 10px;"><?= htmlspecialchars($u['Email']) ?></td>
                    <td style="padding: 10px;"><?= date('d/m/Y', strtotime($u['dataCadastro'])) ?></td>
                </tr>    
                <?php endforeach; ?>       
            </tbody>
        </table>

        <br>
        <a href="dashboard.php" class="back-link">← Voltar ao Dashboard</a>
    </div>
</section>

</body>
</html>