@extends('portal.layout')
@section('content')
<h1 class="page-header">Solicitudes de acreditación</h1>
<div class="card mb-4"><form class="card-body" method="POST" action="{{ route('portal.requests.save') }}" enctype="multipart/form-data">@csrf
<h2 class="h4">{{ $editing ? 'Editar solicitud pendiente' : 'Nueva solicitud' }}</h2>
<input type="hidden" name="id" value="{{ old('id', optional($editing)->id) }}">
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Cuenta a acreditar</label><select class="form-select" name="cuenta" required><option value="">Selecciona una cuenta</option>@foreach($accounts as $account)<option value="{{ $account->id }}" {{ old('cuenta', optional($editing)->customer_tipo_ahorro_id) == $account->id ? 'selected' : '' }}>{{ $account->codigo }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Banco</label><select class="form-select" name="banco" required><option value="">Selecciona un banco</option>@foreach($banks as $bank)<option value="{{ $bank->id }}" {{ old('banco', optional($editing)->banco_id) == $bank->id ? 'selected' : '' }}>{{ $bank->nombre }} · {{ $bank->numero_cuenta }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Valor</label><input class="form-control" type="number" name="valor" step="0.01" min="0.01" max="99999" value="{{ old('valor', optional($editing)->valor) }}" required></div>
<div class="col-md-6"><label class="form-label">Número de depósito</label><input class="form-control" name="numero_deposito" inputmode="numeric" pattern="[0-9]+" maxlength="60" value="{{ old('numero_deposito', optional($editing)->numero_deposito) }}" required></div>
<div class="col-12"><label class="form-label">Observación</label><textarea class="form-control" name="observacion" maxlength="255">{{ old('observacion', optional($editing)->observacion) }}</textarea></div>
<div class="col-12"><label class="form-label">Comprobante (PDF, JPG o PNG; máximo 1 MB)</label><input class="form-control" type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png" {{ $editing ? '' : 'required' }}>@if($editing)<small>Déjalo vacío para conservar el comprobante actual.</small>@endif</div>
<div class="col-12"><button class="btn btn-primary">Enviar a revisión</button>@if($editing)<a href="{{ route('portal.requests') }}" class="btn btn-secondary">Cancelar edición</a>@endif</div>
</div></form></div>
<div class="card"><div class="card-body table-responsive"><table class="table"><thead><tr><th>Fecha</th><th>Valor</th><th>Estado</th><th>Observaciones de revisión</th><th>Acciones</th></tr></thead><tbody>
@forelse($requests as $row)<tr><td>{{ $row->fecha_creacion }}</td><td>{{ number_format($row->valor, 2) }}</td><td>{{ $row->estado }}</td><td>{{ $row->razon_rechazado }}</td><td>@if($row->path)<a class="btn btn-sm btn-outline-secondary" href="{{ route('portal.requests.attachment', $row->id) }}">Comprobante</a>@endif @if($row->estado === 'PENDIENTE')<a class="btn btn-sm btn-outline-primary" href="{{ route('portal.requests', ['editar' => $row->id]) }}">Editar</a><form method="POST" class="d-inline" action="{{ route('portal.requests.delete', $row->id) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar pendiente</button></form>@endif</td></tr>
@empty<tr><td colspan="5">No tienes solicitudes.</td></tr>@endforelse
</tbody></table>{{ $requests->links('pagination::bootstrap-4') }}</div></div>
@endsection
