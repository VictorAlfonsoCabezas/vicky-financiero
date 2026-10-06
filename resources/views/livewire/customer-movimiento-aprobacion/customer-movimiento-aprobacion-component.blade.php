<div class="approval-module">
    <div class="mb-4">
        <h1 class="page-header mb-1"><i class="fa fa-clipboard-check text-primary me-2" aria-hidden="true"></i> Aprobación de movimientos</h1>
        <p class="text-muted mb-0">Revisa los comprobantes y procesa las solicitudes de los clientes.</p>
    </div>
    <div class="panel panel-inverse mb-4">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="fa fa-filter me-2" aria-hidden="true"></i> Buscar solicitudes</h4>
        </div>
        <div class="panel-body">
            <div class="row g-3">
                <div class="col-12 col-md-8"><label class="form-label" for="approval-search">Cliente, cuenta o comprobante</label>
                    <div class="input-group"><span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span><input type="search" id="approval-search" class="form-control" wire:model.debounce.350ms="search" placeholder="Nombre, apellido, identificación, cuenta o comprobante"></div>
                </div>
                <div class="col-12 col-md-4"><label class="form-label" for="approval-state">Estado</label><select id="approval-state" class="form-select" wire:model="estadoFiltro">
                        <option value="PENDIENTE">Pendientes</option>
                        <option value="APROBADO">Aprobadas</option>
                        <option value="RECHAZADO">Rechazadas</option>
                        <option value="">Todos los estados</option>
                    </select></div>
            </div>
        </div>
    </div>
    @error('solicitud')<div class="alert alert-warning" role="alert">{{ $message }}</div>@enderror
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="fa fa-list me-2" aria-hidden="true"></i> Solicitudes <span class="badge bg-primary ms-2">{{ $movimientos->total() }}</span></h4><span wire:loading wire:target="search,estadoFiltro,page,aceptar" role="status"><i class="fa fa-spinner fa-spin me-1" aria-hidden="true"></i> Actualizando...</span>
        </div>
        <div class="panel-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 approval-table">
                    <thead>
                        <tr>
                            <th class="text-end">Valor</th>
                            <th>Entidad bancaria</th>
                            <th>Cliente</th>
                            <th>Cuenta</th>
                            <th>Fecha de solicitud</th>
                            <th>Observación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movimientos as $mov)
                        <tr wire:key="solicitud-{{ $mov->id }}">
                            <td class="text-end text-nowrap fw-bold">$ {{ number_format((float) $mov->valor, 2, '.', ',') }}</td>
                            <td>{{ $mov->banco ?: 'Sin banco registrado' }}<small class="d-block text-muted">{{ $mov->numero_cuenta }}</small></td>
                            <td class="fw-bold">{{ $mov->nombres }} {{ $mov->apellidos }}<small class="d-block text-muted fw-normal">{{ $mov->numero_documento }}</small></td>
                            <td>{{ $mov->customerTipoAhorro->tipoAhorros->name ?? 'No disponible' }}<small class="d-block text-muted">{{ $mov->cuenta_numero }}</small></td>
                            <td class="text-nowrap">{{ $mov->fecha_creacion }}</td>
                            <td class="approval-observation">{{ $mov->observacion }}</td>
                            <td><span class="badge {{ $mov->estado === 'PENDIENTE' ? 'bg-warning text-dark' : ($mov->estado === 'APROBADO' ? 'bg-success' : 'bg-danger') }}">{{ $mov->estado }}</span>@if ($mov->estado === 'RECHAZADO')<small class="d-block text-muted mt-1">{{ $mov->razon_rechazado }}</small>@endif</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1 approval-actions">
                                    <button type="button" class="btn btn-outline-primary btn-sm" wire:click="abrirModal({{ $mov->id }})" data-bs-toggle="modal" data-bs-target="#modalGeneral1"><i class="fa fa-eye me-1" aria-hidden="true"></i> Detalles</button>
                                    @if ($mov->estado === 'PENDIENTE')
                                    <button type="button" class="btn btn-success btn-sm" data-confirmar-aprobacion="{{ $mov->id }}" data-cliente="{{ $mov->nombres }} {{ $mov->apellidos }}" data-valor="{{ number_format((float) $mov->valor, 2, '.', ',') }}" wire:loading.attr="disabled" wire:target="aceptar,storeRechazar"><i class="fa fa-check-circle me-1" aria-hidden="true"></i> Aprobar</button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" wire:click="seleccionar({{ $mov->id }})" data-bs-toggle="modal" data-bs-target="#modalGeneral" wire:loading.attr="disabled" wire:target="aceptar,storeRechazar"><i class="fa fa-times-circle me-1" aria-hidden="true"></i> Rechazar</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5"><i class="fa fa-clipboard-check fa-2x text-muted d-block mb-3" aria-hidden="true"></i>
                                <h5>No hay solicitudes para estos filtros</h5>
                                <p class="text-muted mb-0">Prueba con otro texto o selecciona otro estado.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 border-top"><small class="text-muted">Mostrando {{ $movimientos->firstItem() ?? 0 }}–{{ $movimientos->lastItem() ?? 0 }} de {{ $movimientos->total() }} solicitudes</small>
                <div>{{ $movimientos->links() }}</div>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" id="modalGeneral1" tabindex="-1" aria-labelledby="approval-details-title" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="approval-details-title"><i class="fa fa-file-invoice text-primary me-2" aria-hidden="true"></i> Detalles de la solicitud</h4><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div wire:loading wire:target="abrirModal" class="alert alert-info" role="status">Cargando detalles...</div>
                    <div wire:loading.remove wire:target="abrirModal">
                        <div class="approval-amount mb-4"><small class="text-muted d-block">Valor solicitado</small>
                            <h3 class="mb-0">$ {{ number_format((float) $valor, 2, '.', ',') }}</h3>
                        </div>
                        <dl class="row g-3 mb-4">
                            <dt class="col-sm-4">Solicitante</dt>
                            <dd class="col-sm-8">{{ $user_nombres ?: 'No disponible' }}</dd>
                            <dt class="col-sm-4">Fecha de solicitud</dt>
                            <dd class="col-sm-8">{{ $fecha_creacion }}</dd>
                            <dt class="col-sm-4">Banco / cuenta</dt>
                            <dd class="col-sm-8">{{ $banco ?: 'Sin banco registrado' }} {{ $numero_cuenta }}</dd>
                            <dt class="col-sm-4">Comprobante</dt>
                            <dd class="col-sm-8">{{ $comprobante ?: 'No registrado' }}</dd>
                            <dt class="col-sm-4">Número de depósito</dt>
                            <dd class="col-sm-8">{{ $numero_deposito ?: 'No registrado' }}</dd>
                            <dt class="col-sm-4">Observación</dt>
                            <dd class="col-sm-8">{{ $observacionTransferencia ?: 'Sin observación' }}</dd>
                        </dl>
                        @if ($path)
                        <div class="border rounded p-3">
                            <h5><i class="fa fa-paperclip me-1" aria-hidden="true"></i> Comprobante adjunto</h5><a href="{{ asset($path) }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm mb-3"><i class="fa fa-external-link-alt me-1" aria-hidden="true"></i> Abrir comprobante</a><img class="img-fluid d-block mx-auto" src="{{ asset($path) }}" alt="Comprobante de la solicitud">
                        </div>
                        @else
                        <div class="alert alert-light mb-0"><i class="fa fa-paperclip me-1" aria-hidden="true"></i> Esta solicitud no tiene un comprobante adjunto.</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-white" data-bs-dismiss="modal">Cerrar</button></div>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" id="modalGeneral" tabindex="-1" aria-labelledby="approval-reject-title" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="approval-reject-title"><i class="fa fa-times-circle text-danger me-2" aria-hidden="true"></i> Rechazar solicitud</h4><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form wire:submit.prevent="storeRechazar">
                    <div class="modal-body">
                        <p class="text-muted">Indica el motivo por el que rechazas la solicitud #{{ $movimiento }}.</p><label class="form-label" for="approval-reason">Motivo del rechazo</label><textarea id="approval-reason" rows="4" class="form-control @error('razon_rechazo') is-invalid @enderror" wire:model.defer="razon_rechazo" maxlength="1000" required placeholder="Describe el motivo del rechazo"></textarea>@error('razon_rechazo')<div class="invalid-feedback">{{ $message }}</div>@enderror @error('solicitud')<div class="alert alert-warning mt-3" role="alert">{{ $message }}</div>@enderror
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-white me-auto" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger" wire:loading.attr="disabled" wire:target="storeRechazar,seleccionar"><i class="fa fa-times-circle me-1" aria-hidden="true"></i> Confirmar rechazo</button></div>
                </form>
            </div>
        </div>
    </div>
</div>