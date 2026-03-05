<?php

class UserController extends Controller
{
    private UserModel $userModel;
    private AuditModel $auditModel;

    public function __construct()
    {
        $this->userModel  = new UserModel();
        $this->auditModel = new AuditModel();
    }

    public function perfil(): void
    {
        $this->requireAuth();
        $userId = $this->currentUserId();
        $user   = $this->userModel->findById($userId);
        $token  = $this->csrfToken();
        $sucesso = $this->flashGet('sucesso');
        $erro    = $this->flashGet('erro');
        $pageTitle = 'Meu Perfil';

        $this->view('user/perfil', compact('user', 'token', 'sucesso', 'erro', 'pageTitle'));
    }

    public function atualizarPerfil(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        $userId = $this->currentUserId();

        $nome = trim($_POST['nome'] ?? '');
        if (empty($nome)) {
            $this->flashSet('erro', 'O nome não pode ser vazio.');
            $this->redirect('/perfil');
        }

        $this->userModel->updateProfile($userId, $nome);
        $_SESSION['user_nome'] = $nome;

        $this->auditModel->log($userId, 'perfil_atualizado', "Nome: {$nome}", $this->clientIp());

        $this->flashSet('sucesso', 'Perfil atualizado com sucesso!');
        $this->redirect('/perfil');
    }

    public function alterarSenha(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        $userId = $this->currentUserId();

        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha  = $_POST['nova_senha'] ?? '';
        $confirmar  = $_POST['confirmar_senha'] ?? '';

        if (strlen($novaSenha) < 8) {
            $this->flashSet('erro', 'A nova senha deve ter pelo menos 8 caracteres.');
            $this->redirect('/perfil');
        }

        if ($novaSenha !== $confirmar) {
            $this->flashSet('erro', 'A confirmação de senha não confere.');
            $this->redirect('/perfil');
        }

        $user = $this->userModel->findById($userId);
        if (!$user || !password_verify($senhaAtual, $user['senha'])) {
            $this->flashSet('erro', 'Senha atual incorreta.');
            $this->redirect('/perfil');
        }

        $hash = password_hash($novaSenha, PASSWORD_BCRYPT);
        $this->userModel->changePassword($userId, $hash);

        $this->auditModel->log($userId, 'senha_alterada', null, $this->clientIp());

        $this->flashSet('sucesso', 'Senha alterada com sucesso!');
        $this->redirect('/perfil');
    }
}
