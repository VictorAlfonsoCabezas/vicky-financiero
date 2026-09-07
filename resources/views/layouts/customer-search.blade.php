<div class="modal fade" id="customer-search-modal" tabindex="-1" aria-labelledby="customer-search-title" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customer-search-title">Buscar clientes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p id="customer-search-status" role="status" aria-live="polite" class="mb-3"></p>
                <div class="table-responsive" id="customer-search-results" hidden>
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead><tr><th scope="col">Código</th><th scope="col">Cliente</th><th scope="col">Identificación</th><th scope="col">Accesos</th></tr></thead>
                        <tbody id="customer-search-rows"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <nav class="d-flex align-items-center gap-2 me-auto" aria-label="Páginas de clientes" id="customer-search-pagination" hidden>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="customer-search-prev">Anterior</button>
                    <span id="customer-search-page" class="small"></span>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="customer-search-next">Siguiente</button>
                </nav>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
