<?php

namespace App\Http\Livewire\IntCalculo;

use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Cartola\CartolaController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Models\Customer;
use App\Models\CustomerMovimiento;
use App\Models\CustomerTipoAhorros;
use App\Models\IntCalculoDetail;
use App\Models\IntCalculoHeader;
use App\Models\IntReglas;
use App\Models\Meses;
use App\Models\TipoAhorros;
use App\Models\TypeTransaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class IntCalculoComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $mes;
    public $anio;
    public $search = '';
    public $search2 = '';
    public $regla = null;
    public $headerId = null;
    public $anios = [];
    public $finalizarInteres = false;

    public function __construct()
    {
        $this->mes = date('m');
        $this->anio = date('Y');
    }

    public function calcular($id)
    {
        $header = IntCalculoHeader::where('int_reglas_id', $id)->where('anio', $this->anio)->where('mes', $this->mes);
        if ($header->count()) {
            $header = IntCalculoHeader::where('int_reglas_id', $id)->where('anio', $this->anio)->where('mes', $this->mes)->first();
        } else {
            $header = new IntCalculoHeader();
            $header->company_id = Auth::user()->company_id;
            $header->int_reglas_id = $id;
            $header->fecha_creacion = date('Y-m-d H:i:s');
            $header->anio = $this->anio;
            $header->mes = $this->mes;
            $header->user_created = Auth::user()->id;
            $header->estado = 'PENDIENTE';
            $header->save();
        }
        $reglas = IntReglas::find($id);
        if ($reglas->tipo_ahorro_id == null) {
            if ($header->estado == 'PENDIENTE') {
                $customerTipoAhorros = CustomerTipoAhorros::where('company_id', Auth::user()->company_id)->where('status', true)->get();
                IntCalculoDetail::where('company_id', Auth::user()->company_id)
                    ->where('int_calculo_header_id', $header->id)
                    ->delete();
                foreach ($customerTipoAhorros as $key => $value) {
                    $customer = Customer::find($value->customer_id);
                    $fechaFin = $this->anio . '-' . $this->mes . '-31';
                    $ingresos = CustomerMovimiento::where('company_id', Auth::user()->company_id)
                        ->whereIn('type_transaction_id', [1])
                        ->where('customer_code', $customer->code)
                        ->where('customer_tipo_ahorro_id', $value->id)
                        ->where('date_created', '<', $fechaFin)
                        ->sum('valor_movimiento');
                    $egresos = CustomerMovimiento::where('company_id', Auth::user()->company_id)
                        ->whereIn('type_transaction_id', [2])
                        ->where('customer_code', $customer->code)
                        ->where('customer_tipo_ahorro_id', $value->id)
                        ->where('date_created', '<', $fechaFin)
                        ->sum('valor_movimiento');
                    $subtotal = $ingresos - $egresos;
                    $interes = $reglas->valor;
                    if ($reglas->operacion == '(+)') {
                        $total = $subtotal + $interes;
                    } else {
                        $total = $subtotal - $interes;
                    }
                    $detail = new IntCalculoDetail();
                    $detail->company_id = Auth::user()->company_id;
                    $detail->int_calculo_header_id = $header->id;
                    $detail->customer_id = $customer->id;
                    $detail->tipo_ahorro_id = $value->id;
                    $detail->customer_code = $customer->code;
                    $detail->valor_saldo = $subtotal;
                    $detail->valor_interes = $interes;
                    $detail->valor_total = $total;
                    $detail->estado = 'PENDIENTE';
                    $detail->save();
                }
            }
            //para todos
            $detalles = IntCalculoDetail::where('company_id', Auth::user()->company_id)
                ->where('int_calculo_header_id', $header->id)
                ->get();
        } else {
            if ($header->estado == 'PENDIENTE') {
                $customerTipoAhorros = CustomerTipoAhorros::where('company_id', Auth::user()->company_id)
                    ->where('tipo_ahorros_id', $reglas->tipo_ahorro_id)
                    ->where('status', true)
                    ->get();
                IntCalculoDetail::where('company_id', Auth::user()->company_id)
                    ->where('int_calculo_header_id', $header->id)
                    ->delete();
                foreach ($customerTipoAhorros as $key => $value) {
                    $customer = Customer::find($value->customer_id);
                    $fechaFin = $this->anio . '-' . $this->mes . '-31';
                    $ingresos = CustomerMovimiento::where('company_id', Auth::user()->company_id)
                        ->whereIn('type_transaction_id', [1])
                        ->where('customer_code', $customer->code)
                        ->where('customer_tipo_ahorro_id', $value->id)
                        ->where('date_created', '<', $fechaFin)
                        ->sum('valor_movimiento');
                    $egresos = CustomerMovimiento::where('company_id', Auth::user()->company_id)
                        ->whereIn('type_transaction_id', [2])
                        ->where('customer_code', $customer->code)
                        ->where('customer_tipo_ahorro_id', $value->id)
                        ->where('date_created', '<', $fechaFin)
                        ->sum('valor_movimiento');

                    $subtotal = $ingresos - $egresos;
                    $tipoAhorro = TipoAhorros::find($reglas->tipo_ahorro_id);
                    $interes = $subtotal * ($tipoAhorro->interes / 100);
                    if ($reglas->operacion == '(+)') {
                        $total = $subtotal + $interes;
                    } else {
                        $total = $subtotal - $interes;
                    }
                    $detail = new IntCalculoDetail();
                    $detail->company_id = Auth::user()->company_id;
                    $detail->int_calculo_header_id = $header->id;
                    $detail->customer_id = $customer->id;
                    $detail->tipo_ahorro_id = $value->id;
                    $detail->customer_code = $customer->code;
                    $detail->valor_saldo = $subtotal;
                    $detail->valor_interes = $interes;
                    $detail->valor_total = $total;
                    $detail->estado = 'PENDIENTE';
                    $detail->save();
                }
            }
        }
        $this->headerId = $header->id;
        $this->regla = $id;
        $this->finalizarInteres = true;
    }

    public function ver($id)
    {
        $header = IntCalculoHeader::where('int_reglas_id', $id)->where('anio', $this->anio)->where('mes', $this->mes)->first();
        $reglas = IntReglas::find($id);
        $detalles = IntCalculoDetail::where('company_id', Auth::user()->company_id)
            ->where('int_calculo_header_id', $header->id)
            ->get();
        $this->headerId = $header->id;
        $this->regla = $id;
        $this->finalizarInteres = false;
    }

    public function finalizarRegla()
    {
        $header = IntCalculoHeader::find($this->headerId);
        $detalles = IntCalculoDetail::where('int_calculo_header_id', $header->id)->get();
        foreach ($detalles as $key => $value) {
            $customer = Customer::find($value->customer_id);
            $regla = IntReglas::find($this->regla);
            if ($regla->operacion == '(+)') {
                $nombreCorto = 'IN';
            } else {
                $nombreCorto = 'EG';
            }
            $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', $nombreCorto)
                ->first();
            $tabla = 'customer_movimientos';
            $code = BaseController::generarCodigo($tabla, 9);
            $valorTotal = BaseController::valorTotal();
            $data = [
                "code" => $code,
                "company_id" => Auth::user()->company_id,
                "customer_code" => $customer->code,
                "afecta" => $transaction->afecta,
                "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                "customer_ruc" => $customer->numero_documento,
                "customer_address" => $customer->direccion,
                "customer_telefono" => $customer->telefono,
                "customer_tipo_ahorro_id" => $value->tipo_ahorro_id,
                "type_transaction_id" => $transaction->id,
                "type_transaction_name" => $transaction->name,
                "type_transaction_action" => $transaction->action,
                "valor_movimiento" => $value->valor_interes,
                "saldo_general" => $valorTotal + $value->valor_interes,
                "observation" => $regla->texto,
                "user_created_id" => Auth::user()->id,
                "date_created" => date('Y-m-d'),
                "hour_created" => date("H:i:s"),
                "automatico" => true,
            ];
            $movimientos = CustomerMovimiento::create($data);
            //Finalizar detalle
            $detalle = IntCalculoDetail::find($value->id);
            $detalle->estado = 'FINALIZADO';
            $detalle->fecha_calculado = date('Y-m-d H:i:s');
            $detalle->user_calculado = Auth::user()->id;
            $detalle->save();
            CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $value->valor_interes, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
            CartolaController::ingresoCartola($movimientos->customer_code, $movimientos->type_transaction_id, $movimientos->valor_movimiento, date('Y-m-d'));
            $this->finalizarInteres = false;
        }
        //Finaliza cabecera
        $header->estado = 'FINALIZADO';
        $header->user_calculado = Auth::user()->id;
        $header->save();
    }

    public function vaciarValores()
    {
        $this->regla = null;
        $this->headerId = null;
    }

    public function mount()
    {
        $anioActual = date('Y');
        $aniosAnteriores = [];
        for ($i = 0; $i < 5; $i++) {
            $aniosAnteriores[] = $anioActual - $i;
        }
        $this->anios = array_reverse($aniosAnteriores);
    }

    public function render()
    {
        $ultimoDiaMes = date('Y-m-t', strtotime($this->anio . '-' . $this->mes . '-01'));
        $reglas = IntReglas::where('company_id', Auth::user()->company_id)
            ->where('created_at', '<=', $ultimoDiaMes)
            ->where('nombre', 'like', '%' . $this->search . '%')
            ->where('status', true)
            ->get();
        foreach ($reglas as $key => $reg) {
            $existe = IntCalculoHeader::where('company_id', Auth::user()->company_id)
                ->where('int_reglas_id', $reg->id)
                ->where('anio', $this->anio)
                ->where('mes', $this->mes);
            if ($existe->count() > 0) {
                $header = IntCalculoHeader::where('company_id', Auth::user()->company_id)
                    ->where('int_reglas_id', $reg->id)
                    ->where('anio', $this->anio)
                    ->where('mes', $this->mes)
                    ->first();
                $reg->estadoHeader = $header->estado;
            } else {
                $reg->estadoHeader = 'PENDIENTE';
            }
            if ($reg->tipo_ahorro_id !== null) {
                $tipoAhorro = TipoAhorros::find($reg->tipo_ahorro_id);
                $reg->porcentaje = $tipoAhorro->interes;
            } else {
                $reg->porcentaje = $reg->interes;
            }
        }

        $detalles = IntCalculoDetail::select('int_calculo_detail.*', 'int_reglas.operacion', 'int_reglas.valor')
            ->join('int_calculo_header', 'int_calculo_detail.int_calculo_header_id', '=', 'int_calculo_header.id')
            ->join('customer', 'int_calculo_detail.customer_id', '=', 'customer.id')
            ->join('int_reglas', 'int_calculo_header.int_reglas_id', '=', 'int_reglas.id')
            ->where('int_calculo_detail.company_id', Auth::user()->company_id)
            ->where('int_calculo_detail.int_calculo_header_id', $this->headerId)
            ->where(function ($query) {
                $query->where('customer.nombres', 'like', '%' . $this->search2 . '%')
                    ->orWhere('customer.apellidos', 'like', '%' . $this->search2 . '%');
            })
            ->orderBy('customer_id')
            ->paginate(20);

        $meses = Meses::where('company_id', Auth::user()->company_id)->where('status', true)->orderBy('codigo')->get();
        return view('livewire.int-calculo.int-calculo-component', compact('reglas', 'detalles', 'meses'));
    }
}
