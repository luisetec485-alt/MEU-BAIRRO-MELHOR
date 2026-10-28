DROP DATABASE IF EXISTS MBM;

CREATE DATABASE MBM
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE MBM;

CREATE TABLE login (
    idUsuario INT AUTO_INCREMENT PRIMARY KEY,
    nomeUsuario VARCHAR(50) NOT NULL,
    Senha VARCHAR(255) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    nivelAcesso TINYINT NOT NULL DEFAULT 1,
    dataCadastro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- Níveis de acesso:
-- 0 = Administrador
-- 1 = Usuário comum

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE subcategorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    categoriaid INT NOT NULL,

    CONSTRAINT fk_subcategorias_categoria
        FOREIGN KEY (categoriaid)
        REFERENCES categorias(id)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE problemas (
    id INT AUTO_INCREMENT PRIMARY KEY,

    codigoDenuncia VARCHAR(20) UNIQUE,

    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    imagem TEXT DEFAULT NULL,

    categoriaid INT NOT NULL,
    subcategoriaid INT NOT NULL,

    usuarioid INT NOT NULL,

    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    rua VARCHAR(180) DEFAULT NULL,
    bairro VARCHAR(100) DEFAULT NULL,

    dataproblema DATETIME DEFAULT CURRENT_TIMESTAMP,

    status ENUM(
        'aguardando_encaminhamento',
        'em_analise',
        'recusada',
        'enviado'
    ) NOT NULL DEFAULT 'aguardando_encaminhamento',

    prioridade ENUM(
        'baixa',
        'media',
        'alta',
        'critica'
    ) NOT NULL DEFAULT 'media',

    observacoes TEXT DEFAULT NULL,
    data_observacao DATETIME DEFAULT NULL,

    CONSTRAINT fk_problemas_categoria
        FOREIGN KEY (categoriaid)
        REFERENCES categorias(id),

    CONSTRAINT fk_problemas_subcategoria
        FOREIGN KEY (subcategoriaid)
        REFERENCES subcategorias(id),

    CONSTRAINT fk_problemas_usuario
        FOREIGN KEY (usuarioid)
        REFERENCES login(idUsuario)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE historico_problemas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    problemaid INT NOT NULL,
    codigo_denuncia VARCHAR(20) NOT NULL,
    usuarioid INT DEFAULT NULL,
    campo VARCHAR(50) NOT NULL,
    valor_anterior TEXT DEFAULT NULL,
    valor_novo TEXT DEFAULT NULL,
    data_evento DATETIME DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_historico_problema (problemaid),
    INDEX idx_historico_data (data_evento)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE recuperacao_senha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expira_em DATETIME NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_recuperacao_usuario
        FOREIGN KEY (idUsuario)
        REFERENCES login(idUsuario)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    problemaid INT NOT NULL,
    usuarioid INT NOT NULL,
    mensagem TEXT NOT NULL,
    dataenvio DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_mensagens_problema
        FOREIGN KEY (problemaid)
        REFERENCES problemas(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_mensagens_usuario
        FOREIGN KEY (usuarioid)
        REFERENCES login(idUsuario)
        ON DELETE CASCADE,

    INDEX idx_mensagens_problemaid (problemaid)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuarioid INT NOT NULL,
    mensagem TEXT NOT NULL,
    lida TINYINT(1) NOT NULL DEFAULT 0,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_notificacoes_usuario
        FOREIGN KEY (usuarioid)
        REFERENCES login(idUsuario)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

INSERT INTO login (
    nomeUsuario,
    Senha,
    Email,
    nivelAcesso
)
SELECT
    'Admin',
    '$2y$10$CH1Is0JUrSPInQHVYgA4aewoL2tqFfiF/Dgc5JVnenXs6H.0VTul.',
    'eu@gmail.com',
    0
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1
    FROM login
    WHERE Email = 'eu@gmail.com'
);

-- Administrador: admin@meubairromelhor.com  | senha: Admin@2026
-- Usuário comum: leitor@meubairromelhor.com | senha: Leitor@2026

INSERT INTO login (nomeUsuario, Senha, Email, nivelAcesso)
SELECT
    'Administrador Teste',
    '$2y$10$OSvh8GBDx35/0.DnbGlpNeJ9sA1d9EiF.pxOzqaoEpfMVP4adX0a6',
    'admin@meubairromelhor.com',
    0
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM login WHERE Email = 'admin@meubairromelhor.com'
);

INSERT INTO login (nomeUsuario, Senha, Email, nivelAcesso)
SELECT
    'Leitor Teste',
    '$2y$10$bLePFO5Xtz2ooCqV8nJzRe/8/yStrJTlG.OTrfj96aHQJLL.DN3XC',
    'leitor@meubairromelhor.com',
    1
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM login WHERE Email = 'leitor@meubairromelhor.com'
);

