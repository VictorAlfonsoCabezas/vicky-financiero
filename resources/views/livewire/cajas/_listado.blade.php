<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <p class="text-muted mb-0">Administra la apertura, el cierre y las solicitudes de valores de tus cajas.</p>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalGeneral" wire:click="abrirModal(0)"><i class="fa fa-plus me-1"></i> Abrir caja</button>
</div>

<div class="row g-3 mb-3">
    @foreach ([['total', 'Cajas en el período', 'blue', 'cash-register'], ['abiertas', 'Abiertas', 'teal', 'lock-open'], ['cerradas', 'Cerradas', 'gray-600', 'lock'], ['diferencias', 'Con diferencias', 'red', 'exclamation-circle']] as $indicador)
        <div class="col-6 col-xl-3"><div class="widget widget-stats bg-{{ $indicador[2] }} mb-0 cajas-indicador">
            <div class="stats-icon"><i class="fa fa-{{ $indicador[3] }}" aria-hidden="true"></i></div>
            <div class="stats-info"><h4>{{ $indicador[1] }}</h4><p>{{ number_format($resumen->{$indicador[0]} ?? 0) }}</p></div>
        </div></div>
    @endforeach
</div>

<div class="alert {{ $cajaAbierta > 0 ? 'alert-success' : 'alert-warning' }} d-flex align-items-center gap-2 mb-3" role="status">
    <i class="fa {{ $cajaAbierta > 0 ? 'fa-check-circle' : 'fa-info-circle' }} fs-5" aria-hidden="true"></i>
    <div><strong>{{ $cajaAbierta > 0 ? 'Tu caja de hoy está abierta' : 'No tienes una caja abierta para hoy' }}</strong><span class="d-block small">{{ $cajaAbierta > 0 ? 'Puedes continuar con tus operaciones y preparar el cierre cuando termines.' : 'Abre una caja para empezar a trabajar.' }}</span></div>
</div>

<div class="panel panel-inverse mb-3">
    <div class="panel-heading"><h4 class="panel-title"><i class="fa fa-filter me-2"></i>Filtros de consulta</h4></div>
    <div class="panel-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-6 col-xl-4"><label for="cajas-buscar" class="form-label">Buscar caja</label><div class="input-group"><span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span><input id="cajas-buscar" class="form-control" type="search" wire:model.debounce.350ms="search" placeholder="Código, número o usuario"></div></div>
            <div class="col-md-6 col-xl-3"><label for="cajas-estado" class="form-label">Estado</label><select id="cajas-estado" class="form-select" wire:model="status"><option value="ALL">Todas las cajas</option><option value="ABIERTA">Abiertas</option><option value="CERRADA">Cerradas</option></select></div>
            <div class="col-md-6 col-xl-2"><label for="cajas-desde" class="form-label">Desde</label><input id="cajas-desde" class="form-control" type="date" wire:model="fechaInicio" max="{{ $fechaFin }}"></div>
            <div class="col-md-6 col-xl-3"><label for="cajas-hasta" class="form-label">Hasta</label><input id="cajas-hasta" class="form-control" type="date" wire:model="fechaFin" min="{{ $fechaInicio }}"></div>
        </div>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
            <div class="form-check form-switch"><input id="cajas-diferencias" class="form-check-input" type="checkbox" wire:model="soloDescuadres"><label for="cajas-diferencias" class="form-check-label">Solo cajas con diferencias</label></div>
            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="limpiarFiltros"><i class="fa fa-undo me-1"></i> Restablecer filtros</button>
        </div>
        <small class="text-muted d-block mt-2">Los indicadores corresponden a la búsqueda y al período seleccionado.</small>
    </div>
</div>

