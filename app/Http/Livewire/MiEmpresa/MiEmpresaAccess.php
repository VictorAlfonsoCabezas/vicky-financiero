<?php

namespace App\Http\Livewire\MiEmpresa;

use App\Support\CustomerPortal;
use Illuminate\Support\Facades\Auth;

trait MiEmpresaAccess
{
    protected function authorizeMiEmpresaAccess()
    {
        abort_unless(Auth::check(), 401);
        abort_if((Auth::user()->exists && !Auth::user()->status) || CustomerPortal::customer(Auth::user()), 403);
        $last = session('last_activity');
        if ($last && time() - $last > 1800) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            abort(401);
        }
        session(['last_activity' => time()]);
    }

    public function hydrate()
    {
        $this->authorizeMiEmpresaAccess();
    }
}
