<?php

namespace App\Http\Controllers\MovimientosDiarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MovimientosDiariosController extends Controller
{
    public function index()
    {
        return view('movimientos-diarios.index');
    }
}
