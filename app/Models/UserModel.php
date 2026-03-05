<?php

class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByLogin(string $login): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM lc_users WHERE login = ? LIMIT 1'
        );
        $stmt->execute([$login]);
        return $stmt->fetch();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM lc_users WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(string $nome, string $login, string $senha): int
    {
        $hash = password_hash($senha, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            'INSERT INTO lc_users (nome, login, senha) VALUES (?, ?, ?)'
        );
        $stmt->execute([$nome, $login, $hash]);
        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $id, string $nome): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE lc_users SET nome = ? WHERE id = ?'
        );
        return $stmt->execute([$nome, $id]);
    }

    public function changePassword(int $id, string $hash): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE lc_users SET senha = ? WHERE id = ?'
        );
        return $stmt->execute([$hash, $id]);
    }

    public function listAll(): array
    {
        $stmt = $this->db->query(
            'SELECT u.*, COUNT(m.id) AS total_movimentos
             FROM lc_users u
             LEFT JOIN lc_movimento m ON m.user_id = u.id
             GROUP BY u.id
             ORDER BY u.id ASC'
        );
        return $stmt->fetchAll();
    }

    public function setAtivo(int $id, int $ativo): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE lc_users SET ativo = ? WHERE id = ?'
        );
        return $stmt->execute([$ativo, $id]);
    }

    public function loginExists(string $login): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM lc_users WHERE login = ?'
        );
        $stmt->execute([$login]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
