<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Http\Controllers\Cartola\CartolaController;
use App\Models\Cajas;
use App\Models\CustomerHistorial;
use App\Models\CustomerMovimiento;
use App\Models\DescargoBovedasHeader;
use App\Models\OperacionesDescargoBovedas;
use App\Models\Customer;
use App\Models\CreditFolderHeader;
use App\Models\TypeTransaction;
use App\Models\CustomerTipoAhorros;
use App\Models\CreditFolderDetail;
use App\Models\Company;
use App\Models\TipoAhorrosDetalle;
use App\Models\CartolaHeader;
use App\Models\CartolaDetail;
use App\DenominacionBilletes;
use App\Models\OtrosIngresos;
use App\Models\CajasDenominacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Carbon\Carbon;
use App\Mail\NotificacionesMail;
use App\Mail\NotificacionesCierresCajaMail;
use App\Models\Bovedas;
use App\Models\EstadoCivil;
use App\Models\FormasPago;
use App\Models\Garantes;
use App\Models\HistoricoTransaccional;
use App\Models\InteresFijoParametrizado;
use App\Models\MultasCuentasEjecucion;
use App\Models\Prestamos;
use App\Models\RecurrenciaPrestamos;
use App\Models\TipoAhorros;
use App\Models\TransaccionesCuentas;
use App\Models\TrasaccionCuentasEjecucion;
use App\Models\VariablesDocumentos;
use App\User;
use Illuminate\Support\Facades\Mail;
use PDF;
use Luecano\NumeroALetras\NumeroALetras;
use DateTime;

class BaseController extends Controller
{

    public static function generarCodigo($tabla, $numero)
    {
        $sql = "SELECT id FROM $tabla WHERE `company_id` = " . Auth::user()->company_id . " ORDER BY id DESC LIMIT 1";
        $data = DB::select($sql);

        if (count($data) > 0) {
            $serie = (int) $data[0]->id;
            $serie++;
            $cod = str_pad($serie, $numero, '0', STR_PAD_LEFT);
            $code = $cod;
        } else {
            $cod = str_pad(1, $numero, '0', STR_PAD_LEFT);
            $code = $cod;
        }
        return $code;
    }

    public static function generarCodigoPrestamos($tabla, $numero)
    {
        $maxCode = CreditFolderHeader::where('code', '!=', '')->max('code');
        $maxCode = (int) $maxCode;
        $maxCodeSumado = $maxCode + 1;
        $cod = str_pad($maxCodeSumado, $numero, '0', STR_PAD_LEFT);
        $code = $cod;

        return $code;
    }

    public static function generarCodigoPrestamosautomatico($tabla, $numero)
    {

        $maxCodeSumado = $tabla;
        $cod = str_pad($maxCodeSumado, $numero, '0', STR_PAD_LEFT);
        $code = $cod;

        return $code;
    }

    public static function generarCodigoCron($tabla, $numero)
    {
        $compa = 1;
        $sql = "SELECT id FROM $tabla WHERE `company_id` = " . $compa . " ORDER BY id DESC LIMIT 1";
        $data = DB::select($sql);

        if (count($data) > 0) {
            $serie = (int) $data[0]->id;
            $serie++;
            $cod = str_pad($serie, $numero, '0', STR_PAD_LEFT);
            $code = $cod;
        } else {
            $cod = str_pad(1, $numero, '0', STR_PAD_LEFT);
            $code = $cod;
        }
        return $code;
    }

    public static function saldoGeneral($fecha)
    {
        $ingresos = Cajas::where('company_id', Auth::user()->company_id)
            ->where('user_inicial_id', Auth::user()->id)
            ->where('date_inicial', $fecha)
            ->first();
        if (isset($ingresos->valor_inicial))
            $valorInicial = $ingresos->valor_inicial;
        else
            $valorInicial = 0;

        //$historialEgreso = CustomerHistorial::where('type_transaction_action', 'R')
        //    ->where('customer_movimiento_code', '!=', null)
        //    ->where('customer_movimiento_code', '!=', '')
        //    ->where('date_created', $fecha)
        //    ->where('status', true)
        //    ->sum('valor_movimiento');

        $historialEgreso = CustomerHistorial::join('customer_movimientos', 'customer_movimientos.code', '=', 'customer_historials.customer_movimiento_code')
            ->where('customer_historials.type_transaction_action', 'R')
            ->whereNotNull('customer_historials.customer_movimiento_code')
            ->where('customer_historials.customer_movimiento_code', '!=', '')
            ->where('customer_historials.date_created', $fecha)
            ->where('customer_historials.status', true)
            ->where('customer_movimientos.user_created_id', Auth::user()->id)
            ->sum('customer_historials.valor_movimiento');


        //$historialEgresoNovar = CustomerHistorial::join('type_transactions', 'customer_historials.type_transaction_id', '=', 'type_transactions.id')
        //    ->whereIn('type_transactions.name_corto', ['PVP'])
        //   ->where('customer_historials.customer_movimiento_code', '!=', null)
        //    ->where('customer_historials.customer_movimiento_code', '!=', '')
        //   ->where('customer_historials.date_created', $fecha)
        //   ->where('customer_historials.status', true)
        //    ->sum('customer_historials.valor_movimiento');

        $historialEgresoNovar = 0;

        $historialIngreso = CustomerHistorial::join('customer_movimientos', 'customer_movimientos.code', '=', 'customer_historials.customer_movimiento_code')
            ->where('customer_historials.type_transaction_action', 'S')
            ->whereNotNull('customer_historials.customer_movimiento_code')
            ->where('customer_historials.customer_movimiento_code', '!=', '')
            ->where('customer_historials.date_created', $fecha)
            ->where('customer_historials.status', true)
            ->where('customer_movimientos.user_created_id', Auth::user()->id)
            ->sum('customer_historials.valor_movimiento');
        $total = $valorInicial + $historialIngreso - $historialEgreso - $historialEgresoNovar;
        if ($total > 0) {
            $valor = $total;
        } else {
            $valor = 0;
        }
        return $valor;
    }
    public static function saldoGeneralCaja($fecha, $usuario, $caja = null)
    {
        $tasladosMatriz = 0;
        if ($caja != null) {

            $transferencia = OperacionesDescargoBovedas::where('company_id', Auth::user()->company_id)->where('nombre_corto', 'TRANENV')->firstOrFail();
            $tasladosMatriz = DescargoBovedasHeader::where('company_id', Auth::user()->company_id)
                ->where('estado', 'FINALIZADO')
                ->where('operaciones_descargo_bovedas_id', $transferencia->id)
                ->where('cajas_id', $caja)
                ->sum('valor');
        }

        $ingresos = Cajas::where('company_id', Auth::user()->company_id)
            ->where('user_inicial_id', $usuario)
            ->where('date_inicial', $fecha)
            ->first();
        if (isset($ingresos->valor_inicial))
            $valorInicial = $ingresos->valor_inicial;
        else
            $valorInicial = 0;

        //$historialEgreso = CustomerHistorial::where('type_transaction_action', 'R')
        //    ->where('customer_movimiento_code', '!=', null)
        //    ->where('customer_movimiento_code', '!=', '')
        //    ->where('date_created', $fecha)
        //    ->where('status', true)
        //    ->sum('valor_movimiento');

        $formaPagoCredito = 0;
        $formaTrans = 0;
        $formaPago = FormasPago::where('nombre', 'TRANSFERENCIA')->first();
        if ($formaPago) {
            $formaTrans = $formaPago->id;
            $formaPagoCredito = $formaPago->id;
        }

        $notaDebito = TypeTransaction::where('name_corto', 'DND')->first();
        $notaCredito = TypeTransaction::where('name_corto', 'DNC')->first();

        $historialEgreso = CustomerHistorial::join('customer_movimientos', 'customer_movimientos.code', '=', 'customer_historials.customer_movimiento_code')
            ->where('customer_historials.type_transaction_action', 'R')
            ->whereNotNull('customer_historials.customer_movimiento_code')
            ->where('customer_historials.customer_movimiento_code', '!=', '')
            ->where('customer_historials.date_created', $fecha)
            ->where('customer_historials.status', true)
            ->where('customer_historials.type_transaction_id', '!=', $notaDebito->id)
            ->where('customer_movimientos.user_created_id', $usuario)
            ->where('customer_movimientos.forma_pago_id', '!=', $formaTrans)
            //->where('customer_movimientos.forma_pago_name', '!=', 'TRANSFERENCIA')
            //->whereNotIn('customer_movimientos.forma_pago_name', ['TRANSFERENCIA'])
            ->where(function ($query) use ($formaPagoCredito) {
                $query->where('customer_movimientos.forma_pago_id', '!=', $formaPagoCredito)
                    ->orWhereNull('customer_movimientos.forma_pago_id');
            })
            //->get();
            ->sum('customer_historials.valor_movimiento');


        //$historialEgresoNovar = CustomerHistorial::join('type_transactions', 'customer_historials.type_transaction_id', '=', 'type_transactions.id')
        //    ->whereIn('type_transactions.name_corto', ['PVP'])
        //   ->where('customer_historials.customer_movimiento_code', '!=', null)
        //    ->where('customer_historials.customer_movimiento_code', '!=', '')
        //   ->where('customer_historials.date_created', $fecha)
        //   ->where('customer_historials.status', true)
        //    ->sum('customer_historials.valor_movimiento');

        $historialEgresoNovar = 0;

        $historialIngreso = CustomerHistorial::join('customer_movimientos', 'customer_movimientos.code', '=', 'customer_historials.customer_movimiento_code')
            ->where('customer_historials.type_transaction_action', 'S')
            ->whereNotNull('customer_historials.customer_movimiento_code')
            ->where('customer_historials.customer_movimiento_code', '!=', '')
            ->where('customer_historials.date_created', $fecha)
            ->where('customer_historials.status', true)
            ->where('customer_historials.type_transaction_id', '!=', $notaCredito->id)
            ->where('customer_movimientos.user_created_id', $usuario)
            ->where('customer_movimientos.forma_pago_id', '!=', $formaTrans)
            //->where('customer_movimientos.forma_pago_name', '!=', 'TRANSFERENCIA')
            //->whereNotIn('customer_movimientos.forma_pago_name', ['TRANSFERENCIA'])
            ->where(function ($query) use ($formaPagoCredito) {
                $query->where('customer_movimientos.forma_pago_id', '!=', $formaPagoCredito)
                    ->orWhereNull('customer_movimientos.forma_pago_id');
            })
            //->get();
            ->sum('customer_historials.valor_movimiento');
        //dd($historialEgreso,  $historialIngreso,  $valorInicial ,   $tasladosMatriz);
        $total = $valorInicial + $historialIngreso - $historialEgreso - $historialEgresoNovar - $tasladosMatriz;
        //if ($total > 0) {
        $valor = $total;
        //} else {
        //    $valor = 0;
        //}
        return $valor;
    }

    public static function valorTotalOld($fecha)
    {
        $historialEgreso = CustomerHistorial::where('type_transaction_action', 'R')
            ->where('status', true)
            ->where('date_created', '<=', $fecha)
            ->sum('valor_movimiento');
        $historialIngreso = CustomerHistorial::where('type_transaction_action', 'S')
            ->where('status', true)
            ->where('date_created', '<=', $fecha)
            ->sum('valor_movimiento');
        $total = $historialIngreso - $historialEgreso;
        if ($total > 0) {
            $valor = $total;
        } else {
            $valor = 0;
        }
        return $valor;
    }

