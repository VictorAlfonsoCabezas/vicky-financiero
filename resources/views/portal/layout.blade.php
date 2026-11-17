<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mi cuenta') | Caja Web</title>
    <link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/default/app.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/portal.css') }}" rel="stylesheet">
</head>
<body>
<div id="app" class="app app-header-fixed app-sidebar-fixed customer-portal{{ request()->routeIs('portal.credit') ? ' portal-sidebar-compact' : '' }}">
    <a class="visually-hidden-focusable portal-skip" href="#portal-content">Ir al contenido</a>
    <header id="header" class="app-header">
        <div class="navbar-header">
            <a class="navbar-brand" href="{{ route('portal.index') }}"><span class="navbar-logo"></span><b>Caja</b> Web</a>
            <button type="button" class="navbar-mobile-toggler" data-toggle="app-sidebar-mobile" aria-controls="sidebar" aria-label="Menú de Mi cuenta">
                <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
            </button>
        </div>
        <div class="navbar-nav ms-auto align-items-center">
            <form action="{{ route('logout2') }}" method="POST" class="navbar-item mx-2 mx-md-3">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm text-nowrap"><i class="fa fa-sign-out-alt me-1" aria-hidden="true"></i> Cerrar sesión</button>
            </form>
        </div>
    </header>
    @include('portal.navigation')
    <main id="portal-content" class="app-content" tabindex="-1">
        <div class="portal-container">
            @if(!request()->routeIs('portal.index'))
                <a class="portal-back" href="{{ route('portal.index') }}"><i class="fa fa-arrow-left me-2" aria-hidden="true"></i> Mi cuenta</a>
            @endif
            @if(session('message'))<div class="alert alert-success" role="status">{{ session('message') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </div>
    </main>
</div>
<script src="{{ asset('assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('assets/js/app.min.js') }}"></script>
</body>
</html>
