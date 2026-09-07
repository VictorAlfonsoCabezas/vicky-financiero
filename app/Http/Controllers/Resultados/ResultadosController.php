<?php

namespace App\Http\Controllers\Resultados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\TypeTransaction;
use App\Models\CustomerMovimiento;
use App\Models\Prestamos;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;
use Response;

class ResultadosController extends Controller {

    private function interesesPagados($inicio, $fin)
    {
        validator(compact('inicio', 'fin'), [
            'inicio' => 'required|date_format:Y-m-d',
            'fin' => 'required|date_format:Y-m-d|after_or_equal:inicio',
        ])->validate();
        return DB::table('credit_folder_details')
            ->where('company_id', Auth::user()->company_id)
            ->whereBetween('date_pay', [$inicio, $fin])->where('status', 'PAGADA')
            ->selectRaw('code_folder_header, SUM(interes_periodo) as total_intereses_periodo, SUM(interes_mora) as total_intereses_mora')
            ->groupBy('code_folder_header')->get();
    }

    private function interesesAhorro($inicio, $fin, $transactionId)
    {
        if (!$transactionId) return collect();
        return DB::table('customer_movimientos')
            ->join('customer_tipo_ahorros', 'customer_movimientos.customer_tipo_ahorro_id', '=', 'customer_tipo_ahorros.id')
            ->join('tipo_ahorros', 'customer_tipo_ahorros.tipo_ahorros_id', '=', 'tipo_ahorros.id')
            ->where('customer_movimientos.company_id', Auth::user()->company_id)
            ->where('customer_tipo_ahorros.company_id', Auth::user()->company_id)
            ->where('customer_movimientos.status', true)
            ->where('customer_movimientos.automatico', 1)
            ->where('customer_movimientos.type_transaction_id', $transactionId)
            ->whereBetween('customer_movimientos.date_created', [$inicio, $fin])
            ->selectRaw('SUM(customer_movimientos.valor_movimiento) as total_valor_movimiento, tipo_ahorros.name')
            ->groupBy('tipo_ahorros.id', 'tipo_ahorros.name')->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('resultados/index');
    }

    public function generarResultados($inicio, $fin) {
        $arrayValores = array();
        $dataRes = array();
        $eventos = $this->interesesPagados($inicio, $fin);
        $sumaIntereses = 0;
        foreach ($eventos as $val) {
            $header = CreditFolderHeader::where('company_id', Auth::user()->company_id)->where('code', $val->code_folder_header)->first();
            $nomPrestamo = 'CARGA MASIVA';
            if ($header && $header->tipo_prestamo) {
                $prestamo = Prestamos::find($header->tipo_prestamo);
                $nomPrestamo = $prestamo ? $prestamo->name : 'Préstamo no disponible';
            }
            $arrayValores[] = [
                'prestamoNombre' => $nomPrestamo,
                'interesPrestamo' => $val->total_intereses_periodo,
                'interesMOra' => $val->total_intereses_mora,
            ];
            $sumaIntereses += $val->total_intereses_periodo + $val->total_intereses_mora;
        }
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'GAS')
                ->first();
        $sumaGastos = CustomerMovimiento::where('afecta', 'E')
                ->where('type_transaction_id', optional($transaction)->id ?? -1)
                ->where('status', true)
                ->where('company_id', Auth::user()->company_id)
                ->whereBetween('date_created', [$inicio, $fin])
                ->sum('valor_movimiento');
        //        EGRESO INTERES EMPRESA
        //        gastos de ahorros
        $transactionInteresEmpresa = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'EG')
                ->first();
        $arrayValoresAho = array();
        $eventosAho = $this->interesesAhorro($inicio, $fin, optional($transactionInteresEmpresa)->id);
        $sumaGastosinteresAhorro = 0;
        foreach ($eventosAho as $valAho) {
            $arrayValoresAho[] = [
                'ahorroNombre' => $valAho->name,
                'valorAhorro' => $valAho->total_valor_movimiento,
            ];
            $sumaGastosinteresAhorro += $valAho->total_valor_movimiento;
        }
        $dataRes = [
            'ingresos' => $sumaIntereses,
            'egresos' => round($sumaGastos + $sumaGastosinteresAhorro, 2)
        ];
        return Response::json($dataRes);
    }

    public function generarPdfResultados($inicio, $fin) {
        $arrayValores = array();
        $eventos = $this->interesesPagados($inicio, $fin);
        $sumaIntereses = 0;
        foreach ($eventos as $val) {
            $header = CreditFolderHeader::where('company_id', Auth::user()->company_id)->where('code', $val->code_folder_header)->first();
            $nomPrestamo = 'CARGA MASIVA';
            if ($header && $header->tipo_prestamo) {
                $prestamo = Prestamos::find($header->tipo_prestamo);
                $nomPrestamo = $prestamo ? $prestamo->name : 'Préstamo no disponible';
            }
            $arrayValores[] = [
                'prestamoNombre' => $nomPrestamo,
                'interesPrestamo' => $val->total_intereses_periodo,
                'interesMOra' => $val->total_intereses_mora,
            ];
            $sumaIntereses += $val->total_intereses_periodo + $val->total_intereses_mora;
        }

//        gatos
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'GAS')
                ->first();
        $gastos = CustomerMovimiento::where('afecta', 'E')
                ->where('type_transaction_id', optional($transaction)->id ?? -1)
                ->where('status', true)
                ->where('company_id', Auth::user()->company_id)
                ->whereBetween('date_created', [$inicio, $fin])
                ->get();
        $sumaGastos = CustomerMovimiento::where('afecta', 'E')
                ->where('type_transaction_id', optional($transaction)->id ?? -1)
                ->where('status', true)
                ->where('company_id', Auth::user()->company_id)
                ->whereBetween('date_created', [$inicio, $fin])
                ->sum('valor_movimiento');

//        EGRESO INTERES EMPRESA
        //        gastos de ahorros
        $transactionInteresEmpresa = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'EG')
                ->first();
        $arrayValoresAho = array();
        $eventosAho = $this->interesesAhorro($inicio, $fin, optional($transactionInteresEmpresa)->id);
        $sumaGastosinteresAhorro = 0;
        foreach ($eventosAho as $valAho) {
            $arrayValoresAho[] = [
                'ahorroNombre' => $valAho->name,
                'valorAhorro' => $valAho->total_valor_movimiento,
            ];
            $sumaGastosinteresAhorro += $valAho->total_valor_movimiento;
        }




        $company = Company::find(Auth::user()->company_id);
        if ($company->photo != null && $company->photo != '') {
            $path = 'uploads/companies/' . $company->photo;
            if (!file_exists(public_path($path))) {
                $path = 'codev/negro.png';
            }
        } else {
            $path = 'codev/negro.png';
        }

        $data['company'] = $company;
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);
        $data['detallePrestamos'] = $arrayValores;
        $data['inicio'] = $inicio;
        $data['fin'] = $fin;
        $data['sumaIntereses'] = $sumaIntereses;
        $data['gastos'] = $gastos;
        $data['gastosIntresAhorro'] = $arrayValoresAho;
        $data['sumaGastos'] = $sumaGastos + $sumaGastosinteresAhorro;
        $data['utiliPerdi'] = $sumaIntereses - $sumaGastos - $sumaGastosinteresAhorro;
        return PDF::loadView('resultados.pdf_resultados', compact('data'))
                        ->download('resultados.pdf');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

}
