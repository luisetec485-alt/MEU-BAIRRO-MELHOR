<?php
require_once __DIR__.'/../model/Seguranca.php';
Seguranca::exigirLogin();
$usuario = $usuario ?? [
    'idUsuario' => $_SESSION['id'] ?? '',
    'nomeUsuario' => $_SESSION['nomeUsuario'] ?? '',
    'Email' => $_SESSION['Email'] ?? ''
];
?>
<?php
$pageTitle = 'Editar Usuário - Meu Bairro Melhor';
include __DIR__ . '/header.php';
?>

<section class="screen">
    <div class="dashboard-container max-width-small">
        <div class="welcome-box">
            <h2>Editar Dados</h2>
            <p>Mantenha suas informações de perfil atualizadas.</p>

            <form method="POST" action="../controller/routes.php?acao=atualizar" class="form-moderno">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Seguranca::token(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id" value="<?php echo $usuario['idUsuario']; ?>">

                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nomeUsuario']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['Email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="senha">Nova Senha:</label>
                    <input type="password" id="senha" name="senha" placeholder="Deixe em branco para manter">
                </div>

                <button type="submit" class="btn-green btn-block">Salvar Alterações</button>
            </form>

            <div class="danger-zone">
                <form method="POST" action="../controller/routes.php?acao=excluir" 
                      onsubmit="return confirm('Tem certeza que deseja excluir sua conta?');">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Seguranca::token(), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="id" value="<?php echo $usuario['idUsuario']; ?>">
                    <button type="submit" class="btn-red btn-block">Excluir Conta</button>
                </form>
            </div>

            <a href="../view/dashboard.php" class="btn-secondary btn-block">Voltar ao Dashboard</a>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include __DIR__ . '/footer.php'; ?>