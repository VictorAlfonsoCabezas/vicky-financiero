<?php

namespace App\Http\Controllers\SolicitudEncaje;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SolicitudEncajeController extends Controller
{
    public function index()
    {
        return view('solicitud-encaje.index');
    }
}
