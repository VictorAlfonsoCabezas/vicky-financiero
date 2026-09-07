<?php

namespace App\Http\Controllers\CreditosAutomaticos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreditosAutomaticosController extends Controller
{
    public function index() {
        return view('creditos-automaticos.index');
    }
}
