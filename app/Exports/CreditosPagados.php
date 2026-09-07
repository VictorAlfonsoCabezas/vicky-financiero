<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\CustomerMovimiento;
use App\Models\CreditFolderHeader;
use App\Models\CreditFolderDetail;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Prestamos;
use Illuminate\Support\Facades\Auth;

class CreditosPagados implements FromView
{

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function view(): View
    {
        $datos = $this->data;
        $inicio = $datos['inicio'];
        $fin = $datos['fin'];
        $creditos = CreditFolderHeader::where('date_created', '>=', $inicio)
            ->where('date_created', '<=', $fin)
            //->whereIn('status', ['ENTREGADO', 'FINALIZADO'])
            ->orderBy('code', 'asc')
            ->get();
        $company = Company::find(Auth::user()->company_id);

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
            $val->valorintereses = CreditFolderDetail::where('code_folder_header', $val->code)->sum('interes_periodo');
            $val->valorPagado = CreditFolderDetail::where('code_folder_header', $val->code)->where('status', 'PAGADA')->sum('valor_cuota');
            $val->valorPendiente = CreditFolderDetail::where('code_folder_header', $val->code)->where('status', 'PENDIENTE')->sum('valor_cuota');
            $val->pendientesFecha = CreditFolderDetail::where('code_folder_header', $val->code)->where('date_vencimiento', '<=', date('Y-m-d'))->where('status', 'PENDIENTE')->count();
        }

        return view('reportes.export_creditos')->with('creditos', $creditos)->with('company', $company);
        /*
    $datos = $this->data;
    $inicio = $datos['inicio'];
    $fin = $datos['fin'];
    $creditos = CreditFolderHeader::where('date_created', '>=', $inicio)->where('date_created', '<=', $fin)->whereIn('status' , ['ENTREGADO','FINALIZADO'])->get();
    foreach ($creditos as $val) {
        $val->letras = CreditFolderDetail::where('code_folder_header', $val->code)->get();
        $val->interesPeriodo = CreditFolderDetail::where('code_folder_header', $val->code)->sum('interes_periodo');
        $val->capitalAmortizado = CreditFolderDetail::where('code_folder_header', $val->code)->sum('capital_amortizado');
        $val->fondoDesgrabamen = CreditFolderDetail::where('code_folder_header', $val->code)->sum('fondo_desgravamen');
        $val->CuotasPagar = CreditFolderDetail::where('code_folder_header', $val->code)->sum('valor_cuota');
        $val->valoresPagados = CreditFolderDetail::where('code_folder_header', $val->code)->sum('valor_pagado');
        $val->interesdeMora = CreditFolderDetail::where('code_folder_header', $val->code)->sum('interes_mora');
    }
    $company = Company::find(Auth::user()->company_id);
   
    return view('reportes.export_creditos')->with('creditos', $creditos)->with('company', $company);
    */
    }
}
