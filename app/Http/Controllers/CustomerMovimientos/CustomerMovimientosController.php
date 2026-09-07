<?php

namespace App\Http\Controllers\CustomerMovimientos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerMovimientosController extends Controller
{
   public function index()
   {
      return view('customer-movimientos.index');
   }
}
