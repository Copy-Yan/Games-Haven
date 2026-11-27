CREATE TABLE usuarios (
    id_usuarios int PRIMARY KEY AUTO_INCREMENT,
    nome_usuario varchar (100) UNIQUE NOT NULL,
    nickname varchar (100) UNIQUE NOT NULL,
    email varchar (100) UNIQUE NOT NULL,
    senha varchar (250) NOT NULL,
    data_nascimento DATE NOT NULL,
    status ENUM ('ativo', 'inativo', 'banido') NOT NULL DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
   
) ENGINE=InnoDB;

CREATE TABLE enderecos (
    id_enderecos INT PRIMARY KEY AUTO_INCREMENT,
    id_usuarios INT NOT NULL,
    rua VARCHAR(100) NOT NULL,
    numero INT NOT NULL,
    cidade varchar (50) NOT NULL,
    cep char (8) NOT NULL,
    estado char (2) NOT NULL,
    complemento varchar (55) NULL,
   
   UNIQUE (id_usuarios),
    
    FOREIGN KEY (id_usuarios) REFERENCES usuarios (id_usuarios)
) ENGINE=InnoDB;

CREATE TABLE desenvolvedoras (
     id_desenvolvedoras INT PRIMARY KEY AUTO_INCREMENT,
     nome varchar (50) NOT NULL,
     data_fundacao date NOT NULL,
     pais VARCHAR (45) NOT NULL

) ENGINE=InnoDB;

CREATE TABLE publicadoras (
     id_publicadoras INT PRIMARY KEY AUTO_INCREMENT,
     nome varchar (100) NOT NULL,
     pais VARCHAR (45) NOT NULL

) ENGINE=InnoDB;

CREATE TABLE jogos (
    id_jogos INT PRIMARY KEY AUTO_INCREMENT,
    id_desenvolvedoras INT NOT NULL,
    id_publicadoras INT NOT NULL,	
    titulo varchar (100) NOT NULL,
    descricao varchar (250) NOT NULL	,
    preco DECIMAL(10, 2) NOT NULL,
    data_lancamento DATE NOT NULL,
    classificacao_etaria VARCHAR(3) NOT NULL,
    tamanho_download DECIMAL(10, 2) NOT NULL,
    requisitos_minimos VARCHAR(250) NOT NULL,
    requisitos_recomendados VARCHAR (250) NOT NULL,

    UNIQUE (id_desenvolvedoras, id_publicadoras),
    
    FOREIGN KEY (id_desenvolvedoras) REFERENCES desenvolvedoras (id_desenvolvedoras),  
    FOREIGN KEY (id_publicadoras) REFERENCES publicadoras (id_publicadoras)

) ENGINE=InnoDB;

CREATE TABLE categorias (
     id_categorias INT PRIMARY KEY AUTO_INCREMENT,
     nome VARCHAR(100) NOT NULL

) ENGINE=InnoDB;

CREATE TABLE jogos_categorias (
    id_jogosCategorias INT PRIMARY KEY AUTO_INCREMENT,
    id_jogos INT NOT NULL,
    id_categorias INT NOT NULL,

    UNIQUE (id_jogos, id_categorias), 
    
    FOREIGN KEY (id_jogos) REFERENCES jogos (id_jogos),
    FOREIGN KEY (id_categorias) REFERENCES categorias (id_categorias)

) ENGINE=InnoDB;

CREATE TABLE biblioteca (
    id_biblioteca int PRIMARY KEY AUTO_INCREMENT,
    id_usuarios INT NOT NULL,
    id_jogos INT NOT NULL,
    data_compra date NOT null,

    UNIQUE (id_usuarios, id_jogos),
    
    FOREIGN KEY (id_usuarios) REFERENCES usuarios (id_usuarios),
    FOREIGN KEY (id_jogos) REFERENCES jogos (id_jogos)

) ENGINE=InnoDB;

