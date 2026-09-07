<div class="modal fade" id="frmModalCreditDetalles" tabindex="1" role="dialog" aria-labelledby="PagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width: 1185px; right: 194px;">
            <div class="modal-header">
                <h4 class="modal-title">Seleccione la cuota a Pagar</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-7 col-12">
                                <div class="card-body p-0" style="height: 100vh; overflow: auto;">
                                    <table id="credit_detalle_table" class="table table-striped" style="font-size: 13px;">
                                        <thead>
                                            <tr>
                                                <th scope="col">Pagar</th>  
                                                <th>No. Cuota</th>
                                                <th>Fecha Vencimiento</th>
                                                <th>Valor Cuota</th>
                                                <th>Valor Interes</th>
                                                <th>Total a Pagar</th>
                                                <th>Valor Pagado</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-5 col-12">

                                <form id="frmCuota">
                                    <div class="row col col-lg-12">
                                        <input type="hidden" id="id_detalle">
                                        <table class="table m-0">
                                            <tbody>
                                                <tr>
                                                    <td>NÚMERO DE CUOTA </td>
                                                    <td colspan="2">
                                                        <label id="cuota_pago"></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>FORMA DE PAGO </td>
                                                    <td colspan="2">
                                                        <div class="form-group">
                                                            <select class="custom-select rounded-0" id="tipo_pago" name="tipo_pago">
                                                                <option>EFECTIVO</option>
                                                                <option>TRANSFERENCIA</option>
                                                                <option>CHEQUES</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>FECHA DE PAGO</td>
                                                    <td colspan="2">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                            </div>
                                                            <input type="date" id="date_pago" name="date_pago" class="form-control" value="{{date('Y-m-d')}}" readonly="">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr >
                                                    <td>INTERES GENERADO</td>
                                                    <td colspan="2">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                                            </div>
                                                            <input type="text" id="interes_mora" name="interes_mora"class="form-control" placeholder="0.00" readonly="">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>VALOR A PAGAR</td>
                                                    <td colspan="2">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                                            </div>
                                                            <input type="text" id="valor_pago" name="valor_pago"class="form-control" placeholder="0.00">
                                                        </div>
                                                        <div class="input-group mb-3">
                                                            Se colocará automaticamente el valor correspondiente de la cuota normal
                                                            <div id="tableDesc">
                                                                
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>OBSERVACION</td>
                                                    <td colspan="2">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                                            </div>
                                                            <input type="text" id="observacion_pago" name="observacion_pago" class="form-control" placeholder="Observación">
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>







                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-dark btn-lg" data-bs-dismiss="modal">Cerrar</button>
                    <button onclick="javascript:pagarLetra();" class="btn btn-danger btn-lg"><i class="fas fa-money-bill-alt"></i> Pagar</button>
                </div>
            </div>
        </div>
    </div>
</div> 