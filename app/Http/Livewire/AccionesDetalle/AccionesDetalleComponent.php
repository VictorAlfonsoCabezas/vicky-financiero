<?php

namespace App\Http\Livewire\AccionesDetalle;

use App\Models\AccionesDetalle;
use App\Models\AccionesDetalleValores;
use App\Models\AccionesHeader;
use App\Models\AccionesValores;
use App\Models\Customer;
use App\Models\TypeTransaction;
use App\Http\Controllers\Base\BaseController;
use App\Models\CustomerMovimiento;
use App\Http\Controllers\Customer\CustomerHistorialController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AccionesDetalleComponent extends Component {

    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $detale_selec = 0;
    public $total = 0;
    public $search = '';

    public function mount($detalle_id) {
        $this->detale_selec = ($detalle_id);
    }

    public function crear($id) {
        $header = AccionesHeader::find($id);
        /*if( $header->certificado){
            $customer = Customer::where('company_id', Auth::user()->company_id)
            ->where('fundador', 1)
            ->get();
        }else{
            */
            $customer = Customer::where('company_id', Auth::user()->company_id)->get();
        //}
        foreach ($customer as $key => $custom) {
            $existe = AccionesDetalle::where('company_id', Auth::user()->company_id)
                    ->where('acciones_header_id', $id)
                    ->where('customer_id', $custom->id)
                    ->exists();

            if ($existe) {
                $detalle = AccionesDetalle::where('company_id', Auth::user()->company_id)
                        ->where('acciones_header_id', $id)
                        ->where('customer_id', $custom->id)
                        ->first();
            } else {
                $detalle = new AccionesDetalle();
                $detalle->company_id = Auth::user()->company_id;
                $detalle->acciones_header_id = $id;
                $detalle->customer_id = $custom->id;
                $detalle->descripcion = 'NUEVO';
                $detalle->user_created = Auth::user()->id;
                $detalle->save();
            }

            $valores = AccionesValores::where('company_id', Auth::user()->company_id)->where('status', true)->get();
            foreach ($valores as $key => $value) {
                $existeDetalleValores = AccionesDetalleValores::where('company_id', Auth::user()->company_id)
                        ->where('acciones_header_id', $id)
                        ->where('acciones_detalle_id', $detalle->id)
                        ->where('acciones_valores_id', $value->id)
                        ->exists();
                if (!$existeDetalleValores) {

                    switch ($value->tipo) {
                        case 'SALDO':
                            $CANTIDAD = count($customer);
                            $VALOR = $header->capital / $CANTIDAD;
                            break;
                        case 'PORCENTAJE':
                            $CANTIDAD = count($customer);
                            $VALOR_SIN_TRUNCAR = 100 / $CANTIDAD;
                            $VALOR = number_format($VALOR_SIN_TRUNCAR, 2);
                            break;
                        case 'UTILIDAD':
                            $CANTIDAD = count($customer);
                            $utilidad = $this->calcularUtilidad($header);
                            $VALOR = number_format($utilidad/ $CANTIDAD, 2);
                            break;
                        case 'OTROS':
                            $VALOR = number_format($value->valor_defecto, 2);
                            break;
                    }

                    $historial = new AccionesDetalleValores();
                    $historial->company_id = Auth::user()->company_id;
                    $historial->acciones_header_id = $id;
                    $historial->acciones_detalle_id = $detalle->id;
                    $historial->acciones_valores_id = $value->id;
                    $historial->valor = $VALOR;
                    $historial->fecha_creacion = date('Y-m-d H:i:s');
                    $historial->save();
                }
            }
        }
        $this->detale_selec = $id;
    }

    public function calcularUtilidad($header){
        //primer dia
        $month2 = $header->mes;
        $year2 = $header->anio;
        $primero = date('Y-m-d', mktime(0, 0, 0, $month2, 1, $year2));
        //ultimo dia del mes
        $year = $header->anio;
        $mes = $header->mes;
        $day = date("d", mktime(0, 0, 0, $mes + 1, 0, $year));
        $ultimo = date('Y-m-d', mktime(0, 0, 0, $mes, $day, $year));
        // intereses
        $sql = "SELECT credit_folder_details.code_folder_header, "
                . "SUM(credit_folder_details.interes_periodo) as total_intereses_periodo, "
                . "SUM(credit_folder_details.interes_mora) as total_intereses_mora "
                . "FROM credit_folder_details "
                . "WHERE credit_folder_details.date_pay >= '" . $primero . "' "
                . "AND credit_folder_details.date_pay <= '" . $ultimo . "' "
                . "AND credit_folder_details.status = 'PAGADA' "
                . "GROUP BY credit_folder_details.code_folder_header;";
        $eventos = DB::select($sql);
        $sumaIntereses = 0;
        foreach ($eventos as $val) {
            $sumaIntereses += $val->total_intereses_periodo + $val->total_intereses_mora;
        }
        // gastos de la empresa
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'GAS')
                ->first();
        $sumaGastos = CustomerMovimiento::where('afecta', 'E')
                ->where('type_transaction_id', $transaction->id)
                ->where('status', true)
                ->where('company_id', Auth::user()->company_id)
                ->whereBetween('date_created', [$primero, $ultimo])
                ->sum('valor_movimiento');
        $totacalculo = round($sumaIntereses-$sumaGastos, 2);
        return $totacalculo;
    }

    public function vaciar($header) {
        AccionesDetalleValores::where('company_id', Auth::user()->company_id)->where('acciones_header_id', $header)->delete();
        AccionesDetalle::where('company_id', Auth::user()->company_id)->where('acciones_header_id', $header)->delete();
    }

    public function finalizar($header) {
        $header = AccionesHeader::find($header);
        $header->estado = 'FINALIZADO';
        $header->save();
        return $this->render();
    }

    public function actualizarValores($valores, $detalle, $valor) {
        $accionesDetalleValores = AccionesDetalleValores::where('company_id', Auth::user()->company_id)
                ->where('acciones_valores_id', $valores)
                ->where('acciones_detalle_id', $detalle)
                ->first();
        $accionesDetalleValores->valor = $valor;
        $accionesDetalleValores->save();
    }

    public function entregarSocio($id) {
        $detalle = AccionesDetalle::find($id);
        $detalle->status = 2;
        $detalle->hora_entrega = date('H:i:s');
        $detalle->fecha_entrega = date('Y-m-d');
        $detalle->user_entrega_id = Auth::user()->id;
        $detalle->save();
        $this->generarSalida($detalle->acciones_header_id, $detalle->id, $detalle->customer_id);
        return $this->render();
    }
    
    public function generarSalida($header, $id, $customer) {
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'EUS')
                ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
        $accionesDetalleValores = AccionesDetalleValores::where('company_id', Auth::user()->company_id)
                ->where('acciones_header_id', $header)
                ->where('acciones_detalle_id', $id)
                ->get();
        $totalContado = 0; 
        foreach ($accionesDetalleValores as $value) {
            $accionesValores = AccionesValores::find($value->acciones_valores_id);
            if($accionesValores->operacion == 'S'){
                $totalContado  += $value->valor;
            }else{
                $totalContado  -= $value->valor;
            }
        }
        $customer = Customer::find($customer);
        $data = [
            "code" => $code,
            "company_id" => Auth::user()->company_id,
            "afecta" => $transaction->afecta,    
            "customer_code" => $customer->code,
            "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
            "customer_ruc" => $customer->numero_documento,
            "customer_address" => $customer->direccion,
            "customer_telefono" => $customer->telefono,   
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $totalContado,
            "saldo_general" => $valorTotal - $totalContado,
            "observation" =>$transaction->description.' ' .$customer->nombres . ' ' . $customer->apellidos,
            "observation" => $transaction->name,
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $totalContado, $customer->code, $movimientos->saldo_general, date('Y-m-d'));
    }

    public function render() {
        $header = AccionesHeader::find($this->detale_selec);
        $detalle = AccionesDetalle::select('acciones_detalle.id', 'acciones_detalle.status', 'acciones_detalle.porcentaje', 'acciones_detalle.valor', 'acciones_detalle.porcentaje', (DB::raw("CONCAT(customer.nombres, ' ', customer.apellidos) as nombre_completo")), 'customer.numero_documento')
                ->join('customer', 'acciones_detalle.customer_id', '=', 'customer.id')
                ->where('acciones_detalle.company_id', Auth::user()->company_id)
                ->where('acciones_detalle.acciones_header_id', $this->detale_selec)
                /*
                ->where('customer.nombres', 'like', '%' . $this->search . '%')
                ->orWhere('customer.apellidos', 'like', '%' . $this->search . '%')
                ->orWhere('customer.numero_documento', 'like', '%' . $this->search . '%')
                */
                ->paginate(100);
        $entregados = AccionesDetalle::select('acciones_detalle.id', 'acciones_detalle.status', 'acciones_detalle.porcentaje', 'acciones_detalle.valor', 'acciones_detalle.porcentaje', (DB::raw("CONCAT(customer.nombres, ' ', customer.apellidos) as nombre_completo")), 'customer.numero_documento')
                ->join('customer', 'acciones_detalle.customer_id', '=', 'customer.id')
                ->where('acciones_detalle.company_id', Auth::user()->company_id)
                ->where('acciones_detalle.acciones_header_id', $this->detale_selec)
                ->where('acciones_detalle.status', 2)
                ->count();
        $this->total = AccionesDetalle::where('company_id', Auth::user()->company_id)->where('acciones_header_id', $this->detale_selec)->sum('valor');
        $historial = AccionesValores::where('company_id', Auth::user()->company_id)->where('status', true)->get();
        return view('livewire.acciones-detalle.acciones-detalle-component', compact('header', 'detalle', 'historial', 'entregados'));
    }

}
