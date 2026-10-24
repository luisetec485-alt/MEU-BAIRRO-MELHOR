<?php
function mascararEmail($email) {
    [$usuario, $dominio] = array_pad(explode('@', $email, 2), 2, '');
    $visivel = substr($usuario, 0, 1);
    return $visivel . str_repeat('*', max(1, strlen($usuario) - 1)) . '@' . $dominio;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

if ((int) ($_SESSION['nivelAcesso'] ?? 1) !== 0) {
    http_response_code(403);
    echo 'Acesso negado.';
    exit;
}

require_once __DIR__ . '/../model/Conexao.php';
$conexao = Conexao::getConexao();

$stmt = $conexao->prepare("
    SELECT l.idUsuario, l.nomeUsuario, l.Email, l.dataCadastro, p.endpasta
    FROM login l
    LEFT JOIN perfis p ON l.idUsuario = p.usuarioId
    ORDER BY l.nomeUsuario ASC
");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Relatório de Usuários - Meu Bairro Melhor';
include __DIR__ . '/header.php';
?>

<section class="screen">
    <div class="welcome-box" style="max-width: 1100px; margin: 0 auto;">
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
                    <?php $foto = !empty($u['endpasta']) ? '../' . $u['endpasta'] : '../view/img/logo.png'; ?>
                    <tr style="border-bottom: 1px solid #5A2E1C; text-align: center;">
                        <td style="padding: 10px;">
                            <img src="<?= htmlspecialchars($foto) ?>" width="50" height="50" style="border-radius: 50%; object-fit: cover; border: 2px solid #1F4D36;">
                        </td>
                        <td style="padding: 10px;"><?= (int) $u['idUsuario'] ?></td>
                        <td style="padding: 10px;"><?= htmlspecialchars($u['nomeUsuario']) ?></td>
                        <td style="padding: 10px;"><?= htmlspecialchars(mascararEmail($u['Email'])) ?></td>
                        <td style="padding: 10px;"><?= date('d/m/Y', strtotime($u['dataCadastro'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <br>
        <a href="/controller/routes.php?acao=dashboard" class="btn-secondary btn-block" style="max-width: 220px;">← Voltar ao Dashboard</a>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>