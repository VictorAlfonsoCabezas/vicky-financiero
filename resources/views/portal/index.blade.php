@extends('portal.layout')

@section('content')
<div class="portal-welcome mb-4">
    <h1 class="h2 text-white">Hola, {{ $customer->nombres }}</h1>
    <p class="mb-0">Consulta tus movimientos, revisa tus cuotas y realiza transferencias.</p>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['#mis-cuentas', 'fa-wallet', 'Mis cuentas de ahorro', $accounts->total()],
        ['#mis-creditos', 'fa-file-invoice-dollar', 'Mis créditos', $credits->total()],
        [route('portal.requests'), 'fa-receipt', 'Solicitar acreditación', null],
        [route('portal.transfers'), 'fa-exchange-alt', 'Hacer una transferencia', null],
    ] as [$url, $icon, $label, $count])
        <div class="col-12 col-sm-6 col-xl-3">
            <a class="card portal-action h-100 text-decoration-none" href="{{ $url }}">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="portal-action-icon" aria-hidden="true"><i class="fa {{ $icon }}"></i></span>
                    <span class="flex-grow-1">
                        @if($count !== null)<strong class="d-block fs-4">{{ $count }}</strong>@endif
                        <span class="fw-semibold">{{ $label }}</span>
                    </span>
                    <i class="fa fa-chevron-right small" aria-hidden="true"></i>
                </div>
            </a>
        </div>
    @endforeach
</div>

<section class="card portal-section mb-4" id="mis-cuentas" aria-labelledby="titulo-cuentas">
    <div class="card-header bg-white py-3"><h2 class="h5 mb-0" id="titulo-cuentas">Mis cuentas de ahorro</h2></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th scope="col">Código de cuenta</th><th scope="col">Tipo de ahorro</th><th scope="col">Estado</th><th scope="col">Movimientos</th></tr></thead>
                <tbody>
                    @forelse($accounts as $account)
                        <tr>
                            <th scope="row">{{ $account->codigo }}</th>
                            <td>{{ optional($account->tipoAhorros)->name ?: 'Cuenta de ahorro' }}</td>
                            <td><span class="badge {{ $account->status ? 'bg-success' : 'bg-secondary' }}">{{ $account->status ? 'Activa' : 'Inactiva' }}</span></td>
                            <td><a class="btn btn-sm btn-outline-primary text-nowrap" href="{{ route('portal.movements', $account->id) }}" aria-label="Ver movimientos de {{ $account->codigo }}">Ver movimientos</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No tienes cuentas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $accounts->appends(request()->except('cuentas_page'))->fragment('mis-cuentas')->links('pagination::bootstrap-4') }}
    </div>
</section>

<section class="card portal-section" id="mis-creditos" aria-labelledby="titulo-creditos">
    <div class="card-header bg-white py-3"><h2 class="h5 mb-0" id="titulo-creditos">Mis créditos</h2></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th scope="col">Código</th><th scope="col">Fecha</th><th scope="col" class="text-end">Monto solicitado</th><th scope="col">Estado</th><th scope="col">Cuotas</th></tr></thead>
                <tbody>
                    @forelse($credits as $credit)
                        <tr>
                            <th scope="row">{{ $credit->code }}</th>
                            <td class="text-nowrap">{{ $credit->date_created }}</td>
                            <td class="text-end text-nowrap">$ {{ number_format((float) $credit->valor_solicitado, 2) }}</td>
                            <td><span class="badge bg-secondary">{{ $credit->status }}</span></td>
                            <td><a class="btn btn-sm btn-outline-primary text-nowrap" href="{{ route('portal.credit', $credit->id) }}" aria-label="Ver cuotas de {{ $credit->code }}">Ver cuotas</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No tienes créditos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $credits->appends(request()->except('creditos_page'))->fragment('mis-creditos')->links('pagination::bootstrap-4') }}
    </div>
</section>
@endsection
