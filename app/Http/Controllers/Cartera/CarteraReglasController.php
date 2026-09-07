<?php

namespace App\Http\Controllers\Cartera;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CarteraReglasController extends Controller
{
    public function index()
    {
        return view('cartera-reglas/index');
    }
}
