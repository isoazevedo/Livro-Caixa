<main class="container py-4" style="max-width: 560px;">

    <h2 class="h4 fw-bold mb-4"><i class="bi bi-person-gear me-2"></i>Meu Perfil</h2>

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

    <!-- Dados pessoais -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent fw-semibold">
            <i class="bi bi-person me-2"></i>Dados Pessoais
        </div>
        <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>/perfil">
                <input type="hidden" name="csrf_token" value="<?= View::e($token) ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Login</label>
                    <input type="text" class="form-control" value="<?= View::e($user['login']) ?>" disabled>
                    <div class="form-text">O login não pode ser alterado.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome</label>
                    <input type="text" class="form-control" name="nome"
                           value="<?= View::e($user['nome']) ?>" required maxlength="100">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Salvar Alterações
                </button>
            </form>
        </div>
    </div>

    <!-- Alterar senha -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent fw-semibold">
            <i class="bi bi-shield-lock me-2"></i>Alterar Senha
        </div>
        <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>/perfil/senha">
                <input type="hidden" name="csrf_token" value="<?= View::e($token) ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Senha Atual</label>
                    <input type="password" class="form-control" name="senha_atual" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nova Senha <span class="text-muted fw-normal">(mín. 8 caracteres)</span></label>
                    <input type="password" class="form-control" name="nova_senha" required minlength="8">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Confirmar Nova Senha</label>
                    <input type="password" class="form-control" name="confirmar_senha" required minlength="8">
                </div>

                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-key me-1"></i>Alterar Senha
                </button>
            </form>
        </div>
    </div>

</main>
