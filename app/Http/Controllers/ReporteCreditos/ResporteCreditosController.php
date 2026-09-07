<?php

namespace App\Http\Controllers\ReporteCreditos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResporteCreditosController extends Controller
{
    public function index()
    {
        return view('reporte-creditos.index');
    }
}
