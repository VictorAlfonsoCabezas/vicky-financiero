@extends('portal.layout')
@section('content')
<h1 class="page-header">Movimientos · {{ $account->codigo }}</h1>
<div class="card mb-3"><div class="card-body">Saldo actual: <strong>{{ number_format($balance, 2) }}</strong></div></div>
<form method="GET" class="row g-3 mb-3">
    <div class="col-sm-4"><label class="form-label">Desde</label><input type="date" name="desde" class="form-control" value="{{ request('desde') }}"></div>
    <div class="col-sm-4"><label class="form-label">Hasta</label><input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}"></div>
    <div class="col-sm-4 align-self-end"><button class="btn btn-primary">Filtrar</button></div>
</form>
<div class="card"><div class="card-body table-responsive"><table class="table"><thead><tr><th>Fecha</th><th>Transacción</th><th>Descripción</th><th>Ingreso</th><th>Egreso</th></tr></thead><tbody>
@forelse($movements as $row)<tr><td>{{ $row->date_created }}</td><td>{{ $row->type_transaction_name }}</td><td>{{ $row->observation }}</td><td>{{ $row->type_transaction_action === 'S' ? number_format($row->valor_movimiento, 2) : '—' }}</td><td>{{ $row->type_transaction_action === 'R' ? number_format($row->valor_movimiento, 2) : '—' }}</td></tr>
@empty<tr><td colspan="5">No hay movimientos en este período.</td></tr>@endforelse
</tbody></table>{{ $movements->appends(request()->query())->links('pagination::bootstrap-4') }}</div></div>
@endsection
