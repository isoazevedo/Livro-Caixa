<?php

class CategoriaController extends Controller
{
    private CategoriaModel $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
    }

    public function adicionar(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        $userId = $this->currentUserId();

        $nome = trim($_POST['nome'] ?? '');
        $mes  = (int)($_POST['mes'] ?? date('n'));
        $ano  = (int)($_POST['ano'] ?? date('Y'));

        if (empty($nome)) {
            $this->flashSet('erro', 'Nome da categoria é obrigatório.');
            $this->redirect("/?mes={$mes}&ano={$ano}#categorias");
        }

        $this->categoriaModel->adicionar($nome, $userId);
        $this->flashSet('sucesso', 'Categoria adicionada com sucesso!');
        $this->redirect("/?mes={$mes}&ano={$ano}#categorias");
    }

    public function editar(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        $userId = $this->currentUserId();

        $id   = (int)($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $mes  = (int)($_POST['mes'] ?? date('n'));
        $ano  = (int)($_POST['ano'] ?? date('Y'));

        if (empty($nome) || $id <= 0) {
            $this->flashSet('erro', 'Dados inválidos.');
            $this->redirect("/?mes={$mes}&ano={$ano}#categorias");
        }

        $this->categoriaModel->editar($id, $nome, $userId);
        $this->flashSet('sucesso', 'Categoria atualizada com sucesso!');
        $this->redirect("/?mes={$mes}&ano={$ano}#categorias");
    }

    public function apagar(string $id): void
    {
        $this->requireAuth();
        $userId = $this->currentUserId();
        $id = (int)$id;

        $mes = (int)($_GET['mes'] ?? date('n'));
        $ano = (int)($_GET['ano'] ?? date('Y'));

        $this->categoriaModel->apagar($id, $userId);
        $this->flashSet('sucesso', 'Categoria removida com sucesso!');
        $this->redirect("/?mes={$mes}&ano={$ano}#categorias");
    }
}
