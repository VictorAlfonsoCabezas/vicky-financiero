<div class="vault-module">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="text-muted small mb-2">TESORERÍA / BÓVEDAS</div>
            <h1 class="page-header mb-2">Descargos de bóvedas</h1>
            <p class="text-muted mb-0">Consulta los saldos y administra las cargas, los gastos y las transferencias de tu empresa.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-primary" wire:click="abrirCrearBoveda" wire:loading.attr="disabled">
            <i class="fa fa-plus me-2" aria-hidden="true"></i>Crear bóveda
        </button>
        <button type="button" class="btn btn-white" wire:click="$refresh" wire:loading.attr="disabled">
            <i class="fa fa-sync-alt me-2" aria-hidden="true"></i>Actualizar saldos
        </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach ([
            ['Bóvedas activas', $bovedas->count(), 'fa-archive', 'bg-blue', false],
            ['Cargas iniciales registradas', $valoresInicialesEmpresa->sum('valor'), 'fa-arrow-down', 'bg-teal', true],
            ['Gastos iniciales registrados', $gastosIniciales->sum('valor'), 'fa-arrow-up', 'bg-orange', true],
            ['Créditos vigentes', $creditosVigentes, 'fa-file-invoice-dollar', 'bg-indigo', true],
        ] as $resumen)
            <div class="col-sm-6 col-xl-3">
                <div class="widget widget-stats {{ $resumen[3] }} mb-0 h-100">
                    <div class="stats-icon"><i class="fa {{ $resumen[2] }}" aria-hidden="true"></i></div>
                    <div class="stats-info">
                        <h2 class="vault-stat-label">{{ $resumen[0] }}</h2>
                        <p class="vault-money">{{ $resumen[4] ? '$ ' . number_format($resumen[1], 2, '.', ',') : $resumen[1] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h5 mb-0">Tus bóvedas</h2>
        <span class="small text-muted" wire:loading role="status">Actualizando información…</span>
    </div>
    <div class="row g-4 mb-4">
        @forelse ($bovedas as $bov)
            @php($saldo = $bov->principal ? $bov->saldoBoveda - $creditosVigentes : $bov->saldoBoveda)
            <div class="col-12 col-xl-6" wire:key="vault-{{ $bov->id }}">
                <section class="panel panel-inverse h-100 mb-0 vault-panel" aria-labelledby="vault-title-{{ $bov->id }}">
                    <div class="panel-heading">
                        <h3 class="panel-title" id="vault-title-{{ $bov->id }}"><i class="fa fa-university me-2" aria-hidden="true"></i>{{ $bov->nombre }}</h3>
                        <span class="badge {{ $bov->principal ? 'bg-primary' : 'bg-secondary' }}">{{ $bov->principal ? 'Principal' : 'Bóveda' }}</span>
                    </div>
                    <div class="panel-body p-0">
                        <div class="vault-balance p-4">
                            <p class="small text-muted mb-2">{{ $bov->descripcion ?: 'Administración de fondos' }}</p>
                            <span class="small fw-bold text-muted">Saldo de la bóveda</span>
                            <div class="vault-balance-value vault-money {{ $saldo < 0 ? 'text-danger' : '' }}">$ {{ number_format($saldo, 2, '.', ',') }}</div>
                            @if ($bov->principal)<p class="small text-muted mb-0">Este saldo descuenta los créditos vigentes.</p>@endif
                        </div>
                        <div class="p-3 border-bottom d-flex flex-wrap gap-2">
                            @if ($bov->principal)
                                <button type="button" class="btn btn-primary btn-sm" wire:click="abrirOperacion({{ $bov->id }}, 'carga')" wire:loading.attr="disabled"><i class="fa fa-plus me-1" aria-hidden="true"></i>Carga inicial</button>
                                <button type="button" class="btn btn-outline-danger btn-sm" wire:click="abrirOperacion({{ $bov->id }}, 'gasto')" wire:loading.attr="disabled"><i class="fa fa-minus me-1" aria-hidden="true"></i>Gasto inicial</button>
                            @endif
                            <button type="button" class="btn btn-outline-primary btn-sm" wire:click="abrirOperacion({{ $bov->id }}, 'transferencia')" wire:loading.attr="disabled" @if($bovedas->count() < 2) disabled @endif><i class="fa fa-exchange-alt me-1" aria-hidden="true"></i>Transferir</button>
                        </div>
                        @if ($bov->principal)
                            <div class="p-4">
                                <h4 class="h6 mb-3">Balance actual por banco</h4>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead><tr><th scope="col">Banco</th><th scope="col" class="text-end">Saldo actual</th></tr></thead>
                                        <tbody>
                                            @forelse ($bancosValores as $banco)
                                                <tr><td>{{ $banco->nombre }}<span class="d-block small text-muted">{{ $banco->numero_cuenta }}</span></td><td class="text-end vault-money fw-bold">$ {{ number_format($saldosBancos[$bov->id][$banco->id], 2, '.', ',') }}</td></tr>
                                            @empty
                                                <tr><td colspan="2" class="text-muted text-center py-4">No hay bancos activos con cuenta registrada.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <details class="vault-details mt-3">
                                    <summary>Cargas iniciales <span class="badge bg-primary ms-1">{{ $valoresInicialesEmpresa->where('boveda_origen_id', $bov->id)->count() }}</span></summary>
                                    <div class="table-responsive mt-2">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead><tr><th scope="col">Banco</th><th scope="col" class="text-end">Valor registrado</th></tr></thead>
                                            <tbody>@forelse($valoresInicialesEmpresa->where('boveda_origen_id', $bov->id) as $carga)
                                                <tr><td><button type="button" class="btn btn-link p-0 text-start" wire:click="verDetalleCarga({{ $carga->id }})" wire:loading.attr="disabled" wire:target="verDetalleCarga">{{ $carga->nombre_banco }}<span class="d-block small">Ver detalle de carga #{{ $carga->id }}</span></button></td><td class="text-end vault-money">$ {{ number_format($carga->valor, 2, '.', ',') }}</td></tr>
                                            @empty<tr><td colspan="2" class="text-muted py-3">No hay cargas iniciales registradas.</td></tr>@endforelse</tbody>
                                        </table>
                                    </div>
                                </details>
                                <details class="vault-details mt-2">
                                    <summary>Gastos iniciales <span class="badge bg-secondary ms-1">{{ $gastosIniciales->where('boveda_origen_id', $bov->id)->count() }}</span></summary>
                                    <div class="table-responsive mt-2">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead><tr><th scope="col">Descripción</th><th scope="col" class="text-end">Valor registrado</th></tr></thead>
                                            <tbody>@forelse($gastosIniciales->where('boveda_origen_id', $bov->id) as $gasto)
                                                <tr><td>{{ $gasto->observacion ?: 'Gasto inicial' }}</td><td class="text-end vault-money">$ {{ number_format($gasto->valor, 2, '.', ',') }}</td></tr>
                                            @empty<tr><td colspan="2" class="text-muted py-3">No hay gastos iniciales registrados.</td></tr>@endforelse</tbody>
                                        </table>
                                    </div>
                                </details>
                            </div>
                        @else
                            <div class="p-4 text-muted"><i class="fa fa-exchange-alt me-2" aria-hidden="true"></i>Selecciona Transferir para enviar fondos a otra bóveda.</div>
                        @endif
                        @if ($bovedas->count() < 2)<div class="px-4 pb-4 small text-muted">Necesitas otra bóveda activa para realizar transferencias.</div>@endif
                    </div>
                </section>
            </div>
        @empty
            <div class="col-12"><div class="panel p-5 text-center"><i class="fa fa-university fa-2x text-muted mb-3" aria-hidden="true"></i><h3 class="h5">No hay bóvedas activas</h3><p class="text-muted mb-0">Las bóvedas activas de tu empresa aparecerán aquí.</p></div></div>
        @endforelse
    </div>

    <section class="panel panel-inverse mb-0" aria-labelledby="vault-transfers-title">
        <div class="panel-heading"><h2 class="panel-title" id="vault-transfers-title">Transferencias recientes</h2><span class="small">Últimos 10 registros</span></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th scope="col">Fecha</th><th scope="col">Origen</th><th scope="col">Destino</th><th scope="col">Estado</th><th scope="col" class="text-end">Valor</th></tr></thead>
                <tbody>
                    @forelse ($transferencias as $transferencia)
                        <tr><td class="text-nowrap">{{ $transferencia->fecha_creacion }}</td><td>{{ $transferencia->origen_nombre ?: 'Bóveda no disponible' }}</td><td>{{ $transferencia->destino_nombre ?: 'Bóveda no disponible' }}</td><td><span class="badge {{ $transferencia->estado === 'FINALIZADO' ? 'bg-success' : 'bg-secondary' }}">{{ $transferencia->estado }}</span></td><td class="text-end vault-money fw-bold">$ {{ number_format($transferencia->valor, 2, '.', ',') }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5"><i class="fa fa-exchange-alt d-block fs-3 mb-2" aria-hidden="true"></i>Aún no hay transferencias registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    @include('livewire.descargo-bovedas-header.modals')
</div>
