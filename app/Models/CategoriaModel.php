<?php

class CategoriaModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function listar(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM lc_cat WHERE user_id = ? ORDER BY nome ASC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id, int $userId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM lc_cat WHERE id = ? AND user_id = ? LIMIT 1'
        );
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }

    public function adicionar(string $nome, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO lc_cat (nome, user_id) VALUES (?, ?)'
        );
        return $stmt->execute([$nome, $userId]);
    }

    public function editar(int $id, string $nome, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE lc_cat SET nome = ? WHERE id = ? AND user_id = ?'
        );
        return $stmt->execute([$nome, $id, $userId]);
    }

    public function apagar(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM lc_cat WHERE id = ? AND user_id = ?'
        );
        return $stmt->execute([$id, $userId]);
    }
}
