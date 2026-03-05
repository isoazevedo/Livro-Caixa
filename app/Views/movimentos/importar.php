<?php
$mesesNomes = [
    1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',
    5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',
    9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'
];
?>
<main class="container py-4" style="max-width: 800px;">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="h4 fw-bold mb-0">
            <i class="bi bi-file-earmark-arrow-up me-2"></i>Importar CSV
        </h2>
        <a href="<?= BASE_URL ?>/?mes=<?= $mes ?>&ano=<?= $ano ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent fw-semibold">
            <i class="bi bi-upload me-2"></i>Upload do Arquivo
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">
                O CSV deve ter as colunas: <code>dia, descricao, valor, tipo, categoria</code><br>
                Tipos aceitos: <code>receita</code>, <code>despesa</code> (ou <code>r</code>/<code>d</code>).<br>
                Os movimentos serão importados para <strong><?= $mesesNomes[$mes] ?>/<?= $ano ?></strong>.
            </p>

            <form method="POST" action="<?= BASE_URL ?>/movimentos/importar" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= View::e($token) ?>">
                <input type="hidden" name="mes" value="<?= $mes ?>">
                <input type="hidden" name="ano" value="<?= $ano ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Arquivo CSV</label>
                    <input type="file" class="form-control" name="arquivo" accept=".csv,text/csv" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>Fazer Preview
                </button>
            </form>
        </div>
    </div>

    <?php if ($preview !== null): ?>

        <?php if (!empty($preview['erros'])): ?>
        <div class="alert alert-warning">
            <h6 class="fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Avisos / Erros de Parsing</h6>
            <ul class="mb-0 small">
                <?php foreach ($preview['erros'] as $e): ?>
                <li><?= View::e($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (!empty($preview['linhas'])): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent fw-semibold d-flex align-items-center justify-content-between">
                <span><i class="bi bi-table me-2"></i>Preview — <?= count($preview['linhas']) ?> registro(s)</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Dia</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Tipo</th>
                            <th>Categoria</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($preview['linhas'] as $l): ?>
                    <tr>
                        <td class="ps-3"><?= str_pad($l['dia'], 2, '0', STR_PAD_LEFT) ?></td>
                        <td><?= View::e($l['descricao']) ?></td>
                        <td class="<?= $l['tipo'] === 'receita' ? 'text-success' : 'text-danger' ?>">
                            R$ <?= number_format($l['valor'], 2, ',', '.') ?>
                        </td>
                        <td>
                            <?php if ($l['tipo'] === 'receita'): ?>
                            <span class="badge bg-success-subtle text-success">Receita</span>
                            <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger">Despesa</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small">
                            <?php if ($l['categoria_id']): ?>
                            <?= View::e($l['categoria_nome']) ?>
                            <?php elseif (!empty($l['categoria_nome'])): ?>
                            <span class="text-warning" title="Categoria não encontrada">
                                <?= View::e($l['categoria_nome']) ?> <i class="bi bi-question-circle"></i>
                            </span>
                            <?php else: ?>
                            —
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-transparent">
                <form method="POST" action="<?= BASE_URL ?>/movimentos/importar/confirmar">
                    <input type="hidden" name="csrf_token" value="<?= View::e($token) ?>">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Confirmar Importação
                    </button>
                    <a href="<?= BASE_URL ?>/movimentos/importar?mes=<?= $mes ?>&ano=<?= $ano ?>"
                       class="btn btn-light ms-2">Cancelar</a>
                </form>
            </div>
        </div>
        <?php elseif (empty($preview['erros'])): ?>
        <div class="alert alert-info">Nenhum registro válido encontrado no arquivo.</div>
        <?php endif; ?>

    <?php endif; ?>

</main>
