<div class="modal fade" id="frmModalEncaje" tabindex="1" role="dialog" aria-labelledby="PagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">¿Existe retención porcentual sobre el crédito?</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body center-block">
                <input type="hidden" id="prestamo_id" name="prestamo_id" value="0">
                <input type="hidden" id="porcentDesgra" name="porcentDesgra" value="">
                <center>
                    <button onclick="javascript:entregarDineroEncaje('1');" class="btn btn-success btn-lg"><i class="fas fa-money-bill-alt"></i> SI</button>
                    <button onclick="javascript:entregarDineroEncaje('0');" class="btn btn-danger btn-lg"><i class="fas fa-money-bill-alt"></i> NO</button>
                </center>
            </div>
        </div>
    </div>
</div> 
