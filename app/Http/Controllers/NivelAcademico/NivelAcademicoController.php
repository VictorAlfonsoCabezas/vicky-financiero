<?php

namespace App\Http\Controllers\NivelAcademico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NivelAcademicoController extends Controller
{
    public function index()
    {
        return view('nivel-academico.index');
    }
}
