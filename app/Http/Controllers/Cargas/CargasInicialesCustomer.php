<?php

namespace App\Http\Controllers\Cargas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerExport;
use App\Imports\CustomerImport;
use Response;

class CargasInicialesCustomer extends Controller {

    public function index() {
        return view('cargas/customer');
    }

    public function cargarCustomer(Request $request) {
        $data = array();
        $xls = $request->file('file');
        $row = Excel::import(new CustomerImport, $xls);
        return Response::json(true);
    }

    public function descargaPlantilla() {
        $file_path = public_path('archivos/customerCargar.xlsx');
        return response()->download($file_path);
    }
    public function descargaPlantillaMovimientos() {
        $file_path = public_path('archivos/Plantilla Carga Cuentas.xlsx');
        return response()->download($file_path);
    }

}
