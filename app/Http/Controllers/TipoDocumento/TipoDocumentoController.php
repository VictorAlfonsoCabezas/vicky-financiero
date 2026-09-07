<?php

namespace App\Http\Controllers\TipoDocumento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    public function index()
    {
        return view('tipo-documento.index');
    }
}


   
   