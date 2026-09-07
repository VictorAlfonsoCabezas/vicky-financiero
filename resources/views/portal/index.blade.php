<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mi cuenta | Vicky Financiero</title>
    <link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/default/app.min.css') }}" rel="stylesheet">
</head>
<body>
<div id="app" class="app app-header-fixed app-without-sidebar">
    <header class="app-header">
        <div class="navbar-header"><a class="navbar-brand" href="{{ route('portal.index') }}"><span class="navbar-logo"></span><b>Caja</b> Web</a></div>
        <div class="navbar-nav ms-auto align-items-center">
            <span class="navbar-item d-none d-sm-block">{{ $customer->nombres }}</span>
            <form action="{{ route('logout2') }}" method="POST" class="navbar-item me-3">@csrf<button type="submit" class="btn btn-outline-secondary btn-sm">Cerrar sesión</button></form>
        </div>
    </header>
    <main class="app-content">
        <h1 class="page-header">Hola, {{ $customer->nombres }} <small>Tu Caja Web</small></h1>
        <p class="text-muted">Consulta tus cuentas y créditos asociados.</p>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card h-100"><div class="card-body"><div class="text-muted mb-2">Cliente</div><h5>{{ $customer->nombres }} {{ $customer->apellidos }}</h5><span>Código: {{ $customer->code }}</span></div></div></div>
            <div class="col-md-4"><a class="card h-100 text-decoration-none" href="#mis-cuentas"><div class="card-body"><div class="text-muted">Mis cuentas</div><div class="fs-1 text-primary">{{ $accounts->total() }}</div></div></a></div>
            <div class="col-md-4"><a class="card h-100 text-decoration-none" href="#mis-creditos"><div class="card-body"><div class="text-muted">Mis créditos</div><div class="fs-1 text-success">{{ $credits->total() }}</div></div></a></div>
        </div>
        <section class="panel panel-inverse" id="mis-cuentas">
            <div class="panel-heading"><h2 class="panel-title">Mis cuentas de ahorro</h2></div>
            <div class="panel-body">
                <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Código de cuenta</th><th>Tipo de ahorro</th><th>Estado</th></tr></thead>
                    <tbody>@forelse($accounts as $account)<tr><td>{{ $account->codigo }}</td><td>{{ optional($account->tipoAhorros)->name ?: 'Cuenta de ahorro' }}</td><td><span class="badge {{ $account->status ? 'bg-success' : 'bg-secondary' }}">{{ $account->status ? 'Activa' : 'Inactiva' }}</span></td></tr>@empty<tr><td colspan="3" class="text-muted">No tienes cuentas registradas.</td></tr>@endforelse</tbody>
                </table></div>
                {{ $accounts->appends(request()->except('cuentas_page'))->fragment('mis-cuentas')->links('pagination::bootstrap-4') }}
            </div>
        </section>
        <section class="panel panel-inverse" id="mis-creditos">
            <div class="panel-heading"><h2 class="panel-title">Mis créditos</h2></div>
            <div class="panel-body">
                <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Código</th><th>Fecha</th><th class="text-end">Monto solicitado</th><th>Estado</th></tr></thead>
                    <tbody>@forelse($credits as $credit)<tr><td>{{ $credit->code }}</td><td>{{ $credit->date_created }}</td><td class="text-end">{{ number_format($credit->valor_solicitado, 2) }}</td><td>{{ $credit->status }}</td></tr>@empty<tr><td colspan="4" class="text-muted">No tienes créditos registrados.</td></tr>@endforelse</tbody>
                </table></div>
                {{ $credits->appends(request()->except('creditos_page'))->fragment('mis-creditos')->links('pagination::bootstrap-4') }}
            </div>
        </section>
    </main>
</div>
<script src="{{ asset('assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('assets/js/app.min.js') }}"></script>
</body>
</html>
