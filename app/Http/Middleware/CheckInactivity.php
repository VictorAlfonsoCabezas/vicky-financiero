<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckInactivity
{
    public function handle($request, Closure $next)
    {
        $last = $request->session()->get('last_activity');
        if (Auth::check() && $last && time() - $last > 30 * 60) {
            $login = $request->session()->get('auth.portal') === 'customer' ? 'login2' : 'login';
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            if ($request->expectsJson()) abort(401);
            return redirect()->route($login)->with('mensaje', 'Tu sesión ha expirado por inactividad.');
        }
        $request->session()->put('last_activity', time());
        return $next($request);
    }
}
