<?php

namespace App\Http\Controllers\TypeTransactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TypeTransactionsController extends Controller
{
    public function index()
    {
       return view('type-transactions.index');
    }
}
