<?php

namespace App\Http\Controllers\FormasPago;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FormasPagoController extends Controller
{
    public function index() {
        return view('formas-pago/index');
    }
}
