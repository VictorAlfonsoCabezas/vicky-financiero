<?php

namespace App\Http\Controllers\Bancos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BancosController extends Controller
{
    public function index()
    {
        return view('bancos/index');
    }
}
