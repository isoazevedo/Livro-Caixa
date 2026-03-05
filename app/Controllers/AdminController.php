<?php

class AdminController extends Controller
{
    private UserModel $userModel;
    private AuditModel $auditModel;

    public function __construct()
    {
        $this->userModel  = new UserModel();
        $this->auditModel = new AuditModel();
    }

    public function index(): void
    {
        $this->requireAdmin();
        $usuarios  = $this->userModel->listAll();
        $token     = $this->csrfToken();
        $sucesso   = $this->flashGet('sucesso');
        $erro      = $this->flashGet('erro');
        $pageTitle = 'Painel Admin';

        $this->view('admin/index', compact('usuarios', 'token', 'sucesso', 'erro', 'pageTitle'));
    }

    public function criarUsuario(): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $nome  = trim($_POST['nome'] ?? '');
        $login = trim($_POST['login'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if (empty($nome) || empty($login) || strlen($senha) < 8) {
            $this->flashSet('erro', 'Preencha todos os campos. Senha mínima: 8 caracteres.');
            $this->redirect('/admin');
        }

        if ($this->userModel->loginExists($login)) {
            $this->flashSet('erro', "O login \"{$login}\" j\u00e1 est\u00e1 em uso.");
            $this->redirect('/admin');
        }

        $newId = $this->userModel->create($nome, $login, $senha);

        $this->auditModel->log(
            $this->currentUserId(), 'usuario_criado',
            "Novo usuário: {$login} (ID {$newId})",
            $this->clientIp()
        );

        $this->flashSet('sucesso', "Usuário {$login} criado com sucesso!");
        $this->redirect('/admin');
    }

    public function toggleAtivo(): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $id    = (int)($_POST['id'] ?? 0);
        $ativo = (int)($_POST['ativo'] ?? 0);

        if ($id <= 0) {
            $this->redirect('/admin');
        }

        // Não deixa desativar o próprio admin
        if ($id === $this->currentUserId()) {
            $this->flashSet('erro', 'Você não pode desativar sua própria conta.');
            $this->redirect('/admin');
        }

        $this->userModel->setAtivo($id, $ativo);

        $acao = $ativo ? 'usuario_ativado' : 'usuario_desativado';
        $this->auditModel->log($this->currentUserId(), $acao, "User ID {$id}", $this->clientIp());

        $status = $ativo ? 'ativado' : 'desativado';
        $this->flashSet('sucesso', "Usuário {$status} com sucesso!");
        $this->redirect('/admin');
    }

    public function auditLog(): void
    {
        $this->requireAdmin();

        $pagina    = max(1, (int)($_GET['pagina'] ?? 1));
        $registros = $this->auditModel->listar($pagina);
        $total     = $this->auditModel->total();
        $porPagina = 20;
        $totalPags = (int)ceil($total / $porPagina);
        $pageTitle = 'Log de Auditoria';

        $this->view('admin/audit_log', compact(
            'registros', 'pagina', 'total', 'totalPags', 'pageTitle'
        ));
    }
}
