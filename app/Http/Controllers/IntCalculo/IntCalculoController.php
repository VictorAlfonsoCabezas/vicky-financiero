<?php

namespace App\Http\Controllers\IntCalculo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IntCalculoController extends Controller
{
    public function index()
    {
        return view('int-calculo.index');
    }
}
