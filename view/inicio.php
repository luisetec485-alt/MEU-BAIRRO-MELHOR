<?php
require_once __DIR__ . '/../model/Seguranca.php';
Seguranca::iniciarSessao();

if (isset($_SESSION['id'])) {
    header('Location: ../controller/routes.php?acao=dashboard');
    exit;
}

$pageTitle = 'Meu Bairro Melhor - Participe da transformação';
$bodyClass = 'landing-page';
include __DIR__ . '/header.php';
?>

<main class="landing-main">
    <section class="landing-hero">
        <div class="landing-hero-content">
            <span class="landing-eyebrow">Cuidar do bairro é construir o futuro</span>
            <h1>Uma cidade melhor começa com a sua participação.</h1>
            <p>
                O Meu Bairro Melhor é uma plataforma para moradores de Franco da Rocha
                registrarem problemas urbanos e para administradores organizarem e
                encaminharem esses relatos aos setores responsáveis.
            </p>
            <div class="landing-actions">
                <a class="btn-green" href="cadastro.php">Criar minha conta</a>
                <a class="btn-secondary" href="login.php">Já tenho uma conta</a>
            </div>
        </div>

        <div class="landing-hero-card" aria-label="Resumo do funcionamento do projeto">
            <div class="landing-card-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21a9 9 0 1 0-9-9 9 9 0 0 0 9 9Zm0-13v5l3 2"/></svg>
            </div>
            <strong>Da percepção à ação</strong>
            <p>Seu relato organizado ajuda a direcionar cada situação ao setor mais adequado.</p>
            <div class="landing-card-line"><span></span><span></span><span></span></div>
        </div>
    </section>

    <section class="landing-section">
        <div class="landing-section-heading">
            <span class="landing-eyebrow">Como usar</span>
            <h2>Do registro ao encaminhamento</h2>
            <p>Moradores registram o problema com localização e detalhes; administradores analisam o relato e fazem o encaminhamento manual.</p>
        </div>

        <div class="landing-steps">
            <article class="landing-step">
                <span class="landing-step-number">01</span>
                <div class="landing-step-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h13A1.5 1.5 0 0 1 20 5.5v9a1.5 1.5 0 0 1-1.5 1.5H12l-4.5 4v-4h-2A1.5 1.5 0 0 1 4 14.5v-9Z"/></svg>
                </div>
                <h3>Registre</h3>
                <p>Descreva o problema, escolha a categoria e indique o local no mapa.</p>
            </article>

            <article class="landing-step">
                <span class="landing-step-number">02</span>
                <div class="landing-step-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                </div>
                <h3>Detalhe</h3>
                <p>Adicione rua, bairro, prioridade e imagens ou vídeo para contextualizar.</p>
            </article>

            <article class="landing-step">
                <span class="landing-step-number">03</span>
                <div class="landing-step-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6.5 12 3l8 3.5v11L12 21l-8-3.5v-11Z"/><path d="m4 6.5 8 3.5 8-3.5M12 10v11"/></svg>
                </div>
                <h3>Analise</h3>
                <p>O administrador consulta os dados e verifica o destinatário adequado.</p>
            </article>

            <article class="landing-step">
                <span class="landing-step-number">04</span>
                <div class="landing-step-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12.5 9 17l11-11"/><path d="M4 6h7M4 19h7"/></svg>
                </div>
                <h3>Encaminhe</h3>
                <p>As informações são extraídas e enviadas manualmente ao setor responsável.</p>
            </article>
        </div>
    </section>

    <section class="landing-callout">
        <div>
            <span class="landing-eyebrow">Faça parte</span>
            <h2>Seu olhar ajuda a transformar o lugar onde você vive.</h2>
        </div>
        <a class="btn-green" href="cadastro.php">Começar agora</a>
    </section>
</main>

<?php include __DIR__ . '/footer.php'; ?>
