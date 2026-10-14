<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <p class="text-muted mb-0">Consulta los movimientos y revisa sus asientos contables en un solo lugar.</p>
    </div>
    <a href="{{ url('/asientos') }}" class="btn btn-outline-primary btn-sm"><i class="fa fa-book me-1"></i> Ir a contabilidad</a>
</div>

<div class="row g-3 mb-3">
    @foreach ([['', 'Todos los registros', $resumen->total, 'blue', 'list'], ['cuadrado', 'Asientos cuadrados', $resumen->cuadrados, 'teal', 'check-circle'], ['pendiente', 'Pendientes de revisión', $resumen->pendientes, 'red', 'exclamation-circle'], ['sin_asiento', 'Sin asiento', $resumen->sin_asiento, 'orange', 'file-alt']] as $indicador)
        <div class="col-6 col-xl-3">
            <button type="button" class="widget widget-stats bg-{{ $indicador[3] }} w-100 text-start border-0 mb-0 modulos-indicador {{ $estadoContable === $indicador[0] ? 'modulos-indicador-activo' : '' }}"
                wire:click="$set('estadoContable', '{{ $indicador[0] }}')" aria-pressed="{{ $estadoContable === $indicador[0] ? 'true' : 'false' }}">
                <span class="stats-icon"><i class="fa fa-{{ $indicador[4] }}" aria-hidden="true"></i></span>
                <span class="stats-info d-block"><span class="d-block mb-2">{{ $indicador[1] }}</span><span class="fs-3 fw-bold">{{ number_format($indicador[2] ?? 0) }}</span></span>
            </button>
        </div>
    @endforeach
</div>

