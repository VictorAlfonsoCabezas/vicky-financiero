<?php

namespace App\Http\Controllers\SolicitudPagosCreditos;

use App\Http\Controllers\Controller;
use App\Models\CreditFolderDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SolicitudPagosCreditosController extends Controller
{
    public function index()
    {
        return view('solicitud-pagos-creditos.index');
    }

    public function comprobante($id)
    {
        $detalle = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->findOrFail($id);

        $path = ltrim((string) $detalle->path, '/');

        if (
            $path !== 'uploads/comprobantes_pagos/' . basename($path)
            || !Storage::disk('public')->exists($path)
        ) {
            abort(404, 'El comprobante no se encuentra disponible.');
        }

        return response()->file(Storage::disk('public')->path($path));
    }
}
