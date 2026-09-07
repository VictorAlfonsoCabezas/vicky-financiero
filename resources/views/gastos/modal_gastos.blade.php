<div class="modal fade" id="frmModalGasto" tabindex="1" role="dialog" aria-labelledby="PagoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ingreso de Gastos</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_gasto" autocomplete="false" autocomplete="off" class="validate-form"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row col col-sm-12">
                        <section class="col col-sm-12">
                            <div class="form-group">
                                <label for="valor_gasto">Valor</label>
                                <input type="number" class="form-control text-uppercase" id="valor_gasto"
                                    name="valor_gasto" placeholder="Valor del Gasto" required="">
                            </div>
                        </section>

                        <section class="col col-sm-12">
                            <div class="form-group">
                                <label>Razón</label>
                                <textarea class="form-control" rows="3" id="razon_gasto" name="razon_gasto"
                                    placeholder="Razón del Gasto" required=""></textarea>
                            </div>
                        </section>

                        <section class="col col-sm-12">
                            <div class="col-5" style="margin-bottom: 10px;">
                                <label>Forma Pago</label>
                                <select class="form-control form-control-sm" name="forma_pago_id" id="forma_pago_id">
                                    <option value=""> Seleccione </option>
                                    @foreach ($formasPago as $fp)
                                        <option value="{{ $fp['id'] }}">{{ $fp['nombre'] }}</option>
                                    @endforeach

                                    
                                </select>
                            </div>
                        </section>

                        <section class="col col-sm-12">
                            @if($mostrarTransferencia)
                                <div class="row">

                                    <div class="col-6">
                                        <label>Banco</label>
                                        <select class="form-control form-control-sm" name="banco_id" id="banco_id">
                                            <option value="">Seleccione Banco</option>
                                            @foreach($bancos as $banco)
                                                <option value="{{ $banco->id }}">
                                                    {{ $banco->nombre }} - {{ $banco->numero_cuenta }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-6">
                                        <label># Comprobante</label>
                                        <input type="text" class="form-control form-control-sm"
                                            id="numero_comprobante" name="numero_comprobante" placeholder="Ingrese número de comprobante">
                                    </div>

                                </div>
                            @endif
                        </section>

                        <hr class="w-100">

                        <section class="col-sm-12">
                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold">Plan Cuentas</label>
                                    <select class="form-control form-control-sm" name="gastos_plan_cuentas_id"
                                        id="gastos_plan_cuentas_id">
                                        <option value="">Seleccione</option>
                                        @foreach ($planCuentas as $plan)
                                            <option value="{{ $plan['id'] }}">
                                                {{ $plan['nombre'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold">Centro Costos</label>
                                    <select class="form-control form-control-sm" name="gastos_centro_costos_id"
                                        id="gastos_centro_costos_id">
                                        <option value="">Seleccione</option>
                                        @foreach ($centroCostos as $centro)
                                            <option value="{{ $centro['id'] }}">
                                                {{ $centro['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </section>
                    </div>
                </form>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-dark btn-lg" data-bs-dismiss="modal">Cerrar</button>
                    <button onclick="javascript:guardarGasto();" class="btn btn-danger btn-lg"><i
                            class="fas fa-money-bill-alt"></i> Guardar</button>
                </div>
            </div>


        </div>
    </div>
</div>