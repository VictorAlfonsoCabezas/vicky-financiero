<?php

namespace App\Http\Controllers\Parentezco;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParentezcoController extends Controller
{
    public function index()
    {
        return view('parentezco.index');
    }
}
   
   
   