CREATE TABLE carrinho (
    id_carrinho INT PRIMARY KEY AUTO_INCREMENT,
    id_usuarios INT NOT NULL,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

   UNIQUE (id_usuarios),
    
    FOREIGN KEY (id_usuarios) REFERENCES usuarios (id_usuarios)

) ENGINE=InnoDB;

CREATE TABLE carrinho_itens (
    id_carrinhoItens INT PRIMARY KEY AUTO_INCREMENT,
    id_carrinho INT NOT NULL,
    id_jogos INT NOT NULL,
    quantidade INT NOT NULL,

   UNIQUE (id_carrinho, id_jogos),
 
    FOREIGN KEY (id_carrinho) REFERENCES carrinho (id_carrinho),
    FOREIGN KEY (id_jogos) REFERENCES jogos (id_jogos)

) ENGINE=InnoDB;
    
CREATE TABLE pedidos (
    id_pedidos INT PRIMARY KEY AUTO_INCREMENT,
    id_usuarios INT NOT NULL,
    data_pedido DATE NOT NULL,
    valor_total decimal (10,2) NOT NULL,
    status ENUM('pendente','pago','cancelado') DEFAULT 'pendente',

    UNIQUE (id_usuarios),
    
    FOREIGN KEY (id_usuarios) REFERENCES usuarios (id_usuarios)

) ENGINE=InnoDB;

CREATE TABLE pedido_itens (
    id_pedidoItens INT PRIMARY KEY AUTO_INCREMENT,
    id_pedidos INT NOT NULL,
    id_jogos INT NOT NULL,
    preco_unitario DECIMAL(10, 2) NOT NULL,

    UNIQUE (id_pedidos, id_jogos),
    
    FOREIGN KEY (id_pedidos) REFERENCES pedidos (id_pedidos),
    FOREIGN KEY (id_jogos) REFERENCES jogos (id_jogos)

) ENGINE=InnoDB;


CREATE TABLE pagamentos (
    id_pagamentos INT PRIMARY KEY AUTO_INCREMENT,
    id_pedidos INT NOT NULL,
    data_pagamento DATE NOT NULL,
    metodo ENUM('cartao credito', 'pix', 'cartao debito') NOT NULL,
    status ENUM('pendente', 'aprovado', 'recusado') DEFAULT 'pendente',
    
    UNIQUE (id_pedidos),
    
    FOREIGN KEY (id_pedidos) REFERENCES pedidos (id_pedidos)

) ENGINE=InnoDB;

 CREATE TABLE avaliacoes (
    id_avaliacoes INT PRIMARY KEY AUTO_INCREMENT,
    id_usuarios INT NOT NULL,
    id_jogos INT NOT NULL,
    nota INT(4) NOT NULL,
    comentario VARCHAR(100) NOT NULL,
    data_avaliacao DATE NOT NULL,

    UNIQUE (id_usuarios, id_jogos),
    CHECK (nota >= 0 AND nota <= 5),

    FOREIGN KEY (id_usuarios) REFERENCES usuarios (id_usuarios),
    FOREIGN KEY (id_jogos) REFERENCES jogos(id_jogos)
) ENGINE=InnoDB;

