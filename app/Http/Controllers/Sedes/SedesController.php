<?php

namespace App\Http\Controllers\Sedes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SedesController extends Controller
{
    public function index()
    {
        return view('sedes.index');
    }
}
