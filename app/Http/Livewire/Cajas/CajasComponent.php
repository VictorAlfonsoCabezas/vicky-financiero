<?php

namespace App\Http\Livewire\Cajas;

use App\DenominacionBilletes;
use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Http\Controllers\DescargoBovedasHeader\DescargoBovedasHeaderController;
use App\Http\Controllers\Bovedas\BovedasController;
use App\Models\Bancos;
use App\Models\OtrosIngresos;
use App\Models\Cajas;
use App\Models\DescargoBovedasHeader;
use App\Models\OperacionesDescargoBovedas;
use App\Models\Bovedas;
use App\Models\Company;
use App\Models\CustomerHistorial;
use App\Models\CustomerMovimiento;
use App\Models\CajasDenominacion;
use App\Models\TypeTransaction;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MovimientosCaja\MovimientosExport;
use App\Models\FormasPago;
use App\Models\RegistroFormasLiquidacion;


class CajasComponent extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $fechaInicio;
    public $fechaFin;
    public $status = 'ALL';
    public $fechaCaja;
    public $valorInicial = 0;
    public $observacion = '';
    public $caja_id = 0;
    public $fechaCajaCierre;
    public $valorInicialCierre = 0;
    public $observacionCierre = '';
    public $total = 0;
    public $totalManual = 0;
    public $signo = '';
    public $denominaciones = [];
    public $fechaAnterior = '';
    public $saldoAnterior = 0;
    public $dineroAnterior = 0;
    public $totalAnterior = 0;
    public $saldoGeneral = 0;
    public $primeraCaja = 0;
    public $calculoFormula = 0;
    public $descuadre = 0;
    public $saldoSistema = 0;
    public $boveda = '';
    public $bovedaAprobadas = [];
    public $bovedaSolicitar = '';
    public $valorInicialSolicitar = '';
    public $observacionSolicitar = '';
    public $idCajaSolicitar = '';
    public $bovedadPendientesApro = [];
    public $cajasAbiertas = false;
    public $mensajeErrorCrearCajas = '';
    public $detalleDepositos = [];
    public $detalleRecaudaciones = [];
    public $detalleRetiros = [];
    public $detalleGastos = [];
    public $detalleGastosProveedores = [];
    public $detalleOtrosValores = [];
    public $bancoEnvioMatriz = '';
    public $valorEnvioMatriz = '';
    public $observacionEnvioMatriz = '';
    public $cajaEnviarMatriz = '';
    public $bancoSolicita = '';


    public function __construct()
    {
        $fechaInicio = date('Y-m-d');
        $fechaRestada = date('Y-m-d', strtotime('-1 weeks', strtotime($fechaInicio)));
        $this->fechaInicio = $fechaRestada;
        $this->fechaFin = date('Y-m-d');
        $this->fechaCaja = date('Y-m-d');
    }

    public function mount()
    {
        $denominaciones = DenominacionBilletes::select(
            'id',
            'nombre',
            'valor',
            DB::raw('0 as cantidad'),
            DB::raw('0 as total')
        )
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->toArray();
        $this->denominaciones = $denominaciones;
    }

    public function cambiarValor($id, $nuevoValor)
    {
        $nuevoValor = intval($nuevoValor);
        $cajaDenominacion = CajasDenominacion::where('company_id', Auth::user()->company_id)
            ->where('caja_id', $this->caja_id)
            ->where('denominacion_billetes_id', $id);
        if ($cajaDenominacion->count() == 0) {
            $deno = new CajasDenominacion();
            $deno->company_id = Auth::user()->company_id;
            $deno->caja_id = $this->caja_id;
            $deno->denominacion_billetes_id = $id;
            $deno->cantidad = $nuevoValor;
            $total = DenominacionBilletes::find($id);
            $deno->total = $total->valor * $nuevoValor;
            $deno->fecha_creacion = date('Y-m-d');
            $deno->save();
        } else {
            $deno = $cajaDenominacion->first();
            $deno->cantidad = $nuevoValor;
            $total = DenominacionBilletes::find($id);
            $deno->total = $total->valor * $nuevoValor;
            $deno->save();
        }

        $sumatoria = 0;
        foreach ($this->denominaciones as &$deno) {
            if ($deno['id'] == $id) {
                $deno['cantidad'] = $nuevoValor;
                $deno['total'] = $deno['cantidad'] * $deno['valor'];
            }
            $sumatoria += $deno['cantidad'] * $deno['valor'];
        }

        $this->totalManual = number_format($sumatoria, 2, '.', '');
        $this->cambioValorManual();
    }

    public function storeCaja()
    {
        $this->validate([
            "fechaCaja" => [
                "required",
                "date",
                Rule::unique('cajas', 'date_inicial')->where(function ($query) {
                    return $query->where('company_id', Auth::user()->company_id)
                        ->where('user_inicial_id', Auth::user()->id);
                }),
            ],
            "valorInicial" => "required|numeric|min:0",
            'boveda' => 'required',
        ]);
        $saldoBoveda = BovedasController::saldoBovedas($this->boveda);
        $numeroSinComa = str_replace(',', '', $saldoBoveda);
        if ($this->valorInicial > $numeroSinComa) {
            $this->cajasAbiertas = true;
            $this->mensajeErrorCrearCajas = 'Su boveda tiene un maximo de paertura de ' . $saldoBoveda . ' $';
            return;
        }

        if ($this->caja_id > 0) {
            $caja = Cajas::find($this->caja_id);
        } else {
            $abiertas = Cajas::where('user_inicial_id', Auth::user()->id)->where('status', 'ABIERTA')->count();
            if ($abiertas >= 1) {
                $this->cajasAbiertas = true;
                $this->mensajeErrorCrearCajas = 'Primero debes cerrar las cajas que tengas abiertas.';
                return;
            }

            $caja = new Cajas();
            $tabla = 'cajas';
            $code = BaseController::generarCodigo($tabla, 9);
            $caja->company_id = Auth::user()->company_id;
            $caja->code = $code;
        }
        $caja->valor_inicial = $this->valorInicial;
        $caja->date_inicial = $this->fechaCaja;
        $caja->hour_inicial = date('H:i:s');
        $caja->user_inicial_id = Auth::user()->id;
        $caja->user_name_inicial = Auth::user()->username;
        $caja->save();
        $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'CAJINI')->first();
        DescargoBovedasHeaderController::agregarDescargoBovedasHeader(null, $caja->id, $operacion->id, $this->boveda, null, $this->valorInicial, $operacion->descripcion, 'FINALIZADO');
        $this->dispatchBrowserEvent('closeModal');
        $this->render();
    }

    public function solicitarValoresBodega($id)
    {
        $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'CAJINI')->first();
        $solicitud = new DescargoBovedasHeader();
        $solicitud->company_id = Auth::user()->company_id;
        $solicitud->cajas_id = $id;
        $solicitud->operaciones_descargo_bovedas_id = $operacion->id;
        $solicitud->boveda_origen_id = $this->boveda;
        $solicitud->boveda_destino_id = 0;
        $solicitud->valor = $this->valorInicial;
        $solicitud->observacion = $operacion->descripcion;
        $solicitud->fecha_creacion = $this->fechaCaja;
        $solicitud->estado = 'PENDIENTE';
        $solicitud->save();
    }

    public function aprobarValor($id)
    {
        $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'CAJINI')->first();
        $solicitud = DescargoBovedasHeader::where('cajas_id', $id)->where('operaciones_descargo_bovedas_id', $operacion->id)->first();
        $solicitud->estado = 'APROBADO';
        $solicitud->save();
    }

    public function borrarCaja($id)
    {
        $solicitud = DescargoBovedasHeader::where('cajas_id', $id)->where('estado', 'APROBADO')->count();
        if ($solicitud == 0) {
            $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'CAJINI')->first();
            $solicitud = DescargoBovedasHeader::where('cajas_id', $id)->where('operaciones_descargo_bovedas_id', $operacion->id)->first();
            $solicitud->estado = 'RECHAZADA';
            $solicitud->save();
            Cajas::find($id)->delete();
            $this->render();
        } else {
            $color = 'danger';
            $mensaje = 'No se puede elminar la caja tiene valores aprobados de bodega';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
        }
    }

    public function cerrarCaja($id)
    {
        $this->detalleDepositos = [];
        $this->detalleRecaudaciones = [];
        $this->detalleRetiros = [];
        $this->detalleGastos = [];
        $this->detalleGastosProveedores = [];
        $this->detalleOtrosValores = [];
        $this->caja_id = $id;
        $caja = Cajas::find($id);
        if ($caja->user_inicial_id  != Auth::user()->id) {
            $color = 'danger';
            $mensaje = 'Usted no podra ver los valores por que no es el usuario que aperturo la caja.';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }
        $this->fechaCajaCierre = $caja->date_inicial;
        $this->valorInicialCierre = $caja->valor_inicial;
        $this->observacionCierre = 'Cierre de la caja';

        $valorCuentaCaja = BaseController::valorTotalMovimientosUsuario($caja->date_inicial);
        //$valorCuentaSaldo = BaseController::saldoGeneral($caja->date_inicial);
        $valorCuentaSaldo = BaseController::saldoGeneralCaja($caja->date_inicial, $caja->user_inicial_id, $caja->id);
        $valorBoveda = BaseController::valoresCajasBoveda($id);
        //$this->saldoGeneral = $valorCuentaCaja - $valorCuentaSaldo;
        //$this->saldoGeneral = $valorCuentaCaja + $valorBoveda;        
        $this->saldoGeneral = $valorCuentaSaldo;

        //$this->saldoSistema = $valorBoveda + $valorCuentaSaldo;
        $this->saldoSistema = $this->saldoGeneral;
        $usuarioBusquedaCaja = $caja->user_inicial_id;

        $this->descuadre = $this->saldoSistema - $this->calculoFormula;
        $tipoDepositos = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'IN')
            ->first();
        $this->detalleDepositos = CustomerMovimiento::where('date_created', $caja->date_inicial)
            ->where('type_transaction_id', $tipoDepositos->id)
            ->where('user_created_id', $usuarioBusquedaCaja)
            ->get();
        foreach ($this->detalleDepositos as $deposito) {
            $forma = FormasPago::find($deposito->forma_pago_id);
            $deposito->forma_pago = $forma ? $forma->nombre : '';
        }
        $tipoRetiro = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'EG')
            ->first();
        $tipoRetiroAutomatico = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'DEA')
            ->first();
        $this->detalleRetiros = CustomerMovimiento::where('date_created', $caja->date_inicial)
            ->whereIn('type_transaction_id', [$tipoRetiro->id, $tipoRetiroAutomatico->id])
            ->where('user_created_id', $usuarioBusquedaCaja)
            ->get();
        foreach ($this->detalleRetiros as $deposito) {
            $forma = FormasPago::find($deposito->forma_pago_id);
            $deposito->forma_pago = $forma ? $forma->nombre : '';
        }


        $tipoRecaudacion = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'PC')
            ->first();
        $tipoRecaudacionAutomatica = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'PCA')
            ->first();
        $tipoRecaudacionliquidacion = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'LIC')
            ->first();
        $this->detalleRecaudaciones = CustomerMovimiento::where('date_created', $caja->date_inicial)
            ->whereIn('type_transaction_id', [$tipoRecaudacion->id, $tipoRecaudacionAutomatica->id, $tipoRecaudacionliquidacion->id])
            ->where('user_created_id', $usuarioBusquedaCaja)
            ->get();

        /*foreach ($this->detalleRecaudaciones as $deposito) {
            $forma = FormasPago::find($deposito->forma_pago_id);
            $deposito->forma_pago = $forma ? $forma->nombre : '';
        }*/

        foreach ($this->detalleRecaudaciones as $deposito) {

            $formasLiquidacion = RegistroFormasLiquidacion::where(
                'customer_movimientos_id',
                $deposito->id
            )
                ->where('status', 1)
                ->get();

            if ($formasLiquidacion->isNotEmpty()) {

                $deposito->forma_pago = $formasLiquidacion
                    ->pluck('forma_pago')
                    ->filter()
                    ->unique()
                    ->implode(' + ');
            } else {

                $forma = FormasPago::find($deposito->forma_pago_id);
                $deposito->forma_pago = $forma ? $forma->nombre : '';
            }
        }


        $tipoGastos = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'GAS')
            ->first();
        $this->detalleGastos = CustomerMovimiento::where('date_created', $caja->date_inicial)
            ->where('type_transaction_id', $tipoGastos->id)
            ->where('user_created_id', $usuarioBusquedaCaja)
            ->get();

        foreach ($this->detalleGastos as $deposito) {
            $forma = FormasPago::find($deposito->forma_pago_id);
            $deposito->forma_pago = $forma ? $forma->nombre : '';
        }
        $tipoGastosProveedores = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'PPR')
            ->first();
        $this->detalleGastosProveedores = CustomerMovimiento::where('date_created', $caja->date_inicial)
            ->where('type_transaction_id', $tipoGastosProveedores->id)
            ->where('user_created_id', $usuarioBusquedaCaja)
            ->get();

        foreach ($this->detalleGastosProveedores as $deposito) {
            $forma = FormasPago::find($deposito->forma_pago_id);
            $deposito->forma_pago = $forma ? $forma->nombre : '';
        }


        $tipoOtrosValores = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'IOV')
            ->first();
        $tipoOtrosValoresEmpresa = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'SE')
            ->first();
        $tipoOtrosValoresCliente = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'SC')
            ->first();
        $this->detalleOtrosValores = CustomerMovimiento::where('date_created', $caja->date_inicial)
            ->whereIn('type_transaction_id', [$tipoOtrosValores->id, $tipoOtrosValoresEmpresa->id, $tipoOtrosValoresCliente->id])
            ->where('user_created_id', $usuarioBusquedaCaja)
            ->get();
        foreach ($this->detalleOtrosValores as $otros) {
            $razon = OtrosIngresos::where('movimiento_id', $otros->id)->first();
            $otros->razon = '';
            if ($razon) {

                $otros->razon = strtoupper($razon->name);
            }

            $forma = FormasPago::find($otros->forma_pago_id);
            $otros->forma_pago = $forma ? $forma->nombre : '';
        }


        $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'CAJCIE')->first();
        $apertura = OperacionesDescargoBovedas::where('nombre_corto', 'CAJINI')->first();
        $solicitud = DescargoBovedasHeader::where('cajas_id', $id)
            ->where('estado', 'FINALIZADO')
            ->where('operaciones_descargo_bovedas_id',  $apertura->id)
            ->whereNotIn('operaciones_descargo_bovedas_id', [$operacion->id])
            ->get();

        foreach ($solicitud as $valSol) {
            $bovedas = Bovedas::find($valSol->boveda_origen_id)->first();
            $valSol->nombreBoveda = $bovedas->nombre;
        }
        $this->bovedaAprobadas = $solicitud;
    }

    public function abrirModal($id)
    {
        $this->cajasAbiertas = false;
        $this->caja_id = $id;
        if ($id != 0) {
            $caja = Cajas::find($id);
            $this->fechaCaja = $caja->date_inicial;
            $this->valorInicial = $caja->valor_inicial;
            $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'CAJINI')->first();
            $solicitud = DescargoBovedasHeader::where('cajas_id', $id)->where('operaciones_descargo_bovedas_id', $operacion->id)->first();
            $this->boveda = $solicitud->boveda_origen_id;
        } else {
            $this->fechaCaja = date('Y-m-d');
            $this->valorInicial = 0;
            $this->boveda = '';
        }
    }

    public function solicitarValoresBoveda($id)
    {
        $this->cajasAbiertas = false;
        $this->bovedaSolicitar = '';
        $this->valorInicialSolicitar = '';
        $this->observacionSolicitar = '';
        $this->idCajaSolicitar = $id;
        $this->bancoSolicita = '';
    }

    public function storesolicitudValores()
    {
        $this->validate(
            [
                'bovedaSolicitar' => 'required',
                'valorInicialSolicitar' => 'required',
            ],
            [
                'bovedaSolicitar.required' => 'Debe seleccionar la bobeda que se usará.',
                'valorInicialSolicitar.required' => 'Debe ingresar un valor.',
            ]
        );
        $saldoBoveda = BovedasController::saldoBovedas($this->bovedaSolicitar);
        $numeroSinComa = str_replace(',', '', $saldoBoveda);
        if ($this->valorInicialSolicitar > $numeroSinComa) {
            $this->cajasAbiertas = true;
            $this->mensajeErrorCrearCajas = 'Su boveda tiene un maximo de paertura de ' . $saldoBoveda . ' $';
            return;
        }
        $caja = Cajas::find($this->idCajaSolicitar);
        $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'CAJDESC')->first();

        $bancoSeleccionado = ($this->bancoSolicita > 0) ? $this->bancoSolicita : null;
        DescargoBovedasHeaderController::agregarDescargoBovedasHeader(
            $bancoSeleccionado,
            $caja->id,
            $operacion->id,
            $this->bovedaSolicitar,
            null,
            $this->valorInicialSolicitar,
            $operacion->descripcion,
            'FINALIZADO'
        );

        $this->dispatchBrowserEvent('closeModal');
        $color = 'success';
        $mensaje = 'Se a solicitado el valor $' . $this->valorInicialSolicitar . ' Espere a que sea aprobado';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function cambioFecha()
    {
        $this->valorInicial = $this->saldoUltimaCaja($this->fechaCaja);
    }

    public static function saldoUltimaCaja($fecha)
    {
        $valor = 0;
        $cajaAnterior = Cajas::where('company_id', Auth::user()->company_id)
            ->where('status', 'CERRADA')
            ->where('date_inicial', '<', $fecha)
            ->latest()
            ->first();
        if ($cajaAnterior) {
            $valor = $cajaAnterior->total_final;
        }
        return $valor;
    }

    public function cambioValorManual()
    {
        $totalManualFloat = floatval($this->totalManual);
        $total = floatval($this->total);
        if ($total == $totalManualFloat) {
            $this->signo = '';
        } else if ($total < $totalManualFloat) {
            $this->signo = 'mayor';
        } else {
            $this->signo = 'menor';
        }
        $this->calculoFormula = $this->totalManual;
        $calculo = $this->saldoSistema - $this->calculoFormula;
        $this->descuadre = number_format($calculo, 2);
    }

    public function storeCierreCaja()
    {
        $caja = Cajas::find($this->caja_id);
        //Guardar Gasto si hay descuadre

        $saldoGeneral = BaseController::valorTotalMovimientosUsuario($caja->date_inicial);
        $valorBoveda = BaseController::valoresCajasBoveda($this->caja_id);
        //$caja->saldo_cuenta = $valorBoveda + $saldoGeneral - $this->totalManual;
        $caja->saldo_cuenta = $this->totalManual;
        $caja->total = $this->totalManual;
        $caja->total_final = $this->totalManual;
        $caja->date_finish = date('Y-m-d');
        $caja->hour_finish = date('H:i:s');
        $caja->user_finish_id = Auth::user()->id;
        $caja->user_finish_name = Auth::user()->username;
        $caja->descuadre = $this->descuadre;
        $caja->observacion_cierre = $this->observacionCierre;

        $caja->status = 'CERRADA';
        $caja->save();
        $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'CAJCIE')->first();
        $bovedaOrigen = DescargoBovedasHeader::where('cajas_id', $this->caja_id)->where('estado', 'FINALIZADO')->first();
        DescargoBovedasHeaderController::agregarDescargoBovedasHeader(
            null,
            $caja->id,
            $operacion->id,
            $bovedaOrigen->boveda_origen_id,
            null,
            $this->totalManual,
            $operacion->descripcion,
            'FINALIZADO'
        );
        $this->dispatchBrowserEvent('closeModal2');
    }

    public function guardarGastoDescuadre($caja, $descuadre, $observacion, $fecha, $hora)
    {
        $company = Company::find(Auth::user()->company_id);
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'GAS')
            ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
        $data = [
            "code" => $code,
            "company_id" => $company->id,
            "customer_code" => $company->id,
            "afecta" => $transaction->afecta,
            "customer_name" => $company->company_name,
            "customer_ruc" => $company->ruc,
            "customer_address" => $company->address,
            "customer_telefono" => $company->phone,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $descuadre,
            "saldo_general" => $valorTotal - $descuadre,
            "observation" => 'DESCUADRE DE CAJA - ' . Auth::user()->username . ' - ' . $observacion,
            "user_created_id" => Auth::user()->id,
            "date_created" => $fecha,
            "hour_created" => $hora,
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $descuadre, $company->id, $movimientos->saldo_general, $fecha);
        $cajas = Cajas::find($caja);
        $cajas->customer_movimiento_id = $movimientos->id;
        $cajas->save();
    }

    public function reversarCaja($id)
    {
        $caja = Cajas::find($id);
        $caja->total = null;
        $caja->total_final = null;
        $caja->date_finish = null;
        $caja->hour_finish = null;
        $caja->user_finish_id = null;
        $caja->user_finish_name = null;
        $caja->descuadre = null;
        $caja->status = 'ABIERTA';
        $caja->save();

        $tipoCierreBoveda = OperacionesDescargoBovedas::where('nombre_corto', 'CAJCIE')->first();
        $descargoBoveda = DescargoBovedasHeader::where('cajas_id', $id)
            ->where('operaciones_descargo_bovedas_id', $tipoCierreBoveda->id)
            ->first();
        if ($descargoBoveda  != null) {
            $descargoDelete = DescargoBovedasHeader::find($descargoBoveda->id)->delete();
        }
    }

    public function verValoresPendienteAprovacion($id)
    {
        $solicitud = DescargoBovedasHeader::where('cajas_id', $id)->where('estado', 'PENDIENTE')->get();
        $this->bovedadPendientesApro = $solicitud;
    }

    public function aprobarSoliVal($id)
    {
        $solicitud = DescargoBovedasHeader::find($id);
        $solicitud->estado = 'APROBADO';
        $solicitud->save();
        $this->verValoresPendienteAprovacion($id);
    }

    public function rechazarSoliVal($id)
    {
        $solicitud = DescargoBovedasHeader::find($id);
        $solicitud->estado = 'RECHAZADA';
        $solicitud->save();
        $this->verValoresPendienteAprovacion($id);
    }

    public function obtenerValor($codigo)
    {
        $valor = '';
        $customerMovimiento =  CustomerMovimiento::where('code', $codigo)->first();
        if ($customerMovimiento) {
            $valor = $customerMovimiento->customer_name;
        }
        return $valor;
    }

    public function enviarBoveda($id)
    {
        $this->bancoEnvioMatriz = '';
        $this->valorEnvioMatriz = '';
        $this->observacionEnvioMatriz = '';
        $this->cajaEnviarMatriz = $id;

        $caja = Cajas::find($id);
        $valorCuentaSaldo = BaseController::saldoGeneralCaja($caja->date_inicial, $caja->user_inicial_id);
    }

    public function storeEnvioBoveda()
    {
        $this->validate(
            [
                'bancoEnvioMatriz' => 'required',
                'valorEnvioMatriz' => 'required',
                'observacionEnvioMatriz' => 'required',
            ],
            [
                'bancoEnvioMatriz.required' => 'Debe seleccionar un banco.',
                'valorEnvioMatriz.required' => 'Debe ingresar un valor.',
                'observacionEnvioMatriz.required' => 'Debe ingresar una observación.',
            ]
        );
        $caja = Cajas::find($this->cajaEnviarMatriz);
        $valorCuentaSaldo = BaseController::saldoGeneralCaja($caja->date_inicial, $caja->user_inicial_id,  $caja->id);
        if ($this->valorEnvioMatriz > $valorCuentaSaldo) {
            $color = 'danger';
            $mensaje = 'No puede hacer la transferencia de este valor por que no tiene saldo suficiente.';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }
        $bovedaMatriz =  Bovedas::where('company_id', Auth::user()->company_id)->where('principal', 1)->first();
        if (! $bovedaMatriz) {
            $color = 'danger';
            $mensaje = 'No se puede hacer la transferencia por que no hay una boveda matriz.';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }

        $transferencia = OperacionesDescargoBovedas::where('company_id', Auth::user()->company_id)->where('nombre_corto', 'TRANRECI')->firstOrFail();
        $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'CAJINI')->first();
        $movimientoInicialBoveda = DescargoBovedasHeader::where('cajas_id', $this->cajaEnviarMatriz)->where('operaciones_descargo_bovedas_id', $operacion->id)->first();

        DescargoBovedasHeaderController::agregarDescargoBovedasHeader(
            $this->bancoEnvioMatriz,
            $caja->id,
            $transferencia->id,
            $movimientoInicialBoveda->boveda_origen_id,
            null,
            $this->valorEnvioMatriz,
            strtoupper($this->observacionEnvioMatriz),
            'FINALIZADO'
        );

        $transferencia = OperacionesDescargoBovedas::where('company_id', Auth::user()->company_id)->where('nombre_corto', 'TRANENV')->firstOrFail();
        DescargoBovedasHeaderController::agregarDescargoBovedasHeader(
            $this->bancoEnvioMatriz,
            $caja->id,
            $transferencia->id,
            $movimientoInicialBoveda->boveda_origen_id,
            $bovedaMatriz->id,
            $this->valorEnvioMatriz,
            strtoupper($this->observacionEnvioMatriz),
            'FINALIZADO'
        );
        $this->dispatchBrowserEvent('closeModal');
    }
    public function exportarMovimeintos($id)
    {
        return Excel::download(new MovimientosExport($id), 'movimientos.xlsx');
    }
    public function render()
    {
        $company = Company::find(Auth::user()->company_id);
        $cajaAbierta = Cajas::where('status', 'ABIERTA')->where('date_inicial', date('Y-m-d'))->count();
        $bovedas = Bovedas::where('status', 1)->where('boveda', 1)->get();
        $cajas = Cajas::where('company_id', Auth::user()->company_id)
            //                ->where('user_inicial_id', Auth::user()->id)
            ->when($this->status !== 'ALL', function ($query) {
                return $query->where('status', $this->status);
            })
            ->whereBetween('date_inicial', [$this->fechaInicio, $this->fechaFin])
            ->orderByDesc('id')
            ->paginate(10);
        $detalles = [];
        $detalleTransaccion = [];
        if ($this->caja_id > 0) {
            $caja = Cajas::find($this->caja_id);
            $historial = CustomerHistorial::select('type_transaction_id')
                ->where('company_id', Auth::user()->company_id)
                ->where('customer_movimiento_code', '!=', null)
                ->where('customer_movimiento_code', '!=', '')
                ->where('date_created', $caja->date_inicial)
                ->where('status', true)
                ->groupBy('type_transaction_id')
                ->get();
            foreach ($historial as $key => $value) {
                $detalleTransaccion = CustomerHistorial::select('customer_movimiento_code', 'type_transaction_id', 'valor_movimiento', 'date_created', 'hour_created', 'type_transaction_name')
                    ->where('company_id', Auth::user()->company_id)
                    ->where('type_transaction_id', $value['type_transaction_id'])
                    ->where('customer_movimiento_code', '!=', null)
                    ->where('customer_movimiento_code', '!=', '')
                    ->where('date_created', $caja->date_inicial)
                    ->where('status', true)
                    ->get();

                $totalTransacction = CustomerHistorial::where('company_id', Auth::user()->company_id)
                    ->where('type_transaction_id', $value['type_transaction_id'])
                    ->where('customer_movimiento_code', '!=', null)
                    ->where('customer_movimiento_code', '!=', '')
                    ->where('date_created', $caja->date_inicial)
                    ->where('status', true)
                    ->sum('valor_movimiento');
                $tipoTransacction = TypeTransaction::find($value['type_transaction_id']);
                if ($tipoTransacction) {
                    $detalles[] = [
                        'tipo' => $tipoTransacction->id,
                        'tipo_name' => $tipoTransacction->name,
                        'tipo_description' => $tipoTransacction->description,
                        'total_transacction' => $totalTransacction,
                        'action' => $tipoTransacction->action,
                        'detalleTransaccion' => $detalleTransaccion
                    ];
                }
            }
            $this->total = BaseController::saldoGeneral($caja->date_inicial);
        }
        $bancos = Bancos::where('company_id', Auth::user()->company_id)->where('numero_cuenta', '!=', '')->where('status', true)->get();
        return view('livewire.cajas.cajas-component', compact('cajas', 'detalles', 'cajaAbierta', 'bovedas', 'company', 'bancos'));
    }
}
