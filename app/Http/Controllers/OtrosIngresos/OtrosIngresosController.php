<?php

namespace App\Http\Controllers\OtrosIngresos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OtrosIngresosController extends Controller
{
    public function index()
    {
    return view('otros-ingresos/index');
    }
}
