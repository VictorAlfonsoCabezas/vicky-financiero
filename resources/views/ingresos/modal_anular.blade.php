<div class="modal fade" id="formAnulacion" tabindex="1" role="dialog" aria-labelledby="PagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
            </div>
            <div class="modal-body">
                <form  id="form_razon" method="POST" autocomplete="false" autocomplete="off" class="validate-form" enctype="multipart/form-data">
                    <input type="hidden" id="movimiento_id" name="movimiento_id" value="0">
                    <div class="row col col-sm-12">
                        <section class="col col-sm-12">
                            <div class="form-group">
                                <label>Razón de Anulación</label>
                                <textarea id="razon_ingreso" name="razon_ingreso" class="form-control text-uppercase" rows="3" placeholder="Descripción Completa" maxlength="1000"></textarea>
                            </div>
                        </section>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <a onclick="javascript:cabiarEstado();" class="btn btn-danger btn-lg" style="color: white;">
                            <i class="far fa-trash-alt"></i> ANULAR
                        </a>
                        <button type="button" class="btn btn-dark btn-lg" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>  
            </div>
        </div>
    </div>
</div> 