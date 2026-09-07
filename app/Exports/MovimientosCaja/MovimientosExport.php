<?php

namespace App\Exports\MovimientosCaja;

use App\Models\Cajas;
use App\Models\Company;
use App\Models\CustomerMovimiento;
use App\Models\TypeTransaction;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class MovimientosExport implements FromView
{
    public $id;


    public function __construct($id)
    {
        $this->id = $id;
    }
    public function view(): View
    {
        $company = Company::find(Auth::user()->company_id);
        $caja = Cajas::find($this->id);
        $usuarioBusquedaCaja = $caja->user_inicial_id;
        $tipotransacciones = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->whereIn('name_corto', ['IN', 'EG', 'DEA', 'PC', 'PCA', 'LIC', 'GAS',  'IOV', 'SE', 'SC'])
            ->get();
        foreach ($tipotransacciones as $tipotransaccion) {
            $tipotransaccion->movimientos = CustomerMovimiento::join('users', 'users.id', '=', 'customer_movimientos.user_created_id')
                ->leftJoin('formas_pago', 'formas_pago.id', '=', 'customer_movimientos.forma_pago_id') // Agregamos el LEFT JOIN
                ->where('customer_movimientos.type_transaction_id', $tipotransaccion->id)
                ->where('customer_movimientos.date_created', $caja->date_inicial)
                ->where('customer_movimientos.user_created_id', $usuarioBusquedaCaja)
                ->select(
                    'customer_movimientos.*',
                    'users.username as usuario_nombre',
                    'formas_pago.nombre as formaPagoNombre' // Seleccionamos el nombre de la forma de pago (ajusta según tu tabla)
                );
        }
        return view('reportes.reporte-movimientos-caja')
            ->with('company', $company)
            ->with('tipotransacciones', $tipotransacciones);
    }
}
