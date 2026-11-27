SET NAMES utf8mb4;
-- ============================================================
-- EXTENSÕES DO GAMESHAVEN (adicionadas para o sistema funcionar)
-- Todos os passos abaixo são IDEMPOTENTES: conferem antes de alterar,
-- então podem ser executados mais de uma vez sem dar erro.
-- A tabela original que você mandou não trazia: um jeito de marcar
-- administradores, colunas de capa/nota crítica no jogo, nem ligação
-- entre notas fiscais e pedidos/doações sem exigir os dois ao mesmo tempo.
-- ============================================================

-- 1) Administradores: coluna is_admin em usuarios
SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'is_admin') = 0,
             'ALTER TABLE usuarios ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- 2) criadores e doações têm referência circular (criadores -> doacoes -> criadores).
--    Para conseguir cadastrar o primeiro criador, id_doacoes em criadores vira opcional.
ALTER TABLE criadores MODIFY id_doacoes INT NULL;

-- 3) Respostas do suporte (o admin responde o ticket)
CREATE TABLE IF NOT EXISTS suporte_respostas (
    id_resposta INT PRIMARY KEY AUTO_INCREMENT,
    id_suporte INT NOT NULL,
    id_usuarios INT NOT NULL,
    resposta TEXT NOT NULL,
    data_resposta DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_suporte) REFERENCES suporte (id_suporte),
    FOREIGN KEY (id_usuarios) REFERENCES usuarios (id_usuarios)
) ENGINE=InnoDB;

-- 4) Capa e nota crítica do jogo (os formulários já tinham esses campos)
SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jogos' AND COLUMN_NAME = 'capa') = 0,
             'ALTER TABLE jogos ADD COLUMN capa VARCHAR(255) NULL AFTER titulo',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jogos' AND COLUMN_NAME = 'nota_critica') = 0,
             'ALTER TABLE jogos ADD COLUMN nota_critica DECIMAL(3,1) NULL',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- 5) Jogos: a UNIQUE (id_desenvolvedoras, id_publicadoras) impedia um 2º jogo