CREATE TABLE plataformas (
    id_plataformas INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE jogos_plataformas (
    id_jogosPlataformas INT PRIMARY KEY AUTO_INCREMENT,
    id_jogos INT NOT NULL,
    id_plataformas INT NOT NULL,
    
    UNIQUE (id_jogos, id_plataformas),

    FOREIGN KEY (id_jogos) REFERENCES jogos (id_jogos),
    FOREIGN KEY (id_plataformas) REFERENCES plataformas (id_plataformas) 
) ENGINE=InnoDB;

CREATE TABLE suporte (
    id_suporte INT PRIMARY KEY AUTO_INCREMENT,
    id_usuarios INT NOT NULL,
    assunto VARCHAR (150) NOT NULL,
    descricao TEXT NOT NULL, 
    categoria ENUM('Pagamento', 'Conta', 'Produto') NOT NULL,
    status ENUM ('Aberto', 'Pendente', 'Resolvido', 'Fechado') NOT NULL DEFAULT 'pendente', 
    data_abertura DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_fechamento DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE (id_usuarios),
    
    FOREIGN KEY (id_usuarios) REFERENCES usuarios (id_usuarios)
    
) ENGINE=INNODB;

CREATE TABLE suporte_mensagens (
    id_mensagens INT PRIMARY KEY AUTO_INCREMENT,
    id_suporte INT NOT NULL,
    id_usuarios INT NOT NULL,
    mensagem TEXT NOT NULL,
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,

    UNIQUE (id_suporte, id_usuarios),
    
    FOREIGN KEY (id_suporte) REFERENCES suporte (id_suporte),
    FOREIGN KEY (id_usuarios) REFERENCES usuarios (id_usuarios)
    
) ENGINE=INNODB;

CREATE TABLE doacoes (
    id_doacoes INT PRIMARY KEY AUTO_INCREMENT,
    id_criadores INT NOT NULL,
    id_usuarios INT NOT NULL,
    id_jogos INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    data_doacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    metodo ENUM('cartao credito', 'pix', 'cartao debito') NOT NULL,
    status ENUM('pendente', 'aprovado', 'recusado') DEFAULT 'pendente',

    UNIQUE(id_criadores, id_usuarios, id_jogos),

    FOREIGN KEY (id_usuarios) REFERENCES usuarios (id_usuarios),
    FOREIGN KEY (id_jogos) REFERENCES jogos (id_jogos)
) ENGINE=InnoDB;

CREATE TABLE criadores (
    id_criadores INT PRIMARY KEY AUTO_INCREMENT,
    id_usuarios INT NOT NULL,
    id_doacoes INT NOT NULL,
    CPF CHAR(11) NOT NULL UNIQUE,
    conta_bancaria VARCHAR(50) NOT NULL,
    telefone CHAR(10) NOT NULL UNIQUE,
    nota_fiscal_padrao VARCHAR(200) NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(id_usuarios, id_doacoes),

    FOREIGN KEY (id_usuarios) REFERENCES usuarios (id_usuarios)
) ENGINE=InnoDB; 

CREATE TABLE nota_fiscal (
    id_fiscal INT PRIMARY KEY AUTO_INCREMENT,
    id_pedidos INT NOT NULL,
    id_doacoes INT NOT NULL,
    numero_nota VARCHAR(20) NOT NULL UNIQUE,
    serie VARCHAR(5) NOT NULL DEFAULT '1',
    chave_acesso VARCHAR(44) NULL UNIQUE,
    data_emissao DATETIME DEFAULT CURRENT_TIMESTAMP,
    valor_produtos DECIMAL(10,2) NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    status ENUM('autorizada', 'cancelada', 'pendente') NOT NULL DEFAULT 'pendente',
    motivo_cancelamento VARCHAR(200) NULL,

    UNIQUE (id_pedidos, id_doacoes),

    FOREIGN KEY (id_pedidos) REFERENCES pedidos (id_pedidos),
    FOREIGN KEY (id_doacoes) REFERENCES doacoes (id_doacoes)
) ENGINE=InnoDB;  

CREATE TABLE jogos_criadores (
    id_jogosCriadores INT PRIMARY KEY AUTO_INCREMENT,
    id_jogos INT NOT NULL,
    id_criadores INT NOT NULL,

    UNIQUE (id_jogos, id_criadores),

    FOREIGN KEY (id_jogos) REFERENCES jogos (id_jogos),
    FOREIGN KEY (id_criadores) REFERENCES criadores (id_criadores)
) ENGINE=InnoDB;

ALTER TABLE criadores
ADD CONSTRAINT id_doacoes
FOREIGN KEY (id_doacoes) REFERENCES doacoes (id_doacoes)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE doacoes
ADD CONSTRAINT id_criadores
FOREIGN KEY (id_criadores) REFERENCES criadores (id_criadores)
ON DELETE CASCADE ON UPDATE CASCADE;


SET NAMES utf8mb4;
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
