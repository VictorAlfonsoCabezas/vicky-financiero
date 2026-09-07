<?php

namespace App\Http\Controllers\Comandos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComandosController extends Controller
{
    public function index()
    {
        return view('comandos.index');
    }
}
