<?php

namespace App\Http\Controllers\DocumentosParametrizados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DocumentosParametrizadosController extends Controller
{
    public function index()
    {
        return view('documentos-parametrizados/index');
    }

    public function formatoCreado($id, $doc)
    {
        return view('documentos-parametrizados/formato')
            ->with('doc', $doc)
            ->with('id', $id);
    }
}
