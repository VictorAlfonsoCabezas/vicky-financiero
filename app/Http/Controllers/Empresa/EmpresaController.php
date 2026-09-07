<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index() {
        return view('empresa/index');
    }
}
