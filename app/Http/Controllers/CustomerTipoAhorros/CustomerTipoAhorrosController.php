<?php

namespace App\Http\Controllers\CustomerTipoAhorros;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerTipoAhorrosController extends Controller
{
    public function index()
    {
        return view('customer-tipo-ahorros.index');
    }
}
