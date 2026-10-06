<div class="liquidation-module">
    <ol class="breadcrumb float-xl-end">
        <li class="breadcrumb-item">Créditos</li>
        <li class="breadcrumb-item active">Solicitudes de liquidación</li>
    </ol>
    <h1 class="page-header">Solicitudes de liquidación <small>Revisión de pagos para cancelar créditos</small></h1>
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="fas fa-filter me-2"></i>Filtros de búsqueda</h4>
        </div>
        <div class="panel-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-7 col-md-6"><label for="liquidation-search" class="form-label">Cliente o crédito</label>
                    <div class="input-group"><span class="input-group-text"><i class="fas fa-search"></i></span><input id="liquidation-search" class="form-control" wire:model.debounce.350ms="buscarCedula" placeholder="Cédula, nombres, apellidos o código del crédito"></div>
                </div>
                <div class="col-lg-3 col-md-4"><label for="liquidation-status" class="form-label">Estado de solicitud</label><select id="liquidation-status" class="form-select" wire:model="estadoFiltro">
                        <option value="">Todos los estados</option>
                        <option value="3">Pendientes</option>
                        <option value="1">Aprobadas</option>
                        <option value="2">Rechazadas</option>
                    </select></div>
                <div class="col-lg-2 col-md-2"><button type="button" class="btn btn-white w-100" wire:click="limpiarFiltros"><i class="fas fa-eraser me-1"></i>Limpiar</button></div>
            </div>
        </div>
    </div>
    @error('solicitud')<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>@enderror
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="fas fa-file-invoice-dollar me-2"></i>Liquidaciones registradas</h4><span class="badge bg-secondary">{{ $liquidaciones->total() }} solicitudes</span>
        </div>
        <div class="panel-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 liquidation-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Crédito</th>
                            <th>Fecha de solicitud</th>
                            <th>Registrado por</th>
                            <th class="text-end">Transferencias</th>
                            <th class="text-end">Otros pagos</th>
                            <th class="text-end">Total solicitado</th>
                            <th>Revisado por</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($liquidaciones as $item)
                        <tr wire:key="liquidation-{{ $item->liquidacion_id }}-{{ $item->fecha_solicitud }}-{{ $item->hora_solicitud }}">
                            <td>
                                <div class="fw-bold">{{ $item->apellidos }} {{ $item->nombres }}</div><small class="text-muted">{{ $item->numero_documento }}</small>
                            </td>
                            <td><span class="text-primary fw-bold">{{ $item->codeFolderHeader }}</span></td>
                            <td class="text-nowrap">{{ $item->fecha_solicitud }}<small class="d-block text-muted">{{ $item->hora_solicitud }}</small></td>
                            <td>{{ $item->user_name ?: '—' }}</td>
                            <td class="text-end liquidation-money">$ {{ number_format($item->valor_transferencia, 2) }}</td>
                            <td class="text-end liquidation-money">$ {{ number_format($item->valor_otros, 2) }}</td>
                            <td class="text-end liquidation-money fw-bold">$ {{ number_format($item->valor_total, 2) }}</td>
                            <td>{{ $item->usuario_solicitud ?: '—' }}</td>
                            <td>@if($item->estado_solicitud == 3)<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pendiente</span>@elseif($item->estado_solicitud == 2)<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Rechazada</span>@else<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Aprobada</span>@endif</td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" class="btn btn-outline-primary btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#modalLiquidacion" wire:click="cargarDatosLiquidacion({{ $item->liquidacion_id }}, '{{ $item->fecha_solicitud }}', '{{ $item->hora_solicitud }}')" wire:loading.attr="disabled"><i class="fas fa-eye me-1"></i>Revisar</button>
                                    @if($item->estado_solicitud == 3)<button type="button" class="btn btn-success btn-sm text-nowrap" data-liquidation-action="aprobarSolicitud" data-credit="{{ $item->liquidacion_id }}" data-date="{{ $item->fecha_solicitud }}" data-time="{{ $item->hora_solicitud }}" data-total="{{ number_format($item->valor_total, 2) }}" wire:loading.attr="disabled"><i class="fas fa-check me-1"></i>Aprobar</button>@endif
                                </div>
                            </td>
                        </tr>
                        @empty<tr>
                            <td colspan="10" class="text-center py-5 text-muted"><i class="fas fa-inbox fa-2x d-block mb-3"></i>No hay solicitudes para los filtros seleccionados.</td>
                        </tr>@endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel-footer d-flex flex-wrap gap-2 justify-content-between align-items-center"><small class="text-muted">{{ $liquidaciones->firstItem() ?? 0 }}–{{ $liquidaciones->lastItem() ?? 0 }} de {{ $liquidaciones->total() }} solicitudes</small>{{ $liquidaciones->links() }}</div>
    </div>

    <div wire:ignore.self class="modal fade" id="modalLiquidacion" tabindex="-1" aria-labelledby="liquidation-title" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="liquidation-title"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Revisar liquidación</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div wire:loading wire:target="cargarDatosLiquidacion" class="text-center w-100 py-5 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Cargando solicitud…</div>
                    <div wire:loading.remove wire:target="cargarDatosLiquidacion">
                        @if($registro)
                        @error('solicitud')<div class="alert alert-danger" role="alert">{{ $message }}</div>@enderror
                        <div class="row g-4 mb-4">
                            <div class="col-lg-5">
                                <h6 class="liquidation-section"><i class="fas fa-user me-2"></i>Cliente y crédito</h6>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Cliente</dt>
                                    <dd class="col-sm-8">{{ $registro['apellidos'] }} {{ $registro['nombres'] }}</dd>
                                    <dt class="col-sm-4">Cédula</dt>
                                    <dd class="col-sm-8">{{ $registro['numero_documento'] }}</dd>
                                    <dt class="col-sm-4">Crédito</dt>
                                    <dd class="col-sm-8">{{ $registro['codeFolderHeader'] }}</dd>
                                    <dt class="col-sm-4">Monto original</dt>
                                    <dd class="col-sm-8">$ {{ number_format($registro['valor_solicitado'], 2) }}</dd>
                                </dl>
                            </div>
                            <div class="col-lg-4">
                                <h6 class="liquidation-section"><i class="fas fa-file-signature me-2"></i>Solicitud</h6>
                                <dl class="row mb-0">
                                    <dt class="col-sm-5">Registrado por</dt>
                                    <dd class="col-sm-7">{{ $registro['user_name'] ?: '—' }}</dd>
                                    <dt class="col-sm-5">Fecha</dt>
                                    <dd class="col-sm-7">{{ $registro['fecha_solicitud'] }} {{ $registro['hora_solicitud'] }}</dd>
                                    <dt class="col-sm-5">Revisado por</dt>
                                    <dd class="col-sm-7">{{ $registro['usuario_solicitud'] ?: '—' }}</dd>
                                    <dt class="col-sm-5">Revisión</dt>
                                    <dd class="col-sm-7">{{ $registro['fecha_aprobacion'] ?: '—' }} {{ $registro['hora_aprobacion'] }}</dd>
                                </dl>
                            </div>
                            <div class="col-lg-3">
                                <div class="liquidation-summary"><small>Total solicitado</small>
                                    <h3 class="my-2 liquidation-money">$ {{ number_format(collect($detallePagos)->sum('valor'), 2) }}</h3><span class="badge {{ $registro['estado_solicitud'] == 3 ? 'bg-warning text-dark' : ($registro['estado_solicitud'] == 2 ? 'bg-danger' : 'bg-success') }}">{{ $registro['estado_solicitud'] == 3 ? 'Pendiente' : ($registro['estado_solicitud'] == 2 ? 'Rechazada' : 'Aprobada') }}</span>
                                </div>
                            </div>
                        </div>
                        <h6 class="liquidation-section"><i class="fas fa-wallet me-2"></i>Formas de pago</h6>
                        <div class="table-responsive mb-4">
                            <table class="table align-middle liquidation-table">
                                <thead>
                                    <tr>
                                        <th>Forma de pago</th>
                                        <th>Banco</th>
                                        <th>Comprobante</th>
                                        <th>Estado</th>
                                        <th class="text-end">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detallePagos as $pago)<tr>
                                        <td><i class="fas {{ strtoupper(trim($pago->forma_pago)) === 'TRANSFERENCIA' ? 'fa-university' : 'fa-money-bill-wave' }} text-muted me-2"></i>{{ $pago->forma_pago }}</td>
                                        <td>{{ $pago->banco->nombre ?? '—' }}</td>
                                        <td>{{ $pago->numero_comprobante ?: '—' }}</td>
                                        <td><span class="badge {{ $pago->solicitado == 3 ? 'bg-warning text-dark' : ($pago->solicitado == 2 ? 'bg-danger' : 'bg-success') }}">{{ $pago->solicitado == 3 ? 'Pendiente' : ($pago->solicitado == 2 ? 'Rechazado' : 'Aprobado') }}</span></td>
                                        <td class="text-end liquidation-money">$ {{ number_format($pago->valor, 2) }}</td>
                                    </tr>@endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($registro['observacion'])<div class="alert {{ $registro['estado_solicitud'] == 2 ? 'alert-warning' : 'alert-secondary' }}"><i class="fas fa-comment-alt me-2"></i>{{ $registro['observacion'] }}</div>@endif
                        @if($mostrarFormularioRechazo && $registro['estado_solicitud'] == 3)
                        <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Se rechazarán las transferencias. Las otras formas de pago recibidas se conservarán como abono al crédito.</div>
                        <label for="liquidation-reason" class="form-label">Motivo del rechazo <span class="text-danger">*</span></label><textarea id="liquidation-reason" class="form-control @error('observacionRechazo') is-invalid @enderror" rows="3" maxlength="1000" wire:model.defer="observacionRechazo" placeholder="Explica por qué se rechaza la transferencia"></textarea>@error('observacionRechazo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                        @else<div class="text-center text-muted py-4">Selecciona una solicitud para revisar sus pagos.</div>@endif
                    </div>
                </div>
                <div class="modal-footer">
                    @if($registro && $registro['estado_solicitud'] == 3)
                    @if($mostrarFormularioRechazo)
                    <button type="button" class="btn btn-white" wire:click="$set('mostrarFormularioRechazo', false)" wire:loading.attr="disabled"><i class="fas fa-arrow-left me-1"></i>Cancelar rechazo</button>
                    <button type="button" class="btn btn-danger" data-liquidation-action="rechazarSolicitud" data-credit="{{ $liquidacionSeleccionada }}" data-date="{{ $registro['fecha_solicitud'] }}" data-time="{{ $registro['hora_solicitud'] }}" wire:loading.attr="disabled"><i class="fas fa-times-circle me-1"></i>Confirmar rechazo</button>
                    @else
                    @if(collect($detallePagos)->contains(function ($p) { return strtoupper(trim($p->forma_pago)) === 'TRANSFERENCIA'; }))<button type="button" class="btn btn-outline-danger" wire:click="abrirFormularioRechazo" wire:loading.attr="disabled"><i class="fas fa-times me-1"></i>Rechazar transferencia</button>@endif
                    <button type="button" class="btn btn-success" data-liquidation-action="aprobarSolicitud" data-credit="{{ $liquidacionSeleccionada }}" data-date="{{ $registro['fecha_solicitud'] }}" data-time="{{ $registro['hora_solicitud'] }}" data-total="{{ number_format(collect($detallePagos)->sum('valor'), 2) }}" wire:loading.attr="disabled"><i class="fas fa-check me-1"></i>Aprobar liquidación</button>
                    @endif
                    @endif
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>