<?php

namespace App\Http\Controllers\InteresFijoParametrizado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InteresFijoParametrizadoController extends Controller
{
    public function index() {
        return view('interes-fijo-parametrizado/index');
    }
}
