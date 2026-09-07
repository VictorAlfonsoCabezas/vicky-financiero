<?php

namespace App\Http\Controllers\ReporteIngresos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteIngresosController extends Controller
{
    public function index()
    {
        return view('reporte-ingresos/index');
    }
}
