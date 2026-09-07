<div class="modal fade" id="frmModalCreditIncobrable" tabindex="1" role="dialog" aria-labelledby="PagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width: 1000px; position: relative; right: 100px;">
            <div class="modal-header">
                <h4 class="modal-title">Letras del Credito</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-8">
                        <div class="card-body p-0">
                            <!--<div class="card-body p-0" style="height: 50vh; overflow: auto;">-->
                            <table id="credit_detalle_table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No. Cuota</th>
                                        <th>Fecha Vencimiento</th>
                                        <th>Capital</th>
                                        <th>Desgravamen</th>
                                        <th>Interés</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-4">
                        <form id="frmLiquidar">
                            <div class="row col col-lg-12">
                                <input type="hidden" id="carpeta">
                                <table class="table m-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" colspan="2">RESUMEN INCOBRABLE </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>CAPITAL </td>
                                            <td>
                                                <label id="capital"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>FONDO DE DESGRAVAMEN</td>
                                            <td>
                                                <label id="desgravament"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>INTERESES HASTA LA FECHA</td>
                                            <td>
                                                <label id="interes"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>VALOR A PAGAR</td>
                                            <td>
                                                <label id="total"></label>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="6">
                                                <div class="form-group">
                                                    <label for="exampleSelectBorder"> Motivo de Liquidacion </label>
                                                    <select class="custom-select form-control-border" id="incobrable" name="incobrable">
                                                        <option>INCOBRABILIDAD</option>
                                                        <option>DEFUNCION DEL ADEUDADO</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>    
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-dark btn-lg" data-bs-dismiss="modal"><i class="fas fa-times"></i> CANCELAR</button>
                    <button onclick="javascript:creditoIncobrableFinal();" class="btn btn-primary btn-lg"><i class="fas fa-money-bill-alt"></i> LIQUIDAR CRÉDITO</button>
                </div>
            </div>
        </div>
    </div>
</div> 