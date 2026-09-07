<?php

namespace App\Http\Controllers\Modulos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ModulosController extends Controller
{
    public function index()
    {
        return view('modulos.index');
    }
}
