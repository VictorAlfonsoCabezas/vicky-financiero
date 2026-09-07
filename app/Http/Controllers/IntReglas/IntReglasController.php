<?php

namespace App\Http\Controllers\IntReglas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IntReglasController extends Controller
{
    public function index()
    {
        return view('int-reglas.index');
    }
}
