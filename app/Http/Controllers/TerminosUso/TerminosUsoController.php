<?php

namespace App\Http\Controllers\TerminosUso;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TerminosUsoController extends Controller
{
    public function index()
    {
        return view('terminos-uso/index');
    }
}
