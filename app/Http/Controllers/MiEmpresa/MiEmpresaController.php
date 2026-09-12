<?php

namespace App\Http\Controllers\MiEmpresa;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Support\CustomerPortal;
use Illuminate\Support\Facades\Auth;

class MiEmpresaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless($user, 401);
        abort_if(($user->exists && !$user->status) || CustomerPortal::customer($user), 403);
        abort_unless($user->company_id, 403);
        Company::findOrFail($user->company_id);
        return view('mi-empresa.index');
    }
}
