<?php
$meses = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];
$resultado = $totais['entradas'] - $totais['saidas'];
$balancoTotal = $balanco['entradas'] - $balanco['saidas'];

$anoAtual = date('Y');
$anosLista = $anos;
if (!in_array($anoAtual, $anosLista)) {
    array_unshift($anosLista, $anoAtual);
}
?>

<main class="container-fluid py-4">

    <?php if ($sucesso): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= View::e($sucesso) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($erro): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i><?= View::e($erro) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Cabeçalho com seletor de mês/ano -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
        <div>
            <h2 class="h4 fw-bold mb-0">
                <?= $meses[$mes] ?> de <?= $ano ?>
            </h2>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Seletor de ano -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-calendar3 me-1"></i><?= $ano ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php foreach ($anosLista as $a): ?>
                    <li>
                        <a class="dropdown-item <?= $a == $ano ? 'active' : '' ?>"
                           href="<?= BASE_URL ?>/?mes=<?= $mes ?>&ano=<?= $a ?>">
                            <?= $a ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <!-- D) Exportar CSV -->
            <a href="<?= BASE_URL ?>/movimentos/exportar?mes=<?= $mes ?>&ano=<?= $ano ?>"
               class="btn btn-outline-success btn-sm">
                <i class="bi bi-download me-1"></i>Exportar CSV
            </a>
            <!-- E) Importar CSV -->
            <a href="<?= BASE_URL ?>/movimentos/importar?mes=<?= $mes ?>&ano=<?= $ano ?>"
               class="btn btn-outline-info btn-sm">
                <i class="bi bi-upload me-1"></i>Importar CSV
            </a>
            <!-- Botão adicionar movimento -->
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdicionar">
                <i class="bi bi-plus-lg me-1"></i>Novo Movimento
            </button>
            <!-- Botão gerenciar categorias -->
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCategorias">
                <i class="bi bi-tags me-1"></i>Categorias
            </button>
        </div>
    </div>

    <!-- Navegação por mês -->
    <div class="month-nav mb-4">
        <ul class="nav nav-pills flex-nowrap overflow-auto pb-1">
            <?php foreach ($meses as $n => $nome): ?>
            <li class="nav-item">
                <a class="nav-link <?= $n == $mes ? 'active' : '' ?>"
                   href="<?= BASE_URL ?>/?mes=<?= $n ?>&ano=<?= $ano ?>">
                    <?= substr($nome, 0, 3) ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Cards de resumo -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="summary-icon bg-success-subtle text-success me-3">
                            <i class="bi bi-arrow-up-circle"></i>
                        </div>
                        <span class="text-muted small">Entradas</span>
                    </div>
                    <p class="h5 fw-bold text-success mb-0">
                        R$ <?= number_format($totais['entradas'], 2, ',', '.') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="summary-icon bg-danger-subtle text-danger me-3">
                            <i class="bi bi-arrow-down-circle"></i>
                        </div>
                        <span class="text-muted small">Saídas</span>
                    </div>
                    <p class="h5 fw-bold text-danger mb-0">
                        R$ <?= number_format($totais['saidas'], 2, ',', '.') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="summary-icon <?= $resultado >= 0 ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning' ?> me-3">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <span class="text-muted small">Resultado do Mês</span>
                    </div>
                    <p class="h5 fw-bold <?= $resultado >= 0 ? 'text-primary' : 'text-warning' ?> mb-0">
                        R$ <?= number_format($resultado, 2, ',', '.') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="summary-icon <?= $balancoTotal >= 0 ? 'bg-info-subtle text-info' : 'bg-danger-subtle text-danger' ?> me-3">
                            <i class="bi bi-bank"></i>
                        </div>
                        <span class="text-muted small">Balanço Geral</span>
                    </div>
                    <p class="h5 fw-bold <?= $balancoTotal >= 0 ? 'text-info' : 'text-danger' ?> mb-0">
                        R$ <?= number_format($balancoTotal, 2, ',', '.') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de movimentos -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-list-ul me-2"></i>Movimentos — <?= $meses[$mes] ?>/<?= $ano ?>
                </h5>
                <!-- C) Filtros -->
                <?php if (!empty($movimentos)): ?>
                <div class="d-flex gap-2 flex-wrap" id="filtros">
                    <input type="search" id="filtroTexto" class="form-control form-control-sm"
                           placeholder="Buscar..." style="max-width: 180px;" oninput="filtrarTabela()">
                    <select id="filtroCategoria" class="form-select form-select-sm" style="max-width: 160px;" onchange="filtrarTabela()">
                        <option value="">Todas categorias</option>
                        <?php
                        $cats = array_unique(array_column($movimentos, 'categoria_nome'));
                        sort($cats);
                        foreach ($cats as $cn): if ($cn === null || $cn === '') continue; ?>
                        <option value="<?= View::e($cn) ?>"><?= View::e($cn) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="filtroTipo" class="form-select form-select-sm" style="max-width: 140px;" onchange="filtrarTabela()">
                        <option value="">Todos os tipos</option>
                        <option value="receita">Receita</option>
                        <option value="despesa">Despesa</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($movimentos)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                <p>Nenhum movimento neste mês.</p>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdicionar">
                    <i class="bi bi-plus-lg me-1"></i>Adicionar primeiro movimento
                </button>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelaMovimentos">
                    <thead class="table-light">
                        <tr>
                            <!-- M) Cabeçalhos ordenáveis -->
                            <th class="ps-3 sortable" data-sort="dia" data-col="0" style="cursor:pointer;">
                                Dia <i class="bi bi-chevron-expand text-muted small"></i>
                            </th>
                            <th class="sortable" data-sort="descricao" data-col="1" style="cursor:pointer;">
                                Descrição <i class="bi bi-chevron-expand text-muted small"></i>
                            </th>
                            <th>Categoria</th>
                            <th>Tipo</th>
                            <th class="text-end sortable" data-sort="valor" data-col="4" style="cursor:pointer;">
                                Valor <i class="bi bi-chevron-expand text-muted small"></i>
                            </th>
                            <th class="text-end pe-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($movimentos as $m): ?>
                        <tr data-tipo="<?= View::e($m['tipo']) ?>"
                            data-categoria="<?= View::e($m['categoria_nome'] ?? '') ?>">
                            <td class="ps-3 text-muted"><?= str_pad($m['dia'], 2, '0', STR_PAD_LEFT) ?></td>
                            <td class="fw-semibold">
                                <?= View::e($m['descricao']) ?>
                                <?php if ($m['recorrente']): ?>
                                <i class="bi bi-arrow-repeat text-muted ms-1" title="Recorrente"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($m['categoria_nome']): ?>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    <?= View::e($m['categoria_nome']) ?>
                                </span>
                                <?php else: ?>
                                <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($m['tipo'] === 'receita'): ?>
                                <span class="badge bg-success-subtle text-success">
                                    <i class="bi bi-arrow-up me-1"></i>Receita
                                </span>
                                <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger">
                                    <i class="bi bi-arrow-down me-1"></i>Despesa
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold <?= $m['tipo'] === 'receita' ? 'text-success' : 'text-danger' ?>">
                                R$ <?= number_format($m['valor'], 2, ',', '.') ?>
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-outline-secondary btn-sm me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditar"
                                    data-id="<?= $m['id'] ?>"
                                    data-descricao="<?= View::e($m['descricao']) ?>"
                                    data-valor="<?= number_format($m['valor'], 2, '.', '') ?>"
                                    data-tipo="<?= View::e($m['tipo']) ?>"
                                    data-categoria="<?= $m['categoria_id'] ?>"
                                    data-dia="<?= $m['dia'] ?>"
                                    data-mes="<?= $m['mes'] ?>"
                                    data-ano="<?= $m['ano'] ?>"
                                    data-recorrente="<?= $m['recorrente'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <!-- K) Usar modal de confirmação -->
                                <a href="#"
                                   class="btn btn-outline-danger btn-sm"
                                   onclick="confirmar('<?= BASE_URL ?>/movimentos/apagar/<?= $m['id'] ?>?mes=<?= $mes ?>&ano=<?= $ano ?>', 'Remover Movimento', 'Remover <strong><?= addslashes(View::e($m['descricao'])) ?></strong>? Esta ação não pode ser desfeita.')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

