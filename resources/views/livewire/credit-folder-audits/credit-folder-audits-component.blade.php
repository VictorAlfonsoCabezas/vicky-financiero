<div>
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Auditoría de créditos</h3>
        </div>

        <div class="card-body pb-2">
            <div class="row">
                <div class="col-md-4 col-lg-3 mb-2">
                    <label>Búsqueda</label>
                    <input type="text" class="form-control form-control-sm" wire:model.debounce.400ms="search"
                        placeholder="Código, usuario o descripción">
                </div>
                <div class="col-md-2 mb-2">
                    <label>ID crédito</label>
                    <input type="number" min="1" class="form-control form-control-sm" wire:model.debounce.400ms="headerId">
                </div>
                <div class="col-md-2 mb-2">
                    <label>ID cuota</label>
                    <input type="number" min="1" class="form-control form-control-sm" wire:model.debounce.400ms="detailId">
                </div>
                <div class="col-md-2 mb-2">
                    <label>Origen</label>
                    <select class="form-control form-control-sm" wire:model="source">
                        <option value="">Todos</option>
                        <option value="HEADER">Cabecera</option>
                        <option value="DETAIL">Cuota</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label>Evento</label>
                    <select class="form-control form-control-sm" wire:model="event">
                        <option value="">Todos</option>
                        <option value="CREATED">Creado</option>
                        <option value="UPDATED">Actualizado</option>
                        <option value="DELETED">Eliminado</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label>Acción</label>
                    <select class="form-control form-control-sm" wire:model="action">
                        <option value="">Todas</option>
                        <option value="CREDITO_CREADO">Crédito creado</option>
                        <option value="CREDITO_ACTUALIZADO">Crédito actualizado</option>
                        <option value="ESTADO_CREDITO_ACTUALIZADO">Estado del crédito actualizado</option>
                        <option value="CREDITO_ELIMINADO">Crédito eliminado</option>
                        <option value="CUOTA_CREADA">Cuota creada</option>
                        <option value="CUOTA_ACTUALIZADA">Cuota actualizada</option>
                        <option value="CUOTA_ELIMINADA">Cuota eliminada</option>
                        <option value="PAGO_REGISTRADO">Pago registrado</option>
                        <option value="PAGO_MODIFICADO">Pago modificado</option>
                        <option value="PAGO_ANULADO">Pago anulado</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label>ID usuario</label>
                    <input type="number" min="1" class="form-control form-control-sm" wire:model.debounce.400ms="userId">
                </div>
                <div class="col-md-2 mb-2">
                    <label>Desde</label>
                    <input type="date" class="form-control form-control-sm" wire:model="dateFrom">
                </div>
                <div class="col-md-2 mb-2">
                    <label>Hasta</label>
                    <input type="date" class="form-control form-control-sm" wire:model="dateTo">
                </div>
                <div class="col-md-1 mb-2">
                    <label>Filas</label>
                    <select class="form-control form-control-sm" wire:model="perPage">
                        <option value="15">15</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-default btn-sm btn-block" wire:click="clearFilters">
                        <i class="fas fa-eraser me-1"></i> Limpiar filtros
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th>Crédito</th>
                        <th>ID crédito</th>
                        <th class="text-center">Eventos encontrados</th>
                        <th>Última actividad</th>
                        <th class="text-center">Histórico completo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($audits as $audit)
                        <tr>
                            <td><strong>{{ $audit->code_folder_header ?: 'Sin código' }}</strong></td>
                            <td>{{ $audit->credit_folder_header_id ?: 'N/D' }}</td>
                            <td class="text-center"><span class="badge bg-secondary">{{ $audit->events_count }}</span></td>
                            <td class="text-nowrap">{{ $audit->last_activity ? \Carbon\Carbon::parse($audit->last_activity)->format('d/m/Y H:i:s') : '-' }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-primary btn-xs" wire:click="showCreditHistory({{ $audit->id }})"
                                    data-bs-toggle="modal" data-bs-target="#creditAuditDetailModal">
                                    <i class="fas fa-history"></i> Ver histórico
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No existen créditos para los filtros seleccionados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($audits->hasPages())
            <div class="card-footer clearfix">
                {{ $audits->links() }}
            </div>
        @endif
    </div>

    <div wire:ignore.self class="modal fade" id="creditAuditDetailModal" tabindex="-1" role="dialog"
        aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Histórico completo del crédito</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if ($selectedCredit)
                        <div class="alert alert-light border py-2">
                            <strong>Crédito:</strong> {{ $selectedCredit['code'] ?: 'Sin código' }}
                            <span class="ms-3"><strong>ID:</strong> {{ $selectedCredit['header_id'] ?: 'N/D' }}</span>
                            <span class="ms-3"><strong>Total de eventos:</strong> {{ count($creditHistory) }}</span>
                        </div>

                        <div id="creditAuditHistoryAccordion">
                        @forelse ($creditHistory as $history)
                            <div class="card card-outline {{ $history['source'] === 'HEADER' ? 'card-primary' : 'card-info' }} mb-3">
                                <div class="card-header py-2" id="auditHeading{{ $history['id'] }}">
                                    <button type="button" class="btn btn-link btn-block text-start p-0 text-dark"
                                        data-bs-toggle="collapse" data-bs-target="#auditCollapse{{ $history['id'] }}"
                                        aria-expanded="false" aria-controls="auditCollapse{{ $history['id'] }}">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <div>
                                            <span class="badge {{ $history['source'] === 'HEADER' ? 'bg-primary' : 'bg-info' }}">
                                                {{ $history['source'] === 'HEADER' ? 'Cabecera' : 'Cuota' }}
                                            </span>
                                            <strong class="ms-2">{{ str_replace('_', ' ', $history['action']) }}</strong>
                                            <span class="ms-2">
                                                <i class="fas fa-user text-muted"></i>
                                                <strong>Usuario:</strong> {{ $history['user_name'] ?: 'Sistema' }}
                                            </span>
                                            @if ($history['detail_id'])
                                                <span class="text-muted ms-2">ID cuota: {{ $history['detail_id'] }}</span>
                                            @endif
                                        </div>
                                        <div class="text-muted">
                                            {{ $history['created_at'] }}
                                            <i class="fas fa-chevron-down ms-2"></i>
                                        </div>
                                    </div>
                                    </button>
                                </div>
                                <div id="auditCollapse{{ $history['id'] }}" class="collapse"
                                    aria-labelledby="auditHeading{{ $history['id'] }}"
                                    data-bs-parent="#creditAuditHistoryAccordion">
                                <div class="card-body p-2">
                                    <p class="mb-2">
                                        {{ $history['description'] }}
                                        @if ($history['ip_address'])
                                            <small class="text-muted ms-2">IP: {{ $history['ip_address'] }}</small>
                                        @endif
                                    </p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="width: 25%">Campo</th>
                                                    <th style="width: 37.5%">Valor anterior</th>
                                                    <th style="width: 37.5%">Valor nuevo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($history['changes'] as $change)
                                                    <tr>
                                                        <td><code>{{ $change['attribute'] }}</code></td>
                                                        <td class="text-break">
                                                            @if ($change['has_old'])
                                                                {{ is_array($change['old']) ? json_encode($change['old'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : ($change['old'] === null ? 'NULL' : $change['old']) }}
                                                            @else
                                                                <span class="text-muted">No aplica</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-break">
                                                            @if ($change['has_new'])
                                                                {{ is_array($change['new']) ? json_encode($change['new'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : ($change['new'] === null ? 'NULL' : $change['new']) }}
                                                            @else
                                                                <span class="text-muted">No aplica</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="3" class="text-center text-muted">No hay valores almacenados.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">Este crédito no tiene eventos registrados.</div>
                        @endforelse
                        </div>
                    @else
                        <div class="text-center text-muted py-4">Cargando histórico...</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
