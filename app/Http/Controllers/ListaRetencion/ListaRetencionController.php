<?php

namespace App\Http\Controllers\ListaRetencion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListaRetencionController extends Controller
{
    public function index()
    {
        return view('lista-retencion.index');
    }
}
