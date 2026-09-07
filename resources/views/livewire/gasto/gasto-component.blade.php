<div>
    <div class="row d-flex justify-content-between align-items-center mb-2 mt-2">
        <div class="col-auto">
            <button type="button" class="btn btn-primary" wire:click="abrirModal(0);" data-bs-toggle="modal"
                data-bs-target="#modalGeneral" title="Nuevo Gasto">
                <i class="fa fa-plus"></i>
            </button>
            <button wire:click="abrirModalXML(0)" type="button" data-bs-toggle="modal"
                data-bs-target="#modalGeneral1" type="button"
                class="btn btn-warning" title="Cargar XML de la Factura"><b>XML</b>
            </button>
        </div>

        <div class="col-auto d-flex gap-3">
            <div class="input-group me-4">
                <span class="input-group-text">Estado:</span>
                <select class="form-control" wire:model="estadoFiltro">
                    <option value="TODOS">TODOS</option>
                    <option value="BORRADOR">BORRADOR</option>
                    <option value="PENDIENTE">PENDIENTE</option>
                    <option value="APROBADO">APROBADO</option>
                    <option value="PAGADO">PAGADO</option>
                    <option value="RECHAZADO">RECHAZADO</option>
                </select>
            </div>

            <div class="input-group">
                <span class="input-group-text">Fecha:</span>
                <input type="date" wire:model="fecha_inicio" class="form-control">
                <input type="date" wire:model="fecha_fin" class="form-control">
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header" style="padding: 8px;">
                    <h5 class="card-title"><i class="fa fa-dollar-sign"></i> <b>Gastos</b></h5>
                </div>
                <div class="card-body table-responsive p-2">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr style="padding: 10px;">
                                <th style="width: 1%;">#</th>
                                <th>Categorias</th>
                                <th>Proveedor</th>
                                <th># Factura</th>
                                <th>Fecha Emisión</th>
                                <th>Subtotal</th>
                                <th>Descuento</th>
                                <th>Subtotal Descuento</th>
                                <th>Subtotales %</th>
                                <th>Iva %</th>
                                <th>TOTAL</th>
                                <th>Monto Pagado</th>
                                <th>Monto Retenciones</th>
                                <th>Monto Pendiente</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gastos as $gas)
                            <tr>
                                <td>{{ $gas->id }}</td>
                                <td><b>{{ $gas->categoria_nombre }}</b></td>
                                <td><i class="fa fa-truck"></i> <b>{{ $gas->nombreProveedor }}</b> - {{ $gas->ruc }}</td>
                                <td>{{ $gas->establecimiento }}-{{ $gas->punto_emision }}-{{ $gas->numero }}</td>
                                <td>{{ $gas->fecha_emision }}</td>
                                <td>{{ $gas->valor }}</td>
                                <td>{{ $gas->descuento }}</td>
                                <td>{{ $gas->subtotal_descuento }}</td>
                                <td>{{ $gas->suma_subtotal }}</td>
                                <td>{{ $gas->suma_iva }}</td>
                                <td>{{ $gas->total }}</td>
                                <td>{{ $gas->monto_pagado }}</td>
                                <td>{{ $gas->monto_retencion }}</td>
                                <td>
                                    <b style="color: {{ ($gas->valor_pendiente !== '0.00') ? 'red': '#212550' }};">$ {{ $gas->valor_pendiente }}</b>
                                </td>
                                <td>
                                    @if ($gas->estado == 'BORRADOR')
                                    <small class="badge bg-warning">{{ $gas->estado }}</small>
                                    @elseif ($gas->estado == 'PENDIENTE')
                                    <small class="badge badge-default">{{ $gas->estado }}</small>
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
                                        data-bs-target="#modalGeneral" type="button"
                                        class="btn bg-light btn-xs"><i class="fa fa-pen">
                                    </button>
                                    <button wire:click="confirmarBorrarGasto({{ $gas->id }})"
                                        class="btn bg-danger btn-xs"><i class="fa fa-trash"></i></button>
                                    @elseif ($gas->estado == 'PENDIENTE')
                                    <button wire:click="abrirModalXML({{ $gas->id }})" type="button" data-bs-toggle="modal"
                                        data-bs-target="#modalGeneral1" type="button"
                                        class="btn btn-warning btn-xs" title="Cargar XML de la Factura">XML</button>
                                    <button wire:click="abrirModal({{ $gas->id }})" type="button" data-bs-toggle="modal"
                                        data-bs-target="#modalGeneral" type="button"
                                        class="btn bg-light btn-xs"><i class="fa fa-pen"></i></button>


                                    <button wire:click="verGasto({{ $gas->id }})" type="button" data-bs-toggle="modal"
                                        data-bs-target="#modalGeneral2" type="button"
                                        class="btn bg-light btn-xs"><i class="fa fa-user-check"></i></button>


                                    <button wire:click="confirmarBorrarGasto({{ $gas->id }})"
                                        class="btn bg-danger btn-xs"><i class="fa fa-trash"></i></button>
                                    @elseif ($gas->estado == 'APROBADO')
                                    <a wire:click="abrirModal({{ $gas->id }})" type="button" data-bs-toggle="modal"
                                        data-bs-target="#modalGeneral" type="button"
                                        class="btn bg-light btn-xs"><i class="fa fa-eye"></i></a>

                                    <a class="btn btn-{{($gas->monto_pagado > 0) ? 'danger' : 'primary'}} btn-xs text-white"
                                        wire:click="abrirModalFP({{ $gas->id }})"
                                        data-bs-toggle="modal" data-bs-target="#modalGeneral4">FP</a>

                                    <a class="btn btn-{{($gas->monto_retencion > 0) ? 'danger' : 'primary'}} btn-xs text-white"
                                        wire:click="abrirModalRET({{ $gas->id }})"
                                        data-bs-toggle="modal" data-bs-target="#modalGeneral5">RET</a>

                                    <a class="btn btn-warning btn-xs text-white"
                                        wire:click="reversarPendiente({{ $gas->id }})"
                                        title="Reversar a Pendiente"><i class="fa fa-undo"></i></a>

                                    @if ($gas->valor_pendiente == '0.00')
                                    <a class="btn btn-success btn-xs text-white"
                                        wire:click="pagado({{ $gas->id }})"
                                        title="Enviar a Pagado"><i class="fa fa-dollar-sign"></i></a>
                                    @endif

                                    @elseif ($gas->estado == 'PAGADO')
                                    <a class="btn btn-primary btn-xs text-white"
                                        wire:click="abrirModalAsientos({{ $gas->id }})"
                                        data-bs-toggle="modal" data-bs-target="#modalGeneral3"><i class="fa fa-chair"></i></a>
                                    <a class="btn btn-default btn-xs" href="{{URL::to('gastos/ticket/' . $gas->id)}}"
                                        title="Ticket">
                                        <i class="far fa-file-pdf"></i>
                                    </a>
                                    <a class="btn btn-warning btn-xs text-white"
                                        wire:click="reversarAprobado({{ $gas->id }})"
                                        title="Reversar a Aprobado"><i class="fa fa-undo"></i>
                                    </a>
                                    @elseif ($gas->estado == 'RECHAZADO')
                                    <a class="btn btn-warning btn-xs text-white"
                                        wire:click="reversarPendiente({{ $gas->id }})"
                                        title="Reversar a Pendiente"><i class="fa fa-undo"></i></a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center" colspan="15">
                                    <button type="button" class="btn btn-primary btn-xs" wire:click="abrirModal(0);" data-bs-toggle="modal" data-bs-target="#modalGeneral" title="Nuevo Gasto">
                                        <i class="fa fa-plus"></i> Primer Gastos
                                    </button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 ms-3">
                    {{ $gastos->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-dollar-sign"></i> Gastos </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: gray;">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeGasto">
                    <div class="modal-body">
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
                        <div class="row mb-2">
                            <div class="col-7" style="margin-bottom: 10px;">
                                <label>Proveedores</label>
                                <a href="/proveedores" target="_blank">
                                    <i class="fa fa-plus-circle"></i>
                                </a>
                                <select class="form-control" wire:model="proveedor_id" {{($this->estado !== 'APROBADO') ? '' : 'readonly'}}>
                                    <option value="0"> Seleccione </option>
                                    @foreach ($this->proveedores as $prove)
                                    <option value="{{ $prove['id'] }}">{{ $prove['nombre'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-5" style="margin-bottom: 10px;">
                                <label>Categorias</label>
                                <a href="/gastos-categorias" target="_blank">
                                    <i class="fa fa-plus-circle"></i>
                                </a>
                                
                                <select class="form-control" wire:model="gastos_categorias_id" {{($this->estado !== 'APROBADO') ? '' : 'readonly'}}>
                                    <option value="0"> Seleccione una</option>
                                    @foreach ($this->categorias as $cat)
                                    <option value="{{ $cat['id'] }}">{{ $cat['nombre'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <hr class="w-100">
                            <div class="form-group col-md-4">
                                <label><b>Fecha de Emisión</b></label>
                                <input type="date"
                                    class="form-control form-control-sm"
                                    wire:model="fecha_emision"
                                    {{ ($this->estado === 'APROBADO') ? 'disabled' : '' }}>
                                @error('fecha_emision')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            

                            <hr class="w-100">
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label>SUBTOTAL:</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-dollar-sign"></i>
                                        </span>
                                    </div>

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
                                <div class="card-body mt-0 p-0">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <td class="text-end"><b>Subtotal Descuento: </b></td>
                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="number"
                                                            class="form-control form-control-sm"
                                                            step="0.01"
                                                            placeholder="0.00"
                                                            wire:model="subtotal_descuento"
                                                            readonly>
                                                    </div>
                                                </td>

                                                <td class="text-end">Descuento:</td>
                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="number"
                                                            class="form-control form-control-sm"
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
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="number"
                                                            class="form-control form-control-sm"
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
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <i class="fas fa-dollar-sign"></i>
                                                                </span>
                                                            </div>

                                                            <input type="number"
                                                                class="form-control form-control-sm"
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
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <i class="fas fa-dollar-sign"></i>
                                                                </span>
                                                            </div>

                                                            <input type="number"
                                                                class="form-control form-control-sm"
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
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="number"
                                                            class="form-control form-control-sm"
                                                            step="0.01"
                                                            placeholder="0.00"
                                                            wire:model="suma_subtotal"
                                                            readonly>
                                                    </div>
                                                </td>

                                                <td class="text-end"><b>Ivas: </b></td>
                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        </div>
                                                        <input type="number"
                                                            class="form-control form-control-sm"
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
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label>TOTALES:</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    </div>
                                    <input type="number" class="form-control form-control-lg" step="0.01" placeholder="0.00"
                                        wire:model="total" readonly>
                                </div>
                            </div>

                            <hr class="w-100">
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label>Observación: </label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-comments"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Descripcion"
                                        wire:model="descripcion" {{($this->estado !== 'APROBADO') ? '' : 'readonly'}}>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default me-auto" data-bs-dismiss="modal">
                            Cerrar
                        </button>

                        @if($this->estado !== 'APROBADO')
                            {{-- GUARDA EN BORRADOR --}}
                            <button type="submit" class="btn btn-primary">
                                Guardar
                            </button>

                            {{-- CAMBIA A PENDIENTE --}}
                            <button type="button"
                                class="btn btn-success text-white"
                                wire:click="solicitarAprobacion">
                                <i class="fa fa-thumbs-up"></i> Solicitar Aprobación
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL XML --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-file"></i> Cargar XML </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: gray;">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h2>Subir Archivos</h2>

                    <div class="form-group">
                        <label for="fileInput" class="font-weight-bold">📂 Cargar XML</label>
                        <input type="file" wire:model="xmlFile" class="form-control">
                        @error('xmlFile') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>



                    @if ($xmlData)
                    <div class="invoice p-3 mb-3">
                        {!! $htmlXml !!}
                        <div class="row">
                            <!-- accepted payments column -->
                            <div class="col-6">
                                <p class="lead">Métodos de Pago:</p>

                                {!! $htmlXmlTabla1 !!}
                                <table class="table table-bordered">

                                </table>
                            </div>
                            <!-- /.col -->
                            <div class="col-6">
                                <p class="lead">Desgloce</p>
                                <div class="table-responsive">
                                    {!! $htmlXmlTabla2 !!}

                                </div>
                            </div>
                        </div>

                        <div class="row no-print">
                            <div class="col-12">
                                <a type="button" class="btn btn-primary float-end text-white" style="margin-right: 5px;" wire:click="guardarGastos()">
                                    <i class="fas fa-download"></i> Guardar
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    {{-- MODAL APROBAR --}}
<div wire:ignore.self class="modal fade" id="modalGeneral2" tabindex="-1" role="dialog"
    aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-dollar-sign"></i> Contabilidad Gasto </h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: gray;">×</span>
                </button>
            </div>

            <form wire:submit.prevent="storeGasto">
                <div class="modal-body">
                    <div class="row mb-2">

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

                            <div class="col-4" style="margin-bottom: 10px;">
                                <label>Sustento Tributario</label>
                                <select class="form-control form-control-sm"
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

                            <div class="col-4" style="margin-bottom: 10px;">
                                <label>Tipo de Comprobante</label>
                                <select class="form-control form-control-sm"
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

                            <div class="col-4" style="margin-bottom: 10px;">
                                <label># Factura</label>
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-info"></i></span>
                                    </div>
                                    <input type="number"
                                        class="form-control form-control-sm"
                                        placeholder="0000000001"
                                        wire:model="numero"
                                        {{ $bloquearTributarios ? 'readonly' : '' }}>
                                </div>
                            </div>


                            <div class="col-2" style="margin-bottom: 10px;">
                                <label>Establecimiento</label>
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-info"></i></span>
                                    </div>
                                    <input type="number"
                                        class="form-control form-control-sm"
                                        placeholder="001"
                                        wire:model="establecimiento"
                                        {{ $bloquearTributarios ? 'readonly' : '' }}>
                                </div>
                            </div>

                            <div class="col-2" style="margin-bottom: 10px;">
                                <label>Punto de Emisión</label>
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-info"></i></span>
                                    </div>
                                    <input type="number"
                                        class="form-control form-control-sm"
                                        placeholder="001"
                                        wire:model="punto_emision"
                                        {{ $bloquearTributarios ? 'readonly' : '' }}>
                                </div>
                            </div>

                            {{-- AUTORIZACIÓN --}}
                            <div class="col-6" style="margin-bottom: 10px;">
                                <label>Autorización</label>
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-info"></i></span>
                                    </div>
                                    <input type="text"
                                        wire:model.defer="autorizacion"
                                        class="form-control form-control-sm"
                                        {{ ($bloquearTributarios || $bloquearAutorizacionProveedor) ? 'readonly' : '' }}>
                                </div>
                            </div>

                            {{-- FECHA AUTORIZACIÓN --}}
                            <div class="col-2" style="margin-bottom: 10px;">
                                <label>Fecha de Autorización</label>
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-info"></i></span>
                                    </div>
                                    <input type="date"
                                        wire:model.defer="fecha_autorizacion"
                                        class="form-control form-control-sm"
                                        {{ ($bloquearTributarios || $bloquearAutorizacionProveedor) ? 'readonly' : '' }}>
                                </div>
                            </div>

                            <hr class="w-100">

                            {{-- =========================
                                    VALOR GASTO
                                ========================== --}}
                                <div class="card-body p-0">
                                    <div class="col-12" style="margin-bottom: 10px;">
                                        <label>VALOR:</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-info"></i>
                                                </span>
                                            </div>
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
                                <div class="col-4" style="margin-bottom: 10px;">
                                    <label>Plan cuentas</label>
                                    <select class="form-control form-control-sm" wire:model="gastos_plan_cuentas_id">
                                        <option value="">Seleccione</option>
                                        @foreach ($this->planCuentas as $plan)
                                            <option value="{{ $plan['id'] }}">{{ $plan['nombre'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-4" style="margin-bottom: 10px;">
                                    <label>C. Costos</label>
                                    <select class="form-control form-control-sm" wire:model="gastos_centro_costos_id">
                                        <option value="">Seleccione</option>
                                        @foreach ($this->centroCostos as $centro)
                                            <option value="{{ $centro['id'] }}">{{ $centro['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-3" style="margin-bottom: 10px;">
                                    <label>Valor</label>
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-dollar-sign"></i>
                                            </span>
                                        </div>
                                        <input type="number"
                                            class="form-control form-control-sm"
                                            step="0.01"
                                            min="0"
                                            placeholder="0.00"
                                            wire:model="gastos_detalle_valor">
                                    </div>
                                </div>

                                <div class="col-1 mt-4 p-2">
                                    <a type="button"
                                        class="btn btn-default btn-sm"
                                        wire:click="aumentarCuenta({{ $this->id_aprobar }})">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>

                            {{-- =========================
                                    TABLA DE CUENTAS
                                ========================== --}}
                                <div class="card-body p-0 w-100">
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
                                                        <a class="btn btn-default btn-sm"
                                                            wire:click="eliminarCuenta({{ $cuenta->id }})">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
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
                    <div class="input-group mb-3">
                        <input type="text"
                            class="form-control"
                            placeholder="Observación obligatoria si se deniega..."
                            wire:model="razon_rechaza">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-comment"></i></span>
                        </div>
                    </div>

                    <a class="btn btn-primary text-white"
                        wire:click="aprobar({{ $this->id_aprobar }})">
                        <i class="fa fa-thumbs-up"></i> Aprobar
                    </a>

                    <a class="btn btn-danger text-white"
                        wire:click="denegar({{ $this->id_aprobar }})">
                        <i class="fa fa-thumbs-down"></i> Denegar
                    </a>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

    {{-- MODAL ASIENTOS --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral3" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
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
                                        <!-- <th>Acción</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($asientosHeader as $a)
                                    <tr>
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
                                            <a class="text-white"
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
                                    </tr>
                                    @endforeach
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

    {{-- MODAL FP --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral4" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Formas de Pago </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form>
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
                            <div class="col-4" style="margin-bottom: 10px;">
                                <label>Forma Pago</label>
                                <select class="form-control form-control-sm" wire:model="forma_pago_id">
                                    <option> Seleccione </option>
                                    @foreach ($this->formasPago as $fp)
                                    <option value="{{ $fp['id'] }}">{{ $fp['nombre'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3" style="margin-bottom: 10px;">
                                <label>Fecha</label>
                                <input type="date" class="form-control form-control-sm"
                                    wire:model="fecha_creacion">
                            </div>
                            <div class="col-3" style="margin-bottom: 10px;">
                                <label>Valor</label>
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    </div>
                                    <input type="number" class="form-control form-control-sm" step="0.01" placeholder="0.00"
                                        wire:model="fp_valor">
                                </div>
                            </div>
                            <div class="col-1 mt-4 p-2">
                                <a type="submit" class="btn btn-default btn-sm"
                                    wire:click="aumentarFP()"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>

                        @if($mostrarTransferencia)
                        <div class="row">

                            <div class="col-6">
                                <label>Banco</label>
                                <select class="form-control form-control-sm" wire:model="banco_id">
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
                                    wire:model="numero_comprobante"
                                    placeholder="Ingrese número de comprobante">
                            </div>

                        </div>
                        @endif


                        <hr class="w-100">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Lista Formas de Pago</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm">
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
                                                <a class="btn btn-default btn-sm" wire:click="confirmarQuitarFP({{$fp->id}})"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Sin Formas de pago</td>
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
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL RET --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral5" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Retenciones </h4>
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
                        <div class="row">
                            <div class="col-5" style="margin-bottom: 10px;">
                                <label>Retenciones</label>
                                <select class="form-control form-control-sm" wire:model="lista_retencion_id" wire:change="verTipoRetencion()">
                                    <option value="0"> - Seleccione - </option>
                                    @foreach ($this->listaRetenciones as $ret)
                                    <option value="{{ $ret['id'] }}">{{ $ret['name'] }} - {{ $ret['codigo'] }} - {{ $ret['porcentaje'] }}% - {{ $ret['nombre'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3" style="margin-bottom: 10px;">
                                <label>Base</label>
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    </div>
                                    <input type="number" class="form-control form-control-sm" step="0.01" placeholder="0.00"
                                        wire:model="ret_valor" readonly>
                                </div>
                            </div>
                            <div class="col-3" style="margin-bottom: 10px;">
                                <label>Resultado</label>
                                <div class="input-group mb-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    </div>
                                    <input type="number" class="form-control form-control-sm" step="0.01" placeholder="0.00"
                                        wire:model="ret_calculo" readonly>
                                </div>
                            </div>
                            <div class="col-1 mt-4 p-2">
                                <a type="submit" class="btn btn-default btn-sm"
                                    wire:click="aumentarRET()"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                        <hr class="w-100">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Lista Retenciones</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm">
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
                                                <a class="btn btn-default btn-sm" wire:click="quitarRET({{$ret->id}})" title="Eliminar"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Sin Retenciones</td>
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
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
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