--    do mesmo estúdio+editora. Antes de remover essa UNIQUE, criamos índices
--    simples para as duas chaves estrangeiras (o MySQL recusa o DROP se a FK
--    depender dela).
SET @s := IF((SELECT COUNT(*) FROM information_schema.STATISTICS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jogos' AND INDEX_NAME = 'idx_jogos_desenvolvedoras') = 0,
             'ALTER TABLE jogos ADD INDEX idx_jogos_desenvolvedoras (id_desenvolvedoras)',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF((SELECT COUNT(*) FROM information_schema.STATISTICS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jogos' AND INDEX_NAME = 'idx_jogos_publicadoras') = 0,
             'ALTER TABLE jogos ADD INDEX idx_jogos_publicadoras (id_publicadoras)',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- Acha o índice UNIQUE composto (2 colunas, começando por id_desenvolvedoras) e o remove.
SET @comp := (
    SELECT s.INDEX_NAME FROM information_schema.STATISTICS s
    WHERE s.TABLE_SCHEMA = DATABASE() AND s.TABLE_NAME = 'jogos'
      AND s.NON_UNIQUE = 0 AND s.INDEX_NAME <> 'PRIMARY'
      AND s.COLUMN_NAME = 'id_desenvolvedoras' AND s.SEQ_IN_INDEX = 1
      AND (SELECT COUNT(*) FROM information_schema.STATISTICS t
           WHERE t.TABLE_SCHEMA = s.TABLE_SCHEMA AND t.TABLE_NAME = s.TABLE_NAME
             AND t.INDEX_NAME = s.INDEX_NAME) = 2
    LIMIT 1
);
SET @s := IF(@comp IS NULL, 'DO 0', CONCAT('ALTER TABLE jogos DROP INDEX `', @comp, '`'));
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- 6) Nota fiscal: uma nota pode ser de uma COMPRA ou de uma DOAÇÃO (não as duas
--    obrigatórias ao mesmo tempo). Uma CHECK garante que pelo menos uma exista.
ALTER TABLE nota_fiscal MODIFY id_pedidos INT NULL;
ALTER TABLE nota_fiscal MODIFY id_doacoes INT NULL;

SET @s := IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
              WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'nota_fiscal'
                AND CONSTRAINT_NAME = 'chk_nota_fiscal_origem') = 0,
             'ALTER TABLE nota_fiscal ADD CONSTRAINT chk_nota_fiscal_origem CHECK (id_pedidos IS NOT NULL OR id_doacoes IS NOT NULL)',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- 7) Pedidos: cada compra vira um pedido novo. A UNIQUE(id_usuarios) do schema
--    original impedia um 2º pedido do mesmo usuário (a compra seguinte apagava a anterior).
SET @s := IF((SELECT COUNT(*) FROM information_schema.STATISTICS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pedidos' AND INDEX_NAME = 'idx_pedidos_usuarios') = 0,
             'ALTER TABLE pedidos ADD INDEX idx_pedidos_usuarios (id_usuarios)',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @comp := (
    SELECT s.INDEX_NAME FROM information_schema.STATISTICS s
    WHERE s.TABLE_SCHEMA = DATABASE() AND s.TABLE_NAME = 'pedidos'
      AND s.NON_UNIQUE = 0 AND s.INDEX_NAME <> 'PRIMARY'
      AND s.COLUMN_NAME = 'id_usuarios' AND s.SEQ_IN_INDEX = 1
      AND (SELECT COUNT(*) FROM information_schema.STATISTICS t
           WHERE t.TABLE_SCHEMA = s.TABLE_SCHEMA AND t.TABLE_NAME = s.TABLE_NAME
             AND t.INDEX_NAME = s.INDEX_NAME) = 1
    LIMIT 1
);
SET @s := IF(@comp IS NULL, 'DO 0', CONCAT('ALTER TABLE pedidos DROP INDEX `', @comp, '`'));
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- 8) Doações: cada doação é um registro próprio (nota fiscal por doação e histórico
--    completo). A UNIQUE(id_criadores, id_usuarios, id_jogos) somava doações repetidas.
SET @s := IF((SELECT COUNT(*) FROM information_schema.STATISTICS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'doacoes' AND INDEX_NAME = 'idx_doacoes_criadores') = 0,
             'ALTER TABLE doacoes ADD INDEX idx_doacoes_criadores (id_criadores)',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF((SELECT COUNT(*) FROM information_schema.STATISTICS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'doacoes' AND INDEX_NAME = 'idx_doacoes_usuarios') = 0,
             'ALTER TABLE doacoes ADD INDEX idx_doacoes_usuarios (id_usuarios)',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF((SELECT COUNT(*) FROM information_schema.STATISTICS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'doacoes' AND INDEX_NAME = 'idx_doacoes_jogos') = 0,
             'ALTER TABLE doacoes ADD INDEX idx_doacoes_jogos (id_jogos)',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @comp := (
    SELECT s.INDEX_NAME FROM information_schema.STATISTICS s
    WHERE s.TABLE_SCHEMA = DATABASE() AND s.TABLE_NAME = 'doacoes'
      AND s.NON_UNIQUE = 0 AND s.INDEX_NAME <> 'PRIMARY'
      AND s.COLUMN_NAME = 'id_criadores' AND s.SEQ_IN_INDEX = 1
      AND (SELECT COUNT(*) FROM information_schema.STATISTICS t
           WHERE t.TABLE_SCHEMA = s.TABLE_SCHEMA AND t.TABLE_NAME = s.TABLE_NAME
             AND t.INDEX_NAME = s.INDEX_NAME) = 3
    LIMIT 1
);
SET @s := IF(@comp IS NULL, 'DO 0', CONCAT('ALTER TABLE doacoes DROP INDEX `', @comp, '`'));
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- 9) Criadores: telefone celular tem 11 dígitos (era CHAR(10), o código cortava o número)
--    e conta_bancaria precisa caber agência + chave Pix.
ALTER TABLE criadores MODIFY telefone VARCHAR(20) NOT NULL;
ALTER TABLE criadores MODIFY conta_bancaria VARCHAR(150) NOT NULL;

-- 10) Nota fiscal guarda o criador: a nota de uma venda mostra só o valor dos jogos
--     daquele criador (e não o total do pedido, que pode ter jogos de vários criadores).
SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'nota_fiscal' AND COLUMN_NAME = 'id_criadores') = 0,
             'ALTER TABLE nota_fiscal ADD COLUMN id_criadores INT NULL AFTER id_doacoes',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- 11) Tag do jogo (mesma lista do GameStormy: Jogo, Oferta da semana, Destaque...)
SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jogos' AND COLUMN_NAME = 'tag') = 0,
             "ALTER TABLE jogos ADD COLUMN tag VARCHAR(50) NOT NULL DEFAULT 'Jogo'",
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- 12) Valores iniciais de plataformas e categorias (só insere o que ainda não existe)
INSERT INTO plataformas (nome) SELECT 'PC' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM plataformas WHERE nome = 'PC');
INSERT INTO plataformas (nome) SELECT 'PlayStation 5' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM plataformas WHERE nome = 'PlayStation 5');
INSERT INTO plataformas (nome) SELECT 'Xbox Series X|S' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM plataformas WHERE nome = 'Xbox Series X|S');
INSERT INTO plataformas (nome) SELECT 'Nintendo Switch' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM plataformas WHERE nome = 'Nintendo Switch');
INSERT INTO plataformas (nome) SELECT 'Mobile' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM plataformas WHERE nome = 'Mobile');

