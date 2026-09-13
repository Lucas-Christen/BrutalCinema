-- BrutalCinema - Estrutura do banco de dados
--
-- O banco deve existir antes (veja README.md). Este script cria as tabelas
-- dentro do banco informado na linha de comando, por exemplo:
--   mariadb -u brutal -p brutalcinema < database/schema.sql
--
-- ATENÇÃO: apaga e recria todas as tabelas (perde os dados existentes).

-- Ordem de remoção respeita as chaves estrangeiras
DROP TABLE IF EXISTS ingressos;
DROP TABLE IF EXISTS sessoes;
DROP TABLE IF EXISTS salas;
DROP TABLE IF EXISTS filmes;
DROP TABLE IF EXISTS usuarios;

-- ---------------------------------------------------------------
-- Usuários do sistema (admin e funcionários da bilheteria)
-- ---------------------------------------------------------------
CREATE TABLE usuarios (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(100)  NOT NULL,
    email         VARCHAR(150)  NOT NULL,
    senha_hash    VARCHAR(255)  NOT NULL,
    perfil        ENUM('admin', 'funcionario','cliente') NOT NULL DEFAULT 'cliente',
    ativo         TINYINT(1)    NOT NULL DEFAULT 1,
    criado_em     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_usuarios_email (email)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Catálogo de filmes
-- ---------------------------------------------------------------
CREATE TABLE filmes (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo         VARCHAR(150)      NOT NULL,
    sinopse        TEXT              NULL,
    duracao_min    SMALLINT UNSIGNED NOT NULL,
    classificacao  ENUM('L', '10', '12', '14', '16', '18') NOT NULL,
    genero         VARCHAR(50)       NOT NULL,
    ano_lancamento YEAR              NULL,
    ativo          TINYINT(1)        NOT NULL DEFAULT 1,
    criado_em      DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em  DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Salas de exibição. Capacidade = fileiras * assentos_por_fileira
-- ---------------------------------------------------------------
CREATE TABLE salas (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome                 VARCHAR(50)      NOT NULL,
    fileiras             TINYINT UNSIGNED NOT NULL,
    assentos_por_fileira TINYINT UNSIGNED NOT NULL,
    tipo                 ENUM('2D', '3D', 'IMAX') NOT NULL DEFAULT '2D',
    ativo                TINYINT(1)       NOT NULL DEFAULT 1,
    criado_em            DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em        DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_salas_nome (nome)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Sessões: um filme exibido em uma sala em um horário
-- fim = inicio + duracao do filme + intervalo de limpeza (calculado no PHP)
-- ---------------------------------------------------------------
CREATE TABLE sessoes (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filme_id      INT UNSIGNED  NOT NULL,
    sala_id       INT UNSIGNED  NOT NULL,
    inicio        DATETIME      NOT NULL,
    fim           DATETIME      NOT NULL,
    preco         DECIMAL(8, 2) NOT NULL,
    idioma        ENUM('dublado', 'legendado') NOT NULL,
    status        ENUM('agendada', 'cancelada', 'encerrada') NOT NULL DEFAULT 'agendada',
    criado_em     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sessoes_sala_horario (sala_id, inicio, fim),
    CONSTRAINT fk_sessoes_filme FOREIGN KEY (filme_id) REFERENCES filmes (id) ON DELETE RESTRICT,
    CONSTRAINT fk_sessoes_sala  FOREIGN KEY (sala_id)  REFERENCES salas (id)  ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Ingressos vendidos. Assento = (fileira, numero) dentro da sala.
-- UNIQUE impede vender o mesmo assento duas vezes na mesma sessão.
-- Cancelamento exclui o registro, liberando o assento para nova venda.
-- ---------------------------------------------------------------
CREATE TABLE ingressos (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sessao_id    INT UNSIGNED     NOT NULL,
    usuario_id   INT UNSIGNED     NOT NULL,
    fileira      CHAR(1)          NOT NULL,
    numero       TINYINT UNSIGNED NOT NULL,
    nome_cliente VARCHAR(100)     NOT NULL,
    cpf_cliente  CHAR(11)         NOT NULL,
    tipo         ENUM('inteira', 'meia') NOT NULL DEFAULT 'inteira',
    valor_pago   DECIMAL(8, 2)    NOT NULL,
    vendido_em   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ingressos_assento (sessao_id, fileira, numero),
    CONSTRAINT fk_ingressos_sessao  FOREIGN KEY (sessao_id)  REFERENCES sessoes (id)  ON DELETE RESTRICT,
    CONSTRAINT fk_ingressos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE RESTRICT
) ENGINE=InnoDB;
