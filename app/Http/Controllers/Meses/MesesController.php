<?php

namespace App\Http\Controllers\Meses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MesesController extends Controller
{
    public function index()
    {
        return view('meses.index');
    }

}
