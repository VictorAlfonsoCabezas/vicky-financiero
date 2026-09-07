<?php

namespace App\Http\Controllers\EstadoCivil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EstadoCivilController extends Controller
{
    public function index() {
        return view('estado-civil/index');
    }
}
