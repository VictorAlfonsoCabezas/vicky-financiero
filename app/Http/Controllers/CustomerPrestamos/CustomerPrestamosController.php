<?php

namespace App\Http\Controllers\CustomerPrestamos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerPrestamosController extends Controller
{
    public function index()
    {
        return view('customer-prestamos.index');
    }
}