<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $customerLogin ? 'Acceso de clientes' : 'Acceso administrativo' }} | Vicky Financiero</title>
    <link rel="icon" href="{{ asset('intelho/logo_mini.png') }}" type="image/png">
    <link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/default/app.min.css') }}" rel="stylesheet">
</head>
<body class="pace-top">
<div id="app" class="app">
    <div class="login {{ $customerLogin ? 'login-with-news-feed' : 'login-v2' }}">
        @if($customerLogin)
            <div class="news-feed">
                <div class="news-image" style="background-image:url('{{ asset('assets/img/login-bg/login-bg-11.jpg') }}')"></div>
                <div class="news-caption"><h4 class="caption-title">Tu <b>Caja Web</b></h4><p>Consulta tus cuentas y créditos en un solo lugar.</p></div>
            </div>
        @else
            <div class="login-cover"><div class="login-cover-img" style="background-image:url('{{ asset('assets/img/login-bg/login-bg-17.jpg') }}')"></div><div class="login-cover-bg"></div></div>
        @endif
        <div class="login-container">
            <div class="login-header">
                <div class="brand">
                    <div class="d-flex align-items-center"><img src="{{ asset('intelho/logo_mini.png') }}" alt="" width="30" height="30" class="me-2"><b>Vicky</b>&nbsp;Financiero</div>
                    <small>{{ $customerLogin ? 'Clientes · Caja Web' : 'Administración' }}</small>
                </div>
                <div class="icon"><i class="fa {{ $customerLogin ? 'fa-user' : 'fa-lock' }}" aria-hidden="true"></i></div>
            </div>
            <div class="login-content">
                <p class="mb-4">{{ $customerLogin ? 'Ingresa con el usuario asociado a tu cuenta de cliente.' : 'Ingresa para gestionar tu empresa.' }}</p>
                @include('auth.access-form')
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('assets/js/app.min.js') }}"></script>
<script src="{{ asset('assets/js/theme/default.min.js') }}"></script>
</body>
</html>
