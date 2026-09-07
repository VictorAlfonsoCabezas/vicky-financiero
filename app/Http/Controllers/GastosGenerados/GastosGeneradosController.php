<?php

namespace App\Http\Controllers\GastosGenerados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GastosGeneradosController extends Controller
{
    public function index() {
        return view('gastos-generados/index');
    }
}
