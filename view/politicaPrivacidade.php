<?php
require_once __DIR__ . '/../model/Seguranca.php';
Seguranca::iniciarSessao();

$pageTitle = 'Política de Privacidade - Meu Bairro Melhor';
include __DIR__ . '/header.php';
?>

<main class="screen">
    <article class="dashboard-container privacy-content">
        <div class="welcome-box">
            <h2>Política de Privacidade</h2>
            <p>
                Esta política explica como o projeto Meu Bairro Melhor trata os dados pessoais
                usados para registrar e acompanhar problemas urbanos. O tratamento segue a
                Lei Geral de Proteção de Dados Pessoais (Lei nº 13.709/2018 - LGPD).
            </p>

            <h3>Dados tratados</h3>
            <ul>
                <li>Dados de conta: nome, e-mail, senha armazenada em formato de hash e, se enviada, foto de perfil.</li>
                <li>Dados dos relatos: título, descrição, categoria, subcategoria, bairro, localização no mapa, prioridade, data, status e arquivos de imagem ou vídeo enviados.</li>
                <li>Dados de uso do serviço: mensagens, notificações e histórico de alterações relacionados aos relatos.</li>
                <li>Dados técnicos necessários à sessão e à proteção das requisições, como identificadores de sessão e tokens de segurança.</li>
            </ul>

            <h3>Finalidade do uso</h3>
            <p>
                Usamos esses dados para criar e manter contas, registrar e localizar relatos,
                permitir o acompanhamento e a gestão dos problemas, enviar notificações
                relacionadas ao serviço e proteger as funcionalidades contra acessos indevidos.
                O e-mail também pode ser usado para recuperação de senha.
            </p>

            <h3>Visibilidade e compartilhamento</h3>
            <p>
                Não vendemos dados pessoais nem os compartilhamos com terceiros para publicidade.
                Os relatos cadastrados podem ser consultados por usuários autenticados do próprio
                sistema para que a comunidade acompanhe os problemas; informações exibidas podem
                incluir o nome do autor e os dados do relato. Administradores acessam os dados
                necessários à gestão do serviço. O compartilhamento externo só ocorre quando
                necessário para cumprir obrigação legal ou atender uma solicitação válida de
                autoridade competente.
            </p>

            <h3>Armazenamento e segurança</h3>
            <p>
                Os dados são armazenados no banco de dados e nos arquivos do sistema enquanto
                forem necessários para manter a conta, operar o serviço e preservar o histórico
                dos relatos. Aplicamos controles de acesso por perfil e armazenamos senhas usando
                hash, sem guardar a senha em texto simples. Quando os dados deixam de ser
                necessários, podem ser eliminados ou anonimizados, respeitadas as obrigações
                legais e a preservação necessária do histórico.
            </p>

            <h3>Seus direitos</h3>
            <p>Nos termos da LGPD, você pode solicitar:</p>
            <ul>
                <li>confirmação do tratamento e acesso aos seus dados pessoais;</li>
                <li>correção de dados incompletos, inexatos ou desatualizados;</li>
                <li>anonimização, bloqueio ou eliminação de dados desnecessários ou tratados em desconformidade;</li>
                <li>eliminação dos dados tratados com consentimento, quando aplicável e permitido;</li>
                <li>informações sobre o uso e o compartilhamento dos dados, além de revisão de decisões automatizadas, se houver;</li>
                <li>portabilidade dos dados e revogação do consentimento, quando aplicáveis.</li>
            </ul>
            <p>
                Para exercer seus direitos ou esclarecer dúvidas, entre em contato pelo e-mail
                <a href="mailto:meubairromelhor9@gmail.com">meubairromelhor9@gmail.com</a>.
                Podemos solicitar informações para confirmar a titularidade antes de atender ao pedido.
            </p>

            <h3>Contato</h3>
            <p>
                Equipe do projeto Meu Bairro Melhor:
                <a href="mailto:meubairromelhor9@gmail.com">meubairromelhor9@gmail.com</a>.
            </p>
        </div>
    </article>
</main>

<?php include __DIR__ . '/footer.php'; ?>
