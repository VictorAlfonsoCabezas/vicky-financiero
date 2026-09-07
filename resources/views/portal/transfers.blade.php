@extends('portal.layout')
@section('content')
<h1 class="page-header">Transferencias entre cuentas de la caja</h1>
<div class="card mb-3"><div class="card-body"><form method="GET" class="d-flex gap-2" action="{{ route('portal.transfers') }}"><label class="visually-hidden" for="buscar">Buscar destinatario</label><input class="form-control" id="buscar" name="buscar" placeholder="Nombre, apellido o código de cuenta (mínimo 3 caracteres)" minlength="3" maxlength="100" value="{{ request('buscar') }}" required><button class="btn btn-outline-primary">Buscar</button></form>
@if(request()->filled('buscar'))<ul class="list-group mt-3">@forelse($destinations as $destination)<li class="list-group-item d-flex justify-content-between align-items-center"><span>{{ $destination->nombres }} {{ $destination->apellidos }} · {{ $destination->codigo }}</span><a class="btn btn-sm btn-outline-primary" href="{{ route('portal.transfers', ['destino' => $destination->codigo]) }}">Seleccionar cuenta</a></li>@empty<li class="list-group-item">No se encontraron cuentas activas.</li>@endforelse</ul>@endif
</div></div>
<div class="card"><form class="card-body" method="POST" action="{{ route('portal.transfers.send') }}">
@csrf<input type="hidden" name="operacion" value="{{ old('operacion', $operation) }}">
<div class="mb-3"><label class="form-label" for="cuenta">Cuenta de origen</label><select class="form-select" name="cuenta" id="cuenta" required><option value="">Selecciona una cuenta</option>@foreach($accounts as $account)<option value="{{ $account->id }}" {{ old('cuenta') == $account->id ? 'selected' : '' }}>{{ $account->codigo }} · Saldo {{ number_format($account->balance, 2) }}</option>@endforeach</select></div>
<div class="mb-3"><label class="form-label" for="destino">Código exacto de la cuenta de destino</label><input class="form-control" id="destino" name="destino" value="{{ old('destino', request('destino')) }}" maxlength="100" required></div>
<div class="mb-3"><label class="form-label" for="valor">Valor</label><input class="form-control" id="valor" name="valor" type="number" min="0.01" max="99999" step="0.01" value="{{ old('valor') }}" required></div>
<div class="mb-3"><label class="form-label" for="descripcion">Descripción</label><textarea class="form-control" id="descripcion" name="descripcion" maxlength="255" required>{{ old('descripcion') }}</textarea></div>
<div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="confirmar" name="confirmar" value="1" required><label class="form-check-label" for="confirmar">Verifiqué la cuenta de destino y autorizo la transferencia del valor indicado.</label></div>
<button class="btn btn-primary" type="submit">Confirmar transferencia</button>
</form></div>
@endsection
