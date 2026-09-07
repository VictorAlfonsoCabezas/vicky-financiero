<?php

namespace App\Http\Controllers\ReporteCarteraNiveles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteCarteraNivelesController extends Controller
{
    //
    public function index()
    {
        return view('reporte-cartera-niveles.index');
    }
}
