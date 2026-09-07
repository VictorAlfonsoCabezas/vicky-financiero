<div class="modal fade" id="formBuscarCustomer" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="far fa-user"></i> Buscador de Clientes</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <form class="smart-form">
                    <section>
                        <input type="hidden" id="llamado" name="llamado" value="0">
                        <input type="hidden" id="tipoModalCustomer" name="tipoModalCustomer" value="phone">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search text-muted me-2"></i></span>
                            </div>
                            <input type="text" name="buscar_cliente_modal" id="buscar_cliente_modal" class="custom-select form-control"  placeholder="Información del cliente">
                        </div>
                        <hr>
                    </section>
                </form>
                <table class="table table-bordered table-warning table-sm" id="tabla_buscar_clientes_modal">
                    <thead>
                        <tr>
                            <th>Nro. Documento</th>
                            <th>Razon Social</th>
                            <th>Dirección</th>
                            <th>Elegir</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
