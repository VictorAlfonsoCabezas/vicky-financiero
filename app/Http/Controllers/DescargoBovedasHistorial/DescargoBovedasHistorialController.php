<?php

namespace App\Http\Controllers\DescargoBovedasHistorial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DescargoBovedasHistorialController extends Controller
{
    public function index()
    {
        return view('descargo-bovedas-historial/index');
    }
}
