<?php

namespace App\Http\Controllers\CustomerMovimientoHistorial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerMovimientoHistorialController extends Controller
{
    public function index()
    {
        
        return view('customer-movimiento-historial.index');
    }
}