</main>

<!-- Modal: Adicionar Movimento -->
<div class="modal fade" id="modalAdicionar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>Novo Movimento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= BASE_URL ?>/movimentos/adicionar">
                    <input type="hidden" name="csrf_token" value="<?= View::e($token) ?>">
                    <input type="hidden" name="mes" value="<?= $mes ?>">
                    <input type="hidden" name="ano" value="<?= $ano ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descrição</label>
                        <input type="text" class="form-control" name="descricao" required maxlength="200" placeholder="Ex: Salário, Conta de luz...">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label fw-semibold">Valor (R$)</label>
                            <input type="number" class="form-control" name="valor" step="0.01" min="0.01" required placeholder="0,00">
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-semibold">Dia</label>
                            <input type="number" class="form-control" name="dia" min="1" max="31" value="<?= date('j') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" id="tipoReceita" value="receita" checked>
                                <label class="form-check-label text-success fw-semibold" for="tipoReceita">
                                    <i class="bi bi-arrow-up-circle me-1"></i>Receita
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" id="tipoDespesa" value="despesa">
                                <label class="form-check-label text-danger fw-semibold" for="tipoDespesa">
                                    <i class="bi bi-arrow-down-circle me-1"></i>Despesa
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Categoria <span class="text-muted fw-normal">(opcional)</span></label>
                        <select class="form-select" name="categoria_id">
                            <option value="0">— Sem categoria —</option>
                            <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= View::e($cat['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- A) Recorrente -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="recorrente" id="addRecorrente" value="1">
                            <label class="form-check-label" for="addRecorrente">
                                <i class="bi bi-arrow-repeat me-1"></i>Repetir mensalmente
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Editar Movimento -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2 text-warning"></i>Editar Movimento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?= BASE_URL ?>/movimentos/editar">
                    <input type="hidden" name="csrf_token" value="<?= View::e($token) ?>">
                    <input type="hidden" name="id" id="editId">
                    <input type="hidden" name="mes" id="editMes">
                    <input type="hidden" name="ano" id="editAno">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descrição</label>
                        <input type="text" class="form-control" name="descricao" id="editDescricao" required maxlength="200">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label fw-semibold">Valor (R$)</label>
                            <input type="number" class="form-control" name="valor" id="editValor" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-semibold">Dia</label>
                            <input type="number" class="form-control" name="dia" id="editDia" min="1" max="31" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" id="editTipoReceita" value="receita">
                                <label class="form-check-label text-success fw-semibold" for="editTipoReceita">
                                    <i class="bi bi-arrow-up-circle me-1"></i>Receita
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" id="editTipoDespesa" value="despesa">
                                <label class="form-check-label text-danger fw-semibold" for="editTipoDespesa">
                                    <i class="bi bi-arrow-down-circle me-1"></i>Despesa
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Categoria <span class="text-muted fw-normal">(opcional)</span></label>
                        <select class="form-select" name="categoria_id" id="editCategoria">
                            <option value="0">— Sem categoria —</option>
                            <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= View::e($cat['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- A) Recorrente -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="recorrente" id="editRecorrente" value="1">
                            <label class="form-check-label" for="editRecorrente">
                                <i class="bi bi-arrow-repeat me-1"></i>Repetir mensalmente
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i>Atualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Gerenciar Categorias -->
<div class="modal fade" id="modalCategorias" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-tags me-2 text-primary"></i>Gerenciar Categorias
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <!-- Adicionar categoria -->
                <form method="POST" action="<?= BASE_URL ?>/categorias/adicionar" class="mb-4">
                    <input type="hidden" name="csrf_token" value="<?= View::e($token) ?>">
                    <input type="hidden" name="mes" value="<?= $mes ?>">
                    <input type="hidden" name="ano" value="<?= $ano ?>">
                    <label class="form-label fw-semibold">Nova Categoria</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="nome" placeholder="Nome da categoria" required maxlength="100">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </form>

                <!-- Lista de categorias -->
                <?php if (empty($categorias)): ?>
                <p class="text-muted text-center">Nenhuma categoria cadastrada.</p>
                <?php else: ?>
                <ul class="list-group list-group-flush" id="categorias">
                    <?php foreach ($categorias as $cat): ?>
                    <li class="list-group-item px-0">
                        <div class="d-flex align-items-center gap-2" id="cat-view-<?= $cat['id'] ?>">
                            <span class="flex-grow-1"><?= View::e($cat['nome']) ?></span>
                            <button class="btn btn-outline-secondary btn-sm"
                                onclick="editarCategoria(<?= $cat['id'] ?>, '<?= View::e(addslashes($cat['nome'])) ?>')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="#"
                               class="btn btn-outline-danger btn-sm"
                               onclick="confirmar('<?= BASE_URL ?>/categorias/apagar/<?= $cat['id'] ?>?mes=<?= $mes ?>&ano=<?= $ano ?>', 'Remover Categoria', 'Remover a categoria <strong><?= addslashes(View::e($cat['nome'])) ?></strong>?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                        <form method="POST" action="<?= BASE_URL ?>/categorias/editar"
                              class="d-none mt-2" id="cat-edit-<?= $cat['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= View::e($token) ?>">
                            <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                            <input type="hidden" name="mes" value="<?= $mes ?>">
                            <input type="hidden" name="ano" value="<?= $ano ?>">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="nome"
                                       value="<?= View::e($cat['nome']) ?>" required>
                                <button class="btn btn-success" type="submit"><i class="bi bi-check"></i></button>
                                <button class="btn btn-light" type="button"
                                        onclick="cancelarEdicaoCategoria(<?= $cat['id'] ?>)">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </form>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
