<?php

namespace App\Http\Controllers\CustomerCuentas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerCuentasController extends Controller
{
    public function index()
   {
      return view('customer-cuentas.index');
   }
}
