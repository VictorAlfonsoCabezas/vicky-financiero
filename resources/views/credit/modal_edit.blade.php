<div class="modal fade" id="modalEditCredito" tabindex="1" role="dialog" aria-labelledby="PagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Credito</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editarCredito">
                    <input type="hidden" id="numeroCredito" name="numeroCredito" value="0">
                    <div class="row col col-sm-12">
                        <section class="col col-sm-6">
                            <div class="form-group">
                                <label for="valor_prestamo">Valor</label>
                                <input type="number" class="form-control text-uppercase" id="valor_prestamo" name="valor_prestamo"  step="0.01" placeholder="0.00" required="">
                            </div>
                        </section>
                        <section class="col col-sm-6">
                            <div class="form-group">
                                <label for="numero_cuotas">Cuotas</label>
                                <input type="number" class="form-control text-uppercase" id="numero_cuotas" name="numero_cuotas"  step="1" placeholder="Cuotas" required="">
                               <small class="badge bg-danger"><i class="far fa-clock"></i>Número de meses</small>
                            </div>
                        </section>
                    </div>


                </form>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-dark btn-lg" data-bs-dismiss="modal">Cerrar</button>
                    <button onclick="javascript:guardarEditCredito();" class="btn btn-danger btn-lg"><i class="fas fa-money-bill-alt"></i> Editar</button>
                </div>
            </div>

        </div>
    </div>
</div> 
