<?php

namespace App\Http\Controllers\Gasto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GastoController extends Controller
{
    public function index()
    {
        return view('gasto.index');
    }
}
