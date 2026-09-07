<div>


    <div class="row">
        <!-- Filtros -->
        <div class="col-md-12 mt-1">
            <div class="card card-outline card-primary collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">Filtros</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm text-black" wire:click="abrirModal(0)"
                            data-bs-toggle="modal" data-bs-target="#modalGeneral">
                            <i class="fas fa-plus"></i> Asiento Manual
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="display: block;">
                    <div class="col-md-12">
                        <div class="row mt-2">
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
                                <label>Asiento</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Asiento ID"
                                    wire:model="searchAsiento">
                            </div>
                            <div class="col-3">
                                <label>Asiento Descripción</label>
                                <input type="text" class="form-control form-control-sm" placeholder="Descripción"
                                    wire:model="searchAsientoDescripcion">
                            </div>
                            <div class="col-3">
                                <label>Manual</label>
                                <select class="form-control form-control-sm" wire:model="manual">
                                    <option value="">TODOS</option>
                                    <option value="1">SI</option>
                                    <option value="0">NO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- Grid principal -->
        <div class="col-md-12 mt-2">
            <div class="card-body table-responsive p-0">
                <table class="table table-sm" style="border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 1%;">Asiento</th>
                            <th>Fecha Contable</th>
                            <th>Usuario</th>
                            <th>Cuenta Debe</th>
                            <th>Cuenta Haber</th>
                            <th>Debe Monto</th>
                            <th>Haber Monto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($asientosHeader as $a)
                        <tr class="bg-{{ $a->id == $a->id ? 'primary' : 'danger' }} text-white">
                            <td colspan="3">
                                <b>
                                    <!-- {{ $a->nombre_transaccion }}
                                        -
                                         -->

                                    {{ optional($a->concepto)->nombre ?? 'Sin concepto' }}
                                    <i>({{ optional(optional($a->concepto)->tipoConcepto)->nombre ?? 'Sin tipo' }})</i>
                                    @if ($a->manual)
                                    &nbsp;&nbsp;&nbsp;<i>(Asiento Manual)</i>
                                    @endif
                                </b>
                                <br>
                                Observacion: {{ $a->descripcion }}
                            </td>
                            <td colspan="2"><b>Total:</b></td>
                            <td style="border: 1px solid #dee2e6;">{{ $a->suma_debe }}</td>
                            <td style="border: 1px solid #dee2e6;">{{ $a->suma_haber }}</td>
                            <td style="border: 1px solid #dee2e6; text-align: center;">
                                 <a class="text-white" wire:click="abrirModal({{ $a->id }})" type="button"
                                    data-bs-toggle="modal" data-bs-target="#modalGeneral" title="Agregar una forma de Pago"><b>FP</b></a>
                                    
                                <a class="text-white" wire:click="abrirModal({{ $a->id }})" type="button"
                                    data-bs-toggle="modal" data-bs-target="#modalGeneral"> <i class="fa fa-edit"></i></a>
                                @if ($a->manual)
                                
                                @endif
                                <a class="text-white" href="/asientos/comprobante/{{ $a->id }}"> <i
                                        class="fas fa-print"></i></a>
                                <a class="ms-2 text-white" wire:click="confirmarElminarAsiento({{ $a->id }})"><i
                                        class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        @php
                        $totalDebe = 0;
                        $totalhaber = 0;
                        @endphp
                        @foreach ($a->detalles as $det)
                        <tr>
                            <td style="border: 1px solid #dee2e6;" class="align-middle text-center">
                                {{ $det->asientos_header_id }}
                            </td>
                            <td style="border: 1px solid #dee2e6;">
                                {{ $det->fecha_contable }}
                            </td>
                            <td style="border: 1px solid #dee2e6;" class="align-middle text-center">
                                {{ optional($det->userCreated)->firstname }} {{ optional($det->userCreated)->lastname }}
                            </td>
                            @if ($det->debe_haber)
                            <td style="border: 1px solid #dee2e6;">
                                <b>{{ optional($det->planCuentas)->codigo ?? 'Sin cuenta' }}</b>- {{ optional($det->planCuentas)->nombre ?? 'Cuenta no encontrada' }}
                            </td>
                            <td style="border: 1px solid #dee2e6;">
                            </td>
                            @else
                            <td style="border: 1px solid #dee2e6;">
                            </td>
                            <td style="border: 1px solid #dee2e6;">
                                <b>{{ optional($det->planCuentas)->codigo ?? 'Sin cuenta' }}</b>- {{ optional($det->planCuentas)->nombre ?? 'Cuenta no encontrada' }}
                            </td>
                            @endif
                            @if ($det->debe_haber)
                            <td style="border: 1px solid #dee2e6;text-align: right;">
                                $ {{ number_format($det->valor, 2, '.', '')  }}
                                @php $totalDebe += $det->valor; @endphp
                            </td>
                            <td style="border: 1px solid #dee2e6;text-align: right;">
                                $ 0.00
                            </td>
                            @else
                            <td style="border: 1px solid #dee2e6;text-align: right;">
                                $ 0.00
                            </td>
                            <td style="border: 1px solid #dee2e6;text-align: right;">
                                $ {{ number_format($det->valor , 2, '.', '')  }}
                                @php $totalhaber += $det->valor; @endphp
                            </td>
                            @endif
                            <td style="border: 1px solid #dee2e6;">

                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td style="border: 1px solid #dee2e6;text-align: right;" colspan="5">
                                <b>Total</b>
                            </td>
                            <td style="border: 1px solid #dee2e6;text-align: right;">
                                $ {{ number_format($totalDebe, 2, '.', '')  }}
                            </td>
                            <td style="border: 1px solid #dee2e6;text-align: right;">
                                $ {{ number_format($totalhaber, 2, '.', '') }}
                            </td>
                            <td style="border: 1px solid #dee2e6;text-align: right;">

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $asientosHeader->links() }}
            </div>
        </div>
    </div>


    {{-- MODAL CREAR ASIENTO MANUAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Asientos </h4>
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
                        <div class="row mb-4">
                            <div class="col-6">
                                <label>Fecha Contabilidad</label>
                                <input type="datetime-local" class="form-control" placeholder="Fecha Contable"
                                    wire:model="fecha">
                            </div>
                            <div class="col-6" style="margin-bottom: 10px;">
                                <label for="country_id">Conceptos</label> <a href="/conceptos" target="_blank"
                                    style="font-size: 12px;"> Ver</a>
                                <select title="Seleccionar" class="form-control" wire:model="concepto_id">
                                    <option> - Seleccione uno - </option>
                                    @foreach ($tipoConceptos as $tipoConcepto)
                                    <optgroup label="{{ $tipoConcepto->nombre }}">
                                        @foreach ($conceptos->where('tipo_concepto_id', $tipoConcepto->id) as $concepto)
                                        <option value="{{ $concepto->id }}">{{ $concepto->nombre }}</option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Motivo</label>
                                    <textarea class="form-control" rows="3"
                                        placeholder="Ingrese un motivo para el asiento"
                                        wire:model="descripcion"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">ASIENTO MANUAL</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-12">
                                        <div class="form-group position-relative">
                                            <div class="input-group input-group-lg">
                                                <input type="search" wire:model="search"
                                                    class="form-control form-control-lg"
                                                    placeholder="Escribe tus palabras claves">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-lg btn-default">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @if(!empty($search))
                                            <div class="card card-widget widget-user-2 position-absolute w-100 bg-white shadow"
                                                style="z-index: 1000; top: 100%; left: 0; right: 0; max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-top: none;">
                                                <div class="card-footer p-0">
                                                    <ul class="nav flex-column nav-pills">
                                                        @forelse ($planCuentasBuscador as $key => $plan)
                                                        <li class="nav-item">
                                                            <a wire:click="agregarCuenta({{ $plan->id }});"
                                                                class="nav-link">
                                                                {{ $plan->nombre }} <span
                                                                    class="float-end badge bg-primary">{{ $plan->codigo }}</span>
                                                            </a>
                                                        </li>
                                                        @empty
                                                        <li class="nav-item">
                                                            No hay resultados
                                                        </li>
                                                        @endforelse
                                                    </ul>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Cuenta</th>
                                                <th>Opcion</th>
                                                <th>C.Costos</th>
                                                <th>Debe</th>
                                                <th>Haber</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($this->asientos as $key => $asi)
                                            <tr>
                                                <td><b>{{ $asi['cuenta_codigo'] }}</b> {{ $asi['cuenta_nombre'] }}
                                                </td>
                                                <td>
                                                    @if ($asi['debe_haber'])
                                                    <div class="form-group">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="customSwitch{{ $key }}" checked
                                                                wire:click="actualizarDebHaber({{ $key }})">
                                                            <label class="custom-control-label"
                                                                for="customSwitch{{ $key }}">Debe</label>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="form-group">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="customSwitch{{ $key }}"
                                                                wire:click="actualizarDebHaber({{ $key }})">
                                                            <label class="custom-control-label"
                                                                for="customSwitch{{ $key }}">Haber</label>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <select class="form-control form-control-sm" id="centro-{{ $key }}"
                                                        wire:change="actualizarCentro({{ $key }}, $event.target.value)">
                                                        <option value="0" {{ $asi['sedes_centro_costos'] == 0 ? 'selected' : '' }}>
                                                            Ninguno</option>
                                                        @foreach ($sedes as $se)
                                                        <optgroup label="{{ $se->name }}">
                                                            @foreach ($centro_costos->where('sede_id', $se->id) as $sedeCentro)
                                                            <option value="{{ $sedeCentro->id }}" {{ $asi['sedes_centro_costos'] == $sedeCentro->id ? 'selected' : '' }}>
                                                                <b>{{ optional($sedeCentro->sede)->name ?? 'Sin sede' }}</b> -
                                                                {{ optional($sedeCentro->centroCostos)->name ?? 'Sin centro de costos' }}
                                                            </option>
                                                            @endforeach
                                                        </optgroup>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td style="width: 40px;">
                                                    <input class="form-control form-control-sm" type="number"
                                                        step="0.02" placeholder="0.00" id="debe-{{ $key }}"
                                                        value="{{ $asi['debe'] }}"
                                                        wire:change="actualizarValores({{ $key }}, $event.target.value)"
                                                        style="width: 80px;font-size: 12px;text-align: center; width: 120px;; font-size: 20px;"
                                                        {{ $asi['debe_haber'] ? '' : 'disabled' }}>
                                                </td>
                                                <td style="width: 40px;">
                                                    <input class="form-control form-control-sm" type="number"
                                                        step="0.02" placeholder="0.00" id="haber-{{ $key }}"
                                                        value="{{ $asi['haber'] }}"
                                                        wire:change="actualizarValores({{ $key }}, $event.target.value)"
                                                        style="width: 80px;font-size: 12px;text-align: center; width: 120px; font-size: 20px;"
                                                        {{ $asi['debe_haber'] ? 'disabled' : '' }}>
                                                </td>
                                                <td class="text-center">
                                                    <a class="ms-2 text-primary"
                                                        wire:click="elminarCuenta({{ $key }})"><i
                                                            class="fa fa-trash"></i></a>
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
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('livewire:load', function() {
        Livewire.on('selectoresCargar', function() {
            $('.selector').select2({
                theme: "bootstrap4",
                dropdownDirection: 'bottom'
            }).on('change', function(e) {
                Livewire.emit('cuenta_id', e.target.value);
            });
            /*$('.selectorConceptos').select2({
                theme: "bootstrap4",
                dropdownDirection: 'bottom'
            }).on('change', function(e) {
                Livewire.emit('concepto_id', e.target.value);
            });*/
        });
    });

    window.addEventListener('notificarEliminar', function(event) {
        var id = event.detail.id;
        console.log(id, event);
        Swal.fire({
            title: 'Confirmar Eliminación',
            text: '¿Estás seguro de que deseas eliminar este Asiento?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                Livewire.emit('elminarAsiento', id);
            }
        });
    });
</script>
