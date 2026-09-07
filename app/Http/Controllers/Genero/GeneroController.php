<?php

namespace App\Http\Controllers\Genero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeneroController extends Controller
{
    public function index()
    {
        return view('genero.index');
    }
}
