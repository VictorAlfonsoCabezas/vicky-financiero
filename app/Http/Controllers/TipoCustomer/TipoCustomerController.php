<?php

namespace App\Http\Controllers\TipoCustomer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TipoCustomerController extends Controller
{
    public function index()
    {
        return view('tipo-customer.index');
    }
}
