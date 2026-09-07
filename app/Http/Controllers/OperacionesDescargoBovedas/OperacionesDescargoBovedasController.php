<?php

namespace App\Http\Controllers\OperacionesDescargoBovedas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OperacionesDescargoBovedasController extends Controller
{
    public function index() {
        return view('operaciones-descargo-bovedas/index');
    }
}
