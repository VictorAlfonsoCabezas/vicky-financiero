<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientesController extends Controller
{
    public function index($id)
    {
        return view('clientes.index')->with('id', $id);
    }
}
