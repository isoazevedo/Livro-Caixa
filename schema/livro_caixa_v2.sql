-- ============================================================
-- Livro Caixa v2 - Schema MySQL
-- Compatível com MySQL 5.7+ / MariaDB 10.3+
-- ============================================================

CREATE DATABASE IF NOT EXISTS livro_caixa
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE livro_caixa;

-- ------------------------------------------------------------
-- Tabela de usuários
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lc_users (
    id         INT          NOT NULL AUTO_INCREMENT,
    nome       VARCHAR(100) NOT NULL,
    login      VARCHAR(50)  NOT NULL,
    senha      VARCHAR(255) NOT NULL,           -- password_hash(PASSWORD_BCRYPT)
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_login (login)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela de categorias
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lc_cat (
    id      INT          NOT NULL AUTO_INCREMENT,
    user_id INT          NOT NULL,
    nome    VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    KEY idx_user (user_id),
    CONSTRAINT fk_cat_user FOREIGN KEY (user_id) REFERENCES lc_users (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela de movimentos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lc_movimento (
    id           INT            NOT NULL AUTO_INCREMENT,
    user_id      INT            NOT NULL,
    categoria_id INT                     DEFAULT NULL,
    descricao    VARCHAR(200)   NOT NULL,
    valor        DECIMAL(10, 2) NOT NULL,
    tipo         ENUM('receita','despesa') NOT NULL,
    dia          TINYINT        NOT NULL DEFAULT 1,
    mes          TINYINT        NOT NULL,
    ano          SMALLINT       NOT NULL,
    PRIMARY KEY (id),
    KEY idx_user_mes_ano (user_id, mes, ano),
    CONSTRAINT fk_mov_user FOREIGN KEY (user_id) REFERENCES lc_users (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_mov_cat FOREIGN KEY (categoria_id) REFERENCES lc_cat (id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Usuário de exemplo (senha: admin123)
-- Gere um novo hash em PHP: echo password_hash('suasenha', PASSWORD_BCRYPT);
-- ------------------------------------------------------------
-- Usuário: admin / Senha: admin123
-- TROQUE a senha após o primeiro login!
INSERT INTO lc_users (nome, login, senha) VALUES
('Administrador', 'admin', '$2y$12$bmyIcJfaMGWFJOqOfDKxfO6OPIQD0zRVzOkA.OJk2vGyyqVxCgKzW');
