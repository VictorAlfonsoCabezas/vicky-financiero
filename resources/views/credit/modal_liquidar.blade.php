<div class="modal fade" id="frmModalCreditLiquidar" tabindex="1" role="dialog" aria-labelledby="PagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="width: 1000px; position: relative; right: 100px;">
            <div class="modal-header">
                <h4 class="modal-title">Seleccione la cuota a Pagar</h4>
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
                                <input type="hidden" id="carpeta_liquidar" value="0">
                                <table class="table m-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" colspan="2">DETALLE DE LIQUDACION </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>CAPITAL </td>
                                            <td>
                                                <label id="capital_liquidar"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>FONDO DE DESGRAVAMEN</td>
                                            <td>
                                                <label id="desgravament_liquidar"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>INTERESES HASTA LA FECHA</td>
                                            <td>
                                                <label id="interes_liquidar"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>VALOR A PAGAR</td>
                                            <td>
                                                <label id="total_liquidar"></label>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>    
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-dark btn-lg" data-bs-dismiss="modal">CANCELAR</button>
                    <button onclick="javascript:liquitarCreditoFinal();" class="btn btn-danger btn-lg"><i class="fas fa-money-bill-alt"></i> LIQUIDAR CRÉDITO</button>
                </div>
            </div>
        </div>
    </div>
</div> 