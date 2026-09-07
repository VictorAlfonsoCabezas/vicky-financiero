<?php

namespace App\Http\Controllers\ReporteCartera;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteCarteraController extends Controller
{
    public function index()
    {

        return view('reporte-cartera.index');
    }
}
