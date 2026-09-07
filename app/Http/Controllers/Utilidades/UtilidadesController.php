<?php

namespace App\Http\Controllers\Utilidades;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UtilidadesController extends Controller
{
    public function index()
    {
        return view('utilidades.index');
    }
}
