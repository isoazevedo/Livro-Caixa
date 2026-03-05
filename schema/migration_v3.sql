-- ============================================================
-- Livro Caixa v3 — Migration
-- ============================================================

-- A) Recorrentes: coluna no lc_movimento
ALTER TABLE lc_movimento
    ADD COLUMN recorrente TINYINT(1) NOT NULL DEFAULT 0 AFTER ano;

-- G) Admin flag + usuário ativo
ALTER TABLE lc_users
    ADD COLUMN admin TINYINT(1) NOT NULL DEFAULT 0 AFTER senha,
    ADD COLUMN ativo TINYINT(1) NOT NULL DEFAULT 1 AFTER admin;

UPDATE lc_users SET admin = 1 WHERE login = 'admin';

-- H) Rate limiting por IP
CREATE TABLE IF NOT EXISTS lc_login_attempts (
    id            INT          PRIMARY KEY AUTO_INCREMENT,
    ip            VARCHAR(45)  NOT NULL,
    tentativas    INT          NOT NULL DEFAULT 1,
    bloqueado_ate DATETIME     DEFAULT NULL,
    updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_ip (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- J) Auditoria
CREATE TABLE IF NOT EXISTS lc_audit_log (
    id         INT          PRIMARY KEY AUTO_INCREMENT,
    user_id    INT          DEFAULT NULL,
    acao       VARCHAR(100) NOT NULL,
    detalhes   TEXT         DEFAULT NULL,
    ip         VARCHAR(45)  NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_user (user_id),
    KEY idx_acao (acao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
