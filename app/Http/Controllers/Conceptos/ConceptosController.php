<?php

namespace App\Http\Controllers\Conceptos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConceptosController extends Controller
{
    public function index()
    {
        return view('conceptos.index');
    }
}
