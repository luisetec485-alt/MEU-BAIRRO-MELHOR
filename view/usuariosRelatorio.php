<?php
require_once __DIR__ . '/../model/Seguranca.php';
Seguranca::exigirAdministrador();

function mascararEmail($email) {
    [$usuario, $dominio] = array_pad(explode('@', $email, 2), 2, '');
    $visivel = substr($usuario, 0, 1);
    return $visivel . str_repeat('*', max(1, strlen($usuario) - 1)) . '@' . $dominio;
}

require_once __DIR__ . '/../model/Conexao.php';
$conexao = Conexao::getConexao();

$stmt = $conexao->prepare("
    SELECT l.idUsuario, l.nomeUsuario, l.Email, l.dataCadastro, l.nivelAcesso
    FROM login l
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

        <div class="table-responsive" style="margin-top: 20px;">
        <table class="tabela-dados" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #1F4D36; color: white;">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">Nome</th>
                    <th style="padding: 10px;">E-mail</th>
                    <th style="padding: 10px;">Perfil</th>
                    <th style="padding: 10px;">Membro Desde</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr style="border-bottom: 1px solid #5A2E1C; text-align: center;">
                        <td style="padding: 10px;"><?= (int) $u['idUsuario'] ?></td>
                        <td style="padding: 10px;"><?= htmlspecialchars($u['nomeUsuario']) ?></td>
                        <td style="padding: 10px;"><?= htmlspecialchars(mascararEmail($u['Email'])) ?></td>
                        <td style="padding: 10px;"><?= (int) $u['nivelAcesso'] === 0 ? 'Administrador' : 'Usuário comum' ?></td>
                        <td style="padding: 10px;"><?= date('d/m/Y', strtotime($u['dataCadastro'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <br>
        <a href="../controller/routes.php?acao=painelAdmin" class="btn-secondary btn-block" style="max-width: 220px;">← Voltar ao painel</a>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>