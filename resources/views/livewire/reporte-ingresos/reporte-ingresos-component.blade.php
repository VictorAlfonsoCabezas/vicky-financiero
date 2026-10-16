<div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <p class="text-muted mb-0">Consulta los pagos recibidos y el detalle de su distribución.</p>
        </div>
        <button type="button" class="btn btn-success" wire:click="export" wire:loading.attr="disabled">
            <i class="fa fa-file-excel me-2" aria-hidden="true"></i>Exportar a Excel
        </button>
    </div>

    <div class="panel panel-inverse mb-4">
        <div class="panel-heading"><h2 class="panel-title"><i class="fa fa-filter me-2" aria-hidden="true"></i>Filtros del reporte</h2></div>
        <div class="panel-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-5">
                    <label for="search" class="form-label">Buscar socio</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span>
                        <input type="search" wire:model.debounce.400ms="search" id="search" class="form-control" placeholder="Nombre o identificación del socio">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="fecha_inicio" class="form-label">Desde</label>
                    <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" wire:model="fecha_inicio">
                    @error('fecha_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="fecha_fin" class="form-label">Hasta</label>
                    <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" id="fecha_fin" wire:model="fecha_fin" min="{{ $fecha_inicio }}">
                    @error('fecha_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-lg-1">
                    <button type="button" class="btn btn-default w-100" wire:click="limpiarFiltros" wire:loading.attr="disabled" title="Restablecer filtros a hoy" aria-label="Restablecer filtros a hoy"><i class="fa fa-undo" aria-hidden="true"></i></button>
                </div>
            </div>
            <div class="small text-muted mt-3">Los filtros se aplican automáticamente. Los totales incluyen todos los resultados del período.</div>
        </div>
    </div>

    <div class="row" wire:loading.class="opacity-50">
        @foreach([
            ['Total recibido', $letrasVencidasPagadas->sum('valor_pagado'), 'bg-teal', 'fa-wallet'],
            ['Capital amortizado', $letrasVencidasPagadas->sum('capital_amortizado'), 'bg-blue', 'fa-coins'],
            ['Intereses y mora', $letrasVencidasPagadas->sum('interes_periodo') + $letrasVencidasPagadas->sum('interes_mora'), 'bg-indigo', 'fa-chart-line'],
            ['Componentes y cobranza', $componentesSuma + $gastosCobranzaSumado, 'bg-orange', 'fa-receipt']
        ] as $indicador)
            <div class="col-12 col-md-6 col-xl-3">
                <div class="widget widget-stats {{ $indicador[2] }} mb-4">
                    <div class="stats-icon"><i class="fa {{ $indicador[3] }}" aria-hidden="true"></i></div>
                    <div class="stats-info"><h3 class="fs-12px text-uppercase">{{ $indicador[0] }}</h3><p class="fs-20px">$ {{ number_format($indicador[1], 2, '.', ',') }}</p></div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="panel panel-inverse">
        <div class="panel-heading">
            <h2 class="panel-title">Detalle de pagos</h2>
            <span class="badge bg-success">{{ $letrasVencidas->total() }} registros</span>
        </div>
        <div class="panel-body">
            <div wire:loading role="status" class="text-primary mb-3"><span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Actualizando reporte…</div>
            <div class="table-responsive" wire:loading.class="opacity-50">
                <table class="table table-hover table-striped align-middle text-nowrap mb-0">
                    <caption class="small">Pagos recibidos del {{ $fecha_inicio }} al {{ $fecha_fin }}. Desliza horizontalmente para ver todas las columnas.</caption>
                    <thead>
                        <tr>
                            @foreach(['N.º socio', 'Nombre del socio', 'Identificación', 'Fecha de pago', 'Valor cuota', 'Capital amortizado', 'Componentes', 'Interés', 'Interés de mora', 'Gasto de cobranza', 'Total pagado', 'Forma de pago', 'Observación'] as $encabezado)
                                <th scope="col" class="{{ $loop->index >= 4 && $loop->index <= 10 ? 'text-end' : '' }}">{{ $encabezado }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($letrasVencidas as $letra)
                            <tr wire:key="ingreso-{{ $letra->id }}">
                                <td>{{ $letra->idCliente }}</td>
                                <td class="fw-bold">{{ $letra->nombreSocio }}</td>
                                <td>{{ $letra->identificacionCliente }}</td>
                                <td>{{ $letra->date_pay }}</td>
                                @foreach(['valor_cuota', 'capital_amortizado', 'gastosComponentes', 'interes_periodo', 'interes_mora', 'gastosCobranza', 'valor_pagado'] as $campo)
                                    <td class="text-end {{ $campo === 'valor_pagado' ? 'fw-bold text-success' : '' }}">$ {{ number_format((float) $letra->$campo, 2, '.', ',') }}</td>
                                @endforeach
                                <td><span class="badge bg-gray-200 text-dark">{{ $letra->formaPago ?: 'Sin especificar' }}</span></td>
                                <td class="text-wrap" style="min-width: 180px;">{{ $letra->obervation_pago ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="13" class="text-center py-5"><i class="fa fa-search fa-2x text-muted mb-3" aria-hidden="true"></i><h3 class="fs-16px">No se encontraron pagos</h3><p class="text-muted mb-0">Revisa el período o cambia el nombre o identificación del socio.</p></td></tr>
                        @endforelse
                    </tbody>
                    @if($letrasVencidas->total())
                        <tfoot>
                            <tr class="fw-bold">
                                <th scope="row" colspan="4">Totales del período</th>
                                @foreach([$letrasVencidasPagadasSuma, $letrasVencidasPagadas->sum('capital_amortizado'), $componentesSuma, $letrasVencidasPagadas->sum('interes_periodo'), $letrasVencidasPagadas->sum('interes_mora'), $gastosCobranzaSumado, $letrasVencidasPagadas->sum('valor_pagado')] as $total)
                                    <td class="text-end">$ {{ number_format($total, 2, '.', ',') }}</td>
                                @endforeach
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                <span class="text-muted small">Mostrando {{ $letrasVencidas->firstItem() ?: 0 }}–{{ $letrasVencidas->lastItem() ?: 0 }} de {{ $letrasVencidas->total() }} pagos</span>
                <div>{{ $letrasVencidas->links() }}</div>
            </div>
        </div>
    </div>
</div>
