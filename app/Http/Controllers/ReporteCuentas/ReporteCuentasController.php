<?php

namespace App\Http\Controllers\ReporteCuentas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteCuentasController extends Controller
{
    public function index()
    {
        return view('reporte-cuentas.index');
    }
}
