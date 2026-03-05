<?php

class MovimentoModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function listarPorMesAno(int $userId, int $mes, int $ano): array
    {
        $stmt = $this->db->prepare(
            'SELECT m.*, c.nome AS categoria_nome
             FROM lc_movimento m
             LEFT JOIN lc_cat c ON c.id = m.categoria_id
             WHERE m.user_id = ? AND m.mes = ? AND m.ano = ?
             ORDER BY m.dia ASC, m.id ASC'
        );
        $stmt->execute([$userId, $mes, $ano]);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id, int $userId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM lc_movimento WHERE id = ? AND user_id = ? LIMIT 1'
        );
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }

    public function adicionar(
        int $userId,
        int $categoriaId,
        string $descricao,
        float $valor,
        string $tipo,
        int $dia,
        int $mes,
        int $ano,
        int $recorrente = 0
    ): bool {
        $stmt = $this->db->prepare(
            'INSERT INTO lc_movimento
             (user_id, categoria_id, descricao, valor, tipo, dia, mes, ano, recorrente)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        return $stmt->execute([
            $userId, $categoriaId, $descricao, $valor, $tipo, $dia, $mes, $ano, $recorrente
        ]);
    }

    public function editar(
        int $id,
        int $userId,
        int $categoriaId,
        string $descricao,
        float $valor,
        string $tipo,
        int $dia,
        int $mes,
        int $ano,
        int $recorrente = 0
    ): bool {
        $stmt = $this->db->prepare(
            'UPDATE lc_movimento
             SET categoria_id = ?, descricao = ?, valor = ?, tipo = ?,
                 dia = ?, mes = ?, ano = ?, recorrente = ?
             WHERE id = ? AND user_id = ?'
        );
        return $stmt->execute([
            $categoriaId, $descricao, $valor, $tipo, $dia, $mes, $ano, $recorrente,
            $id, $userId
        ]);
    }

    public function apagar(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM lc_movimento WHERE id = ? AND user_id = ?'
        );
        return $stmt->execute([$id, $userId]);
    }

    public function totaisPorMesAno(int $userId, int $mes, int $ano): array
    {
        $stmt = $this->db->prepare(
            'SELECT
               SUM(CASE WHEN tipo = "receita" THEN valor ELSE 0 END) AS entradas,
               SUM(CASE WHEN tipo = "despesa" THEN valor ELSE 0 END) AS saidas
             FROM lc_movimento
             WHERE user_id = ? AND mes = ? AND ano = ?'
        );
        $stmt->execute([$userId, $mes, $ano]);
        $row = $stmt->fetch();
        return [
            'entradas' => (float)($row['entradas'] ?? 0),
            'saidas'   => (float)($row['saidas'] ?? 0),
        ];
    }

    public function balanco(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
               SUM(CASE WHEN tipo = "receita" THEN valor ELSE 0 END) AS entradas,
               SUM(CASE WHEN tipo = "despesa" THEN valor ELSE 0 END) AS saidas
             FROM lc_movimento
             WHERE user_id = ?'
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return [
            'entradas' => (float)($row['entradas'] ?? 0),
            'saidas'   => (float)($row['saidas'] ?? 0),
        ];
    }

    public function anosDisponiveis(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT DISTINCT ano FROM lc_movimento WHERE user_id = ? ORDER BY ano DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function gerarRecorrentes(
        int $userId,
        int $mesDest,
        int $anoDest,
        int $mesOrig,
        int $anoOrig
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO lc_movimento
             (user_id, categoria_id, descricao, valor, tipo, dia, mes, ano, recorrente)
             SELECT user_id, categoria_id, descricao, valor, tipo, dia, ?, ?, recorrente
             FROM lc_movimento
             WHERE user_id = ? AND mes = ? AND ano = ? AND recorrente = 1'
        );
        $stmt->execute([$mesDest, $anoDest, $userId, $mesOrig, $anoOrig]);
        return $stmt->rowCount();
    }

    public function dadosMensaisAno(int $userId, int $ano): array
    {
        $stmt = $this->db->prepare(
            'SELECT mes,
               SUM(CASE WHEN tipo = "receita" THEN valor ELSE 0 END) AS entradas,
               SUM(CASE WHEN tipo = "despesa" THEN valor ELSE 0 END) AS saidas
             FROM lc_movimento
             WHERE user_id = ? AND ano = ?
             GROUP BY mes
             ORDER BY mes ASC'
        );
        $stmt->execute([$userId, $ano]);
        $rows = $stmt->fetchAll();

        $result = array_fill(1, 12, ['entradas' => 0.0, 'saidas' => 0.0]);
        foreach ($rows as $r) {
            $result[(int)$r['mes']] = [
                'entradas' => (float)$r['entradas'],
                'saidas'   => (float)$r['saidas'],
            ];
        }
        return $result;
    }

    public function despesasPorCategoria(int $userId, int $mes, int $ano): array
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(c.nome, "Sem categoria") AS categoria,
               SUM(m.valor) AS total
             FROM lc_movimento m
             LEFT JOIN lc_cat c ON c.id = m.categoria_id
             WHERE m.user_id = ? AND m.mes = ? AND m.ano = ? AND m.tipo = "despesa"
             GROUP BY m.categoria_id, c.nome
             ORDER BY total DESC'
        );
        $stmt->execute([$userId, $mes, $ano]);
        return $stmt->fetchAll();
    }

    public function evolucaoSaldo(int $userId, int $ano): array
    {
        $dados = $this->dadosMensaisAno($userId, $ano);
        $saldo = 0.0;
        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $saldo += $dados[$m]['entradas'] - $dados[$m]['saidas'];
            $result[$m] = round($saldo, 2);
        }
        return $result;
    }

    public function contarPorMesAno(int $userId, int $mes, int $ano): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM lc_movimento WHERE user_id = ? AND mes = ? AND ano = ?'
        );
        $stmt->execute([$userId, $mes, $ano]);
        return (int) $stmt->fetchColumn();
    }
}
