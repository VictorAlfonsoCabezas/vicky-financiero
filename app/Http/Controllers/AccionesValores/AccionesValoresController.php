<?php

namespace App\Http\Controllers\AccionesValores;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccionesValoresController extends Controller
{
    public function index()
    {
        return view('acciones-valores.index');
    }
}
