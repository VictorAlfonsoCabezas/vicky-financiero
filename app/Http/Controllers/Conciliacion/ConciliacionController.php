<?php

namespace App\Http\Controllers\Conciliacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConciliacionController extends Controller
{
    public function index() {
        return view('conciliacion/index');
    }
}
