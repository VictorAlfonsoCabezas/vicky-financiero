<form method="POST" action="{{ route($customerLogin ? 'login2' : 'login') }}">
    @csrf
    @if(session('mensaje'))<div class="alert alert-info" role="status">{{ session('mensaje') }}</div>@endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <div class="form-floating mb-3">
        <input type="text" name="username" id="username" value="{{ old('username') }}" class="form-control h-45px" placeholder="Usuario" autocomplete="username" required autofocus>
        <label for="username" class="text-gray-600">Usuario</label>
    </div>
    <div class="form-floating mb-3">
        <input type="password" name="password" id="password" class="form-control h-45px" placeholder="Contraseña" autocomplete="current-password" required>
        <label for="password" class="text-gray-600">Contraseña</label>
    </div>
    <div class="form-check mb-3">
        <input type="checkbox" name="remember" value="1" id="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
        <label for="remember" class="form-check-label">Recordarme</label>
    </div>
    <button type="submit" class="btn {{ $customerLogin ? 'btn-primary' : 'btn-success' }} w-100 btn-lg mb-3">
        {{ $customerLogin ? 'Ingresar a mi cuenta' : 'Ingresar a administración' }}
    </button>
    @if(Route::has('password.request'))
        <div class="mb-3"><a href="{{ route('password.request') }}" class="{{ $customerLogin ? '' : 'text-white' }}">¿Olvidaste tu contraseña?</a></div>
    @endif
    <hr>
    <p class="mb-1">{{ $customerLogin ? '¿Eres parte del equipo?' : '¿Eres cliente?' }}</p>
    <a href="{{ route($customerLogin ? 'login' : 'login2') }}" class="{{ $customerLogin ? '' : 'text-white' }} fw-bold">
        {{ $customerLogin ? 'Acceso administrativo' : 'Ir a Caja Web para clientes' }} <i class="fa fa-arrow-right ms-1" aria-hidden="true"></i>
    </a>
</form>
