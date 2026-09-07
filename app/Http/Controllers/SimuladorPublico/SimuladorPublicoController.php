<?php

namespace App\Http\Controllers\SimuladorPublico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SimuladorPublicoController extends Controller
{
    public function index()
    {
        return view('simuladorPublico.index');
    }
}
