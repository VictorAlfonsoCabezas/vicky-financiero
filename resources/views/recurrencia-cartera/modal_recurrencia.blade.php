<div class="modal fade" id="formRecurrenciaNew" tabindex="1" role="dialog" aria-labelledby="PagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Recurrencia</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_prestamo_new" autocomplete="false" autocomplete="off" class="validate-form" enctype="multipart/form-data">
                    @csrf
                    <div class="row col col-sm-12">
                        <section class="col col-sm-4">
                            <div class="form-group">
                                <label for="desde">Desde</label>
                                <input type="number" class="form-control text-uppercase" id="desde" name="desde"   placeholder="Ingrese el inicio" required="">
                            </div>
                        </section>
                        <section class="col col-sm-4">
                            <div class="form-group">
                                <label for="hasta">Hasta</label>
                                <input type="number" class="form-control text-uppercase" id="hasta" name="hasta"  placeholder="Ingrese el Fin" required="">
                            </div>
                        </section>
                        <section class="col col-sm-4">
                            <div class="form-group">
                                <label for="orden">Orden</label>
                                <input type="number" class="form-control text-uppercase" id="orden" name="orden"  placeholder="Orden" required="">
                            </div>
                        </section>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <a type="button" class="btn btn-primary" href="javascript:guardarNuewRecurrencia()" style="color: white" id="boton_generar">Generar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>