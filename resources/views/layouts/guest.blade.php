<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Recuperar contraseña') | Vicky Financiero</title>
    <link rel="icon" href="{{ asset('vicky/icono-fondo-blanc.png') }}" type="image/png">
    <link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/default/app.min.css') }}" rel="stylesheet">
</head>
<body class="pace-top">
    <div id="app" class="app">
        <main class="py-5">
            <div class="text-center mb-4">
                <a href="{{ route('login') }}" class="fs-4 text-decoration-none">
                    <img src="{{ asset('vicky/icono-fondo-blanc.png') }}" alt="" width="30" height="30">
                    <b>Vicky</b> Financiero
                </a>
            </div>
            @yield('content')
            <div class="text-center mt-4">
                <a href="{{ route('login') }}">Volver al inicio de sesión</a>
            </div>
        </main>
    </div>
</body>
</html>
