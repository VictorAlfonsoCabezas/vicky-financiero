<?php

namespace App\Http\Controllers\SustentoTributario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SustentoTributarioController extends Controller
{
    public function index()
    {
        return view('sustento-tributario.index');
    }
}
