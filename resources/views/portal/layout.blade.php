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
        @include('portal.navigation')
        @if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @yield('content')
    </main>
</div>
<script src="{{ asset('assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('assets/js/app.min.js') }}"></script>
</body>
</html>
