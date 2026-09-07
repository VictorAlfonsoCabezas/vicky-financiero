<?php

namespace App\Http\Controllers\Ajustes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AjustesController extends Controller
{
    public function index()
    {
       return view('ajustes.index');
    }
}
