<?php

namespace App\Http\Controllers\CentroCostos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CentroCostosController extends Controller
{
    public function index()
    {
        return view('centro-costos.index');
    }
}
