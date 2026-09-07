<?php

namespace App\Http\Controllers\UserBovedas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserBovedasController extends Controller
{
    public function index()
    {
        return view('user-bovedas.index');
    }	
}
