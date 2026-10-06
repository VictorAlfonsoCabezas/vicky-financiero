<div class="gasto-module">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-header mb-1"><i class="fa fa-receipt text-primary me-2" aria-hidden="true"></i> Gastos</h1>
            <p class="text-muted mb-0">Gestiona tus facturas, aprobaciones y pagos.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-primary" wire:click="abrirModal(0)" data-bs-toggle="modal" data-bs-target="#modalGeneral"><i class="fa fa-plus me-1" aria-hidden="true"></i> Nuevo gasto</button>
            <button type="button" class="btn btn-outline-primary" wire:click="abrirModalXML(0)" data-bs-toggle="modal" data-bs-target="#modalGeneral1"><i class="fa fa-file-code me-1" aria-hidden="true"></i> Importar XML</button>
        </div>
    </div>
    <div class="panel panel-inverse mb-4">
        <div class="panel-heading"><h4 class="panel-title"><i class="fa fa-filter me-2" aria-hidden="true"></i> Filtrar gastos</h4></div>
        <div class="panel-body">
            <div class="row g-3">
                <div class="col-12 col-md-6 col-xl-3">
                    <label class="form-label" for="gasto-estado">Estado</label>
                    <select id="gasto-estado" class="form-select" wire:model="estadoFiltro">
                        <option value="TODOS">Todos los estados</option>
                        <option value="BORRADOR">Borrador</option>
                        <option value="PENDIENTE">Pendiente</option>
                        <option value="APROBADO">Aprobado</option>
                        <option value="PAGADO">Pagado</option>
                        <option value="RECHAZADO">Rechazado</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <label class="form-label" for="gasto-proveedor"><i class="fa fa-building me-1" aria-hidden="true"></i> Proveedor</label>
                    <select id="gasto-proveedor" class="form-select" wire:model="proveedorFiltro">
                        <option value="">Todos los proveedores</option>
                        @foreach ($proveedoresFiltro as $proveedor)
                            <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }} — {{ $proveedor->ruc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-xl-3">
                    <label class="form-label" for="gasto-desde">Fecha de registro desde</label>
                    <input id="gasto-desde" type="date" wire:model="fecha_inicio" class="form-control">
                </div>
                <div class="col-6 col-xl-3">
                    <label class="form-label" for="gasto-hasta">Fecha de registro hasta</label>
                    <input id="gasto-hasta" type="date" wire:model="fecha_fin" class="form-control">
                </div>
            </div>
        </div>
    </div>
    @error('fecha_inicio') <div class="alert alert-danger" role="alert">{{ $message }}</div> @enderror
    @error('fecha_fin') <div class="alert alert-danger" role="alert">{{ $message }}</div> @enderror
    <div class="row">
        <div class="col-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title"><i class="fa fa-list me-2"></i> Gastos registrados <span class="badge bg-primary ms-2">{{ $gastos->total() }}</span></h4>
                    <button type="button" class="btn btn-success btn-sm" wire:click="descargarGastos" wire:loading.attr="disabled" wire:target="descargarGastos" title="Descargar todos los gastos de los filtros actuales en Excel" {{ $gastos->total() === 0 ? 'disabled' : '' }}>
                        <i class="fa fa-file-excel me-1" aria-hidden="true"></i>
                        <span wire:loading.remove wire:target="descargarGastos">Descargar Excel</span>
                        <span wire:loading wire:target="descargarGastos">Generando...</span>
                    </button>
                </div>
                <div class="panel-body table-responsive p-0 gasto-table-wrap">
                    <table class="table table-hover table-striped align-middle mb-0 gasto-table">
                        <thead>
                            <tr style="padding: 10px;">
                                <th style="width: 1%;">#</th>
                                <th>Categorías</th>
                                <th>Proveedor</th>
                                <th># Factura</th>
                                <th>Fecha Emisión</th>
                                <th>Subtotal</th>
                                <th>Descuento</th>
                                <th>Subtotal con descuento</th>
                                <th>Base imponible</th>
                                <th>IVA</th>
                                <th>TOTAL</th>
                                <th>Monto Pagado</th>
                                <th>Monto Retenciones</th>
                                <th>Monto Pendiente</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gastos as $gas)
                            <tr wire:key="gasto-{{ $gas->id }}">
                                <td>{{ $gas->id }}</td>
                                <td><b>{{ $gas->categoria_nombre }}</b></td>
                                <td><i class="fa fa-building text-muted me-1"></i> <b>{{ $gas->nombreProveedor }}</b><small class="d-block text-muted">{{ $gas->ruc }}</small></td>
                                <td>{{ $gas->establecimiento }}-{{ $gas->punto_emision }}-{{ $gas->numero }}</td>
                                <td>{{ $gas->fecha_emision }}</td>
                                <td class="text-end gasto-money">{{ number_format((float) $gas->valor, 2, '.', ',') }}</td>
                                <td class="text-end gasto-money">{{ number_format((float) $gas->descuento, 2, '.', ',') }}</td>
                                <td class="text-end gasto-money">{{ number_format((float) $gas->subtotal_descuento, 2, '.', ',') }}</td>
                                <td class="text-end gasto-money">{{ number_format((float) $gas->suma_subtotal, 2, '.', ',') }}</td>
                                <td class="text-end gasto-money">{{ number_format((float) $gas->suma_iva, 2, '.', ',') }}</td>
                                <td class="text-end gasto-money">{{ number_format((float) $gas->total, 2, '.', ',') }}</td>
                                <td class="text-end gasto-money">{{ number_format((float) $gas->monto_pagado, 2, '.', ',') }}</td>
                                <td class="text-end gasto-money">{{ number_format((float) $gas->monto_retencion, 2, '.', ',') }}</td>
                                <td class="text-end gasto-money">
                                    <b style="color: {{ ((float) $gas->valor_pendiente > 0) ? 'red': '#212550' }};">$ {{ number_format((float) $gas->valor_pendiente, 2, '.', ',') }}</b>
                                </td>
                                <td>
                                    @if ($gas->estado == 'BORRADOR')
                                    <small class="badge bg-warning text-dark">{{ $gas->estado }}</small>
                                    @elseif ($gas->estado == 'PENDIENTE')
                                    <small class="badge bg-secondary">{{ $gas->estado }}</small>
                                    @elseif ($gas->estado == 'APROBADO')
                                    <small class="badge bg-primary">{{ $gas->estado }}</small>
                                    @elseif ($gas->estado == 'PAGADO')
                                    <small class="badge bg-success">{{ $gas->estado }}</small>
                                    @else
                                    <small class="badge bg-danger">{{ $gas->estado }}</small>
                                    <div class="mt-0">
                                        <span style="font-size: 10px;"><b>{{ $gas->id }}: </b></span>
                                        <span class="description"
                                            style="font-size: 10px;">{{ $gas->razon_rechaza }}</span>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($gas->estado == 'BORRADOR')
                                    <button wire:click="abrirModal({{ $gas->id }})" type="button" data-bs-toggle="modal"
                                        data-bs-target="#modalGeneral"
                                        title="Editar gasto" aria-label="Editar gasto" class="btn btn-outline-primary btn-xs"><i class="fa fa-pen"></i>
                                    </button>
                                    <button wire:click="confirmarBorrarGasto({{ $gas->id }})"
                                        type="button" title="Eliminar gasto" aria-label="Eliminar gasto" class="btn btn-outline-danger btn-xs"><i class="fa fa-trash"></i></button>
                                    @elseif ($gas->estado == 'PENDIENTE')
                                    <button wire:click="abrirModalXML({{ $gas->id }})" type="button" data-bs-toggle="modal"
                                        data-bs-target="#modalGeneral1"
                                        class="btn btn-warning btn-xs" title="Importar XML de la factura" aria-label="Importar XML de la factura"><i class="fa fa-file-code"></i></button>
                                    <button wire:click="abrirModal({{ $gas->id }})" type="button" data-bs-toggle="modal"
                                        data-bs-target="#modalGeneral"
                                        title="Editar gasto" aria-label="Editar gasto" class="btn btn-outline-primary btn-xs"><i class="fa fa-pen"></i></button>


                                    <button wire:click="verGasto({{ $gas->id }})" type="button" data-bs-toggle="modal"
                                        data-bs-target="#modalGeneral2"
                                        title="Revisar aprobación" aria-label="Revisar aprobación" class="btn btn-outline-success btn-xs"><i class="fa fa-clipboard-check"></i></button>


                                    <button wire:click="confirmarBorrarGasto({{ $gas->id }})"
                                        type="button" title="Eliminar gasto" aria-label="Eliminar gasto" class="btn btn-outline-danger btn-xs"><i class="fa fa-trash"></i></button>
                                    @elseif ($gas->estado == 'APROBADO')
                                    <button type="button" wire:click="abrirModal({{ $gas->id }})" data-bs-toggle="modal"
                                        data-bs-target="#modalGeneral"
                                        title="Ver gasto" aria-label="Ver gasto" class="btn btn-outline-primary btn-xs"><i class="fa fa-eye"></i></button>

                                    <button type="button" class="btn btn-{{($gas->monto_pagado > 0) ? 'danger' : 'primary'}} btn-xs text-white"
                                        wire:click="abrirModalFP({{ $gas->id }})"
                                        data-bs-toggle="modal" data-bs-target="#modalGeneral4" title="Gestionar pagos" aria-label="Gestionar pagos"><i class="fa fa-credit-card"></i></button>

                                    <button type="button" class="btn btn-{{($gas->monto_retencion > 0) ? 'danger' : 'primary'}} btn-xs text-white"
                                        wire:click="abrirModalRET({{ $gas->id }})"
                                        data-bs-toggle="modal" data-bs-target="#modalGeneral5" title="Gestionar retenciones" aria-label="Gestionar retenciones"><i class="fa fa-file-invoice-dollar"></i></button>

                                    <button type="button" class="btn btn-warning btn-xs text-white"
                                        wire:click="reversarPendiente({{ $gas->id }})"
                                        title="Reversar a Pendiente"><i class="fa fa-undo"></i></button>

                                    @if ($gas->valor_pendiente == '0.00')
                                    <button type="button" class="btn btn-success btn-xs text-white"
                                        wire:click="pagado({{ $gas->id }})"
                                        title="Marcar como pagado"><i class="fa fa-check-circle"></i></button>
                                    @endif

                                    @elseif ($gas->estado == 'PAGADO')
                                    <button type="button" class="btn btn-primary btn-xs text-white"
                                        wire:click="abrirModalAsientos({{ $gas->id }})"
                                        data-bs-toggle="modal" data-bs-target="#modalGeneral3" title="Ver asientos contables" aria-label="Ver asientos contables"><i class="fa fa-book"></i></button>
                                    <a class="btn btn-default btn-xs" href="{{URL::to('gastos/ticket/' . $gas->id)}}"
                                        title="Ticket">
                                        <i class="far fa-file-pdf"></i>
                                    </a>
                                    <button type="button" class="btn btn-warning btn-xs text-white"
                                        wire:click="reversarAprobado({{ $gas->id }})"
                                        title="Reversar a Aprobado"><i class="fa fa-undo"></i>
                                    </button>
                                    @elseif ($gas->estado == 'RECHAZADO')
                                    <button type="button" class="btn btn-warning btn-xs text-white"
                                        wire:click="reversarPendiente({{ $gas->id }})"
                                        title="Reversar a Pendiente"><i class="fa fa-undo"></i></button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center py-5" colspan="16">
                                    <i class="fa fa-receipt fa-2x text-muted mb-3 d-block" aria-hidden="true"></i>
                                    <h5>No hay gastos para estos filtros</h5>
                                    <p class="text-muted">Ajusta el estado o las fechas, o registra un nuevo gasto.</p>
                                    <button type="button" class="btn btn-primary btn-xs" wire:click="abrirModal(0);" data-bs-toggle="modal" data-bs-target="#modalGeneral" title="Nuevo Gasto">
                                        <i class="fa fa-plus"></i> Nuevo gasto
                                    </button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top">
                    {{ $gastos->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" aria-labelledby="modalGeneral-title" tabindex="-1" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalGeneral-title"><i class="fa fa-receipt"></i> Gastos </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form wire:submit.prevent="storeGasto">
                    <div class="modal-body"><div class="gasto-modal-intro">Registra el proveedor, la categoría y los importes de la factura.</div>
                        @if ($errors->any())
                        <div class="col-12">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-danger"><b>Revisa la siguiente
                                            información</b></span>
                                    @foreach ($errors->all() as $error)
                                    <small class="text-danger">{{ $error }}</small>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row g-3 mb-2">
                            <div class="col-12"><h5 class="gasto-section-title"><i class="fa fa-building me-2" aria-hidden="true"></i>Datos de la factura</h5></div><div class="col-12 col-md-7">
                                <label class="form-label">Proveedores</label>
                                <a href="/proveedores" target="_blank">
                                    <i class="fa fa-plus-circle"></i>
                                </a>
                                <select class="form-select" wire:model="proveedor_id" {{($this->estado !== 'APROBADO') ? '' : 'readonly'}}>
                                    <option value="0"> Seleccione </option>
                                    @foreach ($this->proveedores as $prove)
                                    <option value="{{ $prove['id'] }}">{{ $prove['nombre'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-5">
                                <label class="form-label">Categorías</label>
                                <a href="/gastos-categorias" target="_blank">
                                    <i class="fa fa-plus-circle"></i>
                                </a>

                                <select class="form-select" wire:model="gastos_categorias_id" {{($this->estado !== 'APROBADO') ? '' : 'readonly'}}>
                                    <option value="0"> Seleccione una</option>
                                    @foreach ($this->categorias as $cat)
                                    <option value="{{ $cat['id'] }}">{{ $cat['nombre'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <hr class="w-100">
                            <div class="form-group col-md-4">
                                <label class="form-label"><b>Fecha de Emisión</b></label>
                                <input type="date"
                                    class="form-control"
                                    wire:model="fecha_emision"
                                    {{ ($this->estado === 'APROBADO') ? 'disabled' : '' }}>
                                @error('fecha_emision')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>


                            <hr class="w-100">
                            <div class="col-12"><h5 class="gasto-section-title"><i class="fa fa-calculator me-2" aria-hidden="true"></i>Importes e impuestos</h5></div><div class="col-12">
                                <label class="form-label">Subtotal</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                            <i class="fas fa-dollar-sign"></i>
                                        </span>

                                    <input type="number"
                                        step="0.01"
                                        class="form-control form-control-lg"
                                        placeholder="0.00"
                                        wire:model="valor"
                                        wire:keyup.debounce.500ms="calcular()"
                                        {{ ($this->estado !== 'APROBADO') ? '' : 'readonly' }}>

                                    <div class="ms-3">
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                type="radio"
                                                name="tipo_impuesto"
                                                id="con_impuestos"
                                                value="1"
                                                wire:click="configurarImpuestos(1)"
                                                {{ ((string) $this->tiene_impuestos === '1') ? 'checked' : '' }}
                                                {{ ($this->estado === 'APROBADO') ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="con_impuestos">
                                                <b>% Impuestos</b>
                                            </label>
                                        </div>

                                        <div class="form-check mt-2">
                                            <input class="form-check-input"
                                                type="radio"
                                                name="tipo_impuesto"
                                                id="sin_impuestos"
                                                value="0"
                                                wire:click="configurarImpuestos(0)"
                                                {{ ((string) $this->tiene_impuestos === '0') ? 'checked' : '' }}
                                                {{ ($this->estado === 'APROBADO') ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="sin_impuestos">
                                                <b>NO Tributación</b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if((int) $this->tiene_impuestos === 1)
                                <div class="card-body mt-0 p-0 table-responsive">
                                    <table class="table table-hover align-middle gasto-modal-table">
                                        <tbody>
                                            <tr>
                                                <td class="text-end"><b>Subtotal Descuento: </b></td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        <input type="number"
                                                            class="form-control"
                                                            step="0.01"
                                                            placeholder="0.00"
                                                            wire:model="subtotal_descuento"
                                                            readonly>
                                                    </div>
                                                </td>

                                                <td class="text-end">Descuento:</td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        <input type="number"
                                                            class="form-control"
                                                            step="0.01"
                                                            placeholder="0.00"
                                                            wire:model="descuento"
                                                            wire:keyup.debounce.500ms="calcular()"
                                                            {{ ($this->estado !== 'APROBADO') ? '' : 'readonly' }}>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-end">Subtotal Exento: </td>
                                                <td>
                                                    <div class="input-group mb-0">
                                                        <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        <input type="number"
                                                            class="form-control"
                                                            step="0.01"
                                                            placeholder="0.00"
                                                            wire:model="subtotal_exento"
                                                            wire:keyup.debounce.500ms="calcular()"
                                                            {{ ($this->estado !== 'APROBADO') ? '' : 'readonly' }}>
                                                    </div>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>

                                            @forelse ($gastosImpuestos as $imp)

                                                @php
                                                    $porcentaje = 0;

                                                    // CASO 1: viene desde BD con relación impuestos
                                                    if (isset($imp->impuestos) && $imp->impuestos) {
                                                        $porcentaje = (float) $imp->impuestos->valor;
                                                    }
                                                    // CASO 2: viene temporal al crear gasto
                                                    elseif (isset($imp->valor)) {
                                                        $porcentaje = (float) $imp->valor;
                                                    }
                                                @endphp

                                                <tr>
                                                    <td class="text-end">
                                                        Subtotal <b>{{ $porcentaje }}%</b>:
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                    <i class="fas fa-dollar-sign"></i>
                                                                </span>

                                                            <input type="number"
                                                                class="form-control"
                                                                step="0.01"
                                                                placeholder="0.00"
                                                                value="{{ $imp->subtotal ?? 0 }}"
                                                                wire:keyup.debounce.500ms="cambioSubtotal({{ $imp->id }}, $event.target.value)"
                                                                {{ ($this->estado !== 'APROBADO') ? '' : 'readonly' }}>
                                                        </div>
                                                    </td>

                                                    <td class="text-end">
                                                        Iva <b>{{ $porcentaje }}%</b>:
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                    <i class="fas fa-dollar-sign"></i>
                                                                </span>

                                                            <input type="number"
                                                                class="form-control"
                                                                step="0.01"
                                                                placeholder="0.00"
                                                                value="{{ $imp->iva ?? 0 }}"
                                                                readonly>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">
                                                        No hay impuestos configurados para este gasto.
                                                    </td>
                                                </tr>
                                            @endforelse

                                            <tr>
                                                <td class="text-end"><b>Subtotales: </b></td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        <input type="number"
                                                            class="form-control"
                                                            step="0.01"
                                                            placeholder="0.00"
                                                            wire:model="suma_subtotal"
                                                            readonly>
                                                    </div>
                                                </td>

                                                <td class="text-end"><b>Ivas: </b></td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        <input type="number"
                                                            class="form-control"
                                                            step="0.01"
                                                            placeholder="0.00"
                                                            wire:model="suma_iva"
                                                            readonly>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            <div class="col-12">
                                <label class="form-label">Total del gasto</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    <input type="number" class="form-control form-control-lg" step="0.01" placeholder="0.00"
                                        wire:model="total" readonly>
                                </div>
                            </div>

                            <hr class="w-100">
                            <div class="col-12">
                                <label class="form-label">Observación: </label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-comments"></i></span>
                                    <input type="text" class="form-control" placeholder="Descripcion"
                                        wire:model="descripcion" {{($this->estado !== 'APROBADO') ? '' : 'readonly'}}>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white me-auto" data-bs-dismiss="modal">
                            Cerrar
                        </button>

                        @if($this->estado !== 'APROBADO')
                            {{-- GUARDA EN BORRADOR --}}
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1" aria-hidden="true"></i> Guardar borrador</button>

                            {{-- CAMBIA A PENDIENTE --}}
                            <button type="button"
                                class="btn btn-success text-white"
                                wire:click="solicitarAprobacion">
                                <i class="fa fa-paper-plane"></i> Solicitar aprobación
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL XML --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral1" aria-labelledby="modalGeneral1-title" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalGeneral1-title"><i class="fa fa-file-code"></i> Importar XML </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="gasto-modal-intro"><i class="fa fa-file-upload me-2" aria-hidden="true"></i> Selecciona el XML de la factura para revisar sus datos antes de guardar.</div>

                    <div class="gasto-upload-box mb-4">
                        <label for="fileInput" class="form-label">Archivo XML de la factura</label>
                        <input id="fileInput" type="file" accept=".xml,text/xml,application/xml" wire:model="xmlFile" class="form-control"><div class="form-text mb-3" wire:loading wire:target="xmlFile" role="status"><i class="fa fa-spinner fa-spin me-1"></i> Procesando archivo...</div>
                        @error('xmlFile') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>



                    @if ($xmlData)
                    <div class="invoice p-3 mb-3">
                        {!! $htmlXml !!}
                        <div class="row g-3">
                            <!-- accepted payments column -->
                            <div class="col-12 col-lg-6">
                                <p class="lead">Métodos de Pago:</p>

                                {!! $htmlXmlTabla1 !!}
                                <table class="table table-bordered">

                                </table>
                            </div>
                            <!-- /.col -->
                            <div class="col-12 col-lg-6">
                                <p class="lead">Desglose de impuestos</p>
                                <div class="table-responsive">
                                    {!! $htmlXmlTabla2 !!}

                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-white me-auto" data-bs-dismiss="modal">Cerrar</button>
                    @if ($xmlData)
                    <button type="button" class="btn btn-success" wire:click="guardarGastos()" wire:loading.attr="disabled" wire:target="xmlFile,guardarGastos">
                        <i class="fa fa-save me-1" aria-hidden="true"></i> Guardar factura
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>


    {{-- MODAL APROBAR --}}
<div wire:ignore.self class="modal fade" id="modalGeneral2" aria-labelledby="modalGeneral2-title" tabindex="-1" role="dialog"
    aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalGeneral2-title"><i class="fa fa-clipboard-check"></i> Revisión del gasto </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form wire:submit.prevent="storeGasto">
                <div class="modal-body"><div class="gasto-modal-intro">Verifica los datos tributarios y la distribución contable antes de aprobar el gasto.</div>
                    <div class="row g-3 mb-2">

                        @if ($this->id_aprobar !== 0)

                            @php
                                // Bloquear tributarios si NO tiene impuestos
                                $bloquearTributarios = ((int) $this->tiene_impuestos === 0);

                                // Solo autorización y fecha se bloquean por proveedor físico
                                $bloquearAutorizacionProveedor = ($tipo_factura_proveedor === 'fisica');
                            @endphp

                            @if ($errors->any())
                                <div class="col-12">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-danger">
                                                <b>Revisa la siguiente información</b>
                                            </span>
                                            @foreach ($errors->all() as $error)
                                                <small class="text-danger d-block">{{ $error }}</small>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- =========================
                                 CAMPOS TRIBUTARIOS
                            ========================== --}}

                            <div class="col-12"><h5 class="gasto-section-title"><i class="fa fa-file-invoice me-2" aria-hidden="true"></i>Información tributaria</h5></div><div class="col-12 col-md-4">
                                <label class="form-label">Sustento Tributario</label>
                                <select class="form-select"
                                    wire:model="sustento_tributario_id"
                                    {{ $bloquearTributarios ? 'disabled' : '' }}>
                                    <option value="">Seleccione</option>
                                    @foreach ($this->sustentoTributario as $sus)
                                        <option value="{{ $sus['id'] }}">
                                            {{ $sus['codigo_tributario'] }} - {{ $sus['nombre'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">Tipo de Comprobante</label>
                                <select class="form-select"
                                    wire:model="tipo_comprobante_id"
                                    {{ $bloquearTributarios ? 'disabled' : '' }}>
                                    <option value="">Seleccione</option>
                                    @foreach ($this->tipoComprobante as $tipo)
                                        <option value="{{ $tipo['id'] }}">
                                            {{ $tipo['codigo'] }} - {{ $tipo['nombre'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label"># Factura</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-file-invoice" aria-hidden="true"></i></span>
                                    <input type="number"
                                        class="form-control"
                                        placeholder="0000000001"
                                        wire:model="numero"
                                        {{ $bloquearTributarios ? 'readonly' : '' }}>
                                </div>
                            </div>


                            <div class="col-12 col-md-2">
                                <label class="form-label">Establecimiento</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-building" aria-hidden="true"></i></span>
                                    <input type="number"
                                        class="form-control"
                                        placeholder="001"
                                        wire:model="establecimiento"
                                        {{ $bloquearTributarios ? 'readonly' : '' }}>
                                </div>
                            </div>

                            <div class="col-12 col-md-2">
                                <label class="form-label">Punto de Emisión</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-map-marker-alt" aria-hidden="true"></i></span>
                                    <input type="number"
                                        class="form-control"
                                        placeholder="001"
                                        wire:model="punto_emision"
                                        {{ $bloquearTributarios ? 'readonly' : '' }}>
                                </div>
                            </div>

                            {{-- AUTORIZACIÓN --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label">Autorización</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-shield-alt" aria-hidden="true"></i></span>
                                    <input type="text"
                                        wire:model.defer="autorizacion"
                                        class="form-control"
                                        {{ ($bloquearTributarios || $bloquearAutorizacionProveedor) ? 'readonly' : '' }}>
                                </div>
                            </div>

                            {{-- FECHA AUTORIZACIÓN --}}
                            <div class="col-12 col-md-2">
                                <label class="form-label">Fecha de Autorización</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-calendar-check" aria-hidden="true"></i></span>
                                    <input type="date"
                                        wire:model.defer="fecha_autorizacion"
                                        class="form-control"
                                        {{ ($bloquearTributarios || $bloquearAutorizacionProveedor) ? 'readonly' : '' }}>
                                </div>
                            </div>

                            <hr class="w-100">

                            {{-- =========================
                                    VALOR GASTO
                                ========================== --}}
                                <div class="card-body p-0 table-responsive">
                                    <div class="col-12">
                                        <label class="form-label">Valor del gasto</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                    <i class="fa fa-dollar-sign" aria-hidden="true"></i>
                                                </span>
                                            <input type="number"
                                                class="form-control form-control-lg"
                                                step="0.01"
                                                min="0"
                                                placeholder="0.00"
                                                wire:model="subtotal_descuento">
                                        </div>
                                    </div>
                                </div>

                            <hr class="w-100">



                            {{-- =========================
                                    CUENTAS CONTABLES
                                ========================== --}}
                                <div class="col-12"><h5 class="gasto-section-title"><i class="fa fa-book me-2" aria-hidden="true"></i>Distribución contable</h5></div><div class="col-12 col-md-4">
                                    <label class="form-label">Cuenta contable</label>
                                    <select class="form-select" wire:model="gastos_plan_cuentas_id">
                                        <option value="">Seleccione</option>
                                        @foreach ($this->planCuentas as $plan)
                                            <option value="{{ $plan['id'] }}">{{ $plan['nombre'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label">Centro de costos</label>
                                    <select class="form-select" wire:model="gastos_centro_costos_id">
                                        <option value="">Seleccione</option>
                                        @foreach ($this->centroCostos as $centro)
                                            <option value="{{ $centro['id'] }}">{{ $centro['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-3">
                                    <label class="form-label">Valor</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                                <i class="fas fa-dollar-sign"></i>
                                            </span>
                                        <input type="number"
                                            class="form-control"
                                            step="0.01"
                                            min="0"
                                            placeholder="0.00"
                                            wire:model="gastos_detalle_valor">
                                    </div>
                                </div>

                                <div class="col-12 mt-2 mb-3">
                                    <button type="button"
                                        class="btn btn-outline-secondary btn-sm"
                                        wire:click="aumentarCuenta({{ $this->id_aprobar }})">
                                        <i class="fa fa-plus me-1"></i> Agregar cuenta
                                    </button>
                                </div>

                            {{-- =========================
                                    TABLA DE CUENTAS
                                ========================== --}}
                                <div class="card-body p-0 w-100 table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Cuenta</th>
                                                <th>C.C</th>
                                                <th>Valor</th>
                                                <th style="width: 40px">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($cuentasGastos as $cuenta)
                                                <tr>
                                                    <td>{{ $cuenta->planCuentas->nombre ?? '' }}</td>
                                                    <td>{{ $cuenta->centroCostos->name ?? '' }}</td>
                                                    <td>$ {{ number_format($cuenta->valor, 2, '.', ',') }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                                            wire:click="eliminarCuenta({{ $cuenta->id }})">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4">Agregue cuentas y valores</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2"><b>Total:</b></td>
                                                <td><b>$ {{ number_format($valorTotalCuentas, 2, '.', ',') }}</b></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                        @endif
                    </div>
                </div>

                {{-- FOOTER DE ACCIONES --}}
                <div class="modal-footer">
                    <div class="w-100">
                        <label for="gasto-razon-rechazo" class="form-label">Observación (obligatoria para rechazar)</label>
                        <div class="input-group">
                            <input id="gasto-razon-rechazo" type="text" class="form-control"
                                placeholder="Indica el motivo si rechazas el gasto..." wire:model="razon_rechaza">
                            <span class="input-group-text"><i class="fas fa-comment" aria-hidden="true"></i></span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-white me-auto" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" wire:click="aprobar({{ $this->id_aprobar }})">
                        <i class="fa fa-check-circle me-1" aria-hidden="true"></i> Aprobar gasto
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="denegar({{ $this->id_aprobar }})">
                        <i class="fa fa-times-circle me-1" aria-hidden="true"></i> Rechazar gasto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    {{-- MODAL ASIENTOS --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral3" aria-labelledby="modalGeneral3-title" tabindex="-1" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalGeneral3-title"><i class="fa fa-book me-2"></i> Asientos contables </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body"><div class="gasto-modal-intro">Consulta el detalle de las cuentas y abre el comprobante de cada asiento.</div>
                        @if ($errors->any())
                        <div class="alert alert-warning" role="alert">
                            <h5>Verifica estas observaciones.</h5>
                            @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        @endif
                        <div class="table-responsive mt-3">
                            <table class="table table-hover align-middle gasto-modal-table" style="border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th>Asiento</th>
                                        <th>Fecha Contable</th>
                                        <th>CC</th>
                                        <th>Código</th>
                                        <th>Cuenta</th>
                                        <th>Debe</th>
                                        <th>Haber</th>
                                        <th>Comprobante</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($asientosHeader as $a)
                                    <tr class="bg-primary text-white">
                                        <td colspan="7">
                                            <b>
                                                {{
                                                    isset($a->customerMovimiento->typeTransaction->name) ?
                                                     $a->customerMovimiento->typeTransaction->name :
                                                      'FACTURA DE PROVEEDORES'
                                                    }} -
                                                {{ $a->concepto->nombre }}
                                                <i>({{ $a->concepto->tipoConcepto->nombre }})</i>
                                            </b>
                                        </td>
                                        <td style="border: 1px solid #dee2e6; text-align: center;">
                                            <a class="text-white" title="Imprimir comprobante contable" aria-label="Imprimir comprobante contable"
                                                href="/asientos/comprobante/{{ $a->id }}"> <i
                                                    class="fa fa-print"></i></a>
                                        </td>
                                    </tr>
                                    @foreach ($a->detalles as $detalle)
                                    <tr>
                                        <td style="border: 1px solid #dee2e6;" class="align-middle text-start">
                                            <b>{{ $detalle->asientos_header_id }}</b>
                                        </td>
                                        <td style="border: 1px solid #dee2e6;" class="text-center">
                                            {{ $detalle->fecha_contable }}
                                        </td>
                                        <td style="border: 1px solid #dee2e6;" class="text-center">
                                            <b>{{ isset($detalle->sedesCentroCostos->sede->name) ? $detalle->sedesCentroCostos->sede->name : '' }}</b> - {{ isset($detalle->SedesCentroCostos->centroCostos->name) ? $detalle->SedesCentroCostos->centroCostos->name : '' }}
                                        </td>
                                        <td style="border: 1px solid #dee2e6;" class="text-start">
                                            <b>{{ $detalle->planCuentas->codigo }}</b>
                                        </td>
                                        <td style="border: 1px solid #dee2e6;" class="text-start">
                                            {{ $detalle->planCuentas->nombre }}
                                        </td>
                                        @if ($detalle->debe_haber)
                                        <td style="border: 1px solid #dee2e6;" class="align-middle text-center">
                                            <b>$ {{ $detalle->valor }}</b>
                                        </td>
                                        <td style="border: 1px solid #dee2e6;" class="align-middle text-center">
                                        </td>
                                        @else
                                        <td style="border: 1px solid #dee2e6;" class="align-middle text-center">
                                        </td>
                                        <td style="border: 1px solid #dee2e6;" class="align-middle text-center">
                                            <b>$ {{ $detalle->valor }}</b>
                                        </td>
                                        @endif
                                        <td style="border: 1px solid #dee2e6;">

                                        </td>
                                    </tr>
                                    @endforeach

                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL FP --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral4" aria-labelledby="modalGeneral4-title" tabindex="-1" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalGeneral4-title"><i class="fa fa-credit-card me-2"></i> Formas de pago </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form>
                    <div class="modal-body"><div class="gasto-modal-intro">Registra los pagos del gasto y los datos del comprobante cuando corresponda.</div>
                        @if ($errors->any())
                        <div class="alert alert-warning" role="alert">
                            <h5>Verifica estas observaciones.</h5>
                            @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-12"><h5 class="gasto-section-title"><i class="fa fa-credit-card me-2" aria-hidden="true"></i>Nuevo pago</h5></div><div class="col-12 col-md-4">
                                <label class="form-label">Forma de pago</label>
                                <select class="form-select" wire:model="forma_pago_id">
                                    <option> Seleccione </option>
                                    @foreach ($this->formasPago as $fp)
                                    <option value="{{ $fp['id'] }}">{{ $fp['nombre'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Fecha</label>
                                <input type="date" class="form-control"
                                    wire:model="fecha_creacion">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Valor</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    <input type="number" class="form-control" step="0.01" placeholder="0.00"
                                        wire:model="fp_valor">
                                </div>
                            </div>
                            <div class="col-12 mt-2 mb-3">
                                <button type="button" class="btn btn-primary"
                                    wire:click="aumentarFP()"><i class="fa fa-plus me-1"></i> Agregar pago</button>
                            </div>
                        </div>

                        @if($mostrarTransferencia)
                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label class="form-label">Banco</label>
                                <select class="form-select" wire:model="banco_id">
                                    <option value="">Seleccione Banco</option>
                                    @foreach($bancos as $banco)
                                    <option value="{{ $banco->id }}">
                                        {{ $banco->nombre }} - {{ $banco->numero_cuenta }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label"># Comprobante</label>
                                <input type="text" class="form-control"
                                    wire:model="numero_comprobante"
                                    placeholder="Ingrese número de comprobante">
                            </div>

                        </div>
                        @endif


                        <hr class="w-100">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fa fa-credit-card me-2"></i> Pagos registrados</h5>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-hover align-middle gasto-modal-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>

                                            <th>Forma Pago</th>
                                            <th>Comprobante</th>
                                            <th>Valor</th>
                                            <th style="width: 40px">Acción</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($gastoFormaPagos as $fp)
                                        <tr>
                                            <td>{{$fp->id}}</td>
                                            <td><i>{{$fp->formaPago->nombre}}</i></td>

                                            <td>
                                                @if($fp->forma_pago_id == 3)
                                                <i>{{$fp->numero_comprobante}}</i>
                                                @else
                                                <i></i>
                                                @endif
                                            </td>

                                            <td><b>$ {{$fp->valor}}</b></td>
                                            <td>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" title="Eliminar pago" aria-label="Eliminar pago" wire:click="confirmarQuitarFP({{$fp->id}})"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Aún no hay pagos registrados</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL RET --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral5" aria-labelledby="modalGeneral5-title" tabindex="-1" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalGeneral5-title"><i class="fa fa-file-invoice-dollar me-2"></i> Retenciones </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body"><div class="gasto-modal-intro">Selecciona la retención y revisa la base y el valor calculado antes de agregarla.</div>
                        @if ($errors->any())
                        <div class="alert alert-warning" role="alert">
                            <h5>Verifica estas observaciones.</h5>
                            @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-12"><h5 class="gasto-section-title"><i class="fa fa-file-invoice-dollar me-2" aria-hidden="true"></i>Nueva retención</h5></div><div class="col-12 col-md-6">
                                <label class="form-label">Retenciones</label>
                                <select class="form-select" wire:model="lista_retencion_id" wire:change="verTipoRetencion()">
                                    <option value="0"> - Seleccione - </option>
                                    @foreach ($this->listaRetenciones as $ret)
                                    <option value="{{ $ret['id'] }}">{{ $ret['name'] }} - {{ $ret['codigo'] }} - {{ $ret['porcentaje'] }}% - {{ $ret['nombre'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label">Base</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    <input type="number" class="form-control" step="0.01" placeholder="0.00"
                                        wire:model="ret_valor" readonly>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label">Resultado</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    <input type="number" class="form-control" step="0.01" placeholder="0.00"
                                        wire:model="ret_calculo" readonly>
                                </div>
                            </div>
                            <div class="col-12 mt-2 mb-3">
                                <button type="button" class="btn btn-primary"
                                    wire:click="aumentarRET()"><i class="fa fa-plus me-1"></i> Agregar retención</button>
                            </div>
                        </div>
                        <hr class="w-100">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fa fa-file-invoice-dollar me-2"></i> Retenciones registradas</h5>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-hover align-middle gasto-modal-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>Retención</th>
                                            <th>Base</th>
                                            <th>Valor</th>
                                            <th style="width: 40px">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($gastoRetenciones as $ret)
                                        <tr>
                                            <td>{{$ret->id}}</td>
                                            <td><i>{{ \Illuminate\Support\Str::limit($ret->listaRetencion->nombre, 50, '...') }}</i></td>
                                            <td><b>$ {{$ret->valor}}</b></td>
                                            <td><b>$ {{$ret->calculo}}</b></td>
                                            <td>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="quitarRET({{$ret->id}})" title="Eliminar retención" aria-label="Eliminar retención"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Aún no hay retenciones registradas</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:load', function() {
        $('#fileInput').on('change', function(e) {
            let fileNames = Array.from(e.target.files).map(file => file.name).join(', ');
            $(this).next('.custom-file-label').html(fileNames || 'Seleccionar archivos XML...');
        });
    });

    window.addEventListener('notificarEliminar', function(event) {
        var id = event.detail.id;
        console.log(id, event);
        Swal.fire({
            title: 'Confirmar Eliminación',
            text: '¿Estás seguro de que deseas eliminar este Gasto?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                Livewire.emit('elminarGasto', id);
            }
        });
    });

    window.addEventListener('confirmarEliminarFormaPago', function(event) {
        Swal.fire({
            title: 'Confirmar eliminación',
            text: '¿Está seguro de eliminar esta forma de pago y su asiento contable?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                Livewire.emit('quitarFormaPago', event.detail.id);
            }
        });
    });
</script>
