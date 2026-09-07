<?php

namespace App\Http\Controllers\TodosModulos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TodosModulosController extends Controller
{
    public function index()
    {
        return view('todos-modulos.index');
    }
}
