<aside id="sidebar" class="app-sidebar">
    <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
        <div class="portal-profile">
            <span class="portal-profile-icon" aria-hidden="true"><i class="fa fa-user"></i></span>
            <div class="portal-profile-details">
                <strong class="d-block">{{ $customer->nombres }} {{ $customer->apellidos }}</strong>
                <small>{{ $customer->code }}</small>
            </div>
        </div>
        <nav class="menu" aria-label="Mi cuenta">
            @foreach([
                ['portal.index', ['portal.index', 'portal.movements', 'portal.credit'], 'fa-home', 'Cuentas y préstamos'],
                ['portal.requests', ['portal.requests'], 'fa-receipt', 'Acreditaciones'],
                ['portal.transfers', ['portal.transfers'], 'fa-exchange-alt', 'Transferencias'],
                ['portal.simulator', ['portal.simulator'], 'fa-calculator', 'Simulador'],
                ['portal.password', ['portal.password'], 'fa-lock', 'Cambiar contraseña'],
                ['portal.terms', ['portal.terms'], 'fa-file-alt', 'Términos de uso'],
            ] as [$route, $routes, $icon, $label])
                <div class="menu-item {{ request()->routeIs(...$routes) ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route($route) }}" aria-label="{{ $label }}" title="{{ $label }}" @if(request()->routeIs($route)) aria-current="page" @endif>
                        <span class="menu-icon" aria-hidden="true"><i class="fa {{ $icon }}"></i></span>
                        <span class="menu-text">{{ $label }}</span>
                    </a>
                </div>
            @endforeach
        </nav>
    </div>
</aside>
<div class="app-sidebar-bg"></div>
<div class="app-sidebar-mobile-backdrop"><a href="#" data-dismiss="app-sidebar-mobile" class="stretched-link" aria-label="Cerrar menú"></a></div>