INSERT INTO categorias (nome)
SELECT dados.nome
FROM (
    SELECT 'Infraestrutura' AS nome
    UNION ALL SELECT 'Iluminação Pública'
    UNION ALL SELECT 'Limpeza Urbana'
    UNION ALL SELECT 'Saneamento'
    UNION ALL SELECT 'Segurança'
    UNION ALL SELECT 'Trânsito'
    UNION ALL SELECT 'Meio Ambiente'
    UNION ALL SELECT 'Transporte Público'
) AS dados
WHERE NOT EXISTS (
    SELECT 1
    FROM categorias AS c
    WHERE c.nome = dados.nome
);

INSERT INTO subcategorias (nome, categoriaid)
SELECT dados.nome, c.id
FROM (
    SELECT 'Buracos e pavimentação' AS nome, 'Infraestrutura' AS categoria
    UNION ALL SELECT 'Calçadas danificadas', 'Infraestrutura'
    UNION ALL SELECT 'Sinalização de vias', 'Infraestrutura'

    UNION ALL SELECT 'Poste apagado', 'Iluminação Pública'
    UNION ALL SELECT 'Lâmpada quebrada', 'Iluminação Pública'
    UNION ALL SELECT 'Iluminação insuficiente', 'Iluminação Pública'

    UNION ALL SELECT 'Lixo acumulado', 'Limpeza Urbana'
    UNION ALL SELECT 'Entulho', 'Limpeza Urbana'
    UNION ALL SELECT 'Lixeira danificada', 'Limpeza Urbana'

    UNION ALL SELECT 'Vazamento de água', 'Saneamento'
    UNION ALL SELECT 'Esgoto a céu aberto', 'Saneamento'
    UNION ALL SELECT 'Bueiro entupido', 'Saneamento'

    UNION ALL SELECT 'Área de risco', 'Segurança'
    UNION ALL SELECT 'Problema de segurança pública', 'Segurança'

    UNION ALL SELECT 'Semáforo com defeito', 'Trânsito'
    UNION ALL SELECT 'Placa de trânsito danificada', 'Trânsito'
    UNION ALL SELECT 'Problema de faixa de pedestres', 'Trânsito'

    UNION ALL SELECT 'Árvore caída', 'Meio Ambiente'
    UNION ALL SELECT 'Poda necessária', 'Meio Ambiente'
    UNION ALL SELECT 'Poluição', 'Meio Ambiente'

    UNION ALL SELECT 'Ponto de ônibus danificado', 'Transporte Público'
    UNION ALL SELECT 'Problema no transporte público', 'Transporte Público'
) AS dados
INNER JOIN categorias AS c
    ON c.nome = dados.categoria
WHERE NOT EXISTS (
    SELECT 1
    FROM subcategorias AS s
    WHERE s.nome = dados.nome
      AND s.categoriaid = c.id
);

INSERT INTO login (nomeUsuario, Senha, Email, nivelAcesso)
SELECT
    'Administrador Teste',
    '$2y$10$OSvh8GBDx35/0.DnbGlpNeJ9sA1d9EiF.pxOzqaoEpfMVP4adX0a6',
    'admin@meubairro.com',
    0
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM login WHERE Email = 'admin@meubairro.com'
);
 
INSERT INTO login (nomeUsuario, Senha, Email, nivelAcesso)
SELECT
    'Leitor Teste',
    '$2y$10$bLePFO5Xtz2ooCqV8nJzRe/8/yStrJTlG.OTrfj96aHQJLL.DN3XC',
    'leitor@meubairro.com',
    1
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM login WHERE Email = 'leitor@meubairro.com'
);
 
-- Conferir:
SELECT idUsuario, nomeUsuario, Email,
       CASE nivelAcesso WHEN 0 THEN 'Administrador' ELSE 'Usuário comum' END AS perfil
FROM login
WHERE Email IN ('admin@meubairro.com', 'leitor@meubairro.com');

-- CONSULTAS ÚTEIS PARA TESTES

-- Consultar usuários
-- SELECT * FROM login;

-- Consultar categorias
-- SELECT * FROM categorias;

-- Consultar subcategorias com suas categorias
-- SELECT
--     s.id,
--     s.nome AS subcategoria,
--     c.nome AS categoria
-- FROM subcategorias AS s
-- INNER JOIN categorias AS c
--     ON c.id = s.categoriaid;

-- Consultar denúncias
-- SELECT * FROM problemas;

-- Consultar histórico das denúncias
-- SELECT * FROM historico_problemas;

-- Consultar mensagens
-- SELECT * FROM mensagens;

-- Consultar notificações
-- SELECT * FROM notificacoes;

-- Consultar solicitações de recuperação de senha
-- SELECT * FROM recuperacao_senha;

-- Ver a estrutura de uma tabela
-- DESCRIBE problemas;