<div class="balance-module">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-header mb-1"><i class="fa fa-balance-scale text-primary me-2" aria-hidden="true"></i> Balance general</h1>
            <p class="text-muted mb-0">Consulta los saldos de activos, pasivos y patrimonio por cuenta contable.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-danger" wire:click="generarPdf" wire:loading.attr="disabled" wire:target="generarPdf">
                <i class="far fa-file-pdf me-1" aria-hidden="true"></i>
                <span wire:loading.remove wire:target="generarPdf">Descargar PDF</span>
                <span wire:loading wire:target="generarPdf">Generando PDF...</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" disabled title="Cierre contable pendiente de implementación">
                <i class="fa fa-lock me-1" aria-hidden="true"></i> Cierre contable <span class="badge bg-secondary ms-1">Pendiente</span>
            </button>
        </div>
    </div>

    <div class="panel panel-inverse mb-4">
        <div class="panel-heading"><h4 class="panel-title"><i class="fa fa-filter me-2" aria-hidden="true"></i> Filtros del reporte</h4></div>
        <div class="panel-body">
            <div class="row g-3">
                <div class="col-6 col-md-4">
                    <label class="form-label" for="balance-desde">Fecha contable desde</label>
                    <input id="balance-desde" type="date" class="form-control" wire:model="fechaInicio">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label" for="balance-hasta">Fecha contable hasta</label>
                    <input id="balance-hasta" type="date" class="form-control" wire:model="fechaFin">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="balance-saldo">Mostrar cuentas</label>
                    <select id="balance-saldo" class="form-select" wire:model="filtroSaldo">
                        <option value="todos">Todas las cuentas</option>
                        <option value="diferente-de-cero">Solo cuentas con saldo</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-inverse">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="fa fa-list-alt me-2" aria-hidden="true"></i> Detalle por cuenta <span class="badge bg-primary ms-2">{{ $planCuentas->total() }}</span></h4>
            <span wire:loading wire:target="fechaInicio,fechaFin,filtroSaldo,page" role="status"><i class="fa fa-spinner fa-spin me-1" aria-hidden="true"></i> Actualizando...</span>
        </div>
        <div class="panel-body p-0">
            <div class="px-3 py-2 border-bottom text-muted balance-legend"><i class="fa fa-layer-group me-1" aria-hidden="true"></i> Las filas destacadas agrupan cuentas. Sus saldos incluyen las cuentas de niveles inferiores.</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 balance-table">
                    <thead><tr><th scope="col">Código</th><th scope="col">Cuenta</th><th scope="col" class="text-end">Saldo acumulado</th></tr></thead>
                    <tbody>
                        @forelse ($planCuentas as $plan)
                        <tr wire:key="balance-cuenta-{{ $plan->id }}" class="{{ $plan->nivel <= 3 ? 'balance-nivel-' . $plan->nivel : '' }}">
                            <td class="text-nowrap"><span class="fw-bold">{{ $plan->codigo }}</span></td>
                            <td><div class="balance-cuenta" style="--cuenta-nivel: {{ max(0, min(6, $plan->nivel - 1)) }};">@if ($plan->nivel <= 3)<i class="fa fa-folder me-2" aria-hidden="true"></i>@endif{{ $plan->nombre }}</div></td>
                            <td class="text-end balance-money {{ (float) $plan->saldo < 0 ? 'text-danger' : '' }}">$ {{ number_format((float) $plan->saldo, 2, '.', ',') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-5"><i class="fa fa-search fa-2x text-muted mb-3 d-block" aria-hidden="true"></i><h5>No hay cuentas para estos filtros</h5><p class="text-muted mb-0">Revisa el período o selecciona todas las cuentas.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 border-top">
                <small class="text-muted">Mostrando {{ $planCuentas->firstItem() ?? 0 }}–{{ $planCuentas->lastItem() ?? 0 }} de {{ $planCuentas->total() }} cuentas</small>
                <div>{{ $planCuentas->links() }}</div>
            </div>
        </div>
    </div>
</div>
