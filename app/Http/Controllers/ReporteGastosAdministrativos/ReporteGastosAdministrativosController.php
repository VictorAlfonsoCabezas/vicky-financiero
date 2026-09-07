<?php

namespace App\Http\Controllers\ReporteGastosAdministrativos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteGastosAdministrativosController extends Controller
{
    public function index()
    {
        return view('reporte-gastos-admistrativos.index');
    }
}
