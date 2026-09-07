<?php

namespace App\Http\Controllers\CustomerTransferencias;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerTransferenciasController extends Controller
{

    public function index()
    {
        return view('customer-transferencias.index');
    }
}
