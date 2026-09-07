<?php

namespace App\Http\Controllers\TipoAhorros;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TipoAhorrosController extends Controller
{
    public function index()
    {
        return view('tipo-ahorros.index');
    }
}
