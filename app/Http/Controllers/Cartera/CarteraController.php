<?php

namespace App\Http\Controllers\Cartera;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CarteraController extends Controller
{
    public function index()
    {
        return view('cartera/index');
    }
}
