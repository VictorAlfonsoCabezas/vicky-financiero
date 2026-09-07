<?php

namespace App\Http\Controllers\CustomerMovimientoSolicitud;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerMovimientoSolicitudController extends Controller
{
    public function index()
    {
        return view('customer-movimiento-solicitud.index');
    }
}
