<?php

namespace App\Http\Controllers\Impuestos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImpuestosController extends Controller
{
    public function index()
    {
        return view('impuestos.index');
    }
}
