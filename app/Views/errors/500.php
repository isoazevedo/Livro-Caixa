<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Erro interno | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f5f6fa; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
    <div class="text-center px-3">
        <i class="bi bi-bug text-danger" style="font-size: 5rem;"></i>
        <h1 class="display-4 fw-bold mt-3">500</h1>
        <p class="lead text-muted">Ocorreu um erro interno. Tente novamente em instantes.</p>
        <?php if (!empty($mensagem) && defined('APP_DEBUG') && APP_DEBUG): ?>
        <div class="alert alert-danger text-start mt-3" style="max-width: 600px; margin: auto;">
            <pre class="mb-0 small"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></pre>
        </div>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/" class="btn btn-primary mt-3">
            <i class="bi bi-house me-1"></i>Voltar ao início
        </a>
    </div>
</body>
</html>
