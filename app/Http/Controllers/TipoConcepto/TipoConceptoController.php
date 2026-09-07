<?php

namespace App\Http\Controllers\TipoConcepto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TipoConceptoController extends Controller
{
    public function index()
    {
        return view('tipo-concepto.index');
    }
}
