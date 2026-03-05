</div><!-- /.page-wrapper -->

<footer class="footer mt-auto py-3 bg-dark text-center text-white-50 small" data-bs-theme="dark">
    <div class="container">
        <?= APP_NAME ?> &copy; <?= date('Y') ?>
    </div>
</footer>

<!-- K) Modal de confirmação global -->
<div class="modal fade" id="modalConfirm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalConfirmTitulo">Confirmar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalConfirmTexto">Tem certeza?</div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="modalConfirmBtn" class="btn btn-danger">Confirmar</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
