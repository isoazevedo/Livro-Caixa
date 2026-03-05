<?php

class AuditModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function log(?int $userId, string $acao, ?string $detalhes, string $ip): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO lc_audit_log (user_id, acao, detalhes, ip)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $acao, $detalhes, $ip]);
    }

    public function listar(int $pagina = 1, int $porPagina = 20): array
    {
        $offset = ($pagina - 1) * $porPagina;
        $stmt = $this->db->prepare(
            'SELECT a.*, u.nome AS user_nome, u.login AS user_login
             FROM lc_audit_log a
             LEFT JOIN lc_users u ON u.id = a.user_id
             ORDER BY a.id DESC
             LIMIT ? OFFSET ?'
        );
        $stmt->execute([$porPagina, $offset]);
        return $stmt->fetchAll();
    }

    public function total(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) FROM lc_audit_log');
        return (int) $stmt->fetchColumn();
    }
}
