<?php

namespace App\Http\Controllers\CalculadoraPlazoFijo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CalculadoraPlazoFijoController extends Controller
{
    public function index() {
        return view('calculadora-plazo-fijo.index');
    }
}
