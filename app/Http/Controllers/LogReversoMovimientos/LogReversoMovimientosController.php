<?php

namespace App\Http\Controllers\LogReversoMovimientos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogReversoMovimientosController extends Controller
{
    public function index()
    {
        return view('log-reverso-movimientos.index');
    }
}
