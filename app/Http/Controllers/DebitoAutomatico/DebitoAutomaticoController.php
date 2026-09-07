<?php

namespace App\Http\Controllers\DebitoAutomatico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DebitoAutomaticoController extends Controller
{
    public function index()
    {
        return view('debito-automatico.index');
    }
}
