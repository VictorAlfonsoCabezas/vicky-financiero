<?php

namespace App\Http\Controllers\ProformaHeader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProformaHeaderController extends Controller
{
    public function index() 
    {
        return view('proforma-header/index');
    }
}