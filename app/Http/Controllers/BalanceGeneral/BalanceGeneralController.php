<?php

namespace App\Http\Controllers\BalanceGeneral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BalanceGeneralController extends Controller
{
    public function index() {
        return view('balance-general/index');
    }
}
