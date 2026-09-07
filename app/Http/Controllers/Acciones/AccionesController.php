<?php

namespace App\Http\Controllers\Acciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccionesController extends Controller
{
    public function index()
    {
        return view('acciones/index');
    }
}
