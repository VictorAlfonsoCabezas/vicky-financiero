<?php

namespace App\Http\Controllers\ConfiguracionCuenta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfiguracionCuentaController extends Controller
{
    public function index() {
        return view('configuracion-cuenta/index');
    }
}

