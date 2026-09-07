<div class="modal fade" id="modal_cartilla" tabindex="1" role="dialog" aria-labelledby="PagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cartilla del Cliente: <b id="customer_name"></b></h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="header content-header">
                    <center>
                        <h4>CAJA DE AHORRO SAN PABLITO</h4>
                        <h4><b>SEMBRANDO EL FUTURO</b></h4>
                    </center>
                    <table>
                        <tr>
                            <td>CUENTA</td>
                            <td><b id="customer_code"></b></td>
                        </tr>
                        <tr>
                            <td>CÉDULA</td>
                            <td><b id="customer_ruc">001</b></td>
                        </tr>
                        <tr>
                            <td>TITULAR</td>
                            <td><b id="nombre_titular">001</b></td>
                        </tr>
                        <tr>
                            <td>CARTOLA</td>
                            <td><b id="cartola_code">001</b></td>
                        </tr>
                        <tr>
                            <td><b id="imprimir_cartola"></b></td>
                        </tr>
                    </table>
                </div>
                <div class="card-body p-0">
                    <table id="catola_detail" class="table table-striped">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>FECHA</th>
                                <th>DEPOSITO</th>
                                <th>INTERES</th>
                                <th>RETIRO</th>
                                <th style="width: 40px">SALDO</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>