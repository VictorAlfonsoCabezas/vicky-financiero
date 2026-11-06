<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;

class TransactionMovementController extends Controller
{
    public function index()
    {
        return view('reportes.movimientos-transacciones');
    }
}
