<div>
    <h1 class="page-header mb-3"><i class="fa fa-star text-primary me-2" aria-hidden="true"></i>Calificación de socios</h1>

    <div class="row g-3">
        <div class="col-lg-4 col-xl-3">
            <section class="panel panel-inverse h-auto">
                <div class="panel-heading"><h2 class="panel-title">Socios</h2></div>
                <div class="panel-body">
                    <label for="calificacion-buscar" class="form-label fw-semibold">Buscar socio</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span>
                        <input id="calificacion-buscar" type="search" class="form-control" wire:model.debounce.350ms="buscar"
                            maxlength="120" placeholder="Nombre, documento o código">
                    </div>
                    <div class="list-group">
                        @forelse ($socios as $opcion)
                            <button type="button" wire:key="score-socio-{{ $opcion->id }}" wire:click="seleccionarSocio({{ $opcion->id }})"
                                class="list-group-item list-group-item-action {{ $socio && $socio->id === $opcion->id ? 'active' : '' }}"
                                aria-pressed="{{ $socio && $socio->id === $opcion->id ? 'true' : 'false' }}">
                                <span class="fw-semibold d-block">{{ $opcion->nombres }} {{ $opcion->apellidos }}</span>
                                <small>{{ $opcion->numero_documento }} · {{ $opcion->code }}</small>
                            </button>
                        @empty
                            <p class="text-muted mb-0">No se encontraron socios.</p>
                        @endforelse
                    </div>
                    <div class="mt-3">{{ $socios->onEachSide(0)->links() }}</div>
                </div>
            </section>
        </div>

        <div class="col-lg-8 col-xl-9">
            <div wire:loading.delay wire:target="seleccionarSocio" role="status" class="mb-2 text-muted">
                <i class="fa fa-spinner fa-spin me-1" aria-hidden="true"></i>Calculando calificación…
            </div>
            @if (!$resultado)
                <div class="panel"><div class="panel-body p-4 text-muted">Selecciona un socio para consultar su comportamiento crediticio.</div></div>
            @else
                <div wire:key="calificacion-detalle-{{ $socio->id }}">
                    <section class="panel panel-inverse mb-3">
                        <div class="panel-heading gap-2 flex-wrap">
                            <h2 class="panel-title">{{ $socio->nombres }} {{ $socio->apellidos }}</h2>
                            <button type="button" class="btn btn-sm btn-white" wire:click="imprimirPdf"
                                wire:loading.attr="disabled" wire:target="imprimirPdf,seleccionarSocio">
                                <span wire:loading.remove wire:target="imprimirPdf">
                                    <i class="far fa-file-pdf me-1" aria-hidden="true"></i> Imprimir PDF
                                </span>
                                <span wire:loading wire:target="imprimirPdf" role="status">
                                    <i class="fa fa-spinner fa-spin me-1" aria-hidden="true"></i> Generando PDF…
                                </span>
                            </button>
                        </div>
                        <div class="panel-body">
                            <div class="row gy-3 align-items-center">
                                <div class="col-md">
                                    <div class="text-muted">Score interno · Corte {{ $resultado['corte'] }}</div>
                                    @if ($resultado['score'] === null)
                                        <div class="h4 mt-2 mb-0">Sin historial suficiente</div>
                                    @else
                                        <div class="d-flex flex-wrap align-items-baseline gap-2">
                                            <strong class="display-6 text-primary">{{ number_format($resultado['score'], 0, ',', '.') }}</strong>
                                            <span class="text-muted">/ 1.000</span>
                                            @if ($resultado['provisional'])
                                                <span class="badge bg-warning text-dark">Provisional</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-auto">
                                    <div class="text-muted">Saldo vencido de créditos vigentes</div>
                                    <strong class="fs-4">$ {{ number_format($resultado['saldo_vencido'] / 100, 2, '.', ',') }}</strong>
                                    <div>{{ $resultado['atrasadas'] }} cuotas con saldo vencido</div>
                                </div>
                            </div>
                            @if ($resultado['score'] !== null && $resultado['peso_disponible'] < 1000)
                                <p class="small text-muted mt-2 mb-0">Se usan {{ $resultado['peso_disponible'] }} de los 1.000 puntos de ponderación. Los factores con evidencia se ajustan a la escala completa.</p>
                            @endif
                            @if ($resultado['datos_incompletos'])
                                <div class="alert alert-warning mt-3 mb-0">{{ $resultado['datos_incompletos'] }} cuotas tienen datos incompletos para evaluar alguno de los factores.</div>
                            @endif
                        </div>
                    </section>

                    <div class="row g-3 mb-3">
                        @foreach (['puntualidad' => 'Puntualidad histórica', 'liquidacion' => 'Créditos pagados por completo', 'vigentes' => 'Cumplimiento de créditos vigentes'] as $clave => $titulo)
                            @php($factor = $resultado['factores'][$clave])
                            <div class="col-md-4">
                                <section class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <h3 class="h6">{{ $titulo }}</h3>
                                        @if ($factor['puntos'] === null)
                                            <span class="text-muted">Sin evidencia</span>
                                        @else
                                            <strong class="fs-4">{{ number_format($factor['puntos'], 1, ',', '.') }}</strong>
                                            <span class="text-muted">/ {{ $factor['maximo'] }}</span>
                                        @endif
                                        @if ($clave === 'puntualidad' && $resultado['historial'])
                                            <div class="small text-muted">{{ $resultado['puntuales'] }} de {{ $resultado['historial'] }} cuotas completadas a tiempo</div>
                                        @endif
                                    </div>
                                </section>
                            </div>
                        @endforeach
                    </div>

                    <section class="panel panel-inverse mb-3">
                        <div class="panel-heading"><h2 class="panel-title">Créditos evaluados</h2></div>
                        <div class="panel-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light"><tr><th scope="col">Crédito</th><th scope="col">Estado evaluado</th><th scope="col">Cuotas cubiertas</th></tr></thead>
                                    <tbody>
                                        @forelse ($resultado['creditos'] as $credito)
                                            <tr>
                                                <td class="fw-semibold">{{ $credito['codigo'] }}</td>
                                                <td>{{ $credito['estado'] }}</td>
                                                <td>{{ $credito['cubiertas'] }} / {{ $credito['cuotas'] }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="p-3 text-muted">Este socio aún no tiene créditos evaluables.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    <section class="panel panel-inverse mb-3">
                        <div class="panel-heading"><h2 class="panel-title">Detalle de cuotas</h2></div>
                        <div class="panel-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-nowrap">
                                        <tr>
                                            <th scope="col">Crédito / cuota</th><th scope="col">Vencimiento</th>
                                            <th scope="col">Pago completo</th><th scope="col" class="text-end">Importe</th>
                                            <th scope="col" class="text-end">Cubierto</th><th scope="col" class="text-end">Saldo</th>
                                            <th scope="col">Evaluación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($cuotas as $cuota)
                                            <tr wire:key="score-cuota-{{ $cuota['id'] }}">
                                                <td class="text-nowrap fw-semibold">{{ $cuota['credito'] }} / {{ $cuota['numero'] }}</td>
                                                <td>{{ $cuota['vencimiento'] ?? '—' }}</td>
                                                <td>{{ $cuota['fecha_pago'] ?? '—' }}</td>
                                                @foreach (['importe', 'cubierto', 'saldo'] as $monto)
                                                    <td class="text-end text-nowrap">$ {{ number_format($cuota[$monto] / 100, 2, '.', ',') }}</td>
                                                @endforeach
                                                <td>
                                                    @if ($cuota['trasladada'])
                                                        <span class="badge bg-secondary">Trasladada por novación</span>
                                                    @elseif ($cuota['incompleta'])
                                                        <span class="badge bg-warning text-dark">Dato incompleto</span>
                                                    @elseif ($cuota['dias_atraso'] > 0)
                                                        <span class="badge {{ $cuota['completa'] ? 'bg-warning text-dark' : 'bg-danger' }}">{{ $cuota['dias_atraso'] }} días de atraso</span>
                                                    @elseif ($cuota['completa'])
                                                        <span class="badge bg-success">A tiempo</span>
                                                    @else
                                                        <span class="badge bg-secondary">Por vencer</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7" class="p-3 text-muted">No hay cuotas para mostrar.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if ($cuotas->hasPages())
                                <div class="p-3 border-top">{{ $cuotas->links() }}</div>
                            @endif
                        </div>
                    </section>

                    <details class="border rounded bg-white p-3">
                        <summary class="fw-semibold">Cómo se calcula el score</summary>
                        <div class="small mt-2">
                            <p class="mb-1"><strong>Puntualidad.</strong> Cada cuota completada aporta el 100 % a tiempo, 75 % con 1–7 días de atraso, 50 % con 8–30 días, 25 % con 31–60 días y 0 % desde 61 días.</p>
                            <p class="mb-1"><strong>Liquidación.</strong> Proporción de créditos liquidados sobre los cierres verificados y las reestructuraciones. Las cuotas trasladadas se identifican en el detalle.</p>
                            <p class="mb-0"><strong>Créditos vigentes.</strong> El importe cubierto determina el 70 % de este factor. El 30 % restante considera la antigüedad de los saldos pendientes, con los mismos tramos de atraso.</p>
                        </div>
                    </details>
                </div>
            @endif
        </div>
    </div>
</div>
