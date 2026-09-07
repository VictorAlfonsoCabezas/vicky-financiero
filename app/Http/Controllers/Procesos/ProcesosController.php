<?php

namespace App\Http\Controllers\Procesos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Http\Controllers\Cajas\CajasController;
use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Cartola\CartolaController;
use App\Models\CustomerMovimiento;
use Illuminate\Http\Request;
use App\Models\Procesos;
use App\Models\Customer;
use App\Models\CustomerInteres;
use App\Models\CustomerHistorial;
use App\Models\TypeTransaction;
use Illuminate\Support\Facades\Auth;

class ProcesosController extends Controller {

    public static function cumplen3Meses() {
//        $actual = strtotime(date('Y-m-d'));
//        $meses_3 = date("Y-m-d", strtotime("+3 month", $actual));
        $customer = Customer::where('company_id', Auth::user()->company_id)
                ->where('date_interes', '<=', date('Y-m-d'))
                ->where('status_interes', false)
                ->where('status', true);
        if ($customer->count() > 0) {
            foreach ($customer->get() as $value) {
                $clienteTiempo = Customer::find($value->id);
                $clienteTiempo->status_interes = true;
                $clienteTiempo->save();
            }
        }
    }

    public static function procesosBase() {
        $mesactual = config('constants.MESES_NUMEROS.' . date('m') . '.mes');
        $anioactual = date('Y');
        $procesos = Procesos::where('company_id', Auth::user()->company_id);
        if ($procesos->count() > 0) {
            foreach ($procesos->get() as $proce) {
                switch ($proce->recurrencia) {
                    case 'M':
                        $procesoMes = Procesos::where('company_id', Auth::user()->company_id)
                                ->where('recurrencia', 'M')
                                ->where('mes_update', $mesactual)
                                ->where('anio_update', $anioactual);
                        if ($procesoMes->count() == 0 || $proce->first()->mes_update == null || $proce->first()->anio_update == null) {
                            $customer = Customer::where('company_id', Auth::user()->company_id)
                                    ->where('status_interes', true)
                                    ->where('status', true);
                            if ($customer->count() > 0) {
                                foreach ($customer->get() as $custom) {
                                    $interes = CustomerInteres::where('company_id', Auth::user()->company_id)
                                            ->where('customer_id', $custom->id)
                                            ->where('customer_code', $custom->code)
                                            ->where('mes', $mesactual)
                                            ->where('anio', $anioactual);
                                    if ($interes->count() == 0) {
                                        CajasController::abrirCajaAutomatica();
                                        $ingresos = CustomerHistorial::where('customer_code', $custom->code)->where('type_transaction_action', 'S')->where('status', true)->sum('valor_movimiento');
                                        $egresos = CustomerHistorial::where('customer_code', $custom->code)->where('type_transaction_action', 'R')->where('status', true)->sum('valor_movimiento');
                                        $valor = $ingresos - $egresos;
                                        $porcentaje = (real) round(($valor * 0.10), 2);
                                        $interesNew = new CustomerInteres();
                                        $interesNew->company_id = Auth::user()->company_id;
                                        $interesNew->valor = $porcentaje;
                                        $interesNew->customer_id = $custom->id;
                                        $interesNew->customer_code = $custom->id;
                                        $interesNew->customer_name = $custom->id;
                                        $interesNew->mes = $mesactual;
                                        $interesNew->anio = $anioactual;
                                        $interesNew->calculado = true;
                                        $interesNew->save();
                                        $transactionCaja = TypeTransaction::where('company_id', Auth::user()->company_id)
                                                ->where('name_corto', 'EGI')
                                                ->first();
                                        $tablaCaja = 'customer_movimientos';
                                        $codeCaja = BaseController::generarCodigo($tablaCaja, 9);
                                        $dataCaja = [
                                            "code" => $codeCaja,
                                            "company_id" => Auth::user()->company_id,
                                            "customer_code" => $custom->code,
                                            "afecta" => $transactionCaja->afecta,
                                            "customer_name" => $custom->nombres . ' ' . $custom->apellidos,
                                            "customer_ruc" => $custom->numero_documento,
                                            "customer_address" => $custom->direccion,
                                            "customer_telefono" => $custom->telefono,
                                            "type_transaction_id" => $transactionCaja->id,
                                            "type_transaction_name" => $transactionCaja->name,
                                            "type_transaction_action" => $transactionCaja->action,
                                            "valor_movimiento" => $porcentaje,
                                            "observation" => 'EGRESO DE CAJA A FAVOR DEL CLIENTE POR INTERES MENSUAL, CALCULO AUTOMÁTICO MENSUAL',
                                            "user_created_id" => Auth::user()->id,
                                            "date_created" => date('Y-m-d'),
                                            "hour_created" => date("H:i:s"),
                                        ];
                                        $movimientosCaja = CustomerMovimiento::create($dataCaja);
                                        CajasController::calcularCajaAutomatica();
                                        CustomerHistorialController::guardarHistorialAutomatica($movimientosCaja->code, $transactionCaja->id, $porcentaje, $custom->code);
                                        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                                                ->where('name_corto', 'INI')
                                                ->first();
                                        $tabla = 'customer_movimientos';
                                        $code = BaseController::generarCodigo($tabla, 9);
                                        $data = [
                                            "code" => $code,
                                            "company_id" => Auth::user()->company_id,
                                            "customer_code" => $custom->code,
                                            "afecta" => $transaction->afecta,
                                            "customer_name" => $custom->nombres . ' ' . $custom->apellidos,
                                            "customer_ruc" => $custom->numero_documento,
                                            "customer_address" => $custom->direccion,
                                            "customer_telefono" => $custom->telefono,
                                            "type_transaction_id" => $transaction->id,
                                            "type_transaction_name" => $transaction->name,
                                            "type_transaction_action" => $transaction->action,
                                            "valor_movimiento" => $porcentaje,
                                            "observation" => 'INTERÉS A FAVOR, CALCULO AUTOMÁTICO MENSUAL',
                                            "user_created_id" => Auth::user()->id,
                                            "date_created" => date('Y-m-d'),
                                            "hour_created" => date("H:i:s"),
                                        ];
                                        $movimientos = CustomerMovimiento::create($data);
                                        CartolaController::ingresoCartola($movimientos->customer_code, $movimientos->type_transaction_id, $movimientos->valor_movimiento);
                                        CajasController::calcularCajaAutomatica();
                                        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $porcentaje, $custom->code);
                                    }
                                }
                            }
                            $procesosMes = Procesos::find($proce->id);
                            $procesosMes->mes_update = $mesactual;
                            $procesosMes->anio_update = $anioactual;
                            $procesosMes->date_update = date('Y-m-d');
                            $procesosMes->status = 'CORRIDO';
                            $procesosMes->save();
                        }
                        break;
                    case 'A':
                        $procesoMes = Procesos::where('company_id', Auth::user()->company_id)
                                ->where('recurrencia', 'A')
                                ->where('mes_update', 'DICIEMBRE');
                        if ($procesoMes->count() > 0) {
                            if ($procesoMes->first()->mes_update == $mesactual && $procesoMes->first()->dia_update == date('d')) {
                                $customer = Customer::where('company_id', Auth::user()->company_id)
                                        ->where('status', true);
                                if ($customer->count() > 0) {
                                    foreach ($customer->get() as $custom) {
                                        CajasController::abrirCajaAutomatica();
                                        $transactionCaja = TypeTransaction::where('company_id', Auth::user()->company_id)
                                                ->where('name_corto', 'EGA')
                                                ->first();
                                        $tablaCaja = 'customer_movimientos';
                                        $codeCaja = BaseController::generarCodigo($tablaCaja, 9);
                                        $dataCaja = [
                                            "code" => $codeCaja,
                                            "company_id" => Auth::user()->company_id,
                                            "customer_code" => $custom->code,
                                            "afecta" => $transactionCaja->afecta,
                                            "customer_name" => $custom->nombres . ' ' . $custom->apellidos,
                                            "customer_ruc" => $custom->numero_documento,
                                            "customer_address" => $custom->direccion,
                                            "customer_telefono" => $custom->telefono,
                                            "type_transaction_id" => $transactionCaja->id,
                                            "type_transaction_name" => $transactionCaja->name,
                                            "type_transaction_action" => $transactionCaja->action,
                                            "valor_movimiento" => '0.50',
                                            "observation" => $transactionCaja->description,
                                            "user_created_id" => Auth::user()->id,
                                            "date_created" => date('Y-m-d'),
                                            "hour_created" => date("H:i:s"),
                                        ];
                                        $movimientosCaja = CustomerMovimiento::create($dataCaja);
                                        CajasController::calcularCajaAutomatica();
                                        CustomerHistorialController::guardarHistorialAutomatica($movimientosCaja->code, $transactionCaja->id, '0.50', $custom->code);
                                    }
                                }
                                $procesosMes = Procesos::find($proce->id);
                                $procesosMes->mes_update = $mesactual;
                                $procesosMes->anio_update = $anioactual;
                                $procesosMes->date_update = date('Y-m-d');
                                $procesosMes->status = 'CORRIDO';
                                $procesosMes->save();
                            }
                        }
                        break;
                }
            }
        }
    }

}
