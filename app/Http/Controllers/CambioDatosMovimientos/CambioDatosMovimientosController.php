<?php

namespace App\Http\Controllers\CambioDatosMovimientos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CambioDatosMovimientosController extends Controller
{
    public function index()
    {
        return view('cambio-datos-movimientos/index');
    }
}
