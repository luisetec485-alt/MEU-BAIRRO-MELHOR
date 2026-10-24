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

            <hr style="border: 0; border-top: 1px solid #5A2E1C; margin: 30px 0;">

            <h3 style="color: #1F4D36; font-family: 'Poppins', sans-serif;">Alterar Foto de Perfil</h3>

            <form method="POST" action="../controller/routes.php?acao=uploadFoto" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Seguranca::token(), ENT_QUOTES, 'UTF-8') ?>">
                <label style="color: #1F4D36; font-weight: 600; display: block; margin-top: 15px;">
                    Escolha sua Foto (PNG, JPG, JPEG):
                </label>
                <input type="file" name="foto" accept="image/png, image/jpeg, image/jpg" required 
                       style="margin-bottom: 15px; display: block; width: 100%; padding: 10px; border-bottom: 2px solid #5A2E1C;">
                
                <button type="submit" class="btn-salvar" 
                        style="background-color: #E48C2A; color: white; width: 100%; padding: 12px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
                    Fazer Upload da Imagem
                </button>
            </form>

            <a href="../view/dashboard.php" class="btn-secondary btn-block">Voltar ao Dashboard</a>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include __DIR__ . '/footer.php'; ?>