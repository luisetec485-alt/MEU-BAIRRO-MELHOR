<?php

return [
    'email' => getenv('MBM_EMAIL') ?: '',
    'senha' => getenv('MBM_EMAIL_SENHA') ?: '',
    'url' => getenv('MBM_URL') ?: 'http://localhost/MEU-BAIRRO-MELHOR-master'
];

/*
 * Para enviar e-mails reais no ambiente local, configure as variáveis de ambiente:
 * MBM_EMAIL, MBM_EMAIL_SENHA e MBM_URL.
 * Exemplo em Linux/macOS:
 * export MBM_EMAIL="seu-email@gmail.com"
 * export MBM_EMAIL_SENHA="senha-de-app"
 * export MBM_URL="http://localhost/MEU-BAIRRO-MELHOR-master"
 */