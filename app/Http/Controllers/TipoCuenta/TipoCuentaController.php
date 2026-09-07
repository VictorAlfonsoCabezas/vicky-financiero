<?php

namespace App\Http\Controllers\TipoCuenta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TipoCuentaController extends Controller
{

    public function index()
    {

        return view('tipo-cuenta.index');
    }
}
