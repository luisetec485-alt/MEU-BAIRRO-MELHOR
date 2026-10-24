USE MBM;


-- ============================================================
-- CATEGORIAS
-- ============================================================

INSERT INTO categorias (nome) VALUES
('Infraestrutura'),
('Iluminação Pública'),
('Limpeza Urbana'),
('Saneamento'),
('Segurança'),
('Trânsito'),
('Meio Ambiente'),
('Transporte Público');


-- ============================================================
-- SUBCATEGORIAS
-- ============================================================

INSERT INTO subcategorias (nome, categoriaid) VALUES

-- Infraestrutura
('Buracos e pavimentação', 1),
('Calçadas danificadas', 1),
('Sinalização de vias', 1),

-- Iluminação Pública
('Poste apagado', 2),
('Lâmpada quebrada', 2),
('Iluminação insuficiente', 2),

-- Limpeza Urbana
('Lixo acumulado', 3),
('Entulho', 3),
('Lixeira danificada', 3),

-- Saneamento
('Vazamento de água', 4),
('Esgoto a céu aberto', 4),
('Bueiro entupido', 4),

-- Segurança
('Área de risco', 5),
('Problema de segurança pública', 5),

-- Trânsito
('Semáforo com defeito', 6),
('Placa de trânsito danificada', 6),
('Problema de faixa de pedestres', 6),

-- Meio Ambiente
('Árvore caída', 7),
('Poda necessária', 7),
('Poluição', 7),

-- Transporte Público
('Ponto de ônibus danificado', 8),
('Problema no transporte público', 8);


-- ============================================================
-- VERIFICAR CATEGORIAS
-- ============================================================

SELECT * FROM categorias;


-- ============================================================
-- VERIFICAR SUBCATEGORIAS
-- ============================================================

SELECT * FROM subcategorias;