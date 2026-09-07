<?php

namespace App\Http\Controllers\LibroMayor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibroMayorController extends Controller
{
    public function index() {
        return view('libro-mayor/index');
    }
}
