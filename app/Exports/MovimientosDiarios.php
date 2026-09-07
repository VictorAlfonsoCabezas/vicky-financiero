<?php

namespace App\Exports;

use App\Models\CustomerMovimiento;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MovimientosDiarios implements FromView {

    public function view(): View {
        $movimientos = CustomerMovimiento::all();        
        return view('reportes.export_movimientos_diarios')->with('movimientos', $movimientos);
    }

}
