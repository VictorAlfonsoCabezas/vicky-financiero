<div wire:ignore.self class="modal fade" id="balance-detalle-modal" tabindex="-1" aria-labelledby="balance-detalle-titulo" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="balance-detalle-titulo">Detalle del saldo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                @if ($detalle)
                    <h6 class="mb-1">{{ $detalle['cuenta']->codigo }} · {{ $detalle['cuenta']->nombre }}</h6>
                    <p class="text-muted mb-3">Fecha contable del {{ $fechaInicio }} al {{ $fechaFin }}</p>

                    <dl class="row g-2 mb-3">
                        @foreach (['debe' => 'Debe total', 'haber' => 'Haber total', 'neto' => 'Saldo de la cuenta'] as $campo => $etiqueta)
                            <div class="col-sm-4">
                                <div class="border rounded p-2 px-3">
                                    <dt class="small text-muted">{{ $etiqueta }}</dt>
                                    <dd class="balance-money fs-5 fw-bold mb-0 {{ $detalle['totales']->{$campo} < 0 ? 'text-danger' : '' }}">
                                        $ {{ number_format((float) $detalle['totales']->{$campo}, 2, '.', ',') }}
                                    </dd>
                                </div>
                            </div>
                        @endforeach
                    </dl>

                    <div wire:loading.delay wire:target="seleccionarSubcuenta,gotoPage,previousPage,nextPage" role="status" class="text-muted mb-2">
                        <i class="fa fa-spinner fa-spin me-1" aria-hidden="true"></i> Cargando detalle...
                    </div>

                    @if ($detalle['subcuentas'])
                        <div wire:key="balance-resumen-subcuentas">
                            <h6>Aporte por cuenta</h6>
                            <div class="table-responsive border rounded">
                                <table class="table table-hover align-middle mb-0 balance-table">
                                    <thead>
                                        <tr><th scope="col">Cuenta</th><th scope="col" class="text-end">Aporte al saldo</th><th scope="col" class="text-end">Acciones</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($detalle['subcuentas'] as $subcuenta)
                                            <tr wire:key="balance-subcuenta-{{ $subcuenta->id }}">
                                                <td>
                                                    {{ $subcuenta->codigo }} · {{ $subcuenta->nombre }}
                                                    @if ($subcuenta->id === $detalle['cuenta']->id)
                                                        <small class="text-muted d-block">Movimientos directos</small>
                                                    @endif
                                                </td>
                                                <td class="text-end balance-money {{ $subcuenta->neto < 0 ? 'text-danger' : '' }}">
                                                    $ {{ number_format((float) $subcuenta->neto, 2, '.', ',') }}
                                                </td>
                                                <td class="text-end text-nowrap">
                                                    <button type="button" class="btn btn-sm btn-white" wire:click="seleccionarSubcuenta({{ $subcuenta->id }})"
                                                        wire:loading.attr="disabled" wire:target="seleccionarSubcuenta"
                                                        aria-label="Ver asientos de {{ $subcuenta->codigo }} {{ $subcuenta->nombre }}">
                                                        Ver asientos
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">{{ $detalle['subcuentas']->links() }}</div>
                        </div>
                    @else
                        <div wire:key="balance-movimientos-{{ $detalle['subcuenta']->id ?? $detalle['cuenta']->id }}">
                            @if ($detalle['subcuenta'])
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <button type="button" class="btn btn-sm btn-white" wire:click="seleccionarSubcuenta"
                                        wire:loading.attr="disabled" wire:target="seleccionarSubcuenta">
                                        <i class="fa fa-arrow-left me-1" aria-hidden="true"></i> Volver a cuentas
                                    </button>
                                    <h6 class="mb-0">{{ $detalle['subcuenta']->codigo }} · {{ $detalle['subcuenta']->nombre }}</h6>
                                </div>
                            @endif
                            <div class="table-responsive border rounded">
                                <table class="table table-hover align-middle mb-0 balance-table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Fecha contable</th><th scope="col">Asiento</th><th scope="col">Concepto y origen</th>
                                            <th scope="col" class="text-end">Debe</th><th scope="col" class="text-end">Haber</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($detalle['movimientos'] as $movimiento)
                                            @php
                                                $asiento = $movimiento->asientos;
                                                $esDebe = (int) $movimiento->debe_haber === 1;
                                                $importe = number_format((float) $movimiento->valor, 2, '.', ',');
                                            @endphp
                                            <tr wire:key="balance-movimiento-{{ $movimiento->id }}">
                                                <td class="text-nowrap">{{ substr($asiento->fecha_contable, 0, 10) }}</td>
                                                <td class="text-nowrap">#{{ $asiento->id }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ optional($asiento->concepto)->nombre ?? 'Sin concepto' }}</div>
                                                    <div>{{ $asiento->descripcion }}</div>
                                                    @if ($asiento->observacion)
                                                        <small class="text-muted d-block">{{ $asiento->observacion }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-end balance-money">{{ $esDebe ? '$ ' . $importe : '—' }}</td>
                                                <td class="text-end balance-money">{{ $esDebe ? '—' : '$ ' . $importe }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center text-muted py-4">Esta cuenta no tiene movimientos en el período seleccionado.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">{{ $detalle['movimientos']->links() }}</div>
                        </div>
                    @endif
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
