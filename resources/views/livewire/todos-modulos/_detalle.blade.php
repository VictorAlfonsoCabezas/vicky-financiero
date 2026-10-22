<div wire:ignore.self class="modal fade" id="modalGeneral" tabindex="-1" aria-labelledby="modulos-detalle-titulo" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content">
        <div class="modal-header"><h5 id="modulos-detalle-titulo" class="modal-title"><i class="fa fa-book me-2 text-primary"></i>Revisión del asiento {{ $asientosHeader ? '#' . $asientosHeader->id : '' }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
        <div class="modal-body">
            <div wire:loading wire:target="abrirModal" class="text-center w-100 py-4"><span class="spinner-border text-primary" role="status"></span><p class="text-muted mt-2">Cargando asiento…</p></div>
            <div wire:loading.remove wire:target="abrirModal">
                @if ($asientosHeader)
                    @php
                        $debeDetalle = $detalle->where('debe_haber', 1)->sum('valor');
                        $haberDetalle = $detalle->where('debe_haber', 0)->sum('valor');
                        $pendienteDetalle = $asientosHeader->pendiente_contable || round($debeDetalle, 2) != round($haberDetalle, 2) || $debeDetalle <= 0;
                    @endphp
                    <div class="alert {{ $pendienteDetalle ? 'alert-danger' : 'alert-success' }} d-flex gap-2"><i class="fa {{ $pendienteDetalle ? 'fa-exclamation-circle' : 'fa-check-circle' }} mt-1"></i><div><strong>{{ $pendienteDetalle ? 'Pendiente de revisión contable' : 'Asiento cuadrado' }}</strong><div>{{ $asientosHeader->descripcion }}</div></div></div>
                    <p><strong>Observación:</strong> {{ $asientosHeader->observacion ?: 'Sin observación registrada' }}</p>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6"><small class="text-muted d-block">Concepto / fecha contable</small><strong>{{ optional($asientosHeader->concepto)->nombre ?? 'Sin concepto configurado' }}</strong><div class="text-muted">{{ $asientosHeader->fecha_contable }}</div></div>
                        <div class="col-4 col-md-2"><small class="text-muted d-block">Debe</small><strong>$ {{ number_format($debeDetalle, 2) }}</strong></div>
                        <div class="col-4 col-md-2"><small class="text-muted d-block">Haber</small><strong>$ {{ number_format($haberDetalle, 2) }}</strong></div>
                        <div class="col-4 col-md-2"><small class="text-muted d-block">Diferencia</small><strong class="{{ $pendienteDetalle ? 'text-danger' : 'text-success' }}">$ {{ number_format(abs($debeDetalle - $haberDetalle), 2) }}</strong></div>
                    </div>
                    <div class="table-responsive"><table class="table table-bordered align-middle"><thead class="table-light"><tr><th>Código</th><th>Cuenta contable</th><th>Centro de costos</th><th class="text-end">Debe</th><th class="text-end">Haber</th></tr></thead><tbody>
                        @forelse ($detalle as $det)
                            <tr><td class="text-nowrap fw-semibold">{{ $det->plan_codigo ?? '—' }}</td><td>{{ $det->plan_nombre ?? 'Cuenta no disponible' }}</td><td>{{ $det->sedes_nombre ?: 'Sin sede' }}<small class="text-muted d-block">{{ $det->centro_costos_nombre ?: 'Sin centro de costos' }}</small></td><td class="text-end text-nowrap">{{ $det->debe_haber ? '$ ' . number_format($det->valor, 2) : '—' }}</td><td class="text-end text-nowrap">{{ !$det->debe_haber ? '$ ' . number_format($det->valor, 2) : '—' }}</td></tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">El asiento está guardado, pero aún no tiene cuentas. Revisa el motivo pendiente y completa la configuración o edítalo en contabilidad.</td></tr>
                        @endforelse
                    </tbody><tfoot class="table-light"><tr><th colspan="3">Totales</th><th class="text-end">$ {{ number_format($debeDetalle, 2) }}</th><th class="text-end">$ {{ number_format($haberDetalle, 2) }}</th></tr></tfoot></table></div>
                @else
                    <p class="text-muted text-center py-4">Selecciona un asiento para consultar su detalle.</p>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
            @if ($asientosHeader)
                <a href="{{ url('/asientos/comprobante/' . $asientosHeader->id) }}" class="btn btn-outline-primary" target="_blank" rel="noopener"><i class="fa fa-print me-1"></i> Comprobante</a>
                <a href="{{ url('/asientos') }}" class="btn btn-primary"><i class="fa fa-edit me-1"></i> Abrir contabilidad</a>
            @endif
        </div>
    </div></div>
</div>