<div class="panel panel-inverse mb-3">
    <div class="panel-heading"><h4 class="panel-title"><i class="fa fa-filter me-2"></i>Filtros de consulta</h4></div>
    <div class="panel-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-6 col-xl-4">
                <label class="form-label" for="modulos-buscar">Buscar movimiento</label>
                <div class="input-group"><span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span>
                    <input id="modulos-buscar" type="search" class="form-control" wire:model.debounce.350ms="buscarMovimiento" placeholder="Cliente, identificación, módulo o número">
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <label class="form-label" for="modulos-tipo">Módulo</label>
                <select id="modulos-tipo" class="form-select" wire:model="modulo_id">
                    <option value="0">Todos los módulos</option>
                    @foreach ($tipoTransacciones as $tipo)<option value="{{ $tipo->id }}">{{ $tipo->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-6 col-xl-2"><label class="form-label" for="modulos-inicio">Desde</label><input id="modulos-inicio" type="date" class="form-control" wire:model="fechaInicio" max="{{ $fechaFin }}">@error('fechaInicio')<small class="text-danger">{{ $message }}</small>@enderror</div>
            <div class="col-md-6 col-xl-2"><label class="form-label" for="modulos-fin">Hasta</label><input id="modulos-fin" type="date" class="form-control" wire:model="fechaFin" min="{{ $fechaInicio }}">@error('fechaFin')<small class="text-danger">{{ $message }}</small>@enderror</div>
            <div class="col-md-6 col-xl-4">
                <label class="form-label" for="modulos-estado">Estado contable</label>
                <select id="modulos-estado" class="form-select" wire:model="estadoContable"><option value="">Todos los estados</option><option value="pendiente">Pendiente de revisión</option><option value="sin_asiento">Sin asiento</option><option value="cuadrado">Cuadrado</option></select>
            </div>
            <div class="col-md-6 col-xl-8 d-flex flex-wrap justify-content-md-end gap-2">
                <button type="button" class="btn btn-outline-secondary" wire:click="limpiarFiltros"><i class="fa fa-undo me-1"></i> Restablecer filtros</button>
                <button type="button" class="btn btn-primary" wire:click="calcularTodosAsientos" wire:loading.attr="disabled" wire:target="calcularTodosAsientos" title="Recalcula todos los registros del período y módulo seleccionados">
                    <span wire:loading.remove wire:target="calcularTodosAsientos"><i class="fa fa-sync-alt me-1"></i> Recalcular período</span>
                    <span wire:loading wire:target="calcularTodosAsientos"><span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Recalculando…</span>
                </button>
            </div>
        </div>
        <small class="text-muted d-block mt-3">Los indicadores corresponden a la búsqueda, el módulo y las fechas. Recalcular período procesa todo el módulo en esas fechas.</small>
    </div>
</div>

<div class="panel panel-inverse">
    <div class="panel-heading"><h4 class="panel-title">Movimientos y asientos</h4><span class="badge bg-white text-dark">{{ number_format($modulos->total()) }} resultados</span></div>
    <div class="panel-body p-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 px-3 py-3 border-bottom">
            <span class="text-muted small" role="status" aria-live="polite"><span wire:loading.remove>Mostrando {{ $modulos->firstItem() ?? 0 }}–{{ $modulos->lastItem() ?? 0 }} de {{ $modulos->total() }}</span><span wire:loading><span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Actualizando…</span></span>
            <div class="d-flex align-items-center gap-2"><label for="modulos-pagina" class="small text-muted">Por página</label><select id="modulos-pagina" class="form-select form-select-sm w-auto" wire:model="porPagina"><option value="20">20</option><option value="50">50</option><option value="100">100</option></select></div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 modulos-tabla">
                <thead class="table-light"><tr><th>Referencia / fecha</th><th>Cliente</th><th>Módulo</th><th class="text-end">Valor</th><th>Estado contable</th><th class="text-end">Diferencia</th><th class="text-end">Acciones</th></tr></thead>
                <tbody>
                    @forelse ($modulos as $mod)
                        @php $pendiente = $mod->estado_contable === 'pendiente'; @endphp
                        <tr wire:key="modulo-{{ $mod->origen }}-{{ $mod->id ?? 'general' }}-{{ $mod->asiento_header_id ?? 'sin' }}" class="{{ $pendiente ? 'modulos-pendiente' : '' }}">
                            <td><span class="fw-bold">{{ $mod->origen === 'GENERAL' ? 'Asiento' : 'Movimiento' }} #{{ $mod->id ?? $mod->asiento_header_id }}</span><small class="d-block text-muted">{{ $mod->date_created }} <span class="text-nowrap">{{ $mod->hour_created }}</span></small></td>
                            <td><span class="fw-semibold">{{ $mod->customer_name ?: 'Sin cliente asociado' }}</span><small class="d-block text-muted">{{ $mod->customer_ruc ?: '—' }}</small></td>
                            <td><span class="badge bg-light text-dark border">{{ $mod->type_transaction_name ?: 'Sin módulo' }}</span></td>
                            <td class="text-end text-nowrap fw-semibold">$ {{ number_format($mod->valor_movimiento ?? 0, 2) }}</td>
                            <td>
                                <span class="badge {{ $pendiente ? 'bg-danger' : ($mod->estado_contable === 'cuadrado' ? 'bg-success' : 'bg-secondary') }}"><i class="fa {{ $pendiente ? 'fa-exclamation-circle' : ($mod->estado_contable === 'cuadrado' ? 'fa-check-circle' : 'fa-file-alt') }} me-1"></i>{{ $pendiente ? 'Pendiente de revisión' : ($mod->estado_contable === 'cuadrado' ? 'Cuadrado' : 'Sin asiento') }}</span>
                                @if ($mod->asiento_header_id)<small class="d-block text-muted mt-1">Asiento #{{ $mod->asiento_header_id }}</small>@endif
                            </td>
                            <td class="text-end text-nowrap {{ $pendiente ? 'text-danger fw-bold' : 'text-muted' }}">{{ $mod->asiento_header_id ? '$ ' . number_format(abs(($mod->suma_debe ?? 0) - ($mod->suma_haber ?? 0)), 2) : '—' }}</td>
                            <td class="text-end text-nowrap">
                                @if ($mod->asiento_header_id)
                                    <button type="button" class="btn btn-sm {{ $pendiente ? 'btn-outline-danger' : 'btn-outline-primary' }}" wire:click="abrirModal({{ $mod->asiento_header_id }})" data-bs-toggle="modal" data-bs-target="#modalGeneral"><i class="fa fa-eye me-1"></i> Revisar</button>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="abrirModalManual({{ $mod->id }})" data-bs-toggle="modal" data-bs-target="#modalManualAsiento"><i class="fa fa-plus me-1"></i> Crear asiento</button>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-secondary" wire:loading.attr="disabled"
                                    @if ($mod->origen === 'GENERAL') wire:click="recalcularAsientoGeneral({{ $mod->asiento_header_id }}, {{ $mod->type_transaction_id ?? 0 }})" @else wire:click="recalcularAsiento({{ $mod->id }})" @endif
                                    title="Recalcular este registro" aria-label="Recalcular {{ $mod->origen === 'GENERAL' ? 'asiento' : 'movimiento' }} {{ $mod->id ?? $mod->asiento_header_id }}"><i class="fa fa-sync-alt"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-5"><i class="fa fa-search fs-2 text-muted mb-3 d-block" aria-hidden="true"></i><h5>No hay movimientos para esta consulta</h5><p class="text-muted mb-3">Prueba otras fechas, otro estado o una búsqueda diferente.</p><button type="button" class="btn btn-outline-primary btn-sm" wire:click="limpiarFiltros">Restablecer filtros</button></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-3 border-top">{{ $modulos->links() }}</div>
    </div>
</div>
