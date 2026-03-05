<?php
$mesesNomesCompletos = [
    1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',
    5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',
    9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'
];
$anoAtual = date('Y');
if (!in_array($anoAtual, $anos)) array_unshift($anos, $anoAtual);
?>
<main class="container-fluid py-4">

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
        <h2 class="h4 fw-bold mb-0"><i class="bi bi-bar-chart-line me-2"></i>Relatórios</h2>
        <div class="d-flex align-items-center gap-2">
            <!-- Seletor de mês -->
            <select class="form-select form-select-sm" onchange="location.href='?mes='+this.value+'&ano=<?= $ano ?>'">
                <?php foreach ($mesesNomesCompletos as $n => $nome): ?>
                <option value="<?= $n ?>" <?= $n == $mes ? 'selected' : '' ?>><?= $nome ?></option>
                <?php endforeach; ?>
            </select>
            <!-- Seletor de ano -->
            <select class="form-select form-select-sm" onchange="location.href='?mes=<?= $mes ?>&ano='+this.value">
                <?php foreach ($anos as $a): ?>
                <option value="<?= $a ?>" <?= $a == $ano ? 'selected' : '' ?>><?= $a ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row g-4">

        <!-- Gráfico 1: Entradas vs Saídas por mês -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">
                        <i class="bi bi-bar-chart me-2 text-primary"></i>
                        Entradas vs Saídas — <?= $ano ?>
                    </h6>
                    <div id="chartBar"></div>
                </div>
            </div>
        </div>

        <!-- Gráfico 2: Despesas por categoria -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">
                        <i class="bi bi-pie-chart me-2 text-warning"></i>
                        Despesas por Categoria —
                        <?= $mesesNomes[$mes] ?>/<?= $ano ?>
                    </h6>
                    <?php if (empty($labelsPie)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox display-5 d-block mb-2"></i>
                        Sem despesas neste mês.
                    </div>
                    <?php else: ?>
                    <div id="chartPie"></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Gráfico 3: Evolução do saldo -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">
                        <i class="bi bi-graph-up-arrow me-2 text-success"></i>
                        Evolução do Saldo Acumulado — <?= $ano ?>
                    </h6>
                    <div id="chartLine"></div>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- ApexCharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.0/dist/apexcharts.min.js"></script>
<script>
(function () {
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const theme  = isDark ? 'dark' : 'light';
    const fgColor = isDark ? '#dee2e6' : '#495057';

    // Gráfico 1 — Bar
    const labelsBar    = <?= json_encode($labelsBar) ?>;
    const serieEntradas = <?= json_encode($serieEntradas) ?>;
    const serieSaidas   = <?= json_encode($serieSaidas) ?>;

    new ApexCharts(document.getElementById('chartBar'), {
        chart: { type: 'bar', height: 280, background: 'transparent', toolbar: { show: false } },
        theme: { mode: theme },
        series: [
            { name: 'Entradas', data: serieEntradas },
            { name: 'Saídas',   data: serieSaidas   }
        ],
        xaxis: { categories: labelsBar },
        colors: ['#198754', '#dc3545'],
        dataLabels: { enabled: false },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
        yaxis: { labels: { formatter: v => 'R$ ' + v.toLocaleString('pt-BR', {minimumFractionDigits: 2}) } },
        tooltip: { y: { formatter: v => 'R$ ' + v.toLocaleString('pt-BR', {minimumFractionDigits: 2}) } },
    }).render();

    // Gráfico 2 — Pie
    const labelsPie = <?= json_encode($labelsPie) ?>;
    const seriePie  = <?= json_encode($seriePie) ?>;

    if (labelsPie.length > 0) {
        new ApexCharts(document.getElementById('chartPie'), {
            chart: { type: 'pie', height: 280, background: 'transparent' },
            theme: { mode: theme },
            labels: labelsPie,
            series: seriePie,
            legend: { position: 'bottom' },
            tooltip: { y: { formatter: v => 'R$ ' + v.toLocaleString('pt-BR', {minimumFractionDigits: 2}) } },
        }).render();
    }

    // Gráfico 3 — Line
    const serieLine = <?= json_encode(array_values($serieLine)) ?>;

    new ApexCharts(document.getElementById('chartLine'), {
        chart: { type: 'line', height: 250, background: 'transparent', toolbar: { show: false } },
        theme: { mode: theme },
        series: [{ name: 'Saldo Acumulado', data: serieLine }],
        xaxis: { categories: labelsBar },
        stroke: { curve: 'smooth', width: 3 },
        colors: ['#0d6efd'],
        markers: { size: 5 },
        yaxis: { labels: { formatter: v => 'R$ ' + v.toLocaleString('pt-BR', {minimumFractionDigits: 2}) } },
        tooltip: { y: { formatter: v => 'R$ ' + v.toLocaleString('pt-BR', {minimumFractionDigits: 2}) } },
    }).render();
})();
</script>
