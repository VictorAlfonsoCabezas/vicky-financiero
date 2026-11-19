<?php

namespace App\Http\Controllers\CalificacionSocios;

use App\Http\Controllers\Controller;
use App\Services\CalificacionCrediticia;

class CalificacionSociosController extends Controller
{
    public function index(CalificacionCrediticia $calificacion)
    {
        $calificacion->empresaId();

        return view('calificacion-socios.index');
    }
}