    public static function valorTotalMovimientosUsuario($fecha)
    {
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'SOL')
            ->first();
        $historialEgreso = CustomerMovimiento::where('type_transaction_action', 'R')
            ->where('forma_pago_name', '!=', 'TRANSFERENCIA')
            ->where('company_id', Auth::user()->company_id)
            ->where('user_created_id', Auth::user()->id)
            ->where('status', true)
            ->where('date_created', $fecha)
            ->where('type_transaction_id', '!=', $transaction->id)
            ->whereIn('forma_pago_id', [1, null]) // Aqui quemamos 1 que es efectivo o anteriormente vacio ya qu eno habia formas de pago
            ->sum('valor_movimiento');
        $historialIngreso = CustomerMovimiento::where('type_transaction_action', 'S')
            ->where('forma_pago_name', '!=', 'TRANSFERENCIA')
            ->where('company_id', Auth::user()->company_id)
            ->where('user_created_id', Auth::user()->id)
            ->where('status', true)
            ->where('date_created', $fecha)
            ->where('type_transaction_id', '!=', $transaction->id)
            ->whereIn('forma_pago_id', [1, null]) // Aqui quemamos 1 que es efectivo o anteriormente vacio ya qu eno habia formas de pago
            ->sum('valor_movimiento');
        $total = $historialIngreso - $historialEgreso;
        return $total;
    }

    public static function valorTotal()
    {
        $historialEgreso = CustomerHistorial::where('type_transaction_action', 'R')
            ->where('customer_movimiento_code', '!=', null)
            ->where('customer_movimiento_code', '!=', '')
            ->where('status', true)
            ->sum('valor_movimiento');
        $historialIngreso = CustomerHistorial::where('type_transaction_action', 'S')
            ->where('customer_movimiento_code', '!=', null)
            ->where('customer_movimiento_code', '!=', '')
            ->where('status', true)
            ->sum('valor_movimiento');
        $total = $historialIngreso - $historialEgreso;
        if ($total > 0) {
            $valor = $total;
        } else {
            $valor = 0;
        }
        return $valor;
    }

    public static function valorCajaInicialSinMovimientos($fecha)
    {
        $historialEgreso = CustomerHistorial::where('type_transaction_action', 'R')
            ->where('company_id', Auth::user()->company_id)
            ->where('status', true)
            ->where('date_created', '<', $fecha)
            ->sum('valor_movimiento');
        $historialIngreso = CustomerHistorial::where('type_transaction_action', 'S')
            ->where('company_id', Auth::user()->company_id)
            ->where('status', true)
            ->where('date_created', '<', $fecha)
            ->sum('valor_movimiento');
        $total = $historialIngreso - $historialEgreso;
        if ($total > 0) {
            $valor = $total;
        } else {
            $valor = 0;
        }
        return $valor;
    }

    public static function valoresCajasBoveda($id)
    {
        $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'CAJCIE')->first();
        $valores = DescargoBovedasHeader::where('cajas_id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->where('estado', 'FINALIZADO')
            ->where('operaciones_descargo_bovedas_id', '!=', $operacion->id)
            ->sum('valor');

        return $valores;
    }

    public static function calculoInteresMoraLetra($id)
    {
        $value = CreditFolderDetail::find($id);
        $company = Company::find(Auth::user()->company_id);
        $fechaPagoInicial = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
        $fechaPago = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
        $fechaProrroga = $fechaPagoInicial->addDays($company->dias_inicio_cobro);
        $fechaProrrogaTrabajo = $fechaPago->addDays($company->dias_inicio_cobro);
        $fechaActual = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
        $fechaActualTrabajo = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
        $diasDebeFechaActual = $fechaProrrogaTrabajo->diffInDays($fechaActualTrabajo);
        if ($company->select_tipo_interes == 'D') {
            $diasTope = $company->numero_dias_interes;
            $diasReales = 0;
            if ($diasDebeFechaActual > 0) {
                if ($diasDebeFechaActual > $company->dias_inicio_cobro) {
                    $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                } else {
                    $diasReales = $diasDebeFechaActual;
                }
            }
            if ($diasReales > $diasTope) {
                $diasAdd = $diasTope - $company->dias_gracia;
            } else {
                $diasAdd = $diasReales;
            }
        } else if ($company->select_tipo_interes == 'M') {
            $diasTope = $fechaProrrogaTrabajo->endOfMonth();
            $diferencia = $fechaProrroga->diff($diasTope);
            $diasReales = (int) $diferencia->format("%d");
            if ($diasReales > 0) {
                $diasAdd = $diasReales + $company->dias_inicio_cobro - $company->dias_gracia;
            } else {
                $diasAdd = 0;
            }
        } else if ($company->select_tipo_interes == 'S') {
            $fechaLetraInicio = $value->date_vencimiento;
            $code1 = $value->code_folder_header;
            if (isset($vencidas[$key + 1])) {
                $siguienteValor = $vencidas[$key + 1];
                $code2 = $siguienteValor->code_folder_header;
                $fechaLetraFin = $siguienteValor->date_vencimiento;

                if ($code1 == $code2) {
                    $fecha1 = Carbon::createFromFormat('Y-m-d', $fechaLetraInicio);
                    $fecha2 = Carbon::createFromFormat('Y-m-d', $fechaLetraFin);
                    $diferenciaDias = $fecha1->diffInDays($fecha2);
                    $diasReales = 0;
                    if ($diasDebeFechaActual > 0) {
                        $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                    }
                    if ($diasReales > $diferenciaDias) {
                        $diasAdd = $diferenciaDias - $company->dias_gracia;
                    } else {
                        $diasAdd = $diasReales;
                    }
                } else {
                    if ($fechaLetraInicio != date('Y-m-d')) {
                        $fecha1 = Carbon::createFromFormat('Y-m-d', $fechaLetraInicio);
                        $fecha2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                        $diferenciaDias = $fecha1->diffInDays($fecha2);
                        $diasReales = 0;
                        if ($diasDebeFechaActual > 0) {
                            $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                        }
                        if ($diasReales > $diferenciaDias) {
                            $diasAdd = $diferenciaDias - $company->dias_gracia;
                        } else {
                            $diasAdd = $diasReales;
                        }
                    } else {
                        $diasAdd = 0;
                    }
                }
            } else {
                if ($fechaLetraInicio != date('Y-m-d')) {

                    $fecha1 = Carbon::createFromFormat('Y-m-d', $fechaLetraInicio);
                    $fecha2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                    $diferenciaDias = $fecha1->diffInDays($fecha2);
                    $diasReales = 0;

                    if ($diasDebeFechaActual > 0) {
                        $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                    }
                    if ($diasReales > $diferenciaDias) {
                        $diasAdd = $diferenciaDias - $company->dias_gracia;
                    } else {
                        $diasAdd = $diasReales;
                    }
                } else {
                    $diasAdd = 0;
                }
            }
        }
        if ($company->select_tipo_interes == 'D' || $company->select_tipo_interes == 'M' || $company->select_tipo_interes == 'S') {
            $value->dias_mora = $diasAdd;
            $valor = $company->porcentaje_mora;
            $intervalMeses = (int) $diasAdd;
            $porcentaje = (float) round(($value->valor_cuota * $intervalMeses * ($valor / 100)), 2);
            $value->interes = $porcentaje;
        } else {
            $date1 = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
            $date2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            $value->dias_mora = $date1->diffInDays($date2);
            $valor = $company->porcentaje_mora;
            $interval = $date2->diff($date1);
            $intervalMeses = (int) $interval->format("%m");
            $porcentaje = (float) round(($value->valor_cuota * $intervalMeses * ($valor / 100)), 2);
            $value->interes = $porcentaje;
        }
        $letra = CreditFolderDetail::find($value->id);
        $letra->interes_mora = $porcentaje;
        $letra->save();
    }

    public static function calculoInteresMoraLetraValor($id)
    {

        ////***************************************** este no vale hasta nuevo calculo ***************** */
        $porcentaje = 0;
        $value = CreditFolderDetail::find($id);
        if ($value->date_vencimiento < date('Y-m-d')) {
            $company = Company::find(Auth::user()->company_id);
            $fechaPagoInicial = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
            $fechaPago = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
            $fechaProrroga = $fechaPagoInicial->addDays($company->dias_inicio_cobro);
            $fechaProrrogaTrabajo = $fechaPago->addDays($company->dias_inicio_cobro);
            $fechaActual = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            $fechaActualTrabajo = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            $diasDebeFechaActual = $fechaProrrogaTrabajo->diffInDays($fechaActualTrabajo);
            if ($company->select_tipo_interes == 'D') {
                $diasTope = $company->numero_dias_interes;
                $diasReales = 0;
                if ($diasDebeFechaActual > 0) {
                    if ($diasDebeFechaActual > $company->dias_inicio_cobro) {
                        $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                    } else {
                        $diasReales = $diasDebeFechaActual;
                    }
                }
                if ($diasReales > $diasTope) {
                    $diasAdd = $diasTope - $company->dias_gracia;
                } else {
                    $diasAdd = $diasReales;
                }
            } else if ($company->select_tipo_interes == 'M') {

                $fechaActual = Carbon::now();

                // Calcular la diferencia en meses entre la fecha actual y $fechaProrrogaTrabajo
                $mesesDiferencia = $fechaActual->diffInMonths($fechaProrrogaTrabajo->endOfMonth());
                $diasAdd = $mesesDiferencia;
                /*
                dd ($mesesDiferencia);
                $diasTope = $fechaProrrogaTrabajo->endOfMonth();
                $diferencia = $fechaProrroga->diff($diasTope);
                $diasReales = (int) $diferencia->format("%d");
                dd($diasTope, $diferencia, $diasReales, $company->dias_inicio_cobro);
                if ($diasReales > 0) {
                    $diasAdd = $diasReales + $company->dias_inicio_cobro - $company->dias_gracia;
                } else {
                    $diasAdd = 0;
                }
                */
            } else if ($company->select_tipo_interes == 'S') {
                $fechaLetraInicio = $value->date_vencimiento;
                $code1 = $value->code_folder_header;
                if (isset($vencidas[$key + 1])) {
                    $siguienteValor = $vencidas[$key + 1];
                    $code2 = $siguienteValor->code_folder_header;
                    $fechaLetraFin = $siguienteValor->date_vencimiento;

                    if ($code1 == $code2) {
                        $fecha1 = Carbon::createFromFormat('Y-m-d', $fechaLetraInicio);
                        $fecha2 = Carbon::createFromFormat('Y-m-d', $fechaLetraFin);
                        $diferenciaDias = $fecha1->diffInDays($fecha2);
                        $diasReales = 0;
                        if ($diasDebeFechaActual > 0) {
                            $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                        }
                        if ($diasReales > $diferenciaDias) {
                            $diasAdd = $diferenciaDias - $company->dias_gracia;
                        } else {
                            $diasAdd = $diasReales;
                        }
                    } else {
                        if ($fechaLetraInicio != date('Y-m-d')) {
                            $fecha1 = Carbon::createFromFormat('Y-m-d', $fechaLetraInicio);
                            $fecha2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                            $diferenciaDias = $fecha1->diffInDays($fecha2);
                            $diasReales = 0;
                            if ($diasDebeFechaActual > 0) {
                                $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                            }
                            if ($diasReales > $diferenciaDias) {
                                $diasAdd = $diferenciaDias - $company->dias_gracia;
                            } else {
                                $diasAdd = $diasReales;
                            }
                        } else {
                            $diasAdd = 0;
                        }
                    }
                } else {
                    if ($fechaLetraInicio != date('Y-m-d')) {

                        $fecha1 = Carbon::createFromFormat('Y-m-d', $fechaLetraInicio);
                        $fecha2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                        $diferenciaDias = $fecha1->diffInDays($fecha2);
                        $diasReales = 0;

                        if ($diasDebeFechaActual > 0) {
                            $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                        }
                        if ($diasReales > $diferenciaDias) {
                            $diasAdd = $diferenciaDias - $company->dias_gracia;
                        } else {
                            $diasAdd = $diasReales;
                        }
                    } else {
                        $diasAdd = 0;
                    }
                }
            }
            if ($company->select_tipo_interes == 'D' || $company->select_tipo_interes == 'M' || $company->select_tipo_interes == 'S') {
                $value->dias_mora = $diasAdd;
                $valor = $company->porcentaje_mora;
                $intervalMeses = (int) $diasAdd;
                $porcentaje = (float) round(($value->valor_cuota * $intervalMeses * ($valor / 100)), 2);
                $value->interes = $porcentaje;
            } else {
                $date1 = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
                $date2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                $value->dias_mora = $date1->diffInDays($date2);
                $valor = $company->porcentaje_mora;
                $interval = $date2->diff($date1);
                $intervalMeses = (int) $interval->format("%m");
                $porcentaje = (float) round(($value->valor_cuota * $intervalMeses * ($valor / 100)), 2);
                $value->interes = $porcentaje;
            }
        }
        return $porcentaje;
    }
    public static function calculoInteresMoraLetraValorMensual($id)
    {
        $porcentaje = 0;
        $company = Company::find(Auth::user()->company_id);
        $value = CreditFolderDetail::find($id);
        $date1 = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
        $date2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
        $diasMora = $date1->diffInDays($date2);
        if ($value->date_vencimiento < date('Y-m-d')) {
            if ($company->interes_fijo_parametrizado) {
                $reglaInteres = InteresFijoParametrizado::where('valor_inicio', '<=', $value->valor_cuota)
                    ->where('valor_fin', '>=', $value->valor_cuota)
                    ->where('status', true)
                    ->first();

                if ($reglaInteres) {
                    $creditoHeader = CreditFolderHeader::where('code', $value->code_folder_header)->first();
                    $prestamo = Prestamos::find($creditoHeader->tipo_prestamo);
                    $recurrenciaPrestamo = RecurrenciaPrestamos::find($prestamo->periodo_id);
                    switch ($recurrenciaPrestamo->code) {
                        case 'M':
                            if (!$reglaInteres->porcentaje) {
                                if ($diasMora >= 1 && $diasMora <= 30) {
                                    $porcentaje = $reglaInteres->primer_valor;
                                } else if ($diasMora >= 31 && $diasMora <= 60) {
                                    $porcentaje = $reglaInteres->segundo_valor;
                                } else if ($diasMora >= 61 && $diasMora <= 90) {
                                    $porcentaje = $reglaInteres->tercer_valor;
                                } else if ($diasMora > 90) {
                                    $porcentaje = $reglaInteres->cuarto_valor;
                                }
                            } else {
                                if ($diasMora >= 1 && $diasMora <= 30) {
                                    $porcentaje = $value->valor_cuota * ($reglaInteres->primer_valor / 100);
                                } else if ($diasMora >= 31 && $diasMora <= 60) {
                                    $porcentaje = $value->valor_cuota * ($reglaInteres->segundo_valor / 100);
                                } else if ($diasMora >= 61 && $diasMora <= 90) {
                                    $porcentaje = $value->valor_cuota * ($reglaInteres->tercer_valor / 100);
                                } else if ($diasMora > 90) {
                                    $porcentaje = $value->valor_cuota * ($reglaInteres->cuarto_valor / 100);
                                }
                            }
                            break;
                        case 'S':
                            if (!$reglaInteres->porcentaje) {
                                if ($diasMora >= 1 && $diasMora <= 30) {
                                    $porcentaje = $reglaInteres->primer_valor / 4;
                                } else if ($diasMora >= 31 && $diasMora <= 60) {
                                    $porcentaje = $reglaInteres->segundo_valor / 4;
                                } else if ($diasMora >= 61 && $diasMora <= 90) {
                                    $porcentaje = $reglaInteres->tercer_valor / 4;
                                } else if ($diasMora > 90) {
                                    $porcentaje = $reglaInteres->cuarto_valor / 4;
                                }
                            } else {
                                if ($diasMora >= 1 && $diasMora <= 30) {
                                    $porcentaje = ($value->valor_cuota * ($reglaInteres->primer_valor / 100)) / 4;
                                } else if ($diasMora >= 31 && $diasMora <= 60) {
                                    $porcentaje = ($value->valor_cuota * ($reglaInteres->segundo_valor / 100)) / 4;
                                } else if ($diasMora >= 61 && $diasMora <= 90) {
                                    $porcentaje = ($value->valor_cuota * ($reglaInteres->tercer_valor / 100)) / 4;
                                } else if ($diasMora > 90) {
                                    $porcentaje = ($value->valor_cuota * ($reglaInteres->cuarto_valor / 100)) / 4;
                                }
                            }
                            break;
                        case 'D':
                            if (!$reglaInteres->porcentaje) {
                                if ($diasMora >= 1 && $diasMora <= 30) {
                                    $porcentaje = $reglaInteres->primer_valor / 24;
                                } else if ($diasMora >= 31 && $diasMora <= 60) {
                                    $porcentaje = $reglaInteres->segundo_valor / 24;
                                } else if ($diasMora >= 61 && $diasMora <= 90) {
                                    $porcentaje = $reglaInteres->tercer_valor / 24;
                                } else if ($diasMora > 90) {
                                    $porcentaje = $reglaInteres->cuarto_valor / 24;
                                }
                            } else {
                                if ($diasMora >= 1 && $diasMora <= 30) {
                                    $porcentaje = ($value->valor_cuota * ($reglaInteres->primer_valor / 100)) / 24;
                                } else if ($diasMora >= 31 && $diasMora <= 60) {
                                    $porcentaje = ($value->valor_cuota * ($reglaInteres->segundo_valor / 100)) / 24;
                                } else if ($diasMora >= 61 && $diasMora <= 90) {
                                    $porcentaje = ($value->valor_cuota * ($reglaInteres->tercer_valor / 100)) / 24;
                                } else if ($diasMora > 90) {
                                    $porcentaje = ($value->valor_cuota * ($reglaInteres->cuarto_valor / 100)) / 24;
                                }
                            }
                            break;
                    }
                }
            } else {

                if (date('Y-m-d') > $value->date_vencimiento) {
                    $mesesMora = $diasMora / 30;
                    $value->meses_mora = round($mesesMora, 2);
                    $valor = $company->porcentaje_mora;
                    $porcentaje = (float) round(($value->valor_cuota * $value->meses_mora * ($valor / 100)), 2);
                }
            }
        }

        return number_format(round($porcentaje, 2), 2, '.', '');
    }
    public static function calculoInteresMoraLetraValorMensualValores($id)
    {
        $porcentaje = 0;
        $company = Company::find(Auth::user()->company_id);
        $value = CreditFolderDetail::find($id);
        $creditoHeader = CreditFolderHeader::where('code', $value->code_folder_header)->first();
        $date1 = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
        $date2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
        $diasMora = $date1->diffInDays($date2);

        if ($company->interes_fijo_parametrizado and  $creditoHeader) {
            $reglaInteres = InteresFijoParametrizado::where('valor_inicio', '<=', $value->valor_cuota)
                ->where('valor_fin', '>=', $value->valor_cuota)
                ->where('status', true)
                ->first();

            if ($reglaInteres) {

                $prestamo = Prestamos::find($creditoHeader->tipo_prestamo);
                $recurrenciaPrestamo = RecurrenciaPrestamos::find($prestamo->periodo_id);
                switch ($recurrenciaPrestamo->code) {
                    case 'M':
                        if (!$reglaInteres->porcentaje) {
                            if ($diasMora >= 1 && $diasMora <= 30) {
                                $porcentaje = $reglaInteres->primer_valor;
                            } else if ($diasMora >= 31 && $diasMora <= 60) {
                                $porcentaje = $reglaInteres->segundo_valor;
                            } else if ($diasMora >= 61 && $diasMora <= 90) {
                                $porcentaje = $reglaInteres->tercer_valor;
                            } else if ($diasMora > 90) {
                                $porcentaje = $reglaInteres->cuarto_valor;
                            }
                        } else {
                            if ($diasMora >= 1 && $diasMora <= 30) {
                                $porcentaje = $value->valor_cuota * ($reglaInteres->primer_valor / 100);
                            } else if ($diasMora >= 31 && $diasMora <= 60) {
                                $porcentaje = $value->valor_cuota * ($reglaInteres->segundo_valor / 100);
                            } else if ($diasMora >= 61 && $diasMora <= 90) {
                                $porcentaje = $value->valor_cuota * ($reglaInteres->tercer_valor / 100);
                            } else if ($diasMora > 90) {
                                $porcentaje = $value->valor_cuota * ($reglaInteres->cuarto_valor / 100);
                            }
                        }
                        break;
                    case 'S':
                        if (!$reglaInteres->porcentaje) {
                            if ($diasMora >= 1 && $diasMora <= 30) {
                                $porcentaje = $reglaInteres->primer_valor / 4;
                            } else if ($diasMora >= 31 && $diasMora <= 60) {
                                $porcentaje = $reglaInteres->segundo_valor / 4;
                            } else if ($diasMora >= 61 && $diasMora <= 90) {
                                $porcentaje = $reglaInteres->tercer_valor / 4;
                            } else if ($diasMora > 90) {
                                $porcentaje = $reglaInteres->cuarto_valor / 4;
                            }
                        } else {
                            if ($diasMora >= 1 && $diasMora <= 30) {
                                $porcentaje = ($value->valor_cuota * ($reglaInteres->primer_valor / 100)) / 4;
                            } else if ($diasMora >= 31 && $diasMora <= 60) {
                                $porcentaje = ($value->valor_cuota * ($reglaInteres->segundo_valor / 100)) / 4;
                            } else if ($diasMora >= 61 && $diasMora <= 90) {
                                $porcentaje = ($value->valor_cuota * ($reglaInteres->tercer_valor / 100)) / 4;
                            } else if ($diasMora > 90) {
                                $porcentaje = ($value->valor_cuota * ($reglaInteres->cuarto_valor / 100)) / 4;
                            }
                        }
                        break;
                    case 'D':
                        if (!$reglaInteres->porcentaje) {
                            if ($diasMora >= 1 && $diasMora <= 30) {
                                $porcentaje = $reglaInteres->primer_valor / 24;
                            } else if ($diasMora >= 31 && $diasMora <= 60) {
                                $porcentaje = $reglaInteres->segundo_valor / 24;
                            } else if ($diasMora >= 61 && $diasMora <= 90) {
                                $porcentaje = $reglaInteres->tercer_valor / 24;
                            } else if ($diasMora > 90) {
                                $porcentaje = $reglaInteres->cuarto_valor / 24;
                            }
                        } else {
                            if ($diasMora >= 1 && $diasMora <= 30) {
                                $porcentaje = ($value->valor_cuota * ($reglaInteres->primer_valor / 100)) / 24;
                            } else if ($diasMora >= 31 && $diasMora <= 60) {
                                $porcentaje = ($value->valor_cuota * ($reglaInteres->segundo_valor / 100)) / 24;
                            } else if ($diasMora >= 61 && $diasMora <= 90) {
                                $porcentaje = ($value->valor_cuota * ($reglaInteres->tercer_valor / 100)) / 24;
                            } else if ($diasMora > 90) {
                                $porcentaje = ($value->valor_cuota * ($reglaInteres->cuarto_valor / 100)) / 24;
                            }
                        }
                        break;
                }
            }
        } else {

            if (date('Y-m-d') > $value->date_vencimiento) {
                $mesesMora = $diasMora / 30;
                $value->meses_mora = round($mesesMora, 2);
                $valor = $company->porcentaje_mora;
                $porcentaje = (float) round(($value->valor_cuota * $value->meses_mora * ($valor / 100)), 2);
            }
        }
        $data = [
            'diasInteres' => $diasMora,
            'valorInteres' => number_format(round($porcentaje, 2), 2, '.', ''),
        ];
        return $data;
    }

    public static function calculoInteresMoraLetrasUnidas($code, $fecha_inicio, $fecha_fin)
    {
        $porcentaje = 0;
        $letrasVencidas = CreditFolderDetail::where('date_vencimiento', '>=', $fecha_inicio)
            ->where('date_vencimiento', '<=', $fecha_fin)
            ->where('code_folder_header', $code)
            ->where('status', 'PENDIENTE')
            ->get();
        foreach ($letrasVencidas as $val) {
            $interesMora = BaseController::calculoInteresMoraLetraValorMensual($val->id);
            $porcentaje += $interesMora;
        }
        return number_format(round($porcentaje, 2), 2, '.', '');
    }
    public static function calculoDiasLetrasUnidas($code, $fecha_inicio, $fecha_fin)
    {
        $dias = 0;
        $letrasVencidas = CreditFolderDetail::where('date_vencimiento', '>=', $fecha_inicio)
            ->where('date_vencimiento', '<=', $fecha_fin)
            ->where('code_folder_header', $code)
            ->where('status', 'PENDIENTE')
            ->get();
        foreach ($letrasVencidas as $val) {

            $date1 = Carbon::parse($val->date_vencimiento);
            $date2 = Carbon::parse(date('Y-m-d'));

            $difference = $date1->diffInDays($date2);
            $diasCalculo = $difference;

            $dias += $diasCalculo;
        }
        return $dias;
    }
    public static function calculoDiasLetrasUnidasUnico($code, $fecha_inicio, $fecha_fin)
    {
        $dias = 0;
        $letrasVencidas = CreditFolderDetail::where('date_vencimiento', '>=', $fecha_inicio)
            ->where('date_vencimiento', '<=', $fecha_fin)
            ->where('code_folder_header', $code)
            ->where('status', 'PENDIENTE')
            ->orderBy('id', 'desc')
            ->get();
        foreach ($letrasVencidas as $val) {

            $date1 = Carbon::parse($val->date_vencimiento);
            $date2 = Carbon::parse(date('Y-m-d'));

            $difference = $date1->diffInDays($date2);
            $diasCalculo = $difference;

            $dias = $diasCalculo;
        }
        return $dias;
    }
    public static function calculoDiasLetrasUnidasUnicoContador($code, $fecha_inicio, $fecha_fin)
    {
        $dias = 0;
        $letrasVencidas = CreditFolderDetail::where('date_vencimiento', '>=', $fecha_inicio)
            ->where('date_vencimiento', '<=', $fecha_fin)
            ->where('code_folder_header', $code)
            ->where('status', 'PENDIENTE')
            ->orderBy('id', 'desc')
            ->get();
        foreach ($letrasVencidas as $val) {
            $dias += 1;
        }
        return $dias;
    }


    public static function calculoInteresMoraLetraValorCrom($id)
    {
        $porcentaje = 0;
        $value = CreditFolderDetail::find($id);
        if ($value->date_vencimiento < date('Y-m-d')) {
            $company = Company::find(1);
            $fechaPagoInicial = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
            $fechaPago = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
            $fechaProrroga = $fechaPagoInicial->addDays($company->dias_inicio_cobro);
            $fechaProrrogaTrabajo = $fechaPago->addDays($company->dias_inicio_cobro);
            $fechaActual = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            $fechaActualTrabajo = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            $diasDebeFechaActual = $fechaProrrogaTrabajo->diffInDays($fechaActualTrabajo);
            if ($company->select_tipo_interes == 'D') {
                $diasTope = $company->numero_dias_interes;
                $diasReales = 0;
                if ($diasDebeFechaActual > 0) {
                    if ($diasDebeFechaActual > $company->dias_inicio_cobro) {
                        $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                    } else {
                        $diasReales = $diasDebeFechaActual;
                    }
                }
                if ($diasReales > $diasTope) {
                    $diasAdd = $diasTope - $company->dias_gracia;
                } else {
                    $diasAdd = $diasReales;
                }
            } else if ($company->select_tipo_interes == 'M') {
                $diasTope = $fechaProrrogaTrabajo->endOfMonth();
                $diferencia = $fechaProrroga->diff($diasTope);
                $diasReales = (int) $diferencia->format("%d");
                if ($diasReales > 0) {
                    $diasAdd = $diasReales + $company->dias_inicio_cobro - $company->dias_gracia;
                } else {
                    $diasAdd = 0;
                }
            } else if ($company->select_tipo_interes == 'S') {
                $fechaLetraInicio = $value->date_vencimiento;
                $code1 = $value->code_folder_header;
                if (isset($vencidas[$key + 1])) {
                    $siguienteValor = $vencidas[$key + 1];
                    $code2 = $siguienteValor->code_folder_header;
                    $fechaLetraFin = $siguienteValor->date_vencimiento;

                    if ($code1 == $code2) {
                        $fecha1 = Carbon::createFromFormat('Y-m-d', $fechaLetraInicio);
                        $fecha2 = Carbon::createFromFormat('Y-m-d', $fechaLetraFin);
                        $diferenciaDias = $fecha1->diffInDays($fecha2);
                        $diasReales = 0;
                        if ($diasDebeFechaActual > 0) {
                            $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                        }
                        if ($diasReales > $diferenciaDias) {
                            $diasAdd = $diferenciaDias - $company->dias_gracia;
                        } else {
                            $diasAdd = $diasReales;
                        }
                    } else {
                        if ($fechaLetraInicio != date('Y-m-d')) {
                            $fecha1 = Carbon::createFromFormat('Y-m-d', $fechaLetraInicio);
                            $fecha2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                            $diferenciaDias = $fecha1->diffInDays($fecha2);
                            $diasReales = 0;
                            if ($diasDebeFechaActual > 0) {
                                $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                            }
                            if ($diasReales > $diferenciaDias) {
                                $diasAdd = $diferenciaDias - $company->dias_gracia;
                            } else {
                                $diasAdd = $diasReales;
                            }
                        } else {
                            $diasAdd = 0;
                        }
                    }
                } else {
                    if ($fechaLetraInicio != date('Y-m-d')) {

                        $fecha1 = Carbon::createFromFormat('Y-m-d', $fechaLetraInicio);
                        $fecha2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                        $diferenciaDias = $fecha1->diffInDays($fecha2);
                        $diasReales = 0;

                        if ($diasDebeFechaActual > 0) {
                            $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                        }
                        if ($diasReales > $diferenciaDias) {
                            $diasAdd = $diferenciaDias - $company->dias_gracia;
                        } else {
                            $diasAdd = $diasReales;
                        }
                    } else {
                        $diasAdd = 0;
                    }
                }
            }
            if ($company->select_tipo_interes == 'D' || $company->select_tipo_interes == 'M' || $company->select_tipo_interes == 'S') {
                $value->dias_mora = $diasAdd;
                $valor = $company->porcentaje_mora;
                $intervalMeses = (int) $diasAdd;
                $porcentaje = (float) round(($value->valor_cuota * $intervalMeses * ($valor / 100)), 2);
                $value->interes = $porcentaje;
            } else {
                $date1 = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
                $date2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                $value->dias_mora = $date1->diffInDays($date2);
                $valor = $company->porcentaje_mora;
                $interval = $date2->diff($date1);
                $intervalMeses = (int) $interval->format("%m");
                $porcentaje = (float) round(($value->valor_cuota * $intervalMeses * ($valor / 100)), 2);
                $value->interes = $porcentaje;
            }
        }
        return $porcentaje;
    }

    public static function descargoAutomaticoCuentaCredito($valor, $customerTipoAhorro, $leta =  null)
    {
        if ($leta != null) {
            $detalle = CreditFolderDetail::find($leta);
            $header = CreditFolderHeader::where('code', $detalle->code_folder_header)->first();
        }
        $cuenta = CustomerTipoAhorros::find($customerTipoAhorro);
        $customer = Customer::find($cuenta->customer_id);
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'DEA')
            ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
        $data = [
            "code" => $code,
            "company_id" => Auth::user()->company_id,
            "customer_id" => $customer->id,
            "customer_code" => $customer->code,
            "afecta" => $transaction->afecta,
            "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
            "customer_ruc" => $customer->numero_documento,
            "customer_address" => $customer->direccion,
            "customer_telefono" => $customer->telefono,
            "customer_tipo_ahorro_id" => $cuenta->id,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $valor,
            "saldo_general" => $valorTotal + $valor,
            "observation" => $transaction->description . ' ' . date('Y-m-d'),
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
            "credit_folder_details_id" => ($leta != null) ?  $detalle->id : null,
            "credit_folder_header_id" => ($leta != null) ? $header->id : null,
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $valor, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
        CartolaController::ingresoCartola($movimientos->customer_code, $movimientos->type_transaction_id, $movimientos->valor_movimiento, date('Y-m-d'));
        BaseController::guardarValoresCartola($cuenta->id, $customer->id, $movimientos, $transaction);
    }

    public static function descargoAutomaticoCuentaCreditoCron($valor, $customerTipoAhorro)
    {
        $cuenta = CustomerTipoAhorros::find($customerTipoAhorro);
        $customer = Customer::find($cuenta->customer_id);
        $transaction = TypeTransaction::where('company_id', 1)
            ->where('name_corto', 'DEA')
            ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigoCron($tabla, 9);
        $valorTotal = BaseController::valorTotal();
        $data = [
            "code" => $code,
            "company_id" => 1,
            "customer_id" => $customer->id,
            "customer_code" => $customer->code,
            "afecta" => $transaction->afecta,
            "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
            "customer_ruc" => $customer->numero_documento,
            "customer_address" => $customer->direccion,
            "customer_telefono" => $customer->telefono,
            "customer_tipo_ahorro_id" => $cuenta->id,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $valor,
            "saldo_general" => $valorTotal + $valor,
            "observation" => $transaction->description . ' ' . date('Y-m-d'),
            "user_created_id" => 1,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
            "credit_folder_details_id" => $detalle->id,
            "credit_folder_header_id" => $header->id,
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomaticaCron($movimientos->code, $transaction->id, $valor, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
        BaseController::guardarValoresCartolaCron($cuenta->id, $customer->id, $movimientos, $transaction);
    }

    public static function pagoAutomaticoLetraCredito($valor, $letra, $calculoMora)
    {
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'PCA')
            ->first();
        $valorTotal = BaseController::valorTotal();
        $letraEnvia = CreditFolderDetail::find($letra);
        $anteriores = CreditFolderDetail::where('id', '<', $letra)
            ->where('code_folder_header', $letraEnvia->code_folder_header)
            ->where('status', 'PENDIENTE');
        $letraBuscar = $letra;
        if ($anteriores->count() > 0) {
            $letrasAnt = $anteriores->first();
            $letraBuscar = $letrasAnt->id;
            $valor = $letrasAnt->valor_cuota;
        }
        $detalle = CreditFolderDetail::find($letraBuscar);
        $detalle->valor_pagado = $valor;
        $detalle->valor_final = $valor;
        $detalle->interes_mora = $calculoMora;
        $detalle->tipo_pago = 'DEBITO AUTOMATICO';
        $detalle->obervation_pago = $transaction->description;
        $detalle->date_pay = date('Y-m-d');
        $detalle->hour_pay = date('H:i:s');
        $detalle->user_pay_id = Auth::user()->id;
        $detalle->status = 'PAGADA';
        $detalle->save();
        $header = CreditFolderHeader::where('code', $detalle->code_folder_header)->first();
        $customer = Customer::find($header->customer_id);
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $data = [
            "code" => $code,
            "customer_id" => $customer->id,
            "company_id" => Auth::user()->company_id,
            "customer_code" => $customer->code,
            "afecta" => $transaction->afecta,
            "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
            "customer_ruc" => $customer->numero_documento,
            "customer_address" => $customer->direccion,
            "customer_telefono" => $customer->telefono,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $valor,
            "saldo_general" => $valorTotal + $valor,
            "observation" => $transaction->description . ' ' . date('Y-m-d'),
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $valor, $customer->code, $movimientos->saldo_general, date('Y-m-d'));
        $cabecera = CreditFolderHeader::where('code', $detalle->code_folder_header)->first();
        $valornew = $cabecera->total_pagando + $valor;
        $cabecera->total_pagando = $valornew;
        $cabecera->save();
        BaseController::enviarMail($movimientos);
        return true;
    }

    public static function pagoAutomaticoLetraCreditoCron($valor, $letra, $calculoMora)
    {
        $transaction = TypeTransaction::where('company_id', 1)
            ->where('name_corto', 'PCA')
            ->first();
        $valorTotal = BaseController::valorTotal();
        $letraEnvia = CreditFolderDetail::find($letra);
        $anteriores = CreditFolderDetail::where('id', '<', $letra)
            ->where('code_folder_header', $letraEnvia->code_folder_header)
            ->where('status', 'PENDIENTE');
        $letraBuscar = $letra;
        if ($anteriores->count() > 0) {
            $letrasAnt = $anteriores->first();
            $letraBuscar = $letrasAnt->id;
            $valor = $letrasAnt->valor_cuota;
        }
        $detalle = CreditFolderDetail::find($letraBuscar);
        $detalle->valor_pagado = $valor;
        $detalle->valor_final = $valor;
        $detalle->interes_mora = $calculoMora;
        $detalle->tipo_pago = 'DEBITO AUTOMATICO';
        $detalle->obervation_pago = $transaction->description;
        $detalle->date_pay = date('Y-m-d');
        $detalle->hour_pay = date('H:i:s');
        $detalle->user_pay_id = 1;
        $detalle->status = 'PAGADA';
        $detalle->save();
        $header = CreditFolderHeader::where('code', $detalle->code_folder_header)->first();
        $customer = Customer::find($header->customer_id);
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigoCron($tabla, 9);
        $data = [
            "code" => $code,
            "customer_id" => $customer->id,
            "company_id" => 1,
            "customer_code" => $customer->code,
            "afecta" => $transaction->afecta,
            "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
            "customer_ruc" => $customer->numero_documento,
            "customer_address" => $customer->direccion,
            "customer_telefono" => $customer->telefono,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $valor,
            "saldo_general" => $valorTotal + $valor,
            "observation" => $transaction->description . ' ' . date('Y-m-d'),
            "user_created_id" => 1,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomaticaCron($movimientos->code, $transaction->id, $valor, $customer->code, $movimientos->saldo_general, date('Y-m-d'));
        $cabecera = CreditFolderHeader::where('code', $detalle->code_folder_header)->first();
        $valornew = $cabecera->total_pagando + $valor;
        $cabecera->total_pagando = $valornew;
        $cabecera->save();
        BaseController::enviarMail($movimientos);
        return true;
    }

    public static function saldoCuentaCliente($customer_tipo_id)
    {
        $saldoFinal = 0;
        if ($customer_tipo_id != null &&  $customer_tipo_id != '') {
            $customerTipoAhorro = CustomerTipoAhorros::find($customer_tipo_id);
            //echo "  --------- Saldo Cuenta Cliente: " . $customerTipoAhorro->id . "  --------- \n";
            $sumaValores = TipoAhorrosDetalle::where('tipo_ahorros_id', $customerTipoAhorro->tipo_ahorros_id)->sum('valor');
            $retenido = 0;
            if ($sumaValores !== 0) {
                $retenido = TipoAhorrosDetalle::where('tipo_ahorros_id', $customerTipoAhorro->tipo_ahorros_id)->where('bloqueado', true)->sum('valor');
            }
            $saldo = BaseController::saldoEnCuenta($customerTipoAhorro->customer_id, $customerTipoAhorro->id);
            $saldoFinal = floatval($saldo - $retenido);
            if ($saldoFinal < 0) {
                $saldoFinal = 0;
            }
        } else {
            //echo "  --------- No tiene cuenta  --------- \n";
        }


        return $saldoFinal;
    }

    public static function saldoEnCuenta($cliente, $cuenta)
    {
        $cliente = Customer::find($cliente);
        $ingresos = CustomerHistorial::where('customer_code', $cliente->code)
            ->where('type_transaction_action', 'S')
            // ->where('type_transaction_name', 'INGRESOS')
            ->where('customer_tipo_ahorro_id', $cuenta)
            ->where('status', true)
            ->sum('valor_movimiento');
        $egresos = CustomerHistorial::where('customer_code', $cliente->code)
            ->where('type_transaction_action', 'R')
            // ->where('type_transaction_name', 'EGRESOS')
            ->where('customer_tipo_ahorro_id', $cuenta)
            ->where('status', true)
            ->sum('valor_movimiento');
        $valor = $ingresos - $egresos;
        $saldoCuenta = number_format(round($valor, 2), 2, '.', '');
        return $saldoCuenta;
    }

    public static function guardarValoresCartola($ahrroID, $customerID, $movimiento, $transaction)
    {
        $cartolaActiva = CartolaHeader::where('customer_tipo_ahorro_id', $ahrroID)
            ->where('customer_id', $customerID)
            ->where('status', 'ACTIVA')
            ->first();
        if ($cartolaActiva == null) {
            $clientes = Customer::find($customerID);
            $tabla = 'cartola_headers';
            $code = BaseController::generarCodigoCron($tabla, 3);
            $cartolaNew = [
                "code" => $code,
                "numero" => $code,
                "company_id" => 1,
                "customer_id" => $clientes->id,
                "customer_code" => $clientes->code,
                "customer_name" => $clientes->nombres . ' ' . $clientes->apellidos,
                "customer_date_create" => date('Y-m-d'),
                "status" => 'ACTIVA',
                "date_create" => date('Y-m-d'),
                "hour_create" => date('H:i:s'),
                "customer_tipo_ahorro_id" => $ahrroID,
            ];
            $cartolaActiva = CartolaHeader::create($cartolaNew);
        }

        $caraA = CartolaDetail::where('cartola_headers_id', $cartolaActiva->id)->where('cara', 'A')->count();
        $caraB = CartolaDetail::where('cartola_headers_id', $cartolaActiva->id)->where('cara', 'B')->count();
        $company = Company::find(Auth::user()->company_id);
        $finA = $company->cartola_a;
        $finB = $company->cartola_b;

        $transactionNoSuma = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'SE')
            ->first();
        if ($caraA < $finA) {
            $transaccion = TypeTransaction::find($movimiento->type_transaction_id);
            $totalRegistros = CartolaDetail::where('cartola_headers_id', $cartolaActiva->id)->where('cara', 'A')->count();
            $posicion = $totalRegistros + 1;
            $cartolaDetalle = [
                "cartola_header_code" => $cartolaActiva->code,
                "customer_movimientos_id" => $movimiento->id,
                "type_transaction_id" => $transaccion->id,
                "type_transaction_name" => $transaccion->name,
                "type_transaction_action" => $transaccion->action,
                "valor_transaction" => $movimiento->valor_movimiento,
                "date_transaction" => $movimiento->date_created,
                'cartola_headers_id' => $cartolaActiva->id,
                'cara' => 'A',
                'posicion' => $posicion,
            ];
            $cartolaDetail = CartolaDetail::create($cartolaDetalle);
            $cartolasTodas = CartolaHeader::where('customer_tipo_ahorro_id', $cartolaActiva->customer_tipo_ahorro_id)->get();
            $suma = CartolaDetail::whereIn('cartola_headers_id', $cartolasTodas->pluck('id'))
                ->where('type_transaction_action', 'S')
                ->where('type_transaction_id', '!=', $transactionNoSuma->id)
                ->sum('valor_transaction');
            $resta = CartolaDetail::whereIn('cartola_headers_id', $cartolasTodas->pluck('id'))
                ->where('type_transaction_action', 'R')
                ->where('type_transaction_id', '!=', $transactionNoSuma->id)
                ->sum('valor_transaction');
            $diferencia = $suma - $resta;
            $cartolaDetail->saldo_transaction = $diferencia;
            $cartolaDetail->save();
        } else if ($caraB < $finB) {
            $transaccion = TypeTransaction::find($movimiento->type_transaction_id);
            $totalRegistros = CartolaDetail::where('cartola_headers_id', $cartolaActiva->id)->where('cara', 'B')->count();
            $posicion = $totalRegistros + 1;
            $cartolaDetalle = [
                "cartola_header_code" => $cartolaActiva->code,
                "customer_movimientos_id" => $movimiento->id,
                "type_transaction_id" => $transaccion->id,
                "type_transaction_name" => $transaccion->name,
                "type_transaction_action" => $transaccion->action,
                "valor_transaction" => $movimiento->valor_movimiento,
                "date_transaction" => $movimiento->date_created,
                'cartola_headers_id' => $cartolaActiva->id,
                'cara' => 'B',
                'posicion' => $posicion,
            ];
            $cartolaDetail = CartolaDetail::create($cartolaDetalle);
            $cartolasTodas = CartolaHeader::where('customer_tipo_ahorro_id', $cartolaActiva->customer_tipo_ahorro_id)->get();
            $suma = CartolaDetail::whereIn('cartola_headers_id', $cartolasTodas->pluck('id'))
                ->where('type_transaction_action', 'S')
                ->where('type_transaction_id', '!=', $transactionNoSuma->id)
                ->sum('valor_transaction');
            $resta = CartolaDetail::whereIn('cartola_headers_id', $cartolasTodas->pluck('id'))
                ->where('type_transaction_action', 'R')
                ->where('type_transaction_id', '!=', $transactionNoSuma->id)
                ->sum('valor_transaction');
            $diferencia = $suma - $resta;
            $cartolaDetail->saldo_transaction = $diferencia;
            $cartolaDetail->save();
        } else {
            BaseController::crearCabeceraCartola($cartolaActiva, $ahrroID, $customerID, $movimiento, $transaction);
        }
    }
    public static function guardarValoresCartolaCron($ahrroID, $customerID, $movimiento, $transaction)
    {
        $cartolaActiva = CartolaHeader::where('customer_tipo_ahorro_id', $ahrroID)
            ->where('customer_id', $customerID)
            ->where('status', 'ACTIVA')
            ->first();
        $caraA = CartolaDetail::where('cartola_headers_id', $cartolaActiva->id)->where('cara', 'A')->count();
        $caraB = CartolaDetail::where('cartola_headers_id', $cartolaActiva->id)->where('cara', 'B')->count();
        $company = Company::find(1);
        $finA = $company->cartola_a;
        $finB = $company->cartola_b;

        $transactionNoSuma = TypeTransaction::where('company_id', 1)
            ->where('name_corto', 'SE')
            ->first();
        if ($caraA < $finA) {
            $transaccion = TypeTransaction::find($movimiento->type_transaction_id);
            $totalRegistros = CartolaDetail::where('cartola_headers_id', $cartolaActiva->id)->where('cara', 'A')->count();
            $posicion = $totalRegistros + 1;
            $cartolaDetalle = [
                "cartola_header_code" => $cartolaActiva->code,
                "customer_movimientos_id" => $movimiento->id,
                "type_transaction_id" => $transaccion->id,
                "type_transaction_name" => $transaccion->name,
                "type_transaction_action" => $transaccion->action,
                "valor_transaction" => $movimiento->valor_movimiento,
                "date_transaction" => $movimiento->date_created,
                'cartola_headers_id' => $cartolaActiva->id,
                'cara' => 'A',
                'posicion' => $posicion,
            ];
            $cartolaDetail = CartolaDetail::create($cartolaDetalle);
            $cartolasTodas = CartolaHeader::where('customer_tipo_ahorro_id', $cartolaActiva->customer_tipo_ahorro_id)->get();
            $suma = CartolaDetail::whereIn('cartola_headers_id', $cartolasTodas->pluck('id'))
                ->where('type_transaction_action', 'S')
                ->where('type_transaction_id', '!=', $transactionNoSuma->id)
                ->sum('valor_transaction');
            $resta = CartolaDetail::whereIn('cartola_headers_id', $cartolasTodas->pluck('id'))
                ->where('type_transaction_action', 'R')
                ->where('type_transaction_id', '!=', $transactionNoSuma->id)
                ->sum('valor_transaction');
            $diferencia = $suma - $resta;
            $cartolaDetail->saldo_transaction = $diferencia;
            $cartolaDetail->save();
        } else if ($caraB < $finB) {
            $transaccion = TypeTransaction::find($movimiento->type_transaction_id);
            $totalRegistros = CartolaDetail::where('cartola_headers_id', $cartolaActiva->id)->where('cara', 'B')->count();
            $posicion = $totalRegistros + 1;
            $cartolaDetalle = [
                "cartola_header_code" => $cartolaActiva->code,
                "customer_movimientos_id" => $movimiento->id,
                "type_transaction_id" => $transaccion->id,
                "type_transaction_name" => $transaccion->name,
                "type_transaction_action" => $transaccion->action,
                "valor_transaction" => $movimiento->valor_movimiento,
                "date_transaction" => $movimiento->date_created,
                'cartola_headers_id' => $cartolaActiva->id,
                'cara' => 'B',
                'posicion' => $posicion,
            ];
            $cartolaDetail = CartolaDetail::create($cartolaDetalle);
            $cartolasTodas = CartolaHeader::where('customer_tipo_ahorro_id', $cartolaActiva->customer_tipo_ahorro_id)->get();
            $suma = CartolaDetail::whereIn('cartola_headers_id', $cartolasTodas->pluck('id'))
                ->where('type_transaction_action', 'S')
                ->where('type_transaction_id', '!=', $transactionNoSuma->id)
                ->sum('valor_transaction');
            $resta = CartolaDetail::whereIn('cartola_headers_id', $cartolasTodas->pluck('id'))
                ->where('type_transaction_action', 'R')
                ->where('type_transaction_id', '!=', $transactionNoSuma->id)
                ->sum('valor_transaction');
            $diferencia = $suma - $resta;
            $cartolaDetail->saldo_transaction = $diferencia;
            $cartolaDetail->save();
        } else {
            BaseController::crearCabeceraCartolaCron($cartolaActiva, $ahrroID, $customerID, $movimiento, $transaction);
        }
    }

    public static function crearCabeceraCartolaCron($cartolaActiva, $ahrroID, $customerID, $movimiento, $transaction)
    {
        $cartolaOld = CartolaHeader::find($cartolaActiva->id);
        $cartolaOld->status = 'CERRADA';
        $cartolaOld->save();
        $tabla = 'cartola_headers';
        $code = BaseController::generarCodigoCron($tabla, 3);
        $clientes = Customer::find($customerID);
        $cartolaNew = [
            "code" => $code,
            "numero" => $code,
            "company_id" => 1,
            "customer_id" => $clientes->id,
            "customer_code" => $clientes->code,
            "customer_name" => $clientes->nombres . ' ' . $clientes->apellidos,
            "customer_date_create" => date('Y-m-d'),
            "status" => 'ACTIVA',
            "date_create" => date('Y-m-d'),
            "hour_create" => date('H:i:s'),
            "customer_tipo_ahorro_id" => $ahrroID,
        ];
        $cartolanueva = CartolaHeader::create($cartolaNew);
        BaseController::guardarValoresCartola($ahrroID, $customerID, $movimiento, $transaction);
    }
    public static function crearCabeceraCartola($cartolaActiva, $ahrroID, $customerID, $movimiento, $transaction)
    {
        $cartolaOld = CartolaHeader::find($cartolaActiva->id);
        $cartolaOld->status = 'CERRADA';
        $cartolaOld->save();
        $tabla = 'cartola_headers';
        $code = BaseController::generarCodigoCron($tabla, 3);
        $clientes = Customer::find($customerID);
        $cartolaNew = [
            "code" => $code,
            "numero" => $code,
            "company_id" => Auth::user()->company_id,
            "customer_id" => $clientes->id,
            "customer_code" => $clientes->code,
            "customer_name" => $clientes->nombres . ' ' . $clientes->apellidos,
            "customer_date_create" => date('Y-m-d'),
            "status" => 'ACTIVA',
            "date_create" => date('Y-m-d'),
            "hour_create" => date('H:i:s'),
            "customer_tipo_ahorro_id" => $ahrroID,
        ];
        $cartolanueva = CartolaHeader::create($cartolaNew);
        BaseController::guardarValoresCartola($ahrroID, $customerID, $movimiento, $transaction);
    }

    public static function enviarMail($movimiento)
    {
        $tipoTransaccion = TypeTransaction::find($movimiento->type_transaction_id);
        $cartolaDetalle = CartolaDetail::where('customer_movimientos_id', $movimiento->id)->first();
        $company = Company::find($movimiento->company_id);
        if ($company->photo != null && $company->photo != '') {
            $path = 'uploads/companies/' . $company->photo;
            if (!file_exists(public_path($path))) {
                $path = 'codev/negro.png';
            }
        } else {
            $path = 'codev/negro.png';
        }



        if ($company->enviar_mails) {
            $tipoMov = '';
            switch ($tipoTransaccion->name_corto) {
                case 'IN':
                    $tipoMov = 'DEPOSITO';
                    $texto = '<table style="font-size: 11px;width: 100%" border="3px">
                            <thead>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Valor</th>
                                 <th>Saldo</th>
                            </thead>
                            <tbody>
                                <td>' . $tipoMov . '</td>
                                <td>' . $movimiento->date_created . ' ' . $movimiento->hour_created . '</td>
                                <td>$ ' . $cartolaDetalle->valor_transaction . '</td>
                                <td>$ ' . $cartolaDetalle->saldo_transaction . '</td>
                            </tbody>
                        </table>';
                    break;
                case 'EG':
                    $tipoMov = 'RETIRO';
                    $texto = '<table style="font-size: 11px;width: 100%" border="3px">
                            <thead>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Valor</th>
                                 <th>Saldo</th>
                            </thead>
                            <tbody>
                                <td>' . $tipoMov . '</td>
                                <td>' . $movimiento->date_created . ' ' . $movimiento->hour_created . '</td>
                                <td>$ ' . $cartolaDetalle->valor_transaction . '</td>
                                <td>$ ' . $cartolaDetalle->saldo_transaction . '</td>
                            </tbody>
                        </table>';
                    break;
                case 'ENC':
                    $tipoMov = 'ENTREGA DE CREDITO';
                    $texto = '<table style="font-size: 11px;width: 100%" border="3px">
                            <thead>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Valor</th>
                                 <th>Detalle</th>
                            </thead>
                            <tbody>
                                <td>' . $tipoMov . '</td>
                                <td>' . $movimiento->date_created . ' ' . $movimiento->hour_created . '</td>
                                <td>$ ' . $movimiento->valor_movimiento . '</td>
                                <td> ENTREGA DE CREDITO A LA CUENTA DE: ' . $movimiento->customer_name . ' </td>
                            </tbody>
                        </table>';
                    break;
                case 'PCA':
                    $tipoMov = 'PAGO AUTOMATICO DE CREDITO';
                    $texto = '<table style="font-size: 11px;width: 100%" border="3px">
                            <thead>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Valor</th>
                                 <th>Detalle</th>
                            </thead>
                            <tbody>
                                <td>' . $tipoMov . '</td>
                                <td>' . $movimiento->date_created . ' ' . $movimiento->hour_created . '</td>
                                <td>$ ' . $movimiento->valor_movimiento . '</td>
                                <td> SE DEBITA AUTOMATICAMENTE EL CREDITO DE: ' . $movimiento->customer_name . ' </td>
                            </tbody>
                        </table>';
                    break;
            }

            $data = [
                'name' => $movimiento->customer_name,
                'date' => $movimiento->date_created . ' ' . $movimiento->hour_created,
                'transaccion' => $tipoMov,
                'medio' => $tipoTransaccion->description,
                'mensaje' => 'tranasacción correcta',
                'contenido' => $texto,
                'nombreCaja' => $company->comercial_name,
                'imagen' => file_get_contents(public_path($path)),
            ];
            $customer = Customer::find($movimiento->customer_id);
            if ($customer->correo != null && $customer->correo != '') {
                Mail::to($customer->correo)->send(new NotificacionesMail($data));
            }
        }
    }

    public static function envioCierresCaja()
    {
        $caja = BaseController::crearPdfCierreCaja();
        $cierreCaja = BaseController::enviopdfCierreCaja($caja);
    }
    public static function crearPdfCierreCaja()
    {
        $fechaFin = date('Y-m-d');
        $caja = Cajas::where('date_finish', $fechaFin)->first();
        if ($caja) {
            echo "  --------- Cierre de Caja: " . $caja->id . "  --------- \n";
            $denominacion = DenominacionBilletes::where('company_id', Auth::user()->company_id)->get()->toArray();
            $denom = DenominacionBilletes::where('company_id', Auth::user()->company_id)->get();
            foreach ($denom as $value) {
                $cajaDenominacion = CajasDenominacion::where('company_id', Auth::user()->company_id)
                    ->where('caja_id', $caja->id)
                    ->where('denominacion_billetes_id', $value->id)
                    ->first();
                if ($cajaDenominacion != null) {
                    $value->cantidad = $cajaDenominacion->cantidad;
                    $value->totalValor = $cajaDenominacion->total;
                } else {
                    $value->cantidad = 0;
                    $value->totalValor = 0;
                }
            }
            $tipoDepositos = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'IN')
                ->first();
            $data['detalleDepositos'] = CustomerMovimiento::where('date_created', $caja->date_inicial)
                ->where('type_transaction_id', $tipoDepositos->id)
                ->where('user_created_id', Auth::user()->id)
                ->get();

            $tipoRecaudacion = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'PC')
                ->first();
            $tipoRecaudacionAutomatica = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'PCA')
                ->first();
            $tipoRecaudacionliquidacion = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'LIC')
                ->first();

            $data['detalleRecaudaciones'] = CustomerMovimiento::where('date_created', $caja->date_inicial)
                ->whereIn('type_transaction_id', [$tipoRecaudacion->id, $tipoRecaudacionAutomatica->id, $tipoRecaudacionliquidacion->id])
                ->where('user_created_id', Auth::user()->id)
                ->get();

            $tipoRetiro = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'EG')
                ->first();
            $tipoRetiroAutomatico = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'DEA')
                ->first();
            $data['detalleRetiros'] = CustomerMovimiento::where('date_created', $caja->date_inicial)
                ->whereIn('type_transaction_id', [$tipoRetiro->id])
                ->where('user_created_id', Auth::user()->id)
                ->get();

            $tipoGastos = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'GAS')
                ->first();
            $data['detalleGastos'] = CustomerMovimiento::where('date_created', $caja->date_inicial)
                ->where('type_transaction_id', $tipoGastos->id)
                ->where('user_created_id', Auth::user()->id)
                ->get();

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
            $data['caja'] = $caja;

            $data['denominacion'] = $denominacion;
            $data['denom'] = $denom;
            $man = file_get_contents(public_path($path));
            $data['imagen'] = base64_encode($man);
            $data['otrosValores'] = OtrosIngresos::where('date_create', date('Y-m-d'))->get();
            if (!file_exists(config('constants.ROUTS_IMAGES.PATH_COMPROBANTE'))) {
                mkdir(config('constants.ROUTS_IMAGES.PATH_COMPROBANTE'), 0777, true);
            }


            $path2 = 'uploads/cierres/';
            $destinationPath = public_path($path2);
            if (!file_exists($destinationPath)) {
                // Intenta crear el directorio si no existe
                if (!mkdir($destinationPath, 0777, true) && !is_dir($destinationPath)) {
                    // Manejar el caso en que la creación del directorio falla
                    die('Error al crear el directorio...');
                }
            }
            $nombre = $destinationPath . 'Cierre de caja.pdf';

            $arch = PDF::loadView('cajas._pdf_cajas', compact('data'))
                ->setPaper('A4', "portrait")
                ->save($nombre);
            $datosRe = [
                'caja' => $caja,
                'path' => $nombre,
            ];
            return $datosRe;
        } else {
            echo "  --------- Sin caja para enviar --------- \n";
        }
    }

    public static function enviopdfCierreCaja($caja)
    {
        $company = Company::find(Auth::user()->company_id);
        if ($company->photo != null && $company->photo != '') {
            $path = 'uploads/companies/' . $company->photo;
            if (!file_exists(public_path($path))) {
                $path = 'codev/negro.png';
            }
        } else {
            $path = 'codev/negro.png';
        }
        if ($caja != null) {
            $pdfFilePath = $caja['path'];
            $data = [
                'name' => 'Cierre de Caja ' . $caja['caja']->date_finish . '.pdf',
                'caja' => $caja['caja'],
                'imagen' => file_get_contents(public_path($path)),
                'date' => $caja['caja']->date_finish,
                'nombreCaja' => $company->comercial_name,
            ];
            $usuarios = User::where('cierre_caja', true)
                ->where('status', true)
                ->get();
            foreach ($usuarios as $user) {
                Mail::to($user->email)
                    ->send(new NotificacionesCierresCajaMail($data, $pdfFilePath));
            }
        }
    }

    public static function recalcularCartolas($cuenta, $cliente)
    {
        $cartolasTodas = CartolaHeader::where('customer_tipo_ahorro_id', $cuenta)
            ->where('customer_id', $cliente)
            ->get();
        $detalle = CartolaDetail::whereIn('cartola_headers_id', $cartolasTodas->pluck('id'))
            ->get();
        $transactionNoSuma = TypeTransaction::where('company_id', 1)
            ->where('name_corto', 'SE')
            ->first();
        $suma = 0;
        foreach ($detalle as $deta) {
            if ($deta->type_transaction_id != $transactionNoSuma->id) {
                if ($deta->type_transaction_action == 'S') {
                    $suma += $deta->valor_transaction;
                } else {
                    $suma -= $deta->valor_transaction;
                }
            }
            $linea = CartolaDetail::find($deta->id);
            $linea->saldo_transaction = $suma;
            $linea->save();
        }
    }

    public static function valoresGastosCalculo($prestamo, $valor)
    {
        $porcentage1 = 0;
        $porcentage2 = 0;
        $porcentage3 = 0;
        $gasto1 = 0;
        $gasto2 = 0;
        $gasto3 = 0;
        $valorCredito = $valor;
        if ($prestamo->porcentaje_primer_gasto) {
            $porcentage1 = 1;
            $gasto1 = $valorCredito * ($prestamo->primer_gasto / 100);
        } else {
            $gasto1 = $prestamo->primer_gasto;
            $porcentage1 = 0;
        }

        if ($prestamo->porcentaje_segundo_gasto) {
            $porcentage2 = 1;
            $gasto2 = $valorCredito * ($prestamo->segundo_gasto / 100);
        } else {
            $gasto2 = $prestamo->segundo_gasto;
            $porcentage2 = 0;
        }

        if ($prestamo->porcentaje_tercer_gasto) {
            $porcentage3 = 1;
            $gasto3 = $valorCredito * ($prestamo->tercer_gasto / 100);
        } else {
            $gasto3 = $prestamo->tercer_gasto;
            $porcentage3 = 0;
        }

        $data = [
            'porcentage1' => $porcentage1,
            'porcentage2' => $porcentage2,
            'porcentage3' => $porcentage3,
            'gasto1' => number_format($gasto1, 2, '.', ''),
            'gasto2' => number_format($gasto2, 2, '.', ''),
            'gasto3' => number_format($gasto3, 2, '.', '')
        ];
        return $data;
    }

    public static function reemplazarVariablesPagare($texto, $credito)
    {
        $variables = VariablesDocumentos::all();
        foreach ($variables as $variable) {
            //$valor =BaseController::buscarValorVariablePagare($variable->variable, $credito);
            $textoReemplazo = '';
            switch ($variable->variable) {
                case 'Valor_Credito':
                    $cabecera = CreditFolderHeader::find($credito);
                    $textoReemplazo = $cabecera->valor_solicitado;
                    break;
                case 'Nombre_Cliente':
                    $cabecera = CreditFolderHeader::find($credito);
                    $textoReemplazo = $cabecera->customer_name;
                    break;
                case 'Codigo_Socio':
                    $cabecera = CreditFolderHeader::find($credito);
                    $textoReemplazo = str_pad($cabecera->customer_id, 6, '0', STR_PAD_LEFT);
                    break;
                case 'Numero_Pagare':
                    $cabecera = CreditFolderHeader::find($credito);
                    $textoReemplazo = str_pad($cabecera->id, 6, '0', STR_PAD_LEFT);
                    break;
                case 'Nombre_Caja':
                    $company = Company::find(Auth::user()->company_id);
                    $textoReemplazo = $company->comercial_name;
                    break;
                case 'Ciudad_Caja':
                    $company = Company::find(Auth::user()->company_id);
                    $textoReemplazo =  mb_strtoupper($company->ciudad, 'UTF-8');
                    break;
                case 'Valor_Credito_Letras':
                    $cabecera = CreditFolderHeader::find($credito);
                    $formatter = new NumeroALetras();
                    $textoReemplazo = $formatter->toMoney($cabecera->valor_solicitado, 2, 'DÓLARES', 'CENTAVOS');
                    break;
                case 'Porcentaje_Credito':
                    $cabecera = CreditFolderHeader::find($credito);
                    $prestamo = Prestamos::find($cabecera->tipo_prestamo);
                    $textoReemplazo = (isset($prestamo->interes)) ? $prestamo->interes . ' %' : '_____%';
                    break;
                case 'Cuotas_Credito':
                    $cabecera = CreditFolderHeader::find($credito);
                    $textoReemplazo = str_pad($cabecera->cuotas_pagar, 6, '0', STR_PAD_LEFT);
                    break;
                case 'Firma_Deudor':
                    $cabecera = CreditFolderHeader::find($credito);
                    $customer = Customer::find($cabecera->customer_id);
                    $estadocivil = EstadoCivil::find($customer->estado_civil_id);
                    $textoReemplazo = '<table>' .
                        '<tr>' .
                        '<td>f.) ______________________</td>' .
                        ($customer->conyugue_nombre != null ? '<td>f.) ______________________</td>' : '') .
                        '</tr>' .
                        '<tr>' .
                        '<td>Nombre: ' . $cabecera->customer_name . '</td>' .
                        ($customer->conyugue_nombre != null ? '<td>Nombre: ' . $customer->conyugue_nombre . '</td>' : '') .
                        '</tr>' .
                        '<tr>' .
                        '<td>C.I./R.U.C: ' . $customer->numero_documento . '</td>' .
                        ($customer->conyugue_nombre != null ? '<td>C.I./R.U.C: ' . $customer->conyugue_identificacion . '</td>' : '') .
                        '</tr>' .
                        '<tr>' .
                        '<td>Est. Civil: ' . $estadocivil->nombre . '</td>' .
                        ($customer->conyugue_nombre != null ? '<td>Est. Civil: ' . $estadocivil->nombre . '</td>' : '') .
                        '</tr>' .
                        '<tr>' .
                        '<td>Email: ' . $customer->correo . '</td>' .
                        ($customer->conyugue_nombre != null ? '<td>Email: ' . $customer->correo . '</td>' : '') .
                        '</tr>' .
                        '<tr>' .
                        '<td>Teléfono: ' . $customer->telefono . '</td>' .
                        ($customer->conyugue_nombre != null ? '<td>Teléfono: ' . $customer->telefono_parentesco . '</td>' : '') .
                        '</tr>' .
                        '<tr>' .
                        '<td>Dirección: ' . $customer->direccion . '</td>' .
                        ($customer->conyugue_nombre != null ? '<td>Dirección: ' . $customer->direccion . '</td>' : '') .
                        '</tr>' .
                        '</table>';

                    break;
                case 'Firma_Garante':
                    $cabecera = CreditFolderHeader::find($credito);
                    $garante = Customer::find($cabecera->customer_garante_id);
                    if ($cabecera->customer_garante_id != 0 && !$garante) {
                        $estadocivil = EstadoCivil::find($garante->estado_civil_id);
                        $garante = '<table>' .
                            '<tr>' .
                            '<td>f.) ______________________</td>' .
                            ($garante->conyugue_nombre != null ? '<td>f.) ______________________</td>' : '') .
                            '</tr>' .
                            '<tr>' .
                            '<td>Nombre: ' . $garante->nombres . ' ' . $garante->apellidos . '</td>' .
                            ($garante->conyugue_nombre != null ? '<td>Nombre: ' . $garante->conyugue_nombre . '</td>' : '') .
                            '</tr>' .
                            '<tr>' .
                            '<td>C.I./R.U.C: ' . $garante->numero_documento . '</td>' .
                            ($garante->conyugue_nombre != null ? '<td>C.I./R.U.C: ' . $garante->conyugue_identificacion . '</td>' : '') .
                            '</tr>' .
                            '<tr>' .
                            '<td>Est. Civil: ' . $estadocivil->nombre . '</td>' .
                            ($garante->conyugue_nombre != null ? '<td>Est. Civil: ' . $estadocivil->nombre . '</td>' : '') .
                            '</tr>' .
                            '<tr>' .
                            '<td>Email: ' . $garante->correo . '</td>' .
                            ($garante->conyugue_nombre != null ? '<td>Email: ' . $garante->correo . '</td>' : '') .
                            '</tr>' .
                            '<tr>' .
                            '<td>Teléfono: ' . $garante->telefono . '</td>' .
                            ($garante->conyugue_nombre != null ? '<td>Teléfono: ' . $garante->telefono_parentesco . '</td>' : '') .
                            '</tr>' .
                            '<tr>' .
                            '<td>Dirección: ' . $garante->direccion . '</td>' .
                            ($garante->conyugue_nombre != null ? '<td>Dirección: ' . $garante->direccion . '</td>' : '') .
                            '</tr>' .
                            '</table>';
                    }
                    $garantesTabla = Garantes::where('credit_folder_headers_id', $credito)->get();
                    $garanteTabla = '<table style="width: 100%;">' .
                        '<tbody style="font-size: 11px;">';

                    foreach ($garantesTabla as $garantes) {
                        $customer = Customer::find($garantes->customer_id);
                        $estadocivil = EstadoCivil::find($customer->estado_civil_id);
                        $garanteTabla .= '<tr>' .
                            '<td><b>nombre: _______________________________</b></td>';
                        if ($garantes->customer_conyuge_name != "") {
                            $garanteTabla .= '<td><b> FIRMA CO GARANTE: _______________________________</b></td>';
                        }
                        $garanteTabla .= '</tr>';

                        $garanteTabla .= '<tr>' .
                            '<td><b>Nombre: ' . $garantes->customer_name . '</b></td>';
                        if ($garantes->customer_conyuge_name != "") {
                            $garanteTabla .= '<td><b> Nombre : ' . $garantes->customer_conyuge_name . '</b></td>';
                        }
                        $garanteTabla .= '</tr>';

                        $garanteTabla .= '<tr>' .
                            '<td><b>C.I./R.U.C: ' . $garantes->customer_identificacion . '</b></td>';
                        if ($garantes->customer_conyuge_name != "") {
                            $garanteTabla .= '<td><b> C.I./R.U.C : ' . $garantes->customer_conyuge_identificacion . '</b></td>';
                        }
                        $garanteTabla .= '</tr>';

                        $garanteTabla .= '<tr>' .
                            '<td><b>Est. Civil: ' . $estadocivil->nombre . '</b></td>';
                        if ($garantes->customer_conyuge_name != "") {
                            $garanteTabla .= '<td><b> Est. Civil : ' . $estadocivil->nombre . '</b></td>';
                        }
                        $garanteTabla .= '</tr>';

                        $garanteTabla .= '<tr>' .
                            '<td><b>Est. Email: ' . $customer->correo . '</b></td>';
                        if ($garantes->customer_conyuge_name != "") {
                            $garanteTabla .= '<td><b> Email: ' . $customer->correo . '</b></td>';
                        }
                        $garanteTabla .= '</tr>';

                        $garanteTabla .= '<tr>' .
                            '<td><b>Est. Teléfono: ' . $customer->telefono . '</b></td>';
                        if ($garantes->customer_conyuge_name != "") {
                            $garanteTabla .= '<td><b> Teléfono: ' . $customer->telefono_parentesco . '</b></td>';
                        }
                        $garanteTabla .= '</tr>';

                        $garanteTabla .= '<tr>' .
                            '<td><b>Est. Dirección: ' . $customer->direccion . '</b></td>';
                        if ($garantes->customer_conyuge_name != "") {
                            $garanteTabla .= '<td><b> Dirección: ' . $customer->direccion . '</b></td>';
                        }
                        $garanteTabla .= '</tr>';
                    }

                    $garanteTabla .= '</tbody>' .
                        '</table>';

                    $textoReemplazo = $garante . $garanteTabla;
                    break;
                case 'Fecha_Momento':
                    $cabecera = CreditFolderHeader::find($credito);
                    $date = new DateTime($cabecera->date_created);
                    $dia = $date->format('d');
                    $mes = $date->format('m');
                    $año = $date->format('Y');
                    $meses = [
                        '01' => 'Enero',
                        '02' => 'Febrero',
                        '03' => 'Marzo',
                        '04' => 'Abril',
                        '05' => 'Mayo',
                        '06' => 'Junio',
                        '07' => 'Julio',
                        '08' => 'Agosto',
                        '09' => 'Septiembre',
                        '10' => 'Octubre',
                        '11' => 'Noviembre',
                        '12' => 'Diciembre'
                    ];
                    $fecha_formateada = sprintf('%02d de %s del %d', $dia, $meses[$mes], $año);
                    $textoReemplazo = $fecha_formateada;

                    break;
                case 'Firma_Deudor_Simple':
                    $cabecera = CreditFolderHeader::find($credito);
                    $customer = Customer::find($cabecera->customer_id);
                    $textoReemplazo = '<table>' .
                        '<tr>' .
                        '<td>f.) ______________________</td>' .
                        ($customer->conyugue_nombre != null ? '<td>f.) ______________________</td>' : '') .
                        '</tr>' .
                        '<tr>' .
                        '<td>Nombre: ' . $cabecera->customer_name . '</td>' .
                        ($customer->conyugue_nombre != null ? '<td>Nombre: ' . $cabecera->conyugue_nombre . '</td>' : '') .
                        '</tr>' .
                        '</table>';

                    break;
            }
            $texto = str_replace('*' . $variable->variable . '*', $textoReemplazo, $texto);
        }

        $texto = str_replace('Powered by', '', $texto);
        $texto = str_replace('Froala Editor', '', $texto);
        return $texto;
    }

    public static function generarDescargosCuentasTransaccionales()
    {

        echo "Generando descargos de cuentas transaccionales \n";
        $ahorrosTransaccionales = TipoAhorros::where('cuenta_transaccional', 1)
            ->where('company_id', Auth::user()->company_id)
            ->get();

        foreach ($ahorrosTransaccionales as $tipoAhorro) {
            echo "Genrando reglas para el tipo de ahorro : " . $tipoAhorro->name . "\n";
            BaseController::generarReglastransaccionales($tipoAhorro->id);
            echo "Validando saldo de cuentas transaccionales: " . $tipoAhorro->name . "\n";
            BaseController::validarSaldoCuentasClientes($tipoAhorro->id);
        }
    }

    public static function generarReglastransaccionales($id)
    {
        $reglasAhorro = TransaccionesCuentas::where('tipo_ahorro_id', $id)->get();
        $fechaActual = Carbon::now();
        $mes = $fechaActual->format('m');
        $year = $fechaActual->format('Y');

        foreach ($reglasAhorro as $val) {
            echo "Verificar que la regla con parametros exista " . $val->concepto . "\n";
            $existe = TrasaccionCuentasEjecucion::where('transacciones_cuentas_id', $val->id)
                ->where('month', $mes)
                ->where('year', $year)
                ->first();
            if (!$existe) {
                $regla = new TrasaccionCuentasEjecucion();
                $regla->transacciones_cuentas_id = $val->id;
                $regla->month = $mes;
                $regla->year = $year;

                $regla->fecha_inicial = $year . '-' . $mes . '-' . $val->dia_mes;
                $fechaFinal = Carbon::createFromFormat('Y-m-d', $year . '-' . $mes . '-' . $val->dia_mes);
                $nuevaFechaFinal = $fechaFinal->addDays(5);
                $regla->fecha_final =  $nuevaFechaFinal->format('Y-m-d');

                $regla->valor_debito = $val->valor_recaudacion;
                $regla->valor_multa = $val->valor_multa;

                $regla->date_created = date('Y-m-d');
                $regla->hour_created = date("H:i:s");
                $regla->save();
            }
        }
    }

    public static function validarSaldoCuentasClientes($id)
    {
        echo "Validando cuentas transaccionales del tipo de ahorro: " . $id . "\n";
        $customerTipoAhorro = CustomerTipoAhorros::where('tipo_ahorros_id', $id)
            ->get();
        foreach ($customerTipoAhorro as $customer) {
            echo "-------------- Validando saldo de cuenta transaccional: " . $customer->codigo . "-------------- \n";
            $reglas = BaseController::generarDebitoscuentasTransaccionales($id,  $customer->id);

            //$reglas = BaseController::debitarReglasCuentas($id,  $customer->id);
        }
    }

    public static function generarDebitoscuentasTransaccionales($tipo_ahorro_id, $customer_tipo_ahorro_id)
    {
        $reglasAhorro = TransaccionesCuentas::where('tipo_ahorro_id', $tipo_ahorro_id)->get();
        $bovedas = Bovedas::where('status', 1)->where('principal', 1)->first();
        if ($bovedas) {
            foreach ($reglasAhorro as $val) {
                $reflaejecucion = TrasaccionCuentasEjecucion::where('transacciones_cuentas_id', $val->id)
                    ->where('status', 1)
                    ->get();
                foreach ($reflaejecucion as $ejecucion) {
                    $cuenta =  CustomerTipoAhorros::find($customer_tipo_ahorro_id);
                    $fechaCreacionCuneta = $cuenta->created_at;
                    $soloFecha = $fechaCreacionCuneta->format('Y-m-d');
                    $valorDebito = $ejecucion->valor_debito;
                    if ($ejecucion->fecha_inicial >= $soloFecha) {

                        echo "*******    Verificar saldos de la cuenta    *******\n";
                        $saldo = BaseController::verSaldoTipoAhorro($customer_tipo_ahorro_id);
                        echo "Saldo : " . $saldo . "\n";

                        $sumaMultas = MultasCuentasEjecucion::where('transacciones_cuentas_id', $val->id)
                            ->where('trasaccion_cuentas_ejecucions_id', $ejecucion->id)
                            ->where('customer_tipo_ahorros_id', $customer_tipo_ahorro_id)
                            ->sum('valor_multa');
                        $valorDebitoTotal = $valorDebito + $sumaMultas;
                        $textoMultas = '';


                        if ($saldo >=  $valorDebitoTotal) {
                            $existehistorico = HistoricoTransaccional::where('transacciones_cuentas_id', $val->id)
                                ->where('trasaccion_cuentas_ejecucions_id', $ejecucion->id)
                                ->where('customer_tipo_ahorros_id', $customer_tipo_ahorro_id)
                                ->first();

                            if (!$existehistorico) {
                                $customer = Customer::find($cuenta->customer_id);
                                $operacion = "DCT";
                                $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                                    ->where('name_corto', $operacion)
                                    ->first();
                                $tabla = 'customer_movimientos';
                                $code = BaseController::generarCodigo($tabla, 9);
                                $valorTotal = BaseController::valorTotal();
                                $textoBebito = 'DEBITO AUTOMATICO DE LA PARAMETRIZACION ' .  $val->concepto . ' POR EL VALOR ' . $valorDebito . 'DEL CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos;
                                if ($sumaMultas > 0) {
                                    $textoMultas = ' - ¡EL VALOR DE LAS MULTAS ES: ' . $sumaMultas . '!';
                                }

                                $data = [
                                    "code" => $code,
                                    "comprobante" => $code,
                                    "banco_id" => $val->banco_id,
                                    "numero_deposito" => $code,
                                    "company_id" => Auth::user()->company_id,
                                    "customer_id" => $customer->id,
                                    "customer_code" => $customer->code,
                                    "afecta" => $transaction->afecta,
                                    "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                                    "customer_ruc" => $customer->numero_documento,
                                    "customer_address" => $customer->direccion,
                                    "customer_telefono" => $customer->telefono,
                                    "customer_tipo_ahorro_id" => $customer_tipo_ahorro_id,
                                    "type_transaction_id" => $transaction->id,
                                    "type_transaction_name" => $transaction->name,
                                    "type_transaction_action" => $transaction->action,
                                    "valor_movimiento" =>  $valorDebitoTotal,
                                    "saldo_general" => $valorTotal -  $valorDebitoTotal,
                                    "observation" =>  $textoBebito  . $textoMultas,
                                    "user_created_id" => Auth::user()->id,
                                    "date_created" => date('Y-m-d'),
                                    "hour_created" => date("H:i:s"),

                                ];
                                $movimientos = CustomerMovimiento::create($data);
                                CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id,   $valorDebitoTotal, $customer->code, $movimientos->saldo_general, date('Y-m-d'));
                                BaseController::guardarValoresCartola($customer_tipo_ahorro_id, $customer->id, $movimientos, $transaction);
                                /*GUARDADO DE LOG DEL DEBITO*/

                                $dataHistorico = [
                                    "transacciones_cuentas_id" => $val->id,
                                    "trasaccion_cuentas_ejecucions_id" => $ejecucion->id,
                                    "customer_tipo_ahorros_id" => $customer_tipo_ahorro_id,
                                    "customer_id" => $customer->id,
                                    "type_transaction_id" => $transaction->id,
                                    "valor" =>  $valorDebitoTotal,
                                    "date_created" =>  date('Y-m-d'),
                                    "hour_created" => date("H:i:s"),

                                ];
                                HistoricoTransaccional::create($dataHistorico);
                                /*DESCARGO PARA LA BOVEDA PRINCIPAL*/

                                $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'ICT')->first();
                                $solicitud = new DescargoBovedasHeader();
                                $solicitud->company_id = Auth::user()->company_id;
                                $solicitud->operaciones_descargo_bovedas_id = $operacion->id;
                                $solicitud->boveda_origen_id = $bovedas->id;
                                $solicitud->boveda_destino_id = 0;
                                $solicitud->valor = $valorDebitoTotal;
                                $solicitud->observacion = $textoBebito  . $textoMultas;
                                $solicitud->fecha_creacion = date('Y-m-d');
                                $solicitud->estado = 'FINALIZADO';
                                $solicitud->bancos_id = $val->banco_id;
                                $solicitud->save();
                                continue;
                            }
                        } else {
                            echo "Saldo insuficiente para debitar la regla: " . $val->concepto . "\n";
                            $existehistorico = HistoricoTransaccional::where('transacciones_cuentas_id', $val->id)
                                ->where('trasaccion_cuentas_ejecucions_id', $ejecucion->id)
                                ->where('customer_tipo_ahorros_id', $customer_tipo_ahorro_id)
                                ->first();
                            if (!$existehistorico) {
                                if (date('Y-m-d') >= $ejecucion->fecha_final) {
                                    $customer = Customer::find($cuenta->customer_id);
                                    $dataMulta = [
                                        'transacciones_cuentas_id' => $val->id,
                                        'trasaccion_cuentas_ejecucions_id' => $ejecucion->id,
                                        'customer_tipo_ahorros_id' => $customer_tipo_ahorro_id,
                                        'customer_id' => $customer->id,
                                        'valor_multa' => $ejecucion->valor_debito,
                                        'date_created' => date('Y-m-d'),
                                        'hour_created' => date("H:i:s"),
                                    ];
                                    $existeMulta = MultasCuentasEjecucion::where('transacciones_cuentas_id', $val->id)
                                        ->where('trasaccion_cuentas_ejecucions_id', $ejecucion->id)
                                        ->where('customer_tipo_ahorros_id', $customer_tipo_ahorro_id)
                                        ->where('date_created', date('Y-m-d'))
                                        ->first();
                                    if (!$existeMulta) {
                                        $multa = MultasCuentasEjecucion::create($dataMulta);
                                    }
                                }
                            }

                            continue;
                        }
                    }
                }
            }
        }
    }


    public static function debitarReglasCuentas($tipo_ahorro_id, $customer_tipo_ahorro_id)
    {
        $reglasAhorro = TransaccionesCuentas::where('tipo_ahorro_id', $tipo_ahorro_id)->get();
        foreach ($reglasAhorro as $regla) {
            $dia = Carbon::now()->day;
            if ($dia == $regla->dia_mes) {
                $bovedas = Bovedas::where('status', 1)->where('principal', 1)->first();
                if ($bovedas) {
                    echo "Generando regla de descargo: " . $regla->concepto . "\n";
                    $saldo = BaseController::verSaldoTipoAhorro($customer_tipo_ahorro_id);
                    if ($saldo > 0) {
                        $cuenta =  CustomerTipoAhorros::find($customer_tipo_ahorro_id);
                        $customer = Customer::find($cuenta->customer_id);
                        $operacion = "DCT";
                        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                            ->where('name_corto', $operacion)
                            ->first();
                        $tabla = 'customer_movimientos';
                        $code = BaseController::generarCodigo($tabla, 9);
                        $valorTotal = BaseController::valorTotal();
                        $data = [
                            "code" => $code,
                            "comprobante" => $code,
                            // Nuevos
                            "banco_id" => $regla->banco_id,
                            "numero_deposito" => $code,
                            "company_id" => Auth::user()->company_id,
                            "customer_id" => $customer->id,
                            "customer_code" => $customer->code,
                            "afecta" => $transaction->afecta,
                            "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                            "customer_ruc" => $customer->numero_documento,
                            "customer_address" => $customer->direccion,
                            "customer_telefono" => $customer->telefono,
                            "customer_tipo_ahorro_id" => $customer_tipo_ahorro_id,
                            "type_transaction_id" => $transaction->id,
                            "type_transaction_name" => $transaction->name,
                            "type_transaction_action" => $transaction->action,
                            "valor_movimiento" => $regla->valor_recaudacion,
                            "saldo_general" => $valorTotal + $regla->valor_recaudacion,
                            "observation" => 'DEBITO AUTOMATICO DE LA PARAMETRIZACION ' .  $regla->concepto . ' DEL CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos,
                            "user_created_id" => Auth::user()->id,
                            "date_created" => date('Y-m-d'),
                            "hour_created" => date("H:i:s"),

                        ];
                        $movimientos = CustomerMovimiento::create($data);
                        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id,  $regla->valor_recaudacion, $customer->code, $movimientos->saldo_general, date('Y-m-d'));
                        BaseController::guardarValoresCartola($customer_tipo_ahorro_id, $customer->id, $movimientos, $transaction);

                        /*DESCARGO PARA LA BOVEDA PRINCIPAL*/
                        $dataHistorico = [
                            "transacciones_cuentas_id" => $regla->id,
                            "customer_tipo_ahorros_id" => $customer_tipo_ahorro_id,
                            "customer_id" => $customer->id,
                            "type_transaction_id" => $transaction->id,
                            "valor" => $regla->valor_recaudacion,
                            "date_created" =>  date('Y-m-d'),
                            "hour_created" => date("H:i:s"),

                        ];
                        HistoricoTransaccional::create($dataHistorico);
                        /*DESCARGO PARA LA BOVEDA PRINCIPAL*/
                        $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'ICT')->first();
                        $solicitud = new DescargoBovedasHeader();
                        $solicitud->company_id = Auth::user()->company_id;

                        $solicitud->operaciones_descargo_bovedas_id = $operacion->id;
                        $solicitud->boveda_origen_id = $bovedas->id;
                        $solicitud->boveda_destino_id = 0;
                        $solicitud->valor = $regla->valor_recaudacion;
                        $solicitud->observacion = 'DEBITO AUTOMATICO DE LA PARAMETRIZACION ' .  $regla->concepto . ' DEL CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos;
                        $solicitud->fecha_creacion = date('Y-m-d');
                        $solicitud->estado = 'FINALIZADO';
                        $solicitud->bancos_id = $regla->banco_id;
                        $solicitud->save();
                    } else {
                        echo "Sin Saldo Para procesar \n";
                    }
                }
            } else {
            }
        }
    }
    public static function verSaldoTipoAhorro($id)
    {
        $ingresos = CustomerMovimiento::join('type_transactions', 'customer_movimientos.type_transaction_id', '=', 'type_transactions.id')
            ->whereIn('type_transactions.name_corto', ['IN', 'SC', 'SOL', 'PVP', 'IFN', 'DCD', 'DFJ', 'DNC', 'TRR'])
            ->where('customer_movimientos.customer_tipo_ahorro_id', $id)
            ->where('customer_movimientos.status', true)
            ->sum('customer_movimientos.valor_movimiento');
        $egresos = CustomerMovimiento::join('type_transactions', 'customer_movimientos.type_transaction_id', '=', 'type_transactions.id')
            ->whereIn('type_transactions.name_corto', ['EG', 'DEA', 'CVN', 'DND', 'DCT', 'TRE'])
            ->where('customer_movimientos.customer_tipo_ahorro_id', $id)
            ->where('customer_movimientos.status', true)
            ->sum('customer_movimientos.valor_movimiento');
        $valor = $ingresos - $egresos;
        return number_format(round($valor, 2), 2, '.', '');
    }

    public function buscarValorVariablePagare($variable, $credito) {}
}
