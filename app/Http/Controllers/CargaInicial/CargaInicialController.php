<?php

namespace App\Http\Controllers\CargaInicial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CargaInicialController extends Controller
{
    public function index()
    {
        return view('carga-inicial/index');
    }
}
