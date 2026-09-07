<div class="modal fade" id="formRecurrenciaPrestamo" tabindex="1" role="dialog" aria-labelledby="PagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tipo Prestamo.</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_recurrencia_prestamo" autocomplete="false" autocomplete="off" class="validate-form" enctype="multipart/form-data">
                    <input type="hidden" id="id_recu_pres" name="id_recu_pres" value="">
                    @csrf
                    <div class="row col col-sm-12">
                        <section class="col col-sm-4">
                            <div class="form-group">
                                <label for="name">Nombres</label>
                                <input type="text" class="form-control text-uppercase" id="name" name="name"   placeholder="Nombre" required="">
                            </div>
                        </section>
                        <section class="col col-sm-4">
                            <div class="form-group">
                                <label for="separacion">Recurrencia</label>
                                <input type="number" class="form-control text-uppercase" id="separacion" name="separacion"  placeholder="0.00" required="">
                            </div>
                        </section>
                        <section class="col col-sm-4">
                            <div class="form-group">
                                <label for="code">Tipo</label>
                                <select type="text" id="code" name="code" class="form-control" required>
                                    <option value="D" selected=""> DIA</option>
                                    <option value="S"> SEMANA </option>
                                    <option value="M"> MES </option>
                                </select>
                            </div>
                        </section>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <a type="button" class="btn btn-primary" href="javascript:guardarRecurrenciaPrestamo()" style="color: white" id="boton_generar">Generar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>