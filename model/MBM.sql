-- ============================================================
-- BANCO DE DADOS: MEU BAIRRO MELHOR (MBM)
-- ============================================================

CREATE DATABASE IF NOT EXISTS MBM;
USE MBM;

-- ============================================================
-- 1. USUÁRIOS / LOGIN
-- ============================================================

CREATE TABLE login (
    idUsuario INT AUTO_INCREMENT PRIMARY KEY,
    foto VARCHAR(255) DEFAULT NULL,
    nomeUsuario VARCHAR(50) NOT NULL,
    Senha VARCHAR(255) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    nivelAcesso TINYINT NOT NULL DEFAULT 1,
    dataCadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Níveis de acesso:
-- 0 = Administrador
-- 1 = Usuário comum

INSERT INTO login (nomeUsuario, Senha, Email, nivelAcesso)
VALUES ('Admin', '$2y$10$CH1Is0JUrSPInQHVYgA4aewoL2tqFfiF/Dgc5JVnenXs6H.0VTul.', 'eu@gmail.com', 0);

-- ============================================================
-- 2. PERFIS
-- ============================================================

CREATE TABLE perfis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    endpasta VARCHAR(255) NOT NULL,
    usuarioId INT NOT NULL,

    CONSTRAINT fk_perfis_usuario
        FOREIGN KEY (usuarioId)
        REFERENCES login(idUsuario)
        ON DELETE CASCADE
);

-- ============================================================
-- 3. CATEGORIAS
-- ============================================================

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

-- ============================================================
-- 4. SUBCATEGORIAS
-- ============================================================

CREATE TABLE subcategorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    categoriaid INT NOT NULL,

    CONSTRAINT fk_subcategorias_categoria
        FOREIGN KEY (categoriaid)
        REFERENCES categorias(id)
);

-- ============================================================
-- 5. PROBLEMAS / DENÚNCIAS
-- ============================================================

CREATE TABLE problemas (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Identificação da denúncia
    codigoDenuncia VARCHAR(20) UNIQUE,

    -- Dados da denúncia
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    imagem VARCHAR(255) DEFAULT NULL,

    -- Classificação
    categoriaid INT NOT NULL,
    subcategoriaid INT NOT NULL,

    -- Usuário que realizou a denúncia
    usuarioid INT NOT NULL,

    -- Localização
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    bairro varchar (100),

    -- Controle da denúncia
    dataproblema DATETIME DEFAULT CURRENT_TIMESTAMP,

    status ENUM(
        'aberta',
        'em_analise',
        'resolvida',
        'cancelada'
    ) NOT NULL DEFAULT 'aberta',

    prioridade ENUM(
        'baixa',
        'media',
        'alta',
        'critica'
    ) NOT NULL DEFAULT 'media',

    -- Observações do responsável pela análise
    observacoes TEXT DEFAULT NULL,
    data_observacao DATETIME DEFAULT NULL,

    -- Chaves estrangeiras
    CONSTRAINT fk_problemas_categoria
        FOREIGN KEY (categoriaid)
        REFERENCES categorias(id),

    CONSTRAINT fk_problemas_subcategoria
        FOREIGN KEY (subcategoriaid)
        REFERENCES subcategorias(id),

    CONSTRAINT fk_problemas_usuario
        FOREIGN KEY (usuarioid)
        REFERENCES login(idUsuario)
);

CREATE TABLE historico_problemas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    problemaid INT NOT NULL,
    codigo_denuncia VARCHAR(20) NOT NULL,
    usuarioid INT NULL,
    campo VARCHAR(50) NOT NULL,
    valor_anterior TEXT DEFAULT NULL,
    valor_novo TEXT DEFAULT NULL,
    data_evento DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_historico_problema (problemaid),
    INDEX idx_historico_data (data_evento)
);

-- ============================================================
-- 6. RECUPERAÇÃO DE SENHA
-- ============================================================

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
);

-- RF-44: canal simples de comunicação entre cidadão e gestor.
-- Cada linha é uma mensagem trocada dentro de uma denúncia específica.
-- Tanto o cidadão dono da denúncia quanto qualquer gestor (nivelAcesso = 0)
-- podem escrever aqui; o controle de quem pode ler/escrever fica no PHP.

CREATE TABLE mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    problemaid INT NOT NULL,
    usuarioid INT NOT NULL,
    mensagem TEXT NOT NULL,
    dataenvio DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (problemaid) REFERENCES problemas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuarioid) REFERENCES login(idUsuario) ON DELETE CASCADE
);

CREATE INDEX idx_mensagens_problemaid ON mensagens(problemaid);

CREATE TABLE IF NOT EXISTS notificacoes (
	id INT AUTO_INCREMENT PRIMARY KEY,
    usuarioid INT NOT NULL,
    mensagem TEXT NOT NULL,
	lida TINYINT(1) NOT NULL DEFAULT 0,
	data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (usuarioid) REFERENCES login(idUsuario) ON DELETE CASCADE
);

-- ============================================================
-- CONSULTAS ÚTEIS
-- ============================================================

-- Ver usuários
SELECT * FROM login;

-- Ver categorias
-- SELECT * FROM categorias;

-- Ver subcategorias
-- SELECT * FROM subcategorias;

-- Ver denúncias
-- SELECT * FROM problemas;

-- Ver estrutura de uma tabela
-- DESCRIBE problemas;

-- ============================================================
-- APAGAR O BANCO (USAR SOMENTE SE QUISER RECOMEÇAR)
-- ============================================================
-- DROP DATABASE MBM;