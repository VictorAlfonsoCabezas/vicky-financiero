<?php

namespace App\Http\Controllers\Provincia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProvinciaController extends Controller
{
    public function index() {
        return view('provincia/index');
    }
}
