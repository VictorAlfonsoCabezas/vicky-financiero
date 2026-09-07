<?php

namespace App\Http\Controllers\CustomerSimulador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerSimuladorController extends Controller
{
    public function index()
    {
        return view('customer-simulador.index');
    }
}
