<?php

/*
 * Configuração do e-mail usado em "Esqueceu sua senha?" e nos avisos de status.
 *
 * Opção 1 (recomendada na hospedagem): copie config/email.local.exemplo.php para
 * config/email.local.php e preencha com o Gmail do grupo e a senha de app.
 * O arquivo email.local.php NÃO vai para o GitHub (está no .gitignore) e a pasta
 * config/ é bloqueada para acesso pelo navegador no .htaccess.
 *
 * Opção 2: variáveis de ambiente MBM_EMAIL, MBM_EMAIL_SENHA e MBM_URL.
 * Exemplo em Linux/macOS:
 * export MBM_EMAIL="seu-email@gmail.com"
 * export MBM_EMAIL_SENHA="senha-de-app"
 * export MBM_URL="http://localhost/MEU-BAIRRO-MELHOR-master"
 */

$arquivoLocal = __DIR__ . '/email.local.php';

if (is_file($arquivoLocal)) {
    return require $arquivoLocal;
}

return [
    'email' => getenv('MBM_EMAIL') ?: '',
    'senha' => getenv('MBM_EMAIL_SENHA') ?: '',
    'url' => getenv('MBM_URL') ?: 'http://localhost/MEU-BAIRRO-MELHOR-master'
];
