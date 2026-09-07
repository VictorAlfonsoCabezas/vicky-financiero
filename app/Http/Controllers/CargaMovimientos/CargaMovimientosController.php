<?php

namespace App\Http\Controllers\CargaMovimientos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CargaMovimientosController extends Controller
{
    public function index()
    {
        return view('carga-movimientos.index');
    }
}
