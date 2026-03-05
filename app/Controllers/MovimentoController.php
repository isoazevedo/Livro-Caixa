<?php

class MovimentoController extends Controller
{
    private MovimentoModel $movimentoModel;
    private CategoriaModel $categoriaModel;
    private AuditModel $auditModel;

    public function __construct()
    {
        $this->movimentoModel = new MovimentoModel();
        $this->categoriaModel = new CategoriaModel();
        $this->auditModel     = new AuditModel();
    }

    public function index(): void
    {
        $this->requireAuth();
        $userId = $this->currentUserId();

        $mes = (int)($_GET['mes'] ?? date('n'));
        $ano = (int)($_GET['ano'] ?? date('Y'));

        if ($mes < 1 || $mes > 12) $mes = (int)date('n');
        if ($ano < 2000 || $ano > 2100) $ano = (int)date('Y');

        // A) Auto-gerar recorrentes se mês vazio
        $recorrentesGerados = 0;
        if ($this->movimentoModel->contarPorMesAno($userId, $mes, $ano) === 0) {
            $mesAnt = $mes === 1 ? 12 : $mes - 1;
            $anoAnt = $mes === 1 ? $ano - 1 : $ano;
            $recorrentesGerados = $this->movimentoModel->gerarRecorrentes(
                $userId, $mes, $ano, $mesAnt, $anoAnt
            );
            if ($recorrentesGerados > 0) {
                $this->flashSet('sucesso', "{$recorrentesGerados} movimento(s) recorrente(s) gerado(s) automaticamente.");
            }
        }

        $movimentos  = $this->movimentoModel->listarPorMesAno($userId, $mes, $ano);
        $categorias  = $this->categoriaModel->listar($userId);
        $totais      = $this->movimentoModel->totaisPorMesAno($userId, $mes, $ano);
        $balanco     = $this->movimentoModel->balanco($userId);
        $anos        = $this->movimentoModel->anosDisponiveis($userId);
        $token       = $this->csrfToken();
        $sucesso     = $this->flashGet('sucesso');
        $erro        = $this->flashGet('erro');

        $this->view('movimentos/index', compact(
            'movimentos', 'categorias', 'totais', 'balanco',
            'anos', 'mes', 'ano', 'token', 'sucesso', 'erro'
        ));
    }

    public function adicionar(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        $userId = $this->currentUserId();

        $categoriaId = (int)($_POST['categoria_id'] ?? 0);
        $descricao   = trim($_POST['descricao'] ?? '');
        $valor       = (float)str_replace(',', '.', $_POST['valor'] ?? '0');
        $tipo        = $_POST['tipo'] ?? '';
        $dia         = (int)($_POST['dia'] ?? 1);
        $mes         = (int)($_POST['mes'] ?? date('n'));
        $ano         = (int)($_POST['ano'] ?? date('Y'));
        $recorrente  = isset($_POST['recorrente']) ? 1 : 0;

        if (!in_array($tipo, ['receita', 'despesa']) || $valor <= 0 || empty($descricao)) {
            $this->flashSet('erro', 'Dados inválidos. Verifique o formulário.');
            $this->redirect("/?mes={$mes}&ano={$ano}");
        }

        $this->movimentoModel->adicionar(
            $userId, $categoriaId, $descricao, $valor, $tipo, $dia, $mes, $ano, $recorrente
        );

        // J) Auditoria
        $this->auditModel->log(
            $userId, 'movimento_adicionado',
            "{$tipo}: {$descricao} — R$ " . number_format($valor, 2, ',', '.'),
            $this->clientIp()
        );

        $this->flashSet('sucesso', 'Movimento adicionado com sucesso!');
        $this->redirect("/?mes={$mes}&ano={$ano}");
    }

    public function editar(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        $userId = $this->currentUserId();

        $id          = (int)($_POST['id'] ?? 0);
        $categoriaId = (int)($_POST['categoria_id'] ?? 0);
        $descricao   = trim($_POST['descricao'] ?? '');
        $valor       = (float)str_replace(',', '.', $_POST['valor'] ?? '0');
        $tipo        = $_POST['tipo'] ?? '';
        $dia         = (int)($_POST['dia'] ?? 1);
        $mes         = (int)($_POST['mes'] ?? date('n'));
        $ano         = (int)($_POST['ano'] ?? date('Y'));
        $recorrente  = isset($_POST['recorrente']) ? 1 : 0;

        if (!in_array($tipo, ['receita', 'despesa']) || $valor <= 0 || $id <= 0) {
            $this->flashSet('erro', 'Dados inválidos.');
            $this->redirect("/?mes={$mes}&ano={$ano}");
        }

        $this->movimentoModel->editar(
            $id, $userId, $categoriaId, $descricao, $valor, $tipo, $dia, $mes, $ano, $recorrente
        );

        // J) Auditoria
        $this->auditModel->log(
            $userId, 'movimento_editado',
            "ID {$id}: {$tipo} {$descricao} — R$ " . number_format($valor, 2, ',', '.'),
            $this->clientIp()
        );

        $this->flashSet('sucesso', 'Movimento atualizado com sucesso!');
        $this->redirect("/?mes={$mes}&ano={$ano}");
    }

    public function apagar(string $id): void
    {
        $this->requireAuth();
        $userId = $this->currentUserId();
        $id = (int)$id;

        $mes = (int)($_GET['mes'] ?? date('n'));
        $ano = (int)($_GET['ano'] ?? date('Y'));

        $this->movimentoModel->apagar($id, $userId);

        // J) Auditoria
        $this->auditModel->log($userId, 'movimento_apagado', "ID {$id}", $this->clientIp());

        $this->flashSet('sucesso', 'Movimento removido com sucesso!');
        $this->redirect("/?mes={$mes}&ano={$ano}");
    }

    // D) Exportar CSV
    public function exportarCsv(): void
    {
        $this->requireAuth();
        $userId = $this->currentUserId();

        $mes = (int)($_GET['mes'] ?? date('n'));
        $ano = (int)($_GET['ano'] ?? date('Y'));

        $movimentos = $this->movimentoModel->listarPorMesAno($userId, $mes, $ano);

        $filename = "extrato-{$mes}-{$ano}.csv";
        header('Content-Type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        $out = fopen('php://output', 'w');
        // BOM UTF-8
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Dia', 'Descrição', 'Categoria', 'Tipo', 'Valor']);

        foreach ($movimentos as $m) {
            fputcsv($out, [
                str_pad($m['dia'], 2, '0', STR_PAD_LEFT),
                $m['descricao'],
                $m['categoria_nome'] ?? '',
                $m['tipo'],
                number_format($m['valor'], 2, ',', '.'),
            ]);
        }
        fclose($out);
        exit;
    }

    // E) Importar CSV — exibe formulário
    public function importarForm(): void
    {
        $this->requireAuth();
        $token  = $this->csrfToken();
        $mes    = (int)($_GET['mes'] ?? date('n'));
        $ano    = (int)($_GET['ano'] ?? date('Y'));
        $preview = $_SESSION['import_preview'] ?? null;
        unset($_SESSION['import_preview']);

        $this->view('movimentos/importar', compact('token', 'mes', 'ano', 'preview'));
    }

    // E) Importar CSV — parse e preview
    public function importar(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        $userId = $this->currentUserId();

        $mes = (int)($_POST['mes'] ?? date('n'));
        $ano = (int)($_POST['ano'] ?? date('Y'));

        if (empty($_FILES['arquivo']['tmp_name'])) {
            $this->flashSet('erro', 'Nenhum arquivo enviado.');
            $this->redirect("/movimentos/importar?mes={$mes}&ano={$ano}");
        }

        $categorias = $this->categoriaModel->listar($userId);
        $catMap = [];
        foreach ($categorias as $c) {
            $catMap[mb_strtolower(trim($c['nome']))] = (int)$c['id'];
        }

        $handle = fopen($_FILES['arquivo']['tmp_name'], 'r');
        // Remove BOM se presente
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $linhas = [];
        $erros  = [];
        $header = fgetcsv($handle);
        $lnum   = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $lnum++;
            if (count($row) < 5) {
                $erros[] = "Linha {$lnum}: colunas insuficientes.";
                continue;
            }
            [$dia, $descricao, $valor, $tipo, $cat] = $row;

            $dia  = (int)trim($dia);
            $desc = trim($descricao);
            $val  = (float)str_replace([',', ' '], ['.', ''], trim($valor));
            $tipo = mb_strtolower(trim($tipo));

            // Normalizar tipo
            if (in_array($tipo, ['entrada', 'receita', 'r'])) $tipo = 'receita';
            elseif (in_array($tipo, ['saida', 'saída', 'despesa', 'd'])) $tipo = 'despesa';
            else {
                $erros[] = "Linha {$lnum}: tipo inv\u00e1lido \"{$tipo}\".";
                continue;
            }

            if ($dia < 1 || $dia > 31 || $val <= 0 || empty($desc)) {
                $erros[] = "Linha {$lnum}: dados inválidos.";
                continue;
            }

            $catNome = mb_strtolower(trim($cat));
            $catId   = $catMap[$catNome] ?? null;

            $linhas[] = [
                'dia'         => $dia,
                'descricao'   => $desc,
                'valor'       => $val,
                'tipo'        => $tipo,
                'categoria_id'=> $catId,
                'categoria_nome' => trim($cat),
            ];
        }
        fclose($handle);

        $_SESSION['import_preview'] = [
            'linhas' => $linhas,
            'erros'  => $erros,
            'mes'    => $mes,
            'ano'    => $ano,
        ];

        $this->redirect("/movimentos/importar?mes={$mes}&ano={$ano}");
    }

    // E) Confirmar importação
    public function confirmarImportar(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        $userId = $this->currentUserId();

        $dados = $_SESSION['import_preview'] ?? null;
        unset($_SESSION['import_preview']);

        if (!$dados || empty($dados['linhas'])) {
            $this->flashSet('erro', 'Nenhum dado para importar.');
            $this->redirect('/movimentos/importar');
        }

        $mes = (int)($dados['mes'] ?? date('n'));
        $ano = (int)($dados['ano'] ?? date('Y'));
        $count = 0;

        foreach ($dados['linhas'] as $l) {
            $this->movimentoModel->adicionar(
                $userId,
                (int)($l['categoria_id'] ?? 0),
                $l['descricao'],
                (float)$l['valor'],
                $l['tipo'],
                (int)$l['dia'],
                $mes,
                $ano
            );
            $count++;
        }

        // J) Auditoria
        $this->auditModel->log(
            $userId, 'importacao_csv',
            "{$count} movimento(s) importado(s) para {$mes}/{$ano}",
            $this->clientIp()
        );

        $this->flashSet('sucesso', "{$count} movimento(s) importado(s) com sucesso!");
        $this->redirect("/?mes={$mes}&ano={$ano}");
    }
}
