<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
</head>
<body class="login-page d-flex align-items-center justify-content-center min-vh-100">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">

            <div class="text-center mb-4">
                <div class="login-icon mb-3">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <h1 class="h3 fw-bold text-white"><?= APP_NAME ?></h1>
                <p class="text-white-50 mb-0">Controle financeiro pessoal</p>
            </div>

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h2 class="h5 fw-bold text-center mb-4">Entrar na conta</h2>

                    <?php if ($erro): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= View::e($erro) ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/login">
                        <input type="hidden" name="csrf_token" value="<?= View::e($token) ?>">

                        <div class="mb-3">
                            <label for="login" class="form-label fw-semibold">Usuário</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="login"
                                    name="login"
                                    placeholder="Seu login"
                                    required
                                    autocomplete="username"
                                    autofocus
                                >
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="senha" class="form-label fw-semibold">Senha</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="senha"
                                    name="senha"
                                    placeholder="Sua senha"
                                    required
                                    autocomplete="current-password"
                                >
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
