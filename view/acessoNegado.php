<?php
// Página mostrada quando um usuário comum tenta abrir uma função de administrador (3.2).
require_once __DIR__ . '/../model/Seguranca.php';
Seguranca::iniciarSessao();
http_response_code(403);
$pageTitle = 'Acesso negado - Meu Bairro Melhor';
include __DIR__ . '/header.php';
?>

<section class="screen">
    <div class="dashboard-container max-width-small">
        <div class="welcome-box">
            <h2>Acesso negado</h2>
            <p>Esta área é exclusiva para administradores do Meu Bairro Melhor. Se você precisa desse acesso, fale com a equipe pelo e-mail do rodapé.</p>

            <a href="../controller/routes.php?acao=dashboard" class="btn-green btn-block">Voltar ao início</a>
            <a href="../controller/routes.php?acao=listarProblemas" class="btn-secondary btn-block">Ver minhas denúncias</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>