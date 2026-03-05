<?php

class RelatorioController extends Controller
{
    private MovimentoModel $movimentoModel;

    public function __construct()
    {
        $this->movimentoModel = new MovimentoModel();
    }

    public function index(): void
    {
        $this->requireAuth();
        $userId = $this->currentUserId();

        $ano = (int)($_GET['ano'] ?? date('Y'));
        $mes = (int)($_GET['mes'] ?? date('n'));

        if ($mes < 1 || $mes > 12) $mes = (int)date('n');
        if ($ano < 2000 || $ano > 2100) $ano = (int)date('Y');

        $dadosMensais    = $this->movimentoModel->dadosMensaisAno($userId, $ano);
        $despesasCat     = $this->movimentoModel->despesasPorCategoria($userId, $mes, $ano);
        $evolucaoSaldo   = $this->movimentoModel->evolucaoSaldo($userId, $ano);
        $anos            = $this->movimentoModel->anosDisponiveis($userId);

        $mesesNomes = [
            1=>'Jan',2=>'Fev',3=>'Mar',4=>'Abr',5=>'Mai',6=>'Jun',
            7=>'Jul',8=>'Ago',9=>'Set',10=>'Out',11=>'Nov',12=>'Dez'
        ];

        // Preparar arrays para ApexCharts
        $labelsBar    = array_values($mesesNomes);
        $serieEntradas = [];
        $serieSaidas   = [];
        for ($m = 1; $m <= 12; $m++) {
            $serieEntradas[] = round($dadosMensais[$m]['entradas'], 2);
            $serieSaidas[]   = round($dadosMensais[$m]['saidas'], 2);
        }

        $labelsPie  = array_column($despesasCat, 'categoria');
        $seriePie   = array_map(fn($r) => round((float)$r['total'], 2), $despesasCat);

        $serieLine  = array_values($evolucaoSaldo);

        $pageTitle  = 'Relatórios';
        $anoAtual   = date('Y');
        if (!in_array($anoAtual, $anos)) array_unshift($anos, $anoAtual);

        $this->view('relatorios/index', compact(
            'ano', 'mes', 'anos', 'mesesNomes',
            'labelsBar', 'serieEntradas', 'serieSaidas',
            'labelsPie', 'seriePie',
            'serieLine', 'pageTitle'
        ));
    }
}
