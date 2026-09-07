<?php

namespace App\Http\Controllers\CustomerUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerUserController extends Controller
{
    public function index()
    {
        return view('customer-user.index');
    }
}

