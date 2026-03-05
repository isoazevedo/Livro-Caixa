<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? View::e($pageTitle) . ' | ' : '' ?><?= APP_NAME ?></title>
    <!-- L) Dark mode: aplicar tema antes do render para evitar flash -->
    <script>
    (function () {
        var theme = localStorage.getItem('lcTheme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', theme);
    })();
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
</head>
<body>
<?php if (!empty($_SESSION['user_id'])): ?>
<nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/">
            <i class="bi bi-cash-coin me-2"></i><?= APP_NAME ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/">
                        <i class="bi bi-house me-1"></i>Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/relatorios">
                        <i class="bi bi-bar-chart-line me-1"></i>Relatórios
                    </a>
                </li>
                <?php if (!empty($_SESSION['user_admin'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/admin">
                        <i class="bi bi-shield-lock me-1"></i>Admin
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav align-items-center gap-2">
                <!-- L) Toggle dark/light -->
                <li class="nav-item">
                    <button id="btnToggleDark" class="btn btn-sm btn-outline-light" onclick="toggleDarkMode()" title="Alternar tema">
                        <i id="iconTheme" class="bi bi-moon"></i>
                    </button>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/perfil">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= View::e($_SESSION['user_nome'] ?? '') ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-light btn-sm" href="<?= BASE_URL ?>/logout">
                        <i class="bi bi-box-arrow-right me-1"></i>Sair
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>
<div class="page-wrapper">
