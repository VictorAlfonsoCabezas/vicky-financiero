<?php

namespace App\Http\Controllers\Parroquia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParroquiaController extends Controller
{
    public function index() {
        return view('parroquia/index');
    }
}
