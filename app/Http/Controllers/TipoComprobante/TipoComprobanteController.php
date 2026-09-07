<?php

namespace App\Http\Controllers\TipoComprobante;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TipoComprobanteController extends Controller
{
    public function index()
    {
        return view('tipo-comprobante.index');
    }
}
