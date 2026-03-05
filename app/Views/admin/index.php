<main class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="h4 fw-bold mb-0"><i class="bi bi-shield-lock me-2"></i>Painel Administrativo</h2>
        <a href="<?= BASE_URL ?>/admin/audit" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-journal-text me-1"></i>Log de Auditoria
        </a>
    </div>

    <?php if ($sucesso): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i><?= View::e($sucesso) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($erro): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle me-2"></i><?= View::e($erro) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Lista de usuários -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-people me-2"></i>Usuários
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Nome</th>
                                <th>Login</th>
                                <th class="text-center">Admin</th>
                                <th class="text-center">Movimentos</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-3">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td class="ps-3 text-muted"><?= $u['id'] ?></td>
                            <td class="fw-semibold"><?= View::e($u['nome']) ?></td>
                            <td><?= View::e($u['login']) ?></td>
                            <td class="text-center">
                                <?php if ($u['admin']): ?>
                                <span class="badge bg-primary-subtle text-primary">Admin</span>
                                <?php else: ?>
                                <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= $u['total_movimentos'] ?></td>
                            <td class="text-center">
                                <?php if ($u['ativo']): ?>
                                <span class="badge bg-success-subtle text-success">Ativo</span>
                                <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                                <form method="POST" action="<?= BASE_URL ?>/admin/usuarios/toggle" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= View::e($token) ?>">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <input type="hidden" name="ativo" value="<?= $u['ativo'] ? 0 : 1 ?>">
                                    <button type="submit" class="btn btn-sm <?= $u['ativo'] ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                                        <?php if ($u['ativo']): ?>
                                        <i class="bi bi-person-slash"></i> Desativar
                                        <?php else: ?>
                                        <i class="bi bi-person-check"></i> Ativar
                                        <?php endif; ?>
                                    </button>
                                </form>
                                <?php else: ?>
                                <span class="text-muted small">Você</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Criar novo usuário -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-person-plus me-2"></i>Novo Usuário
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>/admin/usuarios/criar">
                        <input type="hidden" name="csrf_token" value="<?= View::e($token) ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nome</label>
                            <input type="text" class="form-control" name="nome" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Login</label>
                            <input type="text" class="form-control" name="login" required maxlength="60">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Senha <span class="text-muted fw-normal">(mín. 8 chars)</span></label>
                            <input type="password" class="form-control" name="senha" required minlength="8">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus-lg me-1"></i>Criar Usuário
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</main>
