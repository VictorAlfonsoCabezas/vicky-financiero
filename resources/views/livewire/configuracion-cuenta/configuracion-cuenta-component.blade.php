<div>
    <div class="row">
        <div class="card col-md-12 mt-2" style="position: relative; left: 0px; top: 0px;">
            <div class="card-header ui-sortable-handle">
                <h3 class="card-title">
                    <i class="fas fa-cogs"></i>
                    Configuracion de Asientos
                </h3>
                <div class="card-tools">
                    <a class="btn btn-primary text-white" wire:click="abrirModal(0)" data-bs-toggle="modal" data-bs-target="#modalGeneral"><i class="fa fa-plus"></i> Agregar</a>
                </div>
            </div>
            <div class="card-body">
                <div class="card-body table-responsive p-0">
                    <table class="table table-sm" style="border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th>Cuenta Debe</th>
                                <th>Cuenta Haber</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $header = 0 @endphp
                            @forelse ($detalle as $key => $det)
                            @if ($header !== $det->config_plan_header_id)
                            <tr class="bg-warning text-white">
                                <td colspan="2">
                                    <b>
                                        {{ $det->type_transactions_nombre }}
                                        <i>({{ $det->conceptos_nombre }})</i>
                                    </b>
                                </td>
                                <td style="border: 1px solid #dee2e6; text-align: center;">
                                    <a class="ms-2 text-white" wire:click="abrirModalAsiento({{ $det->config_plan_header_id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral2"><i class="fa fa-cog"></i></a>
                                    <a class="ms-2 text-white" wire:click="abrirModal({{ $det->config_plan_header_id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"> <i class="fa fa-edit"></i></a>
                                    <a class="ms-2 text-white" wire:click="confirmarElminarConfiguracion({{ $det->config_plan_header_id }})"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                @if ($det->debe_haber)
                                <td style="border: 1px solid #dee2e6;" class="align-middle text-start">
                                    <b>{{ $det->plan_codigo }}</b> {{ $det->plan_nombre }}
                                </td>
                                <td style="border: 1px solid #dee2e6;">

                                </td>
                                @else
                                <td style="border: 1px solid #dee2e6;">

                                </td>
                                <td style="border: 1px solid #dee2e6;" class="align-middle text-start">
                                    <b>{{ $det->plan_codigo }}</b> {{ $det->plan_nombre }}
                                </td>
                                @endif
                                <td style="border: 1px solid #dee2e6;" class="text-center">
                                </td>
                            </tr>
                            @php $header = $det->config_plan_header_id @endphp
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    No existen configuraciones
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $detalle->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CREAR CONFIGURACION --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title"> Configuracion de Asientos </h4>
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
                                <label>Módulo</label>
                                <select wire.ignore class="form-control selectorModulos" wire:model="type_transaction_id">
                                    <optgroup label="OPEREACIONES DIARIAS">
                                        @foreach ($typeTransaction as $typeTran)
                                        <option value="1-{{ $typeTran->id }}">{{ $typeTran->name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="OPEREACIONES BOVEDAS">
                                        @foreach ($operacionesDescargoBovedas as $operacionesDescargo)
                                        <option value="2-{{ $operacionesDescargo->id }}">{{ $operacionesDescargo->nombre }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-6" style="margin-bottom: 10px;">
                                <label for="country_id">Concepto</label><a href="/conceptos" target="_blank" style="font-size: 12px;"> Ver</a>
                                <select title="Seleccionar" class="form-control selectorConceptos" wire:model="concepto_id">
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
                        </div>
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">CUENTAS</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group position-relative">
                                            <div class="input-group input-group-lg">
                                                <input type="search" wire:model="search" class="form-control form-control-lg" placeholder="Escribe tus palabras claves">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-lg btn-default">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @if(!empty($search))
                                            <div class="card card-widget widget-user-2 position-absolute w-100 bg-white shadow" style="z-index: 1000; top: 100%; left: 0; right: 0; max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-top: none;">
                                                <div class="card-footer p-0">
                                                    <ul class="nav flex-column nav-pills">
                                                        @forelse ($planCuentasBuscador as $key => $plan)
                                                        <li class="nav-item">
                                                            <a wire:click="agregarCuenta({{ $plan->id }});" class="nav-link">
                                                                {{ $plan->nombre }} <span class="float-end badge bg-primary">{{ $plan->codigo }}</span>
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
                                                            <input type="checkbox" class="custom-control-input" id="customSwitch{{ $key }}" checked wire:click="actualizarDebHaber({{ $key }})">
                                                            <label class="custom-control-label" for="customSwitch{{ $key }}">Debe</label>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="form-group">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input" id="customSwitch{{ $key }}" wire:click="actualizarDebHaber({{ $key }})">
                                                            <label class="custom-control-label" for="customSwitch{{ $key }}">Haber</label>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </td>
                                                <td class="form-group">
                                                    <a class="ms-2 text-primary" wire:click="elminarConfig({{ $key }})"><i class="fa fa-trash"></i></a>
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

    {{-- MODAL CONGIRUACION CUENTAS --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral2" style="display: none;" aria-hidden="true" data-bs-backdrop="static" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title"> Parametrización de Asientos </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        <div class="col-12">
                            <p class="lead">
                                @if($this->id_header > 0)
                                @if ($headers->type_transaction_id !== null)
                                Operaciones Convecinales
                                @else
                                Operaciones Bóvedas
                                @endif
                                @endif
                            </p>
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        @if($this->id_header > 0)
                                        <tr>
                                            <th style="width:25%">Módulo:</th>
                                            <td>
                                                @if ($headers->type_transaction_id !== null)
                                                {{$headers->typeTransaction->name}}
                                                @else
                                                {{$headers->operacionesDescargoBovedas->nombre}}
                                                @endif
                                            </td>
                                            <th style="width:25%">Concepto:</th>
                                            <td>
                                                {{$headers->concepto->nombre}}
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr class="my-10 mb-4">
                        @if ($errors->any())
                        <div class="callout callout-warning">
                            <h5>Verifica estas observaciones.</h5>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </div>
                        @endif
                        <div class="row">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Cuenta</th>
                                        <th>Opción</th>
                                        <th>Campo Foraneo</th>
                                        <th>Tabla</th>
                                        <th>Campo</th>
                                        <!-- <th class="text-center">Acción</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($detallesConfig as $key => $detalle)
                                    <tr>
                                        <td>
                                            <b>{{ $detalle->planCuentas->nombre }}</b> {{ $detalle->planCuentas->codigo }}
                                        </td>
                                        <td>
                                            @if ($detalle->debe_haber)
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="customSwitch{{ $key }}" checked disabled>
                                                    <label class="custom-control-label" for="customSwitch{{ $key }}">Debe</label>
                                                </div>
                                            </div>
                                            @else
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="customSwitch{{ $key }}" disabled>
                                                    <label class="custom-control-label" for="customSwitch{{ $key }}">Haber</label>
                                                </div>
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($detalle->header->type_transaction_id !== null)
                                            <select class="form-control form-control-sm" wire:change="guardarConfigCampoForaneo({{ $detalle->id }}, $event.target.value)">
                                                @foreach ($columnNombres as $col)
                                                <option value="{{ $col }}" {{($col == $detalle->campo_foraneo) ? 'selected' : '' }}>
                                                    <b>{{ $col }}</b>
                                                </option>
                                                @endforeach
                                            </select>
                                            @else
                                            <select class="form-control form-control-sm" wire:change="guardarConfigCampoForaneo({{ $detalle->id }}, $event.target.value)">
                                                @foreach ($columnNombres as $col)
                                                <option value="{{ $col }}" {{($col == $detalle->campo_foraneo) ? 'selected' : '' }}>
                                                    <b>{{ $col }}</b>
                                                </option>
                                                @endforeach
                                            </select>
                                            @endif

                                        </td>
                                        <td>
                                            @if($detalle->header->type_transaction_id !== null)
                                            <select class="form-control form-control-sm" wire:change="guardarConfigTabla({{ $detalle->id }}, $event.target.value)">
                                                <option value="">Ninguno</option>
                                                @foreach ($columnTablas as $colTable)
                                                <option value="{{ $colTable }}" {{($colTable == $detalle->tabla) ? 'selected' : '' }}>
                                                    <b>{{ $colTable }}</b>
                                                </option>
                                                @endforeach
                                            </select>
                                            @else
                                            <select class="form-control form-control-sm" disabled wire:change="guardarConfigTabla({{ $detalle->id }}, $event.target.value)">
                                                <option value="">Ninguno</option>
                                            </select>
                                            @endif
                                        </td>
                                        <td>
                                            @if($detalle->header->type_transaction_id !== null)
                                            <select class="form-control form-control-sm" wire:change="guardarConfigCampo({{ $detalle->id }}, $event.target.value)">
                                                <option value="">Ninguno</option>
                                                @foreach ($columnPrestamos as $colPres)
                                                <option value="{{ $colPres }}" {{($colPres == $detalle->campo) ? 'selected' : '' }}>
                                                    <b>{{ $colPres }}</b>
                                                </option>
                                                @endforeach
                                            </select>
                                            @else
                                            <select class="form-control form-control-sm" wire:change="guardarConfigCampo({{ $detalle->id }}, $event.target.value)" disabled>
                                                <option value="0">Ninguno</option>
                                                @foreach ($columnPrestamos as $colPres)
                                                <option value="{{ $colPres }}" {{($colPres == $detalle->campo) ? 'selected' : '' }}>
                                                    <b>{{ $colPres }}</b>
                                                </option>
                                                @endforeach
                                            </select>
                                            @endif
                                        </td>
                                        <!-- <td class="text-center">
                                            <a class="ms-2 text-primary" wire:click="guardarConfig({{ $detalle->id }})"><i class="fa fa-save"></i></a>
                                        </td> -->
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
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('livewire:load', function() {
        // Inicializar Select2
        $('.selectorModulos').select2({
            theme: "bootstrap4",
            dropdownDirection: 'bottom'
        });

        // Emitir el cambio a Livewire
        $('.selectorModulos').on('change', function(e) {
            Livewire.emit('moduloChanged', e.target.value);
        });

        // // Escuchar evento de renderizado de Livewire para reinicializar Select2
        Livewire.hook('message.processed', (message, component) => {
            $('.selectorModulos').select2({
                theme: "bootstrap4",
                dropdownDirection: 'bottom'
            }).on('change', function(e) {
                Livewire.emit('moduloChanged', e.target.value);
            });
        });


        // Inicializar Select2
        $('.selectorConceptos').select2({
            theme: "bootstrap4",
            dropdownDirection: 'bottom'
        });

        // Emitir el cambio a Livewire
        $('.selectorConceptos').on('change', function(e) {
            Livewire.emit('conceptoChanged', e.target.value);
        });

        // Escuchar evento de renderizado de Livewire para reinicializar Select2
        Livewire.hook('message.processed', (message, component) => {
            $('.selectorConceptos').select2({
                theme: "bootstrap4",
                dropdownDirection: 'bottom'
            }).on('change', function(e) {
                Livewire.emit('conceptoChanged', e.target.value);
            });
        });

        window.addEventListener('notificarEliminar', function(event) {
            var id = event.detail.id;
            console.log(id, event);
            Swal.fire({
                title: 'Confirmar Eliminación',
                text: '¿Estás seguro de que deseas eliminar este Configuración?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.value) {
                    Livewire.emit('elminarConfigCompleta', id);
                }
            });
        });
    });
</script>