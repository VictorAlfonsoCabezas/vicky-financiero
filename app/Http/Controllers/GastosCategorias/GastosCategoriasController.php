<?php

namespace App\Http\Controllers\GastosCategorias;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GastosCategoriasController extends Controller
{
    public function index()
    {
        return view('gastos-categorias.index');
    }
}