INSERT INTO categorias (nome) SELECT 'Ação' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nome = 'Ação');
INSERT INTO categorias (nome) SELECT 'Aventura' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nome = 'Aventura');
INSERT INTO categorias (nome) SELECT 'RPG' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nome = 'RPG');
INSERT INTO categorias (nome) SELECT 'Indie' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nome = 'Indie');
INSERT INTO categorias (nome) SELECT 'Simulação' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nome = 'Simulação');
INSERT INTO categorias (nome) SELECT 'Estratégia' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nome = 'Estratégia');
INSERT INTO categorias (nome) SELECT 'Terror' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nome = 'Terror');
INSERT INTO categorias (nome) SELECT 'Cozy' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nome = 'Cozy');
INSERT INTO categorias (nome) SELECT 'Esportes' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nome = 'Esportes');
INSERT INTO categorias (nome) SELECT 'Corrida' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nome = 'Corrida');

-- 13) Foto de perfil do usuário (caminho do arquivo em public/uploads/avatares/)
SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'foto_perfil') = 0,
             'ALTER TABLE usuarios ADD COLUMN foto_perfil VARCHAR(255) NULL',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- 14) Ícone pequeno do jogo (miniatura usada nas listas; diferente da capa)
SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jogos' AND COLUMN_NAME = 'icone') = 0,
             'ALTER TABLE jogos ADD COLUMN icone VARCHAR(255) NULL AFTER capa',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- 15) Data de cadastro do jogo (para mostrar os recém-cadastrados na Home)
SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jogos' AND COLUMN_NAME = 'cadastrado_em') = 0,
             'ALTER TABLE jogos ADD COLUMN cadastrado_em TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- 16) Notas fiscais detalhadas: quantidade nos itens do pedido e retrato dos dados na emissão
SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pedido_itens' AND COLUMN_NAME = 'quantidade') = 0,
             'ALTER TABLE pedido_itens ADD COLUMN quantidade INT NOT NULL DEFAULT 1',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'nota_fiscal' AND COLUMN_NAME = 'comprador_nome') = 0,
             'ALTER TABLE nota_fiscal ADD COLUMN comprador_nome VARCHAR(100) NULL',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'nota_fiscal' AND COLUMN_NAME = 'comprador_email') = 0,
             'ALTER TABLE nota_fiscal ADD COLUMN comprador_email VARCHAR(100) NULL',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'nota_fiscal' AND COLUMN_NAME = 'forma_pagamento') = 0,
             'ALTER TABLE nota_fiscal ADD COLUMN forma_pagamento VARCHAR(30) NULL',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'nota_fiscal' AND COLUMN_NAME = 'status_pagamento') = 0,
             'ALTER TABLE nota_fiscal ADD COLUMN status_pagamento VARCHAR(20) NULL',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'nota_fiscal' AND COLUMN_NAME = 'end_rua') = 0,
             'ALTER TABLE nota_fiscal ADD COLUMN end_rua VARCHAR(100) NULL',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'nota_fiscal' AND COLUMN_NAME = 'end_numero') = 0,
             'ALTER TABLE nota_fiscal ADD COLUMN end_numero INT NULL',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'nota_fiscal' AND COLUMN_NAME = 'end_complemento') = 0,
             'ALTER TABLE nota_fiscal ADD COLUMN end_complemento VARCHAR(55) NULL',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'nota_fiscal' AND COLUMN_NAME = 'end_cidade') = 0,
             'ALTER TABLE nota_fiscal ADD COLUMN end_cidade VARCHAR(50) NULL',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'nota_fiscal' AND COLUMN_NAME = 'end_estado') = 0,
             'ALTER TABLE nota_fiscal ADD COLUMN end_estado CHAR(2) NULL',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF((SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'nota_fiscal' AND COLUMN_NAME = 'end_cep') = 0,
             'ALTER TABLE nota_fiscal ADD COLUMN end_cep CHAR(8) NULL',
             'DO 0');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
