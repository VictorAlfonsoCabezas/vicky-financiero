<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CajasManagerController extends Controller
{
    public function index() {
        return view('cajas/index_manager');
    }
}
