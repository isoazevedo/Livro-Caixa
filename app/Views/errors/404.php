<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Página não encontrada | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f5f6fa; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
    <div class="text-center px-3">
        <i class="bi bi-exclamation-circle text-secondary" style="font-size: 5rem;"></i>
        <h1 class="display-4 fw-bold mt-3">404</h1>
        <p class="lead text-muted">Página não encontrada.</p>
        <a href="<?= BASE_URL ?>/" class="btn btn-primary mt-2">
            <i class="bi bi-house me-1"></i>Voltar ao início
        </a>
    </div>
</body>
</html>
