<?php

namespace App\Http\Controllers\Cajas;

use App\DenominacionBilletes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cajas;
use App\Models\CustomerHistorial;
use App\Models\CustomerMovimiento;
use App\Http\Controllers\Base\BaseController;
use App\Models\CajasDenominacion;
use App\Models\Company;
use App\Models\DescargoBovedasHeader;
use App\Models\TypeTransaction;
use App\Models\OtrosIngresos;
use Response;
use Illuminate\Support\Facades\Auth;
use PDF;

class CajasController extends Controller
{

        public function index()
        {
                return view('cajas/index');
        }

        public function cajasComprobanteold($id)
        {
                $caja = Cajas::find($id);
                $usuarioCaja = $caja->user_inicial_id;
                $denominacion = DenominacionBilletes::where('company_id', Auth::user()->company_id)->get()->toArray();
                $denom = DenominacionBilletes::where('company_id', Auth::user()->company_id)->get();
                foreach ($denom as $value) {
                        $cajaDenominacion = CajasDenominacion::where('company_id', Auth::user()->company_id)
                                ->where('caja_id', $id)
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
                        ->where('user_created_id', $usuarioCaja)
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
                        ->where('user_created_id', $usuarioCaja)
                        ->get();

                $tipoRetiro = TypeTransaction::where('company_id', Auth::user()->company_id)
                        ->where('name_corto', 'EG')
                        ->first();
                $tipoRetiroAutomatico = TypeTransaction::where('company_id', Auth::user()->company_id)
                        ->where('name_corto', 'DEA')
                        ->first();
                $data['detalleRetiros'] = CustomerMovimiento::where('date_created', $caja->date_inicial)
                        ->whereIn('type_transaction_id', [$tipoRetiro->id])
                        ->where('user_created_id', $usuarioCaja)
                        ->get();

                $tipoGastos = TypeTransaction::where('company_id', Auth::user()->company_id)
                        ->where('name_corto', 'GAS')
                        ->first();
                $data['detalleGastos'] = CustomerMovimiento::where('date_created', $caja->date_inicial)
                        ->where('type_transaction_id', $tipoGastos->id)
                        ->where('user_created_id', $usuarioCaja)
                        ->get();

                $tipoSumaClientes = TypeTransaction::where('company_id', Auth::user()->company_id)
                        ->where('name_corto', 'SC')
                        ->first();
                $data['detalleSumaClientes'] = CustomerMovimiento::where('date_created', $caja->date_inicial)
                        ->where('type_transaction_id', $tipoSumaClientes->id)
                        ->where('user_created_id', $usuarioCaja)
                        ->get();

                $tipoSumaEmpresa = TypeTransaction::where('company_id', Auth::user()->company_id)
                        ->where('name_corto', 'SE')
                        ->first();
                $data['detalleSumaEmpresa'] = CustomerMovimiento::where('date_created', $caja->date_inicial)
                        ->where('type_transaction_id', $tipoSumaEmpresa->id)
                        ->where('user_created_id', $usuarioCaja)
                        ->get();

                $tipoEntregaCreditos = TypeTransaction::where('company_id', Auth::user()->company_id)
                        ->where('name_corto', 'ENC')
                        ->first();
                $data['detalleEntregaCreditos'] = CustomerMovimiento::where('date_created', $caja->date_inicial)
                        ->where('type_transaction_id', $tipoEntregaCreditos->id)
                        ->where('user_created_id', $usuarioCaja)
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
                return PDF::loadView('cajas.cierre_caja', compact('data'))
                        ->setPaper('A4', "portrait")
                        ->download('cierre-' . $caja->date_finish . '.pdf');
        }
        public function cajasComprobante($id)
        {
                $caja = Cajas::find($id);
                $usuarioCaja = $caja->user_inicial_id;
                $company = Company::find(Auth::user()->company_id);
                $denominacion = DenominacionBilletes::where('company_id', Auth::user()->company_id)->get()->toArray();
                $denom = DenominacionBilletes::where('company_id', Auth::user()->company_id)->get();
                foreach ($denom as $value) {
                        $cajaDenominacion = CajasDenominacion::where('company_id', Auth::user()->company_id)
                                ->where('caja_id', $id)
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
                $data['denominacion'] = $denominacion;
                $data['denom'] = $denom;

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

                $man = file_get_contents(public_path($path));
                $data['imagen'] = base64_encode($man);
                $data['otrosValores'] = OtrosIngresos::where('date_create', $caja->date_inicial)
                        ->where('user_id', $usuarioCaja);
                $tipoDepositos = TypeTransaction::where('company_id', Auth::user()->company_id)
                        ->where('name_corto', 'IN')
                        ->first();
                $data['detalleDepositos'] = CustomerMovimiento::where('date_created', $caja->date_inicial)
                        ->where('type_transaction_id',    $tipoDepositos->id)
                        ->where('user_created_id', $usuarioCaja);

                $tipoRetiro = TypeTransaction::where('company_id', Auth::user()->company_id)
                        ->where('name_corto', 'EG')
                        ->first();
                $data['detalleRetiros'] = CustomerMovimiento::where('date_created', $caja->date_inicial)
                        ->whereIn('type_transaction_id', [$tipoRetiro->id])
                        ->where('user_created_id', $usuarioCaja);

                $tipoEntregaCreditos = TypeTransaction::where('company_id', Auth::user()->company_id)
                        ->where('name_corto', 'ENC')
                        ->first();
                $data['detalleEntregaCreditos'] = CustomerMovimiento::where('date_created', $caja->date_inicial)
                        ->where('type_transaction_id', $tipoEntregaCreditos->id)
                        ->where('user_created_id', $usuarioCaja);

                $tipoSumaClientes = TypeTransaction::where('company_id', Auth::user()->company_id)
                        ->where('name_corto', 'SC')
                        ->first();
                $data['detalleSumaClientes'] = CustomerMovimiento::where('date_created', $caja->date_inicial)
                        ->where('type_transaction_id', $tipoSumaClientes->id)
                        ->where('user_created_id', $usuarioCaja);

                $tipoSumaEmpresa = TypeTransaction::where('company_id', Auth::user()->company_id)
                        ->where('name_corto', 'SE')
                        ->first();
                $data['detalleSumaEmpresa'] = CustomerMovimiento::where('date_created', $caja->date_inicial)
                        ->where('type_transaction_id', $tipoSumaEmpresa->id)
                        ->where('user_created_id', $usuarioCaja);

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
                        ->where('user_created_id', $usuarioCaja);

                $tipoGastosCaja = TypeTransaction::where('company_id', Auth::user()->company_id)
                        ->where('name_corto', 'GAS')
                        ->first();
                $data['detalleGastosCaja'] = CustomerMovimiento::where('date_created', $caja->date_inicial)
                        ->whereIn('type_transaction_id', [$tipoGastosCaja->id])
                        ->where('user_created_id', $usuarioCaja);

                $data['depositoBoveda']  = DescargoBovedasHeader::join('bancos', 'bancos.id', '=', 'descargo_bovedas_header.bancos_id')
                        ->where('descargo_bovedas_header.cajas_id', $caja->id)
                        ->where('descargo_bovedas_header.company_id', Auth::user()->company_id)
                        ->where('descargo_bovedas_header.bancos_id', '!=', null)
                        ->where('descargo_bovedas_header.boveda_destino_id', '!=', null)
                        ->select(
                                'descargo_bovedas_header.*',
                                'bancos.nombre as nombreBanco',
                                'bancos.numero_cuenta as cuentaBanco'

                        );

                return PDF::loadView('cajas.cierre_caja', compact('data'))
                        ->setPaper('A4', "landscape")
                        ->download('cierre-' . $caja->date_finish . '.pdf');
        }

        //Viejas funciones
        public function reportCajas($inicio, $fin)
        {
                $cajas = Cajas::where('company_id', Auth::user()->company_id)
                        ->where('date_inicial', '>=', $inicio)
                        ->where('date_inicial', '<=', $fin)
                        ->get();
                return Response::json($cajas);
        }

        public function verMovimientos($id)
        {
                $caja = Cajas::find($id);
                $movimientos = CustomerHistorial::where('company_id', Auth::user()->company_id)
                        ->where('date_created', $caja->date_created)
                        ->get();
                return Response::json($movimientos);
        }

        public static function abrirCajaAutomatica()
        {
                $cajasActual = Cajas::where('company_id', Auth::user()->company_id)
                        ->where('date_inicial', date('Y-m-d'));
                if ($cajasActual->count() == 0) {
                        $tabla = 'cajas';
                        $code = BaseController::generarCodigo($tabla, 9);
                        $data = [
                                'company_id' => Auth::user()->company_id,
                                'code' => $code,
                                'valor_inicial' => $code,
                                'date_inicial' => date('Y-m-d'),
                                'hour_inicial' => date('H:i:s'),
                                'user_inicial_id' => Auth::user()->id,
                                'user_name_inicial' => Auth::user()->username,
                        ];
                        Cajas::create($data);
                }
        }

        public static function calcularCajaAutomatica()
        {
                $cajasActual = Cajas::where('company_id', Auth::user()->company_id)
                        ->where('date_inicial', date('Y-m-d'))
                        ->first();
                if ($cajasActual == null) {
                        CajasController::abrirCajaAutomatica();
                        $cajasActual = Cajas::where('company_id', Auth::user()->company_id)
                                ->where('date_inicial', date('Y-m-d'))
                                ->first();
                }
                $totalMovimientos = CustomerHistorial::where('company_id', Auth::user()->company_id)
                        ->where('date_created', date('Y-m-d'))
                        ->count();
                $historialSuma = CustomerHistorial::where('company_id', Auth::user()->company_id)
                        ->where('date_created', date('Y-m-d'))
                        ->where('status', true)
                        ->where('type_transaction_action', 'S')->sum('valor_movimiento');
                $historialResta = CustomerHistorial::where('company_id', Auth::user()->company_id)
                        ->where('date_created', date('Y-m-d'))
                        ->where('status', true)
                        ->where('type_transaction_action', 'R')->sum('valor_movimiento');
                $total = $historialSuma - $historialResta;
                $cajasActual->numero_movimientos = $totalMovimientos;
                $cajasActual->valor_ingreso_clientes = $historialSuma;
                $cajasActual->valor_egreso_clientes = $historialResta;
                $cajasActual->total = $total;
                $cajasActual->save();
        }
}
