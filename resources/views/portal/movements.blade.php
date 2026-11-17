@extends('portal.layout')
@section('title', 'Movimientos')
@section('content')
<h1 class="page-header">Movimientos · {{ $account->codigo }}</h1>
<div class="portal-welcome mb-4">
    <p class="mb-1">Saldo actual</p>
    <strong class="fs-2">$ {{ number_format($balance, 2) }}</strong>
</div>
<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-sm-4"><label class="form-label" for="desde">Desde</label><input id="desde" type="date" name="desde" class="form-control" value="{{ request('desde') }}"></div>
            <div class="col-sm-4"><label class="form-label" for="hasta">Hasta</label><input id="hasta" type="date" name="hasta" class="form-control" value="{{ request('hasta') }}"></div>
            <div class="col-sm-4 d-flex flex-wrap gap-2"><button class="btn btn-primary">Filtrar</button>@if(request()->filled('desde') || request()->filled('hasta'))<a class="btn btn-outline-secondary" href="{{ route('portal.movements', $account->id) }}">Limpiar</a>@endif</div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th scope="col">Fecha</th><th scope="col">Transacción</th><th scope="col">Descripción</th><th scope="col" class="text-end">Ingreso</th><th scope="col" class="text-end">Egreso</th></tr></thead>
                <tbody>
                @forelse($movements as $row)
                    <tr><td class="text-nowrap">{{ $row->date_created }}</td><td>{{ $row->type_transaction_name }}</td><td>{{ $row->observation }}</td><td class="text-end">{{ $row->type_transaction_action === 'S' ? number_format($row->valor_movimiento, 2) : '—' }}</td><td class="text-end">{{ $row->type_transaction_action === 'R' ? number_format($row->valor_movimiento, 2) : '—' }}</td></tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay movimientos en este período.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $movements->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
