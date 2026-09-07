<?php

namespace App\Http\Controllers\ReportesResultados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportesResultadosController extends Controller
{
    public function index() {
        return view('reportes-resultados/index');
    }
}
