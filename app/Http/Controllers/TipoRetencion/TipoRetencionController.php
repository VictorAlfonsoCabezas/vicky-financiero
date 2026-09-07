<?php

namespace App\Http\Controllers\TipoRetencion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TipoRetencionController extends Controller
{
    public function index()
    {
        return view('tipo-retencion.index');
    }
}
