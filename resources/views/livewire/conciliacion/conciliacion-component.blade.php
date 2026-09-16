<div>
    <ol class="breadcrumb float-xl-end">
        <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Conciliación bancaria</li>
    </ol>
    <h1 class="page-header">Conciliación bancaria <small>Reporte de movimientos</small></h1>
    <div class="panel panel-inverse">
        <div class="panel-heading"><h4 class="panel-title">Filtros del reporte</h4></div>
        <div class="panel-body">
            <form wire:submit.prevent="applyFilters">
                <div class="row g-3">
                    <div class="col-md-3 col-xl-2"><label for="cb-desde" class="form-label">Desde</label><input id="cb-desde" type="date" class="form-control" wire:model.lazy="filters.desde" required></div>
                    <div class="col-md-3 col-xl-2"><label for="cb-hasta" class="form-label">Hasta</label><input id="cb-hasta" type="date" class="form-control" wire:model.lazy="filters.hasta" required></div>
                    <div class="col-md-6 col-xl-8">
                        <label for="cb-banco" class="form-label">Banco / cuenta bancaria</label>
                        <select id="cb-banco" class="form-select" wire:model.lazy="filters.banco">
                            <option value="">Todos (con y sin banco)</option>
                            <option value="sin_banco">Sin banco / cuenta identificada (incluye efectivo)</option>
                            <option value="bancarios">Solo movimientos con banco / cuenta identificada</option>
                            @foreach ($bancos as $banco)<option value="{{ $banco->id }}">{{ $banco->nombre }} · {{ $banco->numero_cuenta }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-xl-3"><label for="cb-forma" class="form-label">Forma de pago</label><select id="cb-forma" class="form-select" wire:model.lazy="filters.forma"><option value="">Todas</option>@foreach ($formas as $forma)<option value="{{ $forma->id }}">{{ $forma->nombre }}</option>@endforeach</select></div>
                    <div class="col-md-4 col-xl-3"><label for="cb-tipo" class="form-label">Tipo de movimiento</label><select id="cb-tipo" class="form-select" wire:model.lazy="filters.tipo"><option value="">Todos</option>@foreach ($tipos as $tipo)<option value="{{ $tipo->id }}">{{ $tipo->name }}</option>@endforeach</select></div>
                    <div class="col-md-4 col-xl-2"><label for="cb-estado" class="form-label">Estado</label><select id="cb-estado" class="form-select" wire:model.lazy="filters.estado"><option value="activos">Activos</option><option value="anulados">Anulados</option><option value="todos">Todos</option></select></div>
                    <div class="col-xl-4"><label for="cb-buscar" class="form-label">Buscar</label><input id="cb-buscar" class="form-control" wire:model.debounce.400ms="filters.buscar" maxlength="120" placeholder="Cliente, identificación, comprobante o referencia"></div>
                </div>
                @if ($errors->any())<div class="alert alert-danger mt-3" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <button class="btn btn-default" type="button" wire:click="clearFilters" wire:loading.attr="disabled">Limpiar</button>
                    <button class="btn btn-success ms-md-auto" type="button" wire:click="exportExcel" wire:loading.attr="disabled"><i class="fa fa-file-excel me-1"></i> Excel</button>
                    <button class="btn btn-outline-danger" type="button" wire:click="exportPdf" wire:loading.attr="disabled"><i class="fa fa-file-pdf me-1"></i> PDF</button>
                </div>
            </form>
        </div>
    </div>
    <div wire:loading class="alert alert-info" role="status"><span class="spinner-border spinner-border-sm me-2"></span>Preparando reporte…</div>
    <div class="row">
        @foreach ([['Movimientos', number_format($totales->cantidad), 'primary'], ['Entradas registradas', number_format($totales->entradas, 2), 'success'], ['Salidas registradas', number_format($totales->salidas, 2), 'danger'], ['Neto del período', number_format($totales->entradas - $totales->salidas, 2), 'dark']] as $resumen)
            <div class="col-sm-6 col-xl-3"><div class="panel panel-inverse"><div class="panel-body"><div class="text-muted mb-2">{{ $resumen[0] }}</div><div class="fs-24px fw-bold text-{{ $resumen[2] }}">{{ $resumen[1] }}</div></div></div></div>
        @endforeach
    </div>
    <p class="text-muted">Período consultado: <strong>{{ $applied['desde'] }} al {{ $applied['hasta'] }}</strong>. Los totales incluyen únicamente movimientos activos con banco/cuenta identificada y dirección de entrada o salida. El neto no es el saldo del extracto bancario.</p>
    @if ($totales->revisar > 0)<div class="alert alert-warning">{{ $totales->revisar }} movimiento(s) sin banco/cuenta identificada o sin dirección reconocida. Sus valores no se incluyen en los totales bancarios.</div>@endif
    <div class="panel panel-inverse">
        <div class="panel-heading"><h4 class="panel-title">Detalle de movimientos</h4><span class="badge bg-primary">{{ $movimientos->total() }} registros</span></div>
        <div class="panel-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead><tr><th>Fecha</th><th>Banco / cuenta</th><th>Comprobante / referencia</th><th>Cliente</th><th>Concepto / forma de pago</th><th class="text-end">Valor</th><th>Dirección</th><th>Estado / detalle</th></tr></thead>
                    <tbody>
                        @forelse ($movimientos as $movimiento)
                            <tr wire:key="cb-mov-{{ $movimiento->id }}">
                                <td class="text-nowrap">{{ $movimiento->date_created }}<small class="d-block text-muted">{{ $movimiento->hour_created }}</small></td>
                                <td>{{ $movimiento->banco_nombre ?? 'Sin banco/cuenta identificada' }}<small class="d-block text-muted">{{ $movimiento->banco_cuenta }}</small></td>
                                <td><strong>{{ $movimiento->comprobante ?: $movimiento->code }}</strong><small class="d-block">Ref.: {{ $movimiento->numero_deposito ?: 'Sin referencia' }}</small></td>
                                <td>{{ $movimiento->customer_name ?: '—' }}<small class="d-block text-muted">{{ $movimiento->customer_ruc }}</small></td>
                                <td>{{ $movimiento->type_transaction_name }}<small class="d-block text-muted">{{ $movimiento->forma_pago_name ?: 'Sin forma de pago' }}</small></td>
                                <td class="text-end text-nowrap fw-bold">{{ number_format($movimiento->valor_movimiento, 2) }}</td>
                                <td>{{ $movimiento->type_transaction_action === 'S' ? 'Entrada' : ($movimiento->type_transaction_action === 'R' ? 'Salida' : 'Por revisar') }}</td>
                                <td><span class="badge bg-{{ $movimiento->status ? 'success' : 'secondary' }}">{{ $movimiento->status ? 'Activo' : 'Anulado' }}</span>
                                    <details class="mt-2"><summary class="text-primary">Ver detalle</summary><div class="small mt-2"><strong>Código:</strong> {{ $movimiento->code }}<br><strong>Comprobante:</strong> {{ $movimiento->comprobante ?: '—' }}<br><strong>Observación:</strong> {{ $movimiento->observation ?: 'Sin observación' }}@if (!$movimiento->status)<br><strong>Anulación:</strong> {{ $movimiento->date_cancel }} {{ $movimiento->razon_cancel }}@endif</div></details>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-5"><i class="fa fa-search fa-2x mb-3 d-block"></i>No se encontraron movimientos con estos filtros.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel-footer d-flex flex-wrap justify-content-between align-items-center gap-2"><span class="text-muted">{{ $movimientos->firstItem() ?? 0 }}–{{ $movimientos->lastItem() ?? 0 }} de {{ $movimientos->total() }}</span>{{ $movimientos->links() }}</div>
    </div>
</div>
