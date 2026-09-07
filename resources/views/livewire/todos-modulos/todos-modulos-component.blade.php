<div>
    <div class="row mt-2">
        <div class="col-md-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Filtros</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <label>Fecha Inicio</label>
                            <input type="date" class="form-control form-control-sm" placeholder="Fecha Inicio"
                                wire:model="fechaInicio">
                        </div>
                        <div class="col-3">
                            <label>Fecha Fin</label>
                            <input type="date" class="form-control form-control-sm" placeholder="Fecha Fin"
                                wire:model="fechaFin">
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label>Módulos</label>
                                <select class="form-control form-control-sm" wire:model="modulo_id">
                                    <option value="0">-Seleccione-</option>
                                    @foreach ($tipoTransacciones as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-3 mt-4">
                            <a class="btn btn-primary text-white" wire:click="calcularTodosAsientos();"> Calcular
                                Asientos</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Módulos</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Fecha Creación</th>
                                <th>Identificacion</th>
                                <th>Nombres</th>
                                <th>Transacción</th>
                                <th>Valor</th>
                                <th class="text-center">Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($modulos as $mod)
                                <tr>
                                    <td>{{ $mod->id }}</td>
                                    <td>
                                        {{ $mod->date_created }}<br>
                                        <small>{{ $mod->hour_created }}</small>
                                    </td>
                                    <td>{{ $mod->customer_ruc }}</td>
                                    <td>{{ $mod->customer_name }}</td>
                                    <td>{{ $mod->type_transaction_name }}</td>
                                    <td>{{ $mod->valor_movimiento }}</td>
                                    <td class="text-center">
                                        @if ($mod->asiento_header_id !== null)
                                            <a class="btn btn-{{ $mod->suma_debe == $mod->suma_haber ? 'success' : 'primary' }} btn-sm text-white"
                                                wire:click="abrirModal({{ $mod->asiento_header_id }})"
                                                data-bs-toggle="modal" data-bs-target="#modalGeneral"><i
                                                    class="fa fa-chair"></i></a>
                                        @else
                                            <a class="btn btn-default btn-sm"
                                                wire:click="abrirModalManual({{ $mod->id }})"
                                                data-bs-toggle="modal" data-bs-target="#modalManualAsiento"
                                                title="Crear asiento manual"><i class="fa fa-chair"></i></a>
                                        @endif
                                        @if (empty($mod->id))
                                        <a class="btn btn-default btn-sm"
                                            wire:click="recalcularAsientoGeneral({{ $mod->asiento_header_id }}, {{ $modulo_id }})">
                                            <i class="fa fa-play"></i>
                                        </a>
                                        @else
                                        <a class="btn btn-default btn-sm"
                                            wire:click="recalcularAsiento({{ $mod->id }})">
                                            <i class="fa fa-play"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="6">No hay</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $modulos->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CREAR ASIENTO MANUAL --}}
    <div wire:ignore.self class="modal fade" id="modalManualAsiento" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title">Crear asiento manual</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">Ã—</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeManual">
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="callout callout-warning">
                                <h5>Verifica estas observaciones.</h5>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-3">
                                <label>Movimiento</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $movimiento_manual_id }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label>Fecha Contable</label>
                                <input type="datetime-local" class="form-control form-control-sm" wire:model="fecha_manual">
                            </div>
                            <div class="col-md-6">
                                <label>Concepto</label>
                                <select class="form-control form-control-sm" wire:model="concepto_id">
                                    <option value="">- Seleccione -</option>
                                    @foreach ($tipoConceptos as $tipoConcepto)
                                        <optgroup label="{{ $tipoConcepto->nombre }}">
                                            @foreach ($conceptos->where('tipo_concepto_id', $tipoConcepto->id) as $concepto)
                                                <option value="{{ $concepto->id }}">{{ $concepto->nombre }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mt-2">
                                <label>Descripcion</label>
                                <textarea class="form-control form-control-sm" rows="2" wire:model="descripcion"></textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group position-relative">
                                    <label>Agregar cuenta</label>
                                    <input type="search" wire:model="search" class="form-control form-control-sm"
                                        placeholder="Buscar por codigo o nombre">
                                    @if(!empty($search))
                                        <div class="card card-widget widget-user-2 position-absolute w-100 bg-white shadow"
                                            style="z-index: 1000; top: 100%; left: 0; right: 0; max-height: 260px; overflow-y: auto; border: 1px solid #ddd;">
                                            <div class="card-footer p-0">
                                                <ul class="nav flex-column nav-pills">
                                                    @forelse ($planCuentasBuscador as $plan)
                                                        <li class="nav-item">
                                                            <a wire:click="agregarCuenta({{ $plan->id }})" class="nav-link">
                                                                {{ $plan->nombre }}
                                                                <span class="float-end badge bg-primary">{{ $plan->codigo }}</span>
                                                            </a>
                                                        </li>
                                                    @empty
                                                        <li class="nav-item p-2">No hay resultados</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12 table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Cuenta</th>
                                            <th>Opcion</th>
                                            <th>Centro Costos</th>
                                            <th>Debe</th>
                                            <th>Haber</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($asientos as $key => $asi)
                                            <tr>
                                                <td><b>{{ $asi['cuenta_codigo'] }}</b> {{ $asi['cuenta_nombre'] }}</td>
                                                <td>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="manualDebeHaber{{ $key }}"
                                                            wire:click="actualizarDebHaber({{ $key }})"
                                                            {{ $asi['debe_haber'] ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="manualDebeHaber{{ $key }}">
                                                            {{ $asi['debe_haber'] ? 'Debe' : 'Haber' }}
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <select class="form-control form-control-sm"
                                                        wire:change="actualizarCentro({{ $key }}, $event.target.value)">
                                                        <option value="0">Ninguno</option>
                                                        @foreach ($sedes as $se)
                                                            <optgroup label="{{ $se->name }}">
                                                                @foreach ($centro_costos->where('sede_id', $se->id) as $sedeCentro)
                                                                    <option value="{{ $sedeCentro->id }}" {{ $asi['sedes_centro_costos'] == $sedeCentro->id ? 'selected' : '' }}>
                                                                        {{ optional($sedeCentro->sede)->name ?? 'Sin sede' }} -
                                                                        {{ optional($sedeCentro->centroCostos)->name ?? 'Sin centro de costos' }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td style="width: 130px;">
                                                    <input class="form-control form-control-sm text-end" type="number"
                                                        step="0.01" value="{{ $asi['debe'] }}"
                                                        wire:change="actualizarValores({{ $key }}, $event.target.value)"
                                                        {{ $asi['debe_haber'] ? '' : 'disabled' }}>
                                                </td>
                                                <td style="width: 130px;">
                                                    <input class="form-control form-control-sm text-end" type="number"
                                                        step="0.01" value="{{ $asi['haber'] }}"
                                                        wire:change="actualizarValores({{ $key }}, $event.target.value)"
                                                        {{ $asi['debe_haber'] ? 'disabled' : '' }}>
                                                </td>
                                                <td class="text-center">
                                                    <a class="text-danger" wire:click="elminarCuenta({{ $key }})">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-center" colspan="6">Sin cuentas</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL ASIENTOS --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title"> ASIENTOS </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="callout callout-warning">
                                <h5>Verifica estas observaciones.</h5>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </div>
                        @endif
                        <div class="row mt-4">
                            <table class="table table-sm" style="border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th>Asiento</th>
                                        <th>Fecha Contable</th>
                                        <th>CC</th>
                                        <th>Código</th>
                                        <th>Cuenta</th>
                                        <th>Debe</th>
                                        <th>Haber</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $header = 0 @endphp
                                    @forelse ($detalle as $key => $det)
                                        @if ($header !== $det->header_id)
                                            <tr class="bg-danger text-white">
                                                <td colspan="7">
                                                    <b>
                                                        {{ $asientosHeader->concepto->nombre }}
                                                        <i>({{ $asientosHeader->concepto->tipoConcepto->nombre }})</i>
                                                    </b>
                                                </td>
                                                <td style="border: 1px solid #dee2e6; text-align: center;">
                                                    <a class="text-white"
                                                        href="/asientos/comprobante/{{ $det->asientos_header_id }}"> <i
                                                            class="fa fa-print"></i></a>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td style="border: 1px solid #dee2e6;" class="align-middle text-start">
                                                <b>{{ $det->asientos_header_id }}</b>
                                            </td>
                                            <td style="border: 1px solid #dee2e6;" class="text-center">
                                                {{ $det->fecha_contable }}
                                            </td>
                                            <td style="border: 1px solid #dee2e6;" class="text-center">
                                                <b>{{ $det->sedes_nombre }}</b> - {{ $det->centro_costos_nombre }}
                                            </td>
                                            <td style="border: 1px solid #dee2e6;" class="text-start">
                                                <b>{{ $det->plan_codigo }}</b>
                                            </td>
                                            <td style="border: 1px solid #dee2e6;" class="text-start">
                                                {{ $det->plan_nombre }}
                                            </td>
                                            @if ($det->debe_haber)
                                                <td style="border: 1px solid #dee2e6;" class="align-middle text-center">
                                                    <b>$ {{ $det->valor }}</b>
                                                </td>
                                                <td style="border: 1px solid #dee2e6;" class="align-middle text-center">
                                                </td>
                                            @else
                                                <td style="border: 1px solid #dee2e6;" class="align-middle text-center">
                                                </td>
                                                <td style="border: 1px solid #dee2e6;" class="align-middle text-center">
                                                    <b>$ {{ $det->valor }}</b>
                                                </td>
                                            @endif
                                            <td style="border: 1px solid #dee2e6;">

                                            </td>
                                        </tr>
                                        @php $header = $det->header_id @endphp
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                No existen configuraciones
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
