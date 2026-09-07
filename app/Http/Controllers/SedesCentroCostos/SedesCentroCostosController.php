<?php

namespace App\Http\Controllers\SedesCentroCostos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SedesCentroCostosController extends Controller
{
    public function index()
    {
        return view('sedes-centro-costos.index');
    }
}
