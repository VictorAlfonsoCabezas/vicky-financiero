<?php

namespace App\Http\Controllers\SolicitudPagosLiquidacion;

use App\Http\Controllers\Controller;

class SolicitudPagosLiquidacionController extends Controller
{
    public function index()
    {
        return view('solicitud-pagos-liquidacion.index');
    }
}