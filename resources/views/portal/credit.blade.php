@extends('portal.layout')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h1 class="page-header mb-0">Préstamo {{ $credit->code }} <small>{{ $credit->status }}</small></h1>
    <div>
        <span class="d-block text-muted">Monto solicitado</span>
        <strong class="fs-5">$ {{ number_format((float) $credit->valor_solicitado, 2) }}</strong>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h2 class="h4 mb-1">Cuotas del préstamo</h2>
        <p class="text-muted mb-0">Los comprobantes enviados quedan en revisión hasta que se confirme el pago.</p>
    </div>
    <div class="card-body">
        <div class="table-responsive" tabindex="0" role="region" aria-label="Detalle de cuotas del préstamo">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">N.º de cuota</th>
                        <th scope="col">Fecha de vencimiento</th>
                        <th scope="col" class="text-end">Valor cuota</th>
                        <th scope="col" class="text-end">Interés de mora</th>
                        <th scope="col" class="text-end">Faltante anterior</th>
                        <th scope="col" class="text-end">Abono anterior</th>
                        <th scope="col" class="text-end">Total a pagar</th>
                        <th scope="col" class="text-end">Valor pagado</th>
                        <th scope="col">Fecha de pago</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Comprobantes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $detail)
                    @php
                    $mora = (float) $detail->interes_mora;
                    $faltante = (float) $detail->faltante_anterior_cuota;
                    $abono = (float) $detail->saldo_anterior_cuota;
                    $importe = (float) $detail->valor_cuota + $mora + $faltante - $abono;
                    $pagada = $detail->status === 'PAGADA';
                    @endphp
                    <tr>
                        <th scope="row" class="py-3">{{ $detail->numero_cuota ?? '—' }}</th>
                        <td class="text-nowrap">{{ $detail->date_vencimiento }}</td>
                        <td class="text-end text-nowrap">$ {{ number_format((float) $detail->valor_cuota, 2) }}</td>
                        <td class="text-end text-nowrap">$ {{ number_format($mora, 2) }}</td>
                        <td class="text-end text-nowrap">$ {{ number_format($faltante, 2) }}</td>
                        <td class="text-end text-nowrap">$ {{ number_format($abono, 2) }}</td>
                        <td class="text-end text-nowrap fw-bold">$ {{ number_format($importe, 2) }}</td>
                        <td class="text-end text-nowrap">
                            @if($pagada && $detail->valor_pagado !== null)
                            <strong class="d-block text-nowrap">$ {{ number_format((float) $detail->valor_pagado, 2) }}</strong>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-nowrap">{{ $pagada && $detail->date_pay ? $detail->date_pay : '—' }}</td>
                        <td>
                            @if($pagada)
                            <span class="badge bg-success">Pagada</span>
                            @elseif($detail->status === 'STAND BY')
                            <span class="badge bg-warning text-dark">Comprobante en revisión</span>
                            @elseif($detail->status === 'PENDIENTE')
                            <span class="badge bg-secondary">Pendiente</span>
                            @else
                            {{ $detail->status }}
                            @endif
                        </td>
                        <td>
                            @if($detail->path || $detail->status === 'PAGADA')<a class="btn btn-sm btn-outline-primary mb-2" href="{{ route('portal.credit.attachment', [$credit->id, $detail->id]) }}">Descargar comprobante</a>@endif
                            @if($detail->status === 'PENDIENTE')
                            <details {{ $errors->any() && (string) old('cuota_id') === (string) $detail->id ? 'open' : '' }}>
                                <summary class="text-primary fw-semibold">Subir comprobante</summary>
                                <form class="mt-3" method="POST" action="{{ route('portal.credit.pay', [$credit->id, $detail->id]) }}" enctype="multipart/form-data">@csrf
                                    <input type="hidden" name="cuota_id" value="{{ $detail->id }}">
                                    <label class="form-label" for="valor-pago-{{ $detail->id }}">Valor del pago</label>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">$</span>
                                        <input id="valor-pago-{{ $detail->id }}" class="form-control" type="number" name="valor" inputmode="decimal"
                                            min="0.01" max="999999.99" step="0.01" required
                                            value="{{ (string) old('cuota_id') === (string) $detail->id ? old('valor') : number_format((float) $detail->valor_cuota, 2, '.', '') }}">
                                    </div>
                                    <label class="form-label">Banco</label><select class="form-select mb-2" name="banco" required>
                                        <option value="">Selecciona un banco</option>
                                        @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->nombre }} · {{ $bank->numero_cuenta }}</option>
                                        @endforeach
                                    </select>
                                    <label class="form-label">Número del comprobante</label>
                                    <input class="form-control mb-2" name="numero_comprobante" maxlength="100" required>
                                    <label class="form-label">Fecha y hora del pago</label>
                                    <input class="form-control mb-2" type="date" name="fecha_comprobante" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                                    <input class="form-control mb-2" type="time" name="hora_comprobante" required>
                                    <label class="form-label">Comprobante (PDF, JPG o PNG; máximo 5 MB)</label>
                                    <input class="form-control mb-2" type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <button class="btn btn-primary btn-sm">Enviar comprobante</button>
                                </form>
                            </details>
                            @endif
                        </td>
                    </tr>@empty<tr>
                        <td colspan="11" class="text-center text-muted py-4">No hay cuotas registradas.</td>
                    </tr>@endforelse
                </tbody>
            </table>
        </div>
        {{ $details->links('pagination::bootstrap-4') }}
    </div>
</div>
@if($files->count())<div class="card mt-3">
    <div class="card-body">
        <h2 class="h4">Documentos del préstamo</h2>@foreach($files as $file)<a class="d-block mb-2" href="{{ route('portal.credit.file', [$credit->id, $file->id]) }}">{{ $file->descripcion ?: 'Descargar documento' }}</a>@endforeach
    </div>
</div>@endif
@endsection
