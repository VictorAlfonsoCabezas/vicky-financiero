<?php

namespace App\Http\Middleware;

use App\Support\CustomerPortal;
use Closure;
use Illuminate\Support\Facades\Auth;

class CustomerPortalAccess
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) return $next($request);
        $customer = CustomerPortal::customer(Auth::user());
        $wasCustomer = $request->session()->get('auth.portal') === 'customer';
        if ((Auth::user()->exists && !Auth::user()->status) || ($customer && !$customer->status) || ($wasCustomer && !$customer)) {
            $login = $customer || $wasCustomer ? 'login2' : 'login';
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            if ($request->expectsJson()) abort(401);
            return redirect()->route($login)->withErrors(['error' => 'Tu acceso ya no está activo.']);
        }
        if (!$customer) return $next($request);
        $request->session()->put('auth.portal', 'customer');
        $request->attributes->set('portal_customer', $customer);
        if ($request->is('/', 'home', 'onix') && $request->isMethod('GET')) return redirect()->route('portal.index');
        if (!$request->is('login', 'login2') && !in_array($request->route()->getName(), ['portal.index', 'logout', 'logout2'], true)) abort(403);
        return $next($request);
    }
}
