<?php

namespace App\Http\Controllers\DenominacionBilletes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DenominacionBilletesController extends Controller
{
    
    public function index()
    {
        return view('denominacion-billetes/index');
    }

}
