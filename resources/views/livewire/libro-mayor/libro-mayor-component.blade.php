<div>
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary mb-2">
                <div class="card-header py-2">
                    <h3 class="card-title mb-0">
                        <i class="fa fa-filter"></i> Filtros
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body py-3">
                    <div class="row">
                        <div class="col-sm-6 col-lg-2 mb-2">
                            <label class="mb-1">Fecha inicio</label>
                            <input type="date" class="form-control form-control-sm" wire:model="fechaInicio">
                        </div>
                        <div class="col-sm-6 col-lg-2 mb-2">
                            <label class="mb-1">Fecha fin</label>
                            <input type="date" class="form-control form-control-sm" wire:model="fechaFin">
                        </div>
                        <div class="col-sm-6 col-lg-2 mb-2">
                            <label class="mb-1">Asiento</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-hashtag"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="ID o varios IDs"
                                    wire:model.debounce.500ms="searchAsiento">
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 mb-2">
                            <label class="mb-1">Cuenta</label>
                            <div class="position-relative">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escriba codigo o nombre"
                                        wire:model.debounce.300ms="search">
                                    @if ($cuentaFiltro)
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary"
                                            wire:click="limpiarCuentaFiltro" title="Quitar cuenta">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                    @endif
                                </div>
                                @if ($planCuentasBuscador->count() > 0)
                                <div class="list-group position-absolute w-100 shadow-sm"
                                    style="z-index: 1050; max-height: 240px; overflow-y: auto;">
                                    @foreach ($planCuentasBuscador as $cuenta)
                                    <button type="button"
                                        data-bs-toggle="modal" data-bs-target="#modalGeneral1"
                                        class="list-group-item list-group-item-action py-2"
                                        wire:click="seleccionarCuentaFiltro({{ $cuenta->id }})">
                                        <strong>{{ $cuenta->codigo }}</strong>
                                        <span class="text-muted">- {{ $cuenta->nombre }}</span>
                                    </button>
                                    @endforeach
                                </div>
                                @elseif (trim($search) !== '' && !$cuentaFiltro)
                                <div class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050;">
                                    <div class="list-group-item py-2 text-muted">
                                        No hay cuentas con ese criterio.
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-1 mb-2">
                            <label class="mb-1">Manual</label>
                            <select class="form-control form-control-sm" wire:model="manual">
                                <option value="">Todos</option>
                                <option value="1">Si</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg-1 mb-2 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-block"
                                wire:click="limpiarFiltros" title="Limpiar filtros">
                                <i class="fa fa-eraser"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 mb-2">
            <div class="info-box mb-0">
                <span class="info-box-icon bg-primary"><i class="fa fa-book"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Asientos en pagina</span>
                    <span class="info-box-number">{{ $asientosHeader->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-4 mb-2">
            <div class="info-box mb-0">
                <span class="info-box-icon bg-success"><i class="fa fa-arrow-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Debe en pagina</span>
                    <span class="info-box-number">{{ number_format($totalDebePagina, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-4 mb-2">
            <div class="info-box mb-0">
                <span class="info-box-icon bg-info"><i class="fa fa-arrow-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Haber en pagina</span>
                    <span class="info-box-number">{{ number_format($totalHaberPagina, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 mb-2">
            <div class="card card-default h-100">
                <div class="card-header py-2">
                    <h3 class="card-title mb-0">
                        <i class="fa fa-list"></i> Plan de cuentas
                    </h3>
                </div>
                <div class="card-body p-2">
                    <div class="input-group input-group-sm mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Filtrar cuentas"
                            wire:model.debounce.300ms="searchCuentaPlan">
                    </div>

                    @if ($cuentaFiltro)
                    <div class="alert alert-info py-2 px-2 mb-2">
                        <div class="small text-muted">Cuenta seleccionada</div>
                        <div class="font-weight-bold">{{ $cuentaFiltroTexto }}</div>
                        <button type="button" class="btn btn-xs btn-outline-secondary mt-2"
                            wire:click="limpiarCuentaFiltro">
                            <i class="fa fa-times"></i> Quitar filtro
                        </button>
                    </div>
                    @endif

                    <div class="list-group" style="max-height: 560px; overflow-y: auto;">
                        @forelse ($planCuentas as $cuentaPlan)
                        <button type="button"
                            data-bs-toggle="modal" data-bs-target="#modalGeneral1"
                            class="list-group-item list-group-item-action py-2 {{ $cuentaFiltro == $cuentaPlan->id ? 'active' : '' }}"
                            wire:key="cuenta-plan-{{ $cuentaPlan->id }}"
                            wire:click="seleccionarCuentaFiltro({{ $cuentaPlan->id }})">
                            <div class="d-flex justify-content-between align-items-start">
                                <strong>{{ $cuentaPlan->codigo }}</strong>
                                @if ($cuentaFiltro == $cuentaPlan->id)
                                <i class="fa fa-check"></i>
                                @endif
                            </div>
                            <div class="{{ $cuentaFiltro == $cuentaPlan->id ? '' : 'text-muted' }} small">
                                {{ $cuentaPlan->nombre }}
                            </div>
                        </button>
                        @empty
                        <div class="list-group-item text-muted py-3 text-center">
                            No hay cuentas con ese criterio.
                        </div>
                        @endforelse
                    </div>

                    @if ($planCuentas->count() >= 80)
                    <small class="text-muted d-block mt-2">
                        Se muestran las primeras 80 cuentas. Use el filtro para precisar.
                    </small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card card-default">
                <div class="card-header py-2">
                    <h3 class="card-title mb-0">
                        <i class="fa fa-book"></i> Libro Mayor
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-sm table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 70px;">Asiento</th>
                                <th style="min-width: 115px;">Fecha contable</th>
                                <th style="min-width: 150px;">Usuario</th>
                                <th style="min-width: 240px;">Cuenta debe</th>
                                <th style="min-width: 240px;">Cuenta haber</th>
                                <th class="text-end" style="min-width: 115px;">Debe</th>
                                <th class="text-end" style="min-width: 115px;">Haber</th>
                                <th class="text-center" style="width: 80px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($asientosHeader as $a)
                            <tr class="bg-primary text-white">
                                <td colspan="3">
                                    <div class="d-flex flex-wrap align-items-center">
                                        <span class="badge bg-light me-2">#{{ $a->id }}</span>
                                        <strong>{{ $a->nombre_transaccion }}</strong>
                                        <span class="mx-1">-</span>
                                        <span>{{ optional($a->concepto)->nombre ?? 'Sin concepto' }}</span>
                                        <em class="ms-1">({{ optional(optional($a->concepto)->tipoConcepto)->nombre ?? 'Sin tipo' }})</em>
                                        @if ($a->manual)
                                        <span class="badge bg-warning ms-2">Manual</span>
                                        @endif
                                    </div>
                                    <small>Observacion: {{ $a->descripcion ?: 'Sin observacion' }}</small>
                                </td>
                                <td colspan="2" class="align-middle text-end"><strong>Total asiento:</strong></td>
                                <td class="align-middle text-end">
                                    <strong>{{ number_format($a->suma_debe, 2) }}</strong>
                                </td>
                                <td class="align-middle text-end">
                                    <strong>{{ number_format($a->suma_haber, 2) }}</strong>
                                </td>
                                <td class="align-middle text-center">
                                    <a class="text-white" href="/asientos/comprobante/{{ $a->id }}"
                                        title="Imprimir comprobante">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                            @foreach ($a->detalles as $det)
                            <tr class="{{ $cuentaFiltro == $det->plan_cuentas_id ? 'table-warning' : '' }}">
                                <td class="align-middle text-center">{{ $det->asientos_header_id }}</td>
                                <td>{{ $det->fecha_contable }}</td>
                                <td>
                                    {{ optional($det->userCreated)->firstname }}
                                    {{ optional($det->userCreated)->lastname }}
                                </td>
                                @if ($det->debe_haber)
                                <td>
                                    <strong>{{ optional($det->planCuentas)->codigo ?? 'Sin cuenta' }}</strong>
                                    - {{ optional($det->planCuentas)->nombre ?? 'Cuenta no encontrada' }}
                                </td>
                                <td></td>
                                <td class="text-end">{{ number_format($det->valor, 2) }}</td>
                                <td class="text-end">0.00</td>
                                @else
                                <td></td>
                                <td>
                                    <strong>{{ optional($det->planCuentas)->codigo ?? 'Sin cuenta' }}</strong>
                                    - {{ optional($det->planCuentas)->nombre ?? 'Cuenta no encontrada' }}
                                </td>
                                <td class="text-end">0.00</td>
                                <td class="text-end">{{ number_format($det->valor, 2) }}</td>
                                @endif
                                <td></td>
                            </tr>
                            @endforeach
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No se encontraron asientos con los filtros seleccionados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-2">
                    {{ $asientosHeader->links() }}
                </div>
            </div>
        </div>
    </div>


    {{-- MODAL LISTADO --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral1" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Cuenta {{$this->nombreCuenta}}</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form>
                    <div class="modal-body">



                        <div class="card mb-4">

                            <div class="card-body">
                                <table class="table table-bordered" role="table" style="font-size: 12px;">


                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col">Asiento</th>
                                            <th scope="col">Fecha contable</th>
                                            <th scope="col">Usuario</th>
                                            <th scope="col">Cuenta debe</th>
                                            <th scope="col">Cuenta haber</th>
                                            <th scope="col">Debe</th>
                                            <th scope="col">Haber</th>
                                        </tr>
                                    </thead>



                                    <tbody>
                                        @php
                                        $totalDebe = 0;
                                        $totalHaber = 0;
                                        @endphp


                                        @forelse ($asientosHeader as $a)
                                        @foreach ($a->detalles as $det)
                                        @if( optional($det->planCuentas)->codigo == $this->codigoComparar)
                                        <tr>
                                            <td class="align-middle text-center">{{ $det->asientos_header_id }}</td>
                                            <td>{{ $det->fecha_contable }}</td>
                                            <td>
                                                {{ optional($det->userCreated)->firstname }}
                                                {{ optional($det->userCreated)->lastname }}
                                            </td>
                                            @if ($det->debe_haber)
                                            <td>
                                                <strong>{{ optional($det->planCuentas)->codigo ?? 'Sin cuenta' }}</strong>
                                                - {{ optional($det->planCuentas)->nombre ?? 'Cuenta no encontrada' }}
                                            </td>
                                            <td></td>
                                            <td class="text-end">{{ number_format($det->valor, 2) }}</td>
                                            <td class="text-end">0.00</td>

                                            @php
                                            $totalDebe += $det->valor;
                                            @endphp


                                            @else
                                            <td></td>
                                            <td>
                                                <strong>{{ optional($det->planCuentas)->codigo ?? 'Sin cuenta' }}</strong>
                                                - {{ optional($det->planCuentas)->nombre ?? 'Cuenta no encontrada' }}
                                            </td>
                                            <td class="text-end">0.00</td>
                                            <td class="text-end">{{ number_format($det->valor, 2) }}</td>
                                            @php
                                            $totalHaber += $det->valor;
                                            @endphp
                                            @endif
                                        </tr>
                                        @endif
                                        @endforeach
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                No se encontraron asientos con los filtros seleccionados.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-end">Totales:</th>
                                            <th class="text-end">${{ number_format($totalDebe, 2) }}</th>
                                            <th class="text-end">$ {{ number_format($totalHaber, 2) }}</th>
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