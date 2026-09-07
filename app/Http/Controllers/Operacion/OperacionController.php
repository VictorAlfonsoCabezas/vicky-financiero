<?php

namespace App\Http\Controllers\Operacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OperacionController extends Controller
{
    public function index()
    {
        return view('operacion.index');
    }
}
