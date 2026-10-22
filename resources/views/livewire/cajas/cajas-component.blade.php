<div class="cajas-modulo">
    @include('livewire.cajas._listado')
    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" tabindex="-1" aria-labelledby="modalGeneral-title" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="modalGeneral-title"><i class="fa fa-cash-register me-2 text-primary" aria-hidden="true"></i>Apertura de caja</h5>
                        <p class="text-muted small mb-0 mt-1">Define el saldo inicial para comenzar la jornada.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form wire:submit.prevent="storeCaja">
                    <div class="modal-body cajas-formulario">
                        @if ($errors->any())
                        <div class="alert alert-warning">
                            <h5>Verifica estas observaciones.</h5>
                            <ul class="mb-0"> @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @if($this->cajasAbiertas)
                        <div class="alert alert-danger alert-dismissible">
                            <h5><i class="icon fas fa-info"></i> Observación!</h5>
                            {{$this->mensajeErrorCrearCajas}}
                        </div>
                        @endif
                        @if(!$primeraCaja)
                        <div class="alert alert-info alert-dismissible">
                            <h5><i class="icon fas fa-info"></i> Observación!</h5>
                            Esta es tu primera caja
                        </div>
                        @endif
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">Fecha</label>
                                <input readonly="" wire:change="cambioFecha();" type="date" class="form-control" placeholder="Fecha Caja" wire:model="fechaCaja" id="fechaCaja">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Bóveda de origen </label>
                                <select id="boveda" wire:model="boveda" class="form-select">
                                    <option value=""> --SELECCIONE--</option>
                                    @foreach ($bovedas as $bov)
                                    <option value="{{ $bov->id }}">{{ $bov->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mt-3">
                                <label class="form-label">Valor Inicial</label>
                                <input step="0.01" {{(Auth::user()->permiso_caja_valor  == false) ? 'readonly' :''}} type="number" class="form-control" placeholder="Ingrese el valor con el que inicia el caja" wire:model="valorInicial" id="valorInicial">
                                <small><i class="fa fa-key" tabindex="-1" aria-labelledby="modalGeneral-title" aria-hidden="true"></i> Para editar necesita permisos.</small>
                            </div>

                            <div class="col-12 mt-3">
                                <label class="form-label">Observación</label>
                                <textarea class="form-control" wire:model="observacion" rows="3" placeholder="Observación de la caja de : {{ $this->fechaCaja }}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Abrir caja</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL CIERRE CAJA --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral2" style="display: none;" tabindex="-1" aria-labelledby="modalGeneral2-title" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="modalGeneral2-title"><i class="fa fa-lock me-2 text-primary" aria-hidden="true"></i>Cierre y arqueo de caja</h5>
                        <p class="text-muted small mb-0 mt-1">Revisa los movimientos y cuenta el efectivo antes de confirmar.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form wire:submit.prevent="storeCierreCaja">
                    <div class="modal-body">
                        @if ($errors->any())
                        <div class="alert alert-warning">
                            <h5>Verifica estas observaciones.</h5>
                            <ul class="mb-0"> @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg">Fecha</span>
                            </p>
                            <p class="ms-auto d-flex flex-column text-end">
                                <span class="text-success">
                                    <i class="fas fa-calendar"></i> {{$this->fechaCajaCierre}}
                                </span>
                            </p>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <th>Boveda</th>
                                                <th>Valor</th>
                                                <th>Observación</th>
                                            </thead>
                                            <tbody>
                                                @php
                                                $total = 0;
                                                @endphp

                                                @foreach($this->bovedaAprobadas as $val)
                                                @php
                                                $total += $val->valor;
                                                @endphp
                                                <tr>
                                                    <td>{{$val->nombreBoveda}}</td>
                                                    <td><b>$ {{$val->valor}}</b></td>
                                                    <td>{{$val->observacion}}</td>
                                                </tr>
                                                @endforeach
                                            <tfoot>
                                                <tr>
                                                    <td style="text-align: right;">Total: </td>
                                                    <td>{{$total}} $</td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="row mb-4">
                            <div class="col-12">
                                <label>Detalle de transacciones</label>
                            </div>
                        </div>
                        <div id="accordion" class="accordion mb-4">
                            <div class="accordion-item">
                                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-depositos" aria-expanded="false" aria-controls="collapse-depositos">Depósitos a las cuentas</button></h2>
                                <div id="collapse-depositos" class="accordion-collapse collapse" data-bs-parent="#accordion" style="">
                                    <div class="card-body">
                                        <div class="card-body table-responsive p-0">
                                            <table class="table table-striped align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>TRANSACCION </th>
                                                        <th>DOCUMENTO</th>
                                                        <th>NOMBRE</th>
                                                        <th>OBSERVACION</th>
                                                        <th>VALOR</th>
                                                        <th>FORMA PAGO</th>
                                                        <th>STATUS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                    $total = 0;
                                                    @endphp
                                                    @foreach ($this->detalleDepositos as $det)
                                                    @php
                                                    $total += $det->valor_movimiento;
                                                    @endphp
                                                    <tr>
                                                        <td>DEPOSITOS</td>
                                                        <td>{{$det->comprobante}}</td>
                                                        <td>
                                                            {{ $det->customer_name}}<br>
                                                            <b>{{$det->customer_ruc}}</b>
                                                        </td>
                                                        <td>{{$det->observation}}</td>
                                                        <td>${{$det->valor_movimiento}}</td>
                                                        <td>{{$det->forma_pago}}</td>
                                                        <td>PROCESADO </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4"></td>
                                                        <td>$ {{number_format($total, 2)}}</td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="accordion" class="accordion mb-4">
                            <div class="accordion-item">
                                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-recaudaciones" aria-expanded="false" aria-controls="collapse-recaudaciones">Recaudaciones</button></h2>
                                <div id="collapse-recaudaciones" class="accordion-collapse collapse" data-bs-parent="#accordion" style="">
                                    <div class="card-body">
                                        <div class="card-body table-responsive p-0">
                                            <table class="table table-striped align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>TRANSACCION </th>
                                                        <th>DOCUMENTO</th>
                                                        <th>NOMBRE</th>
                                                        <th>OBSERVACION</th>
                                                        <th>VALOR</th>
                                                        <th>FORMA PAGO</th>
                                                        <th>STATUS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                    $total = 0;
                                                    @endphp
                                                    @foreach ($this->detalleRecaudaciones as $det)
                                                    @php
                                                    $total += $det->valor_movimiento;
                                                    @endphp
                                                    <tr>
                                                        <td>RECAUDACION </td>
                                                        <td>{{$det->comprobante}}</td>
                                                        <td>
                                                            {{ $det->customer_name}}<br>
                                                            <b>{{$det->customer_ruc}}</b>
                                                        </td>
                                                        <td>{{$det->observation}}</td>
                                                        <td>{{$det->valor_movimiento}}</td>
                                                        <td>{{$det->forma_pago}}</td>
                                                        <td>PROCESADO </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4"></td>
                                                        <td>$ {{number_format($total, 2)}}</td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="accordion" class="accordion mb-4">
                            <div class="accordion-item">
                                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-otrosValores" aria-expanded="false" aria-controls="collapse-otrosValores">Otros valores</button></h2>
                                <div id="collapse-otrosValores" class="accordion-collapse collapse" data-bs-parent="#accordion" style="">
                                    <div class="card-body">
                                        <div class="card-body table-responsive p-0">
                                            <table class="table table-striped align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>TRANSACCION </th>
                                                        <th>DOCUMENTO</th>
                                                        <th>RAZON</th>
                                                        <th>VALOR</th>
                                                        <th>FORMA PAGO</th>
                                                        <th>STATUS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                    $total = 0;
                                                    @endphp
                                                    @foreach ($this->detalleOtrosValores as $det)
                                                    @php
                                                    $total += $det->valor_movimiento;
                                                    @endphp
                                                    <tr>
                                                        <td>OTROS VALORES </td>
                                                        <td>{{$det->code}}</td>
                                                        <td>{{$det->razon}}</td>
                                                        <td>{{$det->valor_movimiento}}</td>
                                                        <td>{{$det->forma_pago}}</td>
                                                        <td>PROCESADO </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4"></td>
                                                        <td>$ {{number_format($total, 2)}}</td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="accordion" class="accordion mb-4">
                            <div class="card card-danger">
                                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-retiros" aria-expanded="false" aria-controls="collapse-retiros">Retiros</button></h2>
                                <div id="collapse-retiros" class="accordion-collapse collapse" data-bs-parent="#accordion" style="">
                                    <div class="card-body">
                                        <div class="card-body table-responsive p-0">
                                            <table class="table table-striped align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>TRANSACCION </th>
                                                        <th>DOCUMENTO</th>
                                                        <th>NOMBRE</th>
                                                        <th>OBSERVACION</th>
                                                        <th>VALOR</th>
                                                        <th>FORMA PAGO</th>
                                                        <th>STATUS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                    $total = 0;
                                                    @endphp
                                                    @foreach ($this->detalleRetiros as $det)
                                                    @php
                                                    $total += $det->valor_movimiento;
                                                    @endphp
                                                    <tr>
                                                        <td>RETIRO </td>
                                                        <td>{{$det->comprobante}}</td>
                                                        <td>
                                                            {{ $det->customer_name}}<br>
                                                            <b>{{$det->customer_ruc}}</b>
                                                        </td>
                                                        <td>{{$det->observation}}</td>
                                                        <td>{{$det->valor_movimiento}}</td>
                                                        <td>{{$det->forma_pago}}</td>
                                                        <td>PROCESADO </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4"></td>
                                                        <td>$ {{number_format($total, 2)}}</td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="accordion" class="accordion mb-4">
                            <div class="card card-danger">
                                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-gastos" aria-expanded="false" aria-controls="collapse-gastos">Gastos</button></h2>
                                <div id="collapse-gastos" class="accordion-collapse collapse" data-bs-parent="#accordion" style="">
                                    <div class="card-body">
                                        <h4><b>Caja chica</b></h4>
                                        <div class="card-body table-responsive p-0">
                                            <table class="table table-striped align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>TRANSACCION </th>
                                                        <th>CODIGO</th>
                                                        <th>NOMBRE</th>
                                                        <th>OBSERVACION</th>
                                                        <th style="width: 50px;">FECHA</th>
                                                        <th>VALOR</th>
                                                        <th>FORMA PAGO</th>
                                                        <th>STATUS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                    $total = 0;
                                                    @endphp
                                                    @foreach ($this->detalleGastos as $det)
                                                    @php
                                                    $total += $det->valor_movimiento;
                                                    @endphp
                                                    <tr>
                                                        <td>GASTOS</td>
                                                        <td>{{$det->code}}</td>
                                                        <td>
                                                            {!! $det->customer_name !!}<br>
                                                            <b>{{$det->customer_ruc}}</b>
                                                        </td>
                                                        <td>{{$det->observation}}</td>
                                                        <td>{{$det->date_created}} - {{ $det->hour_created}}</td>
                                                        <td>{{$det->valor_movimiento}}</td>
                                                        <td>{{$det->forma_pago}}</td>
                                                        <td>PROCESADO </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="5"></td>
                                                        <td>$ {{number_format($total, 2)}}</td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <hr>
                                        <h4><b>Pago Proveedores</b></h4>
                                        <hr>
                                        <div class="card-body table-responsive p-0">
                                            <table class="table table-striped align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>TRANSACCION </th>
                                                        <th>CODIGO</th>
                                                        <th>NOMBRE</th>
                                                        <th>OBSERVACION</th>
                                                        <th style="width: 50px;">FECHA</th>
                                                        <th>VALOR</th>
                                                        <th>FORMA PAGO</th>
                                                        <th>STATUS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                    $total = 0;
                                                    @endphp
                                                    @foreach ($this->detalleGastosProveedores as $det)
                                                    @php
                                                    $total += $det->valor_movimiento;
                                                    @endphp
                                                    <tr>
                                                        <td>GASTOS</td>
                                                        <td>{{$det->code}}</td>
                                                        <td>
                                                            {!! $det->customer_name !!}<br>
                                                            <b>{{$det->customer_ruc}}</b>
                                                        </td>
                                                        <td>{{$det->observation}}</td>
                                                        <td>{{$det->date_created}} - {{ $det->hour_created}}</td>
                                                        <td>{{$det->valor_movimiento}}</td>
                                                        <td>{{$det->forma_pago}}</td>
                                                        <td>PROCESADO </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="5"></td>
                                                        <td>$ {{number_format($total, 2)}}</td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12">
                                <label>Arqueo de Caja</label>
                                <br>
                                <a href="/denominacion-billetes" target="_blank" style="font-size: 12px;">Crear Denominaciones</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <td>Lista</td>
                                                    <td>Valor</td>
                                                    <td>Cantidad</td>
                                                    <td>Total</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($denominaciones as $deno)
                                                <tr>
                                                    <td style="font-size: 14px;"><b>{{ $deno['nombre'] }}</b></td>
                                                    <td class="text-center">{{ $deno['valor'] }}</td>
                                                    <td style="width: 4%;"><input wire:model="denominaciones.{{ $loop->index }}.cantidad" wire:change="cambiarValor({{ $deno['id'] }}, $event.target.value)" type="number" class="form-control form-control-sm" placeholder="cantidad" value="{{ $deno['cantidad'] }}" style="min-width: 90px; text-align: right;"></td>
                                                    <td style="width: 4%;"><input wire:model="denominaciones.{{ $loop->index }}.total" type="number" class="form-control form-control-sm" placeholder="total" value="{{ $deno['total'] }}" style="min-width: 90px; text-align: right;" readonly></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <table class="table table-bordered table-sm mt-3">
                                            <tbody>
                                                <tr class="table-active">
                                                    <td style="font-size: 14px;">
                                                        <span class="text-success">
                                                            @if ($this->signo == 'mayor')
                                                            <i class="fas fa-arrow-up"></i>
                                                            @elseif($this->signo == 'menor')
                                                            <i class="fas fa-arrow-down"></i>
                                                            @endif
                                                        </span>
                                                        <b>Efectivo en caja</b>
                                                    </td>
                                                    <td style="width: 40%;">
                                                        <input class="form-control form-control-sm" type="text" wire:model="totalManual" inputmode="decimal" placeholder="Valor Físico" wire:keyup="cambioValorManual()" style="text-align: center;">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        @include('livewire.cajas._arqueo-resumen')

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label mt-3">Observación del cierre</label>
                            <textarea class="form-control" rows="3" placeholder="Observación de la caja de : {{ $this->fechaCaja }}" wire:model="observacionCierre"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Confirmar cierre</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL SOLICITAR VALOR BOVEDA--}}
    <div wire:ignore.self class="modal fade" id="modalGeneral3" style="display: none;" tabindex="-1" aria-labelledby="modalGeneral3-title" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="modalGeneral3-title"><i class="fa fa-hand-holding-usd me-2 text-primary" aria-hidden="true"></i>Solicitar valores a bóveda</h5>
                        <p class="text-muted small mb-0 mt-1">Selecciona el origen e indica el importe que necesitas recibir.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form wire:submit.prevent="storesolicitudValores">
                    <div class="modal-body cajas-formulario">
                        @if ($errors->any())
                        <div class="alert alert-warning">
                            <h5>Verifica estas observaciones.</h5>
                            <ul class="mb-0"> @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @if($this->cajasAbiertas)
                        <div class="alert alert-danger alert-dismissible">
                            <h5><i class="icon fas fa-info"></i> Observación!</h5>
                            {{$this->mensajeErrorCrearCajas}}
                        </div>
                        @endif
                        <div class="col-12">
                            <label class="form-label">Bóveda de origen </label>
                            <select id="bovedaSolicitar" wire:model="bovedaSolicitar" class="form-select">
                                <option value=""> --SELECCIONE--</option>
                                @foreach ($bovedas as $bov)
                                <option value="{{ $bov->id }}">{{ $bov->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label">Importe a solicitar</label>
                            <input step="0.01" type="number" class="form-control" placeholder="Ingrese el valor a solicitar" wire:model="valorInicialSolicitar" id="valorInicialSolicitar">
                            <small><i class="fa fa-key" tabindex="-1" aria-labelledby="modalGeneral3-title" aria-hidden="true"></i> .</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Seleccione el Banco</label>
                            <select class="form-select" wire:model="bancoSolicita">
                                <option> -Selecciones uno- </option>
                                @foreach ($bancos as $banco)
                                <option value="{{ $banco['id'] }}">{{ $banco['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Observación </label>
                            <textarea class="form-control" rows="3" placeholder="Observación " wire:model="observacionSolicitar"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Enviar solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- MODAL LISTA DE VALORES POR APROBAR--}}
    <div wire:ignore.self class="modal fade" id="modalGeneral4" style="display: none;" tabindex="-1" aria-labelledby="modalGeneral4-title" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="modalGeneral4-title"><i class="fa fa-clock me-2 text-primary" aria-hidden="true"></i>Valores pendientes</h5>
                        <p class="text-muted small mb-0 mt-1">Revisa las solicitudes antes de aprobar o rechazar los valores.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form wire:submit.prevent="storesolicitudValores">
                    <div class="modal-body">
                        @if ($errors->any())
                        <div class="alert alert-warning">
                            <h5>Verifica estas observaciones.</h5>
                            <ul class="mb-0"> @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Observación</th>
                                        <th>Valor</th>
                                        <th>Acciones</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($this->bovedadPendientesApro as $solicitudes)
                                    <tr>
                                        <td class="small">
                                            {{ $solicitudes->observacion}}
                                        </td>
                                        <td class="small">
                                            {{ $solicitudes->valor }}
                                        </td>
                                        <td class="small">
                                            <button type="button" class="btn btn-success btn-sm" wire:loading.attr="disabled" title="Aprobar" wire:click="aprobarSoliVal({{ $solicitudes->id }})">
                                                <i class="far fa-money-bill-alt"></i> aprobar
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" wire:loading.attr="disabled" title="Rechazar" wire:click="rechazarSoliVal({{ $solicitudes->id }})">
                                                <i class="far fa-money-bill-alt"></i> rechazar
                                            </button>
                                        </td>

                                    </tr>
                                    @empty <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No hay valores pendientes por revisar.</td>
                                    </tr> @endforelse

                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL ENVIAR MATRIZ--}}
    <div wire:ignore.self class="modal fade" id="modalGeneral5" style="display: none;" tabindex="-1" aria-labelledby="modalGeneral5-title" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="modalGeneral5-title"><i class="fa fa-paper-plane me-2 text-primary" aria-hidden="true"></i>Enviar valores a la bóveda matriz</h5>
                        <p class="text-muted small mb-0 mt-1">Registra la cuenta y el importe que vas a enviar a la matriz.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form wire:submit.prevent="storeEnvioBoveda">
                    <div class="modal-body cajas-formulario">
                        @if ($errors->any())
                        <div class="alert alert-warning">
                            <h5>Verifica estas observaciones.</h5>
                            <ul class="mb-0"> @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="col-12">
                            <label class="form-label">Seleccionar Banco </label>
                            <select id="bancoEnvioMatriz" wire:model="bancoEnvioMatriz" class="form-select">
                                <option value=""> --SELECCIONE--</option>
                                @foreach ($bancos as $ban)
                                <option value="{{ $ban->id }}">{{ $ban->nombre }} #{{(empty($ban->numero_cuenta) ? '' : $ban->numero_cuenta)}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label">Valor</label>
                            <input step="0.01" type="number" class="form-control" placeholder="Ingrese el valor a transferir" wire:model="valorEnvioMatriz" id="valorEnvioMatriz">
                            <small><i class="fa fa-key" tabindex="-1" aria-labelledby="modalGeneral5-title" aria-hidden="true"></i> .</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Observación </label>
                            <textarea class="form-control" rows="3" placeholder="Observación " wire:model="observacionEnvioMatriz"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Enviar valores</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>