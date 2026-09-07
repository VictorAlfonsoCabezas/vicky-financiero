<?php

namespace App\Exports\Gastos;

use App\Models\Company;
use App\Models\CreditFolderHeader;
use App\Models\Customer;
use App\Models\CustomerMovimiento;
use App\Models\CustomerTipoAhorros;
use App\Models\Prestamos;
use App\Models\TipoAhorros;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class GastosExport implements FromView
{
    public $fecha_inicio;
    public $fecha_fin;
    public $cuenta_prestamo;
    public function __construct($fecha_inicio, $fecha_fin, $cuenta_prestamo)
    {
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->cuenta_prestamo = $cuenta_prestamo;
    }


    public function view(): View
    {
        $inicio = $this->fecha_inicio;
        $fin = $this->fecha_fin;
        $company = Company::find(Auth::user()->company_id);
        $creditos = CreditFolderHeader::where('status', 'ENTREGADO')
            ->where('date_created', '>=', $inicio)
            ->where('date_created', '<=', $fin)
            ->orderBy('code', 'asc')
            ->get();
        $sumnaCreditosGastos = 0;
        $sumnaCreditosGastosPrimer = 0;
        $sumnaCreditosGastosSegundo = 0;
        $sumnaCreditosGastosTercero = 0;

        foreach ($creditos as $val) {
            $customer = Customer::find($val->customer_id);
            $val->customerIdentificacion =  $customer->numero_documento;
            $val->customerNombre =  $customer->apellidos . ' '  . $customer->nombres;
            $pretamo = Prestamos::find($val->tipo_prestamo);
            $val->prestamoNombre = '';
            $val->prestamoInteres = '';
            if ($pretamo) {
                $val->prestamoNombre = $pretamo->name;
                $val->prestamoInteres = $pretamo->interes;
            }
            $sumnaCreditosGastos = $sumnaCreditosGastos + $val->gasto_administrativo;
            $sumnaCreditosGastosPrimer = $sumnaCreditosGastosPrimer + $val->primer_gasto;
            $sumnaCreditosGastosSegundo = $sumnaCreditosGastosSegundo + $val->segundo_gasto;
            $sumnaCreditosGastosTercero = $sumnaCreditosGastosTercero + $val->tercer_gasto;
        }
        $cuentas = CustomerMovimiento::select('customer_tipo_ahorro_id', 'customer_name', 'customer_ruc', 'observation', 'date_created', DB::raw('SUM(valor_movimiento) as total_valor_movimiento'))
            ->whereNotNull('customer_tipo_ahorro_id')
            ->where('date_created', '>=', $this->fecha_inicio)
            ->where('date_created', '<=', $this->fecha_fin)
            ->whereIn('type_transaction_id', [20, 21])
            ->groupBy('customer_tipo_ahorro_id')
            ->get();
        $sumnaCuentas = 0;
        foreach ($cuentas  as $valCuenta) {
            $cuatomerTipoAhorro =  CustomerTipoAhorros::find($valCuenta->customer_tipo_ahorro_id);
            $valCuenta->numeroCuenta =  $cuatomerTipoAhorro->codigo;
            $ahorr =  TipoAhorros::find($cuatomerTipoAhorro->tipo_ahorros_id);
            $valCuenta->nombreHorro = $ahorr->name;
            $sumnaCuentas = $sumnaCuentas + $valCuenta->total_valor_movimiento;
        }
        return view('reportes.reporte-gastos-administrativos')
            ->with('creditos', $creditos)
            ->with('sumnaCreditosGastos', $sumnaCreditosGastos)
            ->with('sumnaCreditosGastosPrimer', $sumnaCreditosGastosPrimer)
            ->with('sumnaCreditosGastosSegundo', $sumnaCreditosGastosSegundo)
            ->with('sumnaCreditosGastosTercero', $sumnaCreditosGastosTercero)
            ->with('cuentas', $cuentas)
            ->with('sumnaCuentas', $sumnaCuentas)
            ->with('cuenta_prestamo', $this->cuenta_prestamo)
            ->with('company', $company);
    }
}