<div class="panel panel-inverse">
    <div class="panel-heading"><h4 class="panel-title">Listado de cajas</h4><span class="badge bg-white text-dark">{{ number_format($cajas->total()) }} resultados</span></div>
    <div class="panel-body p-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom px-3 py-3">
            <span class="text-muted small" aria-live="polite"><span wire:loading.remove>Mostrando {{ $cajas->firstItem() ?? 0 }}–{{ $cajas->lastItem() ?? 0 }} de {{ $cajas->total() }}</span><span wire:loading><span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Actualizando…</span></span>
            <div class="d-flex align-items-center gap-2"><label for="cajas-pagina" class="text-muted small">Por página</label><select id="cajas-pagina" class="form-select form-select-sm w-auto" wire:model="porPagina"><option value="10">10</option><option value="25">25</option><option value="50">50</option></select></div>
        </div>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0 cajas-tabla">
            <thead class="table-light"><tr><th>Caja</th><th>Apertura</th><th>Cierre</th><th class="text-end">Valor inicial</th><th class="text-end">Total registrado</th><th class="text-end">Diferencia</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
                @forelse ($cajas as $caj)
                    @php $tieneDiferencia = abs(round((float) $caj->descuadre, 2)) > 0; @endphp
                    <tr wire:key="caja-{{ $caj->id }}" class="{{ $tieneDiferencia ? 'cajas-diferencia' : '' }}">
                        <td><strong>{{ $caj->code ?: 'Caja #' . $caj->id }}</strong><small class="d-block text-muted">#{{ $caj->id }}</small></td>
                        <td><span class="text-nowrap">{{ $caj->date_inicial }} <small>{{ $caj->hour_inicial }}</small></span><small class="d-block text-muted">{{ $caj->user_name_inicial ?: 'Sin usuario' }}</small></td>
                        <td>@if ($caj->date_finish)<span class="text-nowrap">{{ $caj->date_finish }} <small>{{ $caj->hour_finish }}</small></span><small class="d-block text-muted">{{ $caj->user_finish_name ?: 'Sin usuario' }}</small>@else<span class="text-muted">Sin cierre</span>@endif</td>
                        <td class="text-end text-nowrap">$ {{ number_format($caj->valor_inicial ?? 0, 2) }}</td>
                        <td class="text-end text-nowrap fw-semibold">$ {{ number_format($caj->total ?? 0, 2) }}</td>
                        <td class="text-end text-nowrap {{ $tieneDiferencia ? 'text-danger fw-bold' : 'text-muted' }}">$ {{ number_format($caj->descuadre ?? 0, 2) }}@if ($tieneDiferencia)<small class="d-block">Revisar diferencia</small>@endif</td>
                        <td><span class="badge {{ $caj->status === 'ABIERTA' ? 'bg-success' : 'bg-secondary' }}"><i class="fa {{ $caj->status === 'ABIERTA' ? 'fa-lock-open' : 'fa-lock' }} me-1" aria-hidden="true"></i>{{ $caj->status === 'ABIERTA' ? 'Abierta' : ($caj->status === 'CERRADA' ? 'Cerrada' : $caj->status) }}</span></td>
                        <td class="text-end">
                            @if (Auth::id() == $caj->user_inicial_id || Auth::user()->admin == 1)
                                <div class="d-flex justify-content-end flex-wrap gap-1">
                                    @if ($caj->status === 'ABIERTA')
                                        <button type="button" class="btn btn-outline-danger btn-sm" wire:click="cerrarCaja({{ $caj->id }})" data-bs-toggle="modal" data-bs-target="#modalGeneral2"><i class="fa fa-lock me-1"></i> Cerrar caja</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="solicitarValoresBoveda({{ $caj->id }})" data-bs-toggle="modal" data-bs-target="#modalGeneral3"><i class="fa fa-hand-holding-usd me-1"></i> Solicitar valores</button>
                                        @if ($company->caja_boveda)<button type="button" class="btn btn-outline-success btn-sm" wire:click="enviarBoveda({{ $caj->id }})" data-bs-toggle="modal" data-bs-target="#modalGeneral5"><i class="fa fa-paper-plane me-1"></i> Enviar a bóveda</button>@endif
                                    @elseif ($caj->status === 'CERRADA')
                                        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="exportarMovimeintos({{ $caj->id }})" wire:loading.attr="disabled"><i class="fa fa-file-excel me-1"></i> Exportar</button>
                                        <a href="{{ url('cajas/caja-comprobante/' . $caj->id) }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener" title="Comprobante de caja {{ $caj->code }}"><i class="fa fa-print me-1"></i> Comprobante</a>
                                        @if (Auth::user()->reversar_cajas)<button type="button" class="btn btn-outline-danger btn-sm" wire:click="reversarCaja({{ $caj->id }})" wire:loading.attr="disabled"><i class="fa fa-undo me-1"></i> Reabrir</button>@endif
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small">Sin acciones disponibles</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5"><i class="fa fa-cash-register fs-2 text-muted d-block mb-3" aria-hidden="true"></i><h5>No hay cajas para esta consulta</h5><p class="text-muted">Prueba otras fechas, un estado diferente o restablece los filtros.</p><button type="button" class="btn btn-outline-primary btn-sm" wire:click="limpiarFiltros">Restablecer filtros</button></td></tr>
                @endforelse
            </tbody>
        </table></div>
        <div class="border-top px-3 py-3">{{ $cajas->links() }}</div>
    </div>
</div>
