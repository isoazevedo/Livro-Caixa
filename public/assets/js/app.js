/**
 * Livro Caixa - Scripts
 */

document.addEventListener('DOMContentLoaded', function () {

    // Preenche o modal de edição de movimento com os dados do botão clicado
    const modalEditar = document.getElementById('modalEditar');
    if (modalEditar) {
        modalEditar.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;

            document.getElementById('editId').value        = btn.dataset.id;
            document.getElementById('editDescricao').value = btn.dataset.descricao;
            document.getElementById('editValor').value     = btn.dataset.valor;
            document.getElementById('editDia').value       = btn.dataset.dia;
            document.getElementById('editMes').value       = btn.dataset.mes;
            document.getElementById('editAno').value       = btn.dataset.ano;

            const tipo = btn.dataset.tipo;
            document.getElementById('editTipoReceita').checked = tipo === 'receita';
            document.getElementById('editTipoDespesa').checked = tipo === 'despesa';

            const selectCat = document.getElementById('editCategoria');
            if (selectCat) {
                selectCat.value = btn.dataset.categoria || '0';
            }

            // A) Recorrente
            const chkRec = document.getElementById('editRecorrente');
            if (chkRec) {
                chkRec.checked = btn.dataset.recorrente === '1';
            }
        });
    }

    // Auto-dismiss alerts after 4s
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 4000);
    });

    // L) Inicializar ícone do tema
    atualizarIconeTema();

    // M) Ordenação de tabela
    document.querySelectorAll('th.sortable').forEach(function (th) {
        th.addEventListener('click', function () {
            const tipo = this.dataset.sort;
            const col  = parseInt(this.dataset.col, 10);
            sortTable(col, tipo, this);
        });
    });
});

// ── C) Filtros ────────────────────────────────────────────────────────────────

/**
 * Filtra as linhas da tabela de movimentos com base em texto, categoria e tipo.
 */
function filtrarTabela() {
    const texto     = (document.getElementById('filtroTexto')?.value || '').toLowerCase();
    const categoria = (document.getElementById('filtroCategoria')?.value || '').toLowerCase();
    const tipo      = (document.getElementById('filtroTipo')?.value || '').toLowerCase();

    const tabela = document.getElementById('tabelaMovimentos');
    if (!tabela) return;

    tabela.querySelectorAll('tbody tr').forEach(function (row) {
        const rowTipo = (row.dataset.tipo || '').toLowerCase();
        const rowCat  = (row.dataset.categoria || '').toLowerCase();
        const rowText = row.textContent.toLowerCase();

        const matchText = !texto     || rowText.includes(texto);
        const matchCat  = !categoria || rowCat  === categoria;
        const matchTipo = !tipo      || rowTipo === tipo;

        row.style.display = (matchText && matchCat && matchTipo) ? '' : 'none';
    });
}

// ── K) Modal de confirmação ───────────────────────────────────────────────────

/**
 * Abre o modal de confirmação para ações destrutivas.
 * @param {string} url   - URL de destino ao confirmar
 * @param {string} titulo - Título do modal
 * @param {string} texto  - Corpo da mensagem (pode conter HTML)
 */
function confirmar(url, titulo, texto) {
    document.getElementById('modalConfirmTitulo').textContent = titulo;
    document.getElementById('modalConfirmTexto').innerHTML    = texto;
    document.getElementById('modalConfirmBtn').href           = url;
    new bootstrap.Modal(document.getElementById('modalConfirm')).show();
    return false;
}

// ── L) Dark mode ─────────────────────────────────────────────────────────────

/**
 * Alterna entre tema claro e escuro, salvando preferência no localStorage.
 */
function toggleDarkMode() {
    const html    = document.documentElement;
    const current = html.getAttribute('data-bs-theme') || 'light';
    const novo    = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', novo);
    localStorage.setItem('lcTheme', novo);
    atualizarIconeTema();
}

function atualizarIconeTema() {
    const icon  = document.getElementById('iconTheme');
    const theme = document.documentElement.getAttribute('data-bs-theme') || 'light';
    if (icon) {
        icon.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon';
    }
}

// ── M) Ordenação de tabela ────────────────────────────────────────────────────

var _sortDir = {};

/**
 * Ordena as linhas do tbody pelo índice de coluna fornecido.
 * @param {number} colIndex - Índice da coluna (0-based)
 * @param {string} tipo     - 'dia' | 'valor' para numérico, qualquer outro para string
 * @param {HTMLElement} th  - Cabeçalho clicado (para indicador visual)
 */
function sortTable(colIndex, tipo, th) {
    const tabela = document.getElementById('tabelaMovimentos');
    if (!tabela) return;

    const tbody = tabela.querySelector('tbody');
    const rows  = Array.from(tbody.querySelectorAll('tr'));

    const key   = colIndex + '_' + tipo;
    _sortDir[key] = _sortDir[key] === 'asc' ? 'desc' : 'asc';
    const dir   = _sortDir[key];

    rows.sort(function (a, b) {
        const cellA = a.cells[colIndex]?.textContent.trim() || '';
        const cellB = b.cells[colIndex]?.textContent.trim() || '';

        let cmp = 0;
        if (tipo === 'dia' || tipo === 'valor') {
            const numA = parseFloat(cellA.replace(/[^\d,.-]/g, '').replace(',', '.')) || 0;
            const numB = parseFloat(cellB.replace(/[^\d,.-]/g, '').replace(',', '.')) || 0;
            cmp = numA - numB;
        } else {
            cmp = cellA.localeCompare(cellB, 'pt-BR');
        }

        return dir === 'asc' ? cmp : -cmp;
    });

    rows.forEach(function (r) { tbody.appendChild(r); });

    // Atualizar ícones de ordenação
    document.querySelectorAll('th.sortable').forEach(function (h) {
        const icon = h.querySelector('i');
        if (!icon) return;
        icon.className = 'bi bi-chevron-expand text-muted small';
    });
    if (th) {
        const icon = th.querySelector('i');
        if (icon) {
            icon.className = dir === 'asc'
                ? 'bi bi-chevron-up text-primary small'
                : 'bi bi-chevron-down text-primary small';
        }
    }
}

// ── Categorias (existente) ────────────────────────────────────────────────────

/**
 * Exibe o formulário inline de edição de categoria.
 */
function editarCategoria(id, nome) {
    document.getElementById('cat-view-' + id).classList.add('d-none');
    const form = document.getElementById('cat-edit-' + id);
    form.classList.remove('d-none');
    form.querySelector('input[name="nome"]').focus();
}

/**
 * Cancela a edição inline de categoria.
 */
function cancelarEdicaoCategoria(id) {
    document.getElementById('cat-view-' + id).classList.remove('d-none');
    document.getElementById('cat-edit-' + id).classList.add('d-none');
}
