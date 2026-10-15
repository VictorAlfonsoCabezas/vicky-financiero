<div class="reporte-cuentas">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <p class="text-muted mb-0">Consulta las cuentas de ahorro, revisa sus saldos y gestiona las aprobaciones pendientes.</p>
        <button type="button" class="btn btn-success" wire:click="export" wire:loading.attr="disabled" wire:target="export">
            <span wire:loading.remove wire:target="export"><i class="fa fa-file-excel me-2" aria-hidden="true"></i>Exportar Excel</span>
            <span wire:loading wire:target="export"><span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Preparando archivo…</span>
        </button>
    </div>

    <div class="panel panel-inverse mb-3">
        <div class="panel-heading"><h4 class="panel-title"><i class="fa fa-filter me-2" aria-hidden="true"></i>Filtros de consulta</h4></div>
        <div class="panel-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-6 col-xl-7">
                    <label for="reporte-cuentas-buscar" class="form-label">Buscar socio</label>
                    <div class="input-group"><span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span><input type="search" id="reporte-cuentas-buscar" wire:model.debounce.350ms="search" class="form-control" placeholder="Nombres, apellidos o documento"></div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <label for="reporte-cuentas-tipo" class="form-label">Tipo de cuenta</label>
                    <select id="reporte-cuentas-tipo" wire:model="tipo_cuenta" class="form-select">
                        <option value="0">Todos los tipos</option>
                        @foreach ($tipoAhorros as $ahorros)<option value="{{ $ahorros->id }}">{{ $ahorros->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-xl-2"><button type="button" class="btn btn-outline-secondary w-100" wire:click="limpiarFiltros"><i class="fa fa-undo me-1" aria-hidden="true"></i>Restablecer</button></div>
            </div>
            <small class="text-muted d-block mt-3">La exportación incluye las cuentas que coinciden con los filtros seleccionados.</small>
        </div>
    </div>

    <div class="panel panel-inverse">
        <div class="panel-heading"><h4 class="panel-title">Listado de cuentas</h4><span class="badge bg-white text-dark">{{ number_format($cuentas->total()) }} resultados</span></div>
        <div class="panel-body p-0">
            <div class="px-3 py-3 border-bottom text-muted small" role="status" aria-live="polite">
                <span wire:loading.remove wire:target="search,tipo_cuenta,page,limpiarFiltros">Mostrando {{ $cuentas->firstItem() ?? 0 }}–{{ $cuentas->lastItem() ?? 0 }} de {{ $cuentas->total() }} cuentas</span>
                <span wire:loading wire:target="search,tipo_cuenta,page,limpiarFiltros"><span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Actualizando resultados…</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 reporte-cuentas-tabla">
                    <thead class="table-light"><tr><th scope="col">Cuenta</th><th scope="col">Tipo de cuenta</th><th scope="col">Creación</th><th scope="col">Propietario</th><th scope="col" class="text-end">Saldo</th><th scope="col">Estado</th><th scope="col" class="text-end">Acciones</th></tr></thead>
                    <tbody>
                        @forelse ($cuentas as $cuenta)
                            <tr wire:key="reporte-cuenta-{{ $cuenta->id }}">
                                <td class="fw-semibold text-nowrap">{{ $cuenta->codigo }}</td>
                                <td>{{ $cuenta->tipoAhorro }}</td>
                                <td class="text-nowrap">{{ $cuenta->created_at ? $cuenta->created_at->format('d/m/Y') : '—' }}</td>
                                <td><strong class="d-block">{{ $cuenta->cliente }}</strong><small class="text-muted">{{ $cuenta->numero_documento ?: 'Sin documento' }}</small></td>
                                <td class="text-end text-nowrap fw-semibold {{ (float) $cuenta->saldo < 0 ? 'text-danger' : '' }}">$ {{ number_format((float) $cuenta->saldo, 2) }}</td>
                                <td><span class="badge {{ $cuenta->status == 1 ? 'bg-success' : 'bg-warning text-dark' }}">{{ $cuenta->status == 1 ? 'Aprobada' : 'Pendiente' }}</span></td>
                                <td><div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#modalCuenta" wire:click="verCuenta({{ $cuenta->id }})" wire:loading.attr="disabled"><i class="fa fa-eye me-1" aria-hidden="true"></i>Ver detalle</button>
                                    @if ($cuenta->status == 0)<button type="button" class="btn btn-outline-success btn-sm" wire:click="aprobarCuenta({{ $cuenta->id }})" wire:loading.attr="disabled">Aprobar</button>@endif
                                </div></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-5"><i class="fa fa-search fs-2 text-muted mb-3 d-block" aria-hidden="true"></i><h5>No se encontraron cuentas</h5><p class="text-muted">Prueba otro nombre, documento o tipo de cuenta.</p><button type="button" class="btn btn-outline-primary btn-sm" wire:click="limpiarFiltros">Restablecer filtros</button></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($cuentas->hasPages())<div class="border-top px-3 py-3">{{ $cuentas->links() }}</div>@endif
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="modalCuenta" tabindex="-1" aria-labelledby="modalCuentaTitulo" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"><div class="modal-content">
            <div class="modal-header"><div><h5 class="modal-title" id="modalCuentaTitulo"><i class="fa fa-wallet text-primary me-2" aria-hidden="true"></i>Detalle de cuenta</h5><p class="text-muted small mb-0 mt-1">Información del propietario y datos de la cuenta de ahorro.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
            <div class="modal-body">
                <div class="text-center text-muted py-4" wire:loading wire:target="verCuenta" role="status"><span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Cargando detalle de la cuenta…</div>
                <div wire:loading.remove wire:target="verCuenta">
                @if ($cuentaSeleccionada)
                    <div class="border rounded p-3 mb-4 d-flex flex-wrap justify-content-between gap-3 bg-light">
                        <div><small class="text-muted d-block">Cuenta {{ $cuentaSeleccionada->codigo }}</small><strong class="fs-5 d-block">{{ $cuentaSeleccionada->tipoAhorro }}</strong><span class="badge {{ $cuentaSeleccionada->status == 1 ? 'bg-success' : 'bg-warning text-dark' }} mt-2">{{ $cuentaSeleccionada->status == 1 ? 'Aprobada' : 'Pendiente de aprobación' }}</span></div>
                        <div><small class="text-muted d-block">Saldo</small><strong class="fs-3">$ {{ number_format((float) $cuentaSeleccionada->saldo, 2) }}</strong></div>
                    </div>
                    <h6 class="mb-3">Propietario y forma de pago</h6>
                    <dl class="row g-3 mb-0">
                        <div class="col-sm-6"><dt class="small text-muted fw-normal">Propietario</dt><dd class="mb-0 fw-semibold">{{ $cuentaSeleccionada->cliente }}</dd></div>
                        <div class="col-sm-6"><dt class="small text-muted fw-normal">Documento</dt><dd class="mb-0">{{ $cuentaSeleccionada->numero_documento ?: 'Sin documento' }}</dd></div>
                        <div class="col-sm-6"><dt class="small text-muted fw-normal">Forma de pago</dt><dd class="mb-0">{{ $cuentaSeleccionada->forma_pago_nombre }}</dd></div>
                        @if (in_array($cuentaSeleccionada->forma_pago_id, [2, 3]))
                            <div class="col-sm-6"><dt class="small text-muted fw-normal">Banco</dt><dd class="mb-0">{{ $cuentaSeleccionada->banco_nombre ?: 'Sin registrar' }}</dd></div>
                            <div class="col-sm-6"><dt class="small text-muted fw-normal">Comprobante</dt><dd class="mb-0">{{ $cuentaSeleccionada->comprobante ?: 'Sin registrar' }}</dd></div>
                            <div class="col-sm-6"><dt class="small text-muted fw-normal">Número de depósito</dt><dd class="mb-0">{{ $cuentaSeleccionada->numero_deposito ?: 'Sin registrar' }}</dd></div>
                        @endif
                    </dl>
                @endif
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>@if ($cuentaSeleccionada && $cuentaSeleccionada->status == 0)<button type="button" class="btn btn-success" wire:click="aprobarCuenta({{ $cuentaSeleccionada->id }})" wire:loading.attr="disabled" wire:loading.class="d-none" wire:target="verCuenta"><i class="fa fa-check me-1" aria-hidden="true"></i>Aprobar cuenta</button>@endif</div>
        </div></div>
    </div>
</div>
