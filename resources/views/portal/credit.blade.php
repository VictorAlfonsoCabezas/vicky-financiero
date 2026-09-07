@extends('portal.layout')
@section('content')
<h1 class="page-header">Préstamo {{ $credit->code }} <small>{{ $credit->status }}</small></h1>
<p>Monto solicitado: <strong>{{ number_format($credit->valor_solicitado, 2) }}</strong>. Los comprobantes enviados quedan sujetos a revisión.</p>
<div class="card"><div class="card-body table-responsive"><table class="table"><thead><tr><th>Vencimiento</th><th>Cuota</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
@forelse($details as $detail)<tr><td>{{ $detail->date_vencimiento }}</td><td>{{ number_format($detail->valor_cuota, 2) }}</td><td>{{ $detail->status }}</td><td>
@if($detail->path || $detail->status === 'PAGADA')<a class="btn btn-sm btn-outline-primary mb-2" href="{{ route('portal.credit.attachment', [$credit->id, $detail->id]) }}">Descargar comprobante</a>@endif
@if($detail->status === 'PENDIENTE')
<details><summary class="text-primary">Subir comprobante de pago</summary>
<form class="mt-3" method="POST" action="{{ route('portal.credit.pay', [$credit->id, $detail->id]) }}" enctype="multipart/form-data">@csrf
<label class="form-label">Banco</label><select class="form-select mb-2" name="banco" required><option value="">Selecciona un banco</option>@foreach($banks as $bank)<option value="{{ $bank->id }}">{{ $bank->nombre }} · {{ $bank->numero_cuenta }}</option>@endforeach</select>
<label class="form-label">Número del comprobante</label><input class="form-control mb-2" name="numero_comprobante" maxlength="100" required>
<label class="form-label">Fecha y hora del pago</label><input class="form-control mb-2" type="date" name="fecha_comprobante" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required><input class="form-control mb-2" type="time" name="hora_comprobante" required>
<label class="form-label">Comprobante (PDF, JPG o PNG; máximo 5 MB)</label><input class="form-control mb-2" type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png" required>
<button class="btn btn-primary btn-sm">Enviar comprobante</button></form></details>
@elseif($detail->status === 'STAND BY')<span>Comprobante en revisión</span>@endif
</td></tr>@empty<tr><td colspan="4">No hay cuotas registradas.</td></tr>@endforelse
</tbody></table>{{ $details->links('pagination::bootstrap-4') }}</div></div>
@if($files->count())<div class="card mt-3"><div class="card-body"><h2 class="h4">Documentos del préstamo</h2>@foreach($files as $file)<a class="d-block mb-2" href="{{ route('portal.credit.file', [$credit->id, $file->id]) }}">{{ $file->descripcion ?: 'Descargar documento' }}</a>@endforeach</div></div>@endif
@endsection
