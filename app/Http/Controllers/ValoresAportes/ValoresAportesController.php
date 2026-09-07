<?php

namespace App\Http\Controllers\ValoresAportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ValoresAportesController extends Controller
{
    public function index()
    {
        return view('valores-aportes.index');
    }
}
