<?php

namespace App\Http\Controllers\ReporteNiveles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteNivelesController extends Controller
{
    public function index()
    {
        
        return view('reporte-niveles.index');
    }
}
