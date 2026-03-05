<?php

abstract class Controller
{
    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (empty($_SESSION['user_admin'])) {
            $this->redirect('/');
        }
    }

    protected function redirect(string $path): never
    {
        $url = BASE_URL . $path;
        header('Location: ' . $url);
        exit;
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            exit('Requisição inválida (CSRF).');
        }
    }

    protected function view(string $view, array $data = []): void
    {
        View::renderWithLayout($view, $data);
    }

    protected function currentUserId(): int
    {
        return (int) $_SESSION['user_id'];
    }

    protected function flashSet(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    protected function flashGet(string $key): string
    {
        $msg = $_SESSION['flash'][$key] ?? '';
        unset($_SESSION['flash'][$key]);
        return $msg;
    }

    protected function clientIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
