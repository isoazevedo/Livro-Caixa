<?php

class AuthController extends Controller
{
    private UserModel $userModel;
    private AuditModel $auditModel;
    private PDO $db;

    public function __construct()
    {
        $this->userModel  = new UserModel();
        $this->auditModel = new AuditModel();
        $this->db         = Database::getInstance();
    }

    public function loginForm(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        $erro = $this->flashGet('erro');
        $token = $this->csrfToken();
        View::render('auth/login', compact('erro', 'token'));
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $login = trim($_POST['login'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $ip    = $this->clientIp();

        // H) Rate limiting
        $bloqueio = $this->verificarBloqueio($ip);
        if ($bloqueio !== null) {
            $hora = date('H:i', strtotime($bloqueio));
            $this->flashSet('erro', "Muitas tentativas. Tente novamente às {$hora}.");
            $this->redirect('/login');
        }

        $user = $this->userModel->findByLogin($login);

        if (!$user || !password_verify($senha, $user['senha'])) {
            $this->registrarFalha($ip);
            $this->flashSet('erro', 'Login ou senha inválidos.');
            $this->redirect('/login');
        }

        // G) Verificar se usuário está ativo
        if (empty($user['ativo'])) {
            $this->flashSet('erro', 'Sua conta está desativada. Contate o administrador.');
            $this->redirect('/login');
        }

        // Sucesso: limpar tentativas
        $this->limparTentativas($ip);

        session_regenerate_id(true);
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_nome']  = $user['nome'];
        $_SESSION['user_admin'] = (bool)($user['admin'] ?? false);

        // J) Auditoria
        $this->auditModel->log($user['id'], 'login', "Login do usuário {$login}", $ip);

        $this->redirect('/');
    }

    public function logout(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        $ip     = $this->clientIp();

        if ($userId) {
            $this->auditModel->log((int)$userId, 'logout', null, $ip);
        }

        session_unset();
        session_destroy();
        $this->redirect('/login');
    }

    // ── Rate limiting helpers ──────────────────────────────────────────────

    private function verificarBloqueio(string $ip): ?string
    {
        $stmt = $this->db->prepare(
            'SELECT bloqueado_ate FROM lc_login_attempts
             WHERE ip = ? AND bloqueado_ate > NOW()
             LIMIT 1'
        );
        $stmt->execute([$ip]);
        $row = $stmt->fetch();
        return $row ? $row['bloqueado_ate'] : null;
    }

    private function registrarFalha(string $ip): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO lc_login_attempts (ip, tentativas)
             VALUES (?, 1)
             ON DUPLICATE KEY UPDATE
               tentativas    = tentativas + 1,
               bloqueado_ate = IF(tentativas + 1 >= 5, DATE_ADD(NOW(), INTERVAL 10 MINUTE), bloqueado_ate)'
        );
        $stmt->execute([$ip]);
    }

    private function limparTentativas(string $ip): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM lc_login_attempts WHERE ip = ?'
        );
        $stmt->execute([$ip]);
    }
}
