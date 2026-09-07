<?php

namespace App\Http\Controllers\CustomerMovimientoAprobacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerMovimientoAprobacionController extends Controller
{
    public function index()
    {
        return view('customer-movimiento-aprobacion.index');
    }
}
