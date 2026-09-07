<?php

namespace App\Http\Controllers\ClasificacionFormasPago;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClasificacionFormasPagoController extends Controller
{
    public function index()
    {
        return view('clasificacion-formas-pago/index');
    }
}
