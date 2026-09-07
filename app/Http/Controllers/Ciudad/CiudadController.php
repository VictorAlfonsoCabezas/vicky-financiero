<?php

namespace App\Http\Controllers\Ciudad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CiudadController extends Controller
{
    public function index() {
        return view('ciudad/index');
    }
}
