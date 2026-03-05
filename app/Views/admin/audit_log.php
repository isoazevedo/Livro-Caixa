<main class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="h4 fw-bold mb-0">
            <i class="bi bi-journal-text me-2"></i>Log de Auditoria
        </h2>
        <a href="<?= BASE_URL ?>/admin" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent small text-muted">
            <?= $total ?> registro(s) — página <?= $pagina ?> de <?= max(1, $totalPags) ?>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Data/Hora</th>
                        <th>Usuário</th>
                        <th>Ação</th>
                        <th>Detalhes</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($registros as $r): ?>
                <tr>
                    <td class="ps-3 text-muted"><?= $r['id'] ?></td>
                    <td class="text-nowrap"><?= View::e($r['created_at']) ?></td>
                    <td>
                        <?php if ($r['user_nome']): ?>
                        <span class="fw-semibold"><?= View::e($r['user_nome']) ?></span>
                        <br><small class="text-muted"><?= View::e($r['user_login'] ?? '') ?></small>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td><code><?= View::e($r['acao']) ?></code></td>
                    <td class="small text-muted"><?= View::e($r['detalhes'] ?? '') ?></td>
                    <td class="small font-monospace"><?= View::e($r['ip']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($registros)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Nenhum registro.</td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <?php if ($totalPags > 1): ?>
        <div class="card-footer bg-transparent">
            <nav>
                <ul class="pagination pagination-sm mb-0 justify-content-center">
                    <?php for ($p = 1; $p <= $totalPags; $p++): ?>
                    <li class="page-item <?= $p == $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $p ?>"><?= $p ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</main>
