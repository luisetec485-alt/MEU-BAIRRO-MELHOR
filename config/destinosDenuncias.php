<?php

/*
 * Destinos sugeridos para o encaminhamento manual.
 * Estes contatos são referências iniciais e não representam validação
 * oficial para todas as ocorrências.
 */
return [
    'Infraestrutura' => [
        'Buracos e pavimentação' => [
            'tipo' => 'email',
            'destino' => 'obras@francodarocha.sp.gov.br'
        ],
        'Calçadas danificadas' => [
            'tipo' => 'email',
            'destino' => 'obras@francodarocha.sp.gov.br'
        ],
        'Sinalização de vias' => [
            'tipo' => 'email',
            'destino' => 'transito@francodarocha.sp.gov.br'
        ]
    ],
    'Iluminação Pública' => [
        'Poste apagado' => ['tipo' => 'portal', 'destino' => 'https://ip.somasig.com.br/ocorrencias/francodarocha'],
        'Lâmpada quebrada' => ['tipo' => 'portal', 'destino' => 'https://ip.somasig.com.br/ocorrencias/francodarocha'],
        'Iluminação insuficiente' => ['tipo' => 'portal', 'destino' => 'https://ip.somasig.com.br/ocorrencias/francodarocha']
    ],
    'Limpeza Urbana' => [
        'Lixo acumulado' => ['tipo' => 'email', 'destino' => 'diretoriadeservicos@francodarocha.sp.gov.br'],
        'Entulho' => ['tipo' => 'email', 'destino' => 'diretoriadeservicos@francodarocha.sp.gov.br'],
        'Lixeira danificada' => ['tipo' => 'email', 'destino' => 'diretoriadeservicos@francodarocha.sp.gov.br']
    ],
    'Saneamento' => [
        'Vazamento de água' => ['tipo' => 'externo', 'destino' => 'Canal oficial da Sabesp'],
        'Esgoto a céu aberto' => ['tipo' => 'externo', 'destino' => 'Sabesp, quando relacionado à rede de esgoto'],
        'Bueiro entupido' => ['tipo' => 'email', 'destino' => 'diretoriadeservicos@francodarocha.sp.gov.br']
    ],
    'Segurança' => [
        'Área de risco' => ['tipo' => 'email', 'destino' => 'habitacao@francodarocha.sp.gov.br', 'observacao' => 'Confirmar competência'],
        'Problema de segurança pública' => ['tipo' => 'email', 'destino' => 'segurancaemobilidade@francodarocha.sp.gov.br']
    ],
    'Trânsito' => [
        'Semáforo com defeito' => ['tipo' => 'email', 'destino' => 'transito@francodarocha.sp.gov.br'],
        'Placa de trânsito danificada' => ['tipo' => 'email', 'destino' => 'transito@francodarocha.sp.gov.br'],
        'Problema de faixa de pedestres' => ['tipo' => 'email', 'destino' => 'transito@francodarocha.sp.gov.br']
    ],
    'Meio Ambiente' => [
        'Árvore caída' => ['tipo' => 'email', 'destino' => 'obras@francodarocha.sp.gov.br'],
        'Poda necessária' => ['tipo' => 'email', 'destino' => 'obras@francodarocha.sp.gov.br'],
        'Poluição' => ['tipo' => 'email', 'destino' => 'habitacao@francodarocha.sp.gov.br', 'observacao' => 'Confirmar competência']
    ],
    'Transporte Público' => [
        'Ponto de ônibus danificado' => ['tipo' => 'email', 'destino' => 'segurancaemobilidade@francodarocha.sp.gov.br', 'observacao' => 'Confirmar competência'],
        'Problema no transporte público' => ['tipo' => 'email', 'destino' => 'segurancaemobilidade@francodarocha.sp.gov.br', 'observacao' => 'Confirmar competência']
    ]
];
