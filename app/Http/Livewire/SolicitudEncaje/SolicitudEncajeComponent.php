<?php

namespace App\Http\Livewire\SolicitudEncaje;

use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerHistorial;
use App\Models\CustomerTipoAhorros;
use App\Models\EntregaEncajes;
use App\Models\TipoAhorros;
use App\Models\TipoAhorrosProgramadosDetalle;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SolicitudEncajeComponent extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['debitoAutomatico'];

    public $fechaInicio;
    public $fechaFin;
    public $search = '';
    public $tipo_reporte = '';
    public $valor = 0;
    public $porcentajeRetenido = 0;
    public $valorRetenido = 0;
    public $valorEntregar = 0;
    public $fechaRetiroEncaje = '';
    public $horaRetiroEncaje = '';
    public $cuentaEncajeID = '';
    public $cuentaEncajeCode = '';
    public $estadoFiltro = '';

    public $porcentaje_plazo_fijo = 0;
    public $valor_plazo_fijo = 0;
    public $taza_plazo_fijo = '';
    public $pago_plazo_fijo = '';
    public $dias_plazo_fijo = '';
    public $beneficiario_plazo_fijo = '';
    public $interesPlazoFijo = [];
    public $activarManualNovacion = false;
    public $usarManualNovacion = '';




    public function __construct()
    {
        $this->fechaInicio = date('Y-01-01');
        $this->fechaFin = date('Y-m-d');
    }

    public function generarSolicitudEncaje($cuenta, $codigo,  $valor, $porcentajeEncaje)
    {

        $this->porcentajeRetenido = 0;
        $this->fechaRetiroEncaje = date('Y-m-d');
        $this->horaRetiroEncaje = date('H:i:s');
        $this->valor = 0;
        $this->valorRetenido = 0;
        $this->valorEntregar = 0;

        $this->porcentaje_plazo_fijo = 0;
        $this->valor_plazo_fijo = 0;
        $this->taza_plazo_fijo = '';
        $this->pago_plazo_fijo = '';
        $this->dias_plazo_fijo = '';
        $this->beneficiario_plazo_fijo = '';
         $this->usarManualNovacion =false;


        $this->cuentaEncajeID = $cuenta;
        $this->cuentaEncajeCode = $codigo;

        if ($valor == 0) {
            $color = 'danger';
            $mensaje = 'No se puede realizar la solicitud debe tener saldo para solicitar';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('closeModal');
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }

        $this->porcentajeRetenido = $porcentajeEncaje;
        $this->fechaRetiroEncaje = date('Y-m-d');
        $this->horaRetiroEncaje = date('H:i:s');
        $this->valor = number_format($valor, 2, '.', '');
        $this->valorRetenido = number_format($valor * ($porcentajeEncaje / 100), 2, '.', '');
       

        $existeSolicitud = EntregaEncajes::where('customer_tipo_ahorro_id', $this->cuentaEncajeID)
            ->where('status', 'PENDIENTE');
        if ($existeSolicitud->count() > 0) {
            $existeSolicitudPrimera = EntregaEncajes::where('customer_tipo_ahorro_id', $this->cuentaEncajeID)
                ->where('status', 'PENDIENTE')
                ->first();
            $this->porcentaje_plazo_fijo = $existeSolicitudPrimera->porcentaje_plazo_fijo;
            $this->valor_plazo_fijo = $existeSolicitudPrimera->valor_plazo_fijo;
            $this->taza_plazo_fijo = $existeSolicitudPrimera->taza_plazo_fijo;
            $this->pago_plazo_fijo = $existeSolicitudPrimera->pago_plazo_fijo;
            $this->dias_plazo_fijo = $existeSolicitudPrimera->dias_plazo_fijo;
            $this->beneficiario_plazo_fijo = $existeSolicitudPrimera->beneficiario_plazo_fijo;
            if( $this->dias_plazo_fijo > 0){
                $this->activarManualNovacion =true;
                $this->usarManualNovacion =true;
            }else{
                $this->activarManualNovacion =false;
                $this->usarManualNovacion =false;

            }
        }
         $this->valorEntregar = number_format($this->valor - $this->valorRetenido -$this->valor_plazo_fijo, 2, '.', '');
    }

    public function calcularRetencion($tipo)
    {
        if ($tipo == 1) {
            $this->valorRetenido = number_format($this->valor * ($this->porcentajeRetenido / 100), 2, '.', '');
            //$this->valorEntregar = number_format($this->valor - $this->valorRetenido, 2, '.', '');
        } else {
            $this->porcentajeRetenido = number_format((($this->valorRetenido * 100) / $this->valor), 2, '.', '');
            //$this->valorEntregar = number_format($this->valor - $this->valorRetenido, 2, '.', '');
        }
        $valorEntregar = number_format($this->valor - $this->valorRetenido, 2, '.', '');
     
        $porcentajePlazoFijo = number_format($valorEntregar * ($this->porcentaje_plazo_fijo / 100), 2, '.', '');
        $this->valor_plazo_fijo = number_format($porcentajePlazoFijo, 2, '.', '');
        $this->valorEntregar = number_format($this->valor - $this->valorRetenido - $porcentajePlazoFijo, 2, '.', '');
    }

    public function calcularPlazo()
    {
        $valorEntregar = number_format($this->valor - $this->valorRetenido, 2, '.', '');
        $porcentajePlazoFijo = number_format($valorEntregar * ($this->porcentaje_plazo_fijo / 100), 2, '.', '');
        $this->valor_plazo_fijo = number_format($porcentajePlazoFijo, 2, '.', '');
        $this->valorEntregar = number_format($this->valor - $this->valorRetenido - $porcentajePlazoFijo, 2, '.', '');
    }

    public function calcularRetencionValor()
    {
        $this->porcentajeRetenido = number_format((($this->valorRetenido * 100) / $this->valor), 2, '.', '');
        $this->valorEntregar = number_format($this->valor - $this->valorRetenido, 2, '.', '');
    }

    public function entregarEncaje()
    {

        $this->validate(
            [
                'valor' => 'required',
                'porcentajeRetenido' => 'required',
                'valorRetenido' => 'required',
                'valorEntregar' => 'required',
                'taza_plazo_fijo' => ($this->porcentaje_plazo_fijo > 0) ? 'required' : '',
                'pago_plazo_fijo' => ($this->porcentaje_plazo_fijo > 0) ? 'required' : '',
            ],
            [
                'valor.required' => 'Necesita ingresar un valor.',
                'porcentajeRetenido.required' => 'Necesita seleccionar un porcentaje.',
                'valorRetenido.required' => 'Necesita ingresar un valor.',
                'valorEntregar.required' => 'Necesita ingresar un valor.',
                'taza_plazo_fijo.required' => 'Se necesita seleccionar una taza.',
                'pago_plazo_fijo.required' => 'Se necesita seleccionar un medio de pago.',
            ]
        );



        $encajes = EntregaEncajes::where('customer_tipo_ahorro_id',  $this->cuentaEncajeID)
            ->where('status', 'PENDIENTE')
            ->count();
        $cuenta =  CustomerTipoAhorros::find($this->cuentaEncajeID);
        $customer = Customer::find($cuenta->customer_id);
        if ($encajes == 0) {
            if ($this->porcentaje_plazo_fijo > 0) {
                $data = [
                    'customer_id' => $cuenta->customer_id,
                    'customer_tipo_ahorro_id' => $this->cuentaEncajeID,
                    'valor' => $this->valor,
                    'porcentaje_retenido' => $this->porcentajeRetenido,
                    'valor_retenido' => $this->valorRetenido,
                    'valor_entregado' => $this->valorEntregar,
                    'date_created' => date('Y-m-d'),
                    'hour_created' => date('H:i:s'),
                    'user_created_id' => Auth::user()->id,
                    'user_created_name' => Auth::user()->username,

                    'porcentaje_plazo_fijo' => $this->porcentaje_plazo_fijo,
                    'valor_plazo_fijo' => $this->valor_plazo_fijo,
                    'taza_plazo_fijo' => $this->taza_plazo_fijo,
                    'pago_plazo_fijo' => $this->pago_plazo_fijo,
                    'dias_plazo_fijo' => $this->dias_plazo_fijo,
                    'beneficiario_plazo_fijo' => $this->beneficiario_plazo_fijo,
                ];
            } else {

                $data = [
                    'customer_id' => $cuenta->customer_id,
                    'customer_tipo_ahorro_id' => $this->cuentaEncajeID,
                    'valor' => $this->valor,
                    'porcentaje_retenido' => $this->porcentajeRetenido,
                    'valor_retenido' => $this->valorRetenido,
                    'valor_entregado' => $this->valorEntregar,
                    'date_created' => date('Y-m-d'),
                    'hour_created' => date('H:i:s'),
                    'user_created_id' => Auth::user()->id,
                    'user_created_name' => Auth::user()->username
                ];
            }
            EntregaEncajes::create($data);
            $this->dispatchBrowserEvent('closeModal');
        } else {
            $color = 'danger';
            $mensaje = 'No se puede realizar el ingreso, tiene solicitudes pendientes';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('closeModal');
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }
    }
    public function negarSolicitud($id)
    {
        $solicitud = EntregaEncajes::find($id);
        $solicitud->status = 'CANCELADO';
        $solicitud->user_cancel_id = Auth::user()->id;
        $solicitud->user_cancel_name = Auth::user()->username;
        $solicitud->date_cancel = date('Y-m-d');
        $solicitud->hour_cancel = date('H:i:s');
        $solicitud->save();
    }
    public function imprimirEntregaEncaje($id)
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
        $data['company'] = $company;
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);
        $data['encaje'] = EntregaEncajes::whereIn('customer_id', Customer::where('company_id', Auth::user()->company_id)->select('id'))->findOrFail($id);
        $data['customer'] = Customer::where('company_id', Auth::user()->company_id)->findOrFail($data['encaje']->customer_id);

        // Fecha original
        $fechaOriginal = $data['encaje']->date_created;

        // Crear una instancia de Carbon con la fecha original
        $fecha = Carbon::createFromFormat('Y-m-d', $fechaOriginal);
        // Mantener la fecha real del comprobante.
        $data['fechaFormateada'] = $fecha->locale('es')->translatedFormat('d \d\e F \d\e\l Y');

        $pdf = PDF::loadView('reportes.generarPdfEncajeRetenido', $data);

        // Devuelve el PDF como descarga
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Encaje Retenido.pdf');
    }
    public function toggleUsarManualNovacion()
    {
        if ($this->usarManualNovacion) {
            $this->dias_plazo_fijo = 30;
            $this->activarManualNovacion = true;
        } else {
            $this->dias_plazo_fijo = '';
            $this->activarManualNovacion = false;
        }
    }
    public function mount()
    {

        $tipoAhorro = TipoAhorros::where('company_id', Auth::user()->company_id)->where('programado', true)->first();
        if ($tipoAhorro) {
            $this->interesPlazoFijo = TipoAhorrosProgramadosDetalle::where('tipo_ahorros_id', $tipoAhorro->id)->get();
        }
    }

    public function onDiasManualChangeNovacion()
    {

        if ($this->dias_plazo_fijo < 30) {
            $color = 'danger';
            $mensaje = 'El valor no puede ser inferior a 30 días, se colocara automáticamente el valor mínimo.';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            $this->dias_plazo_fijo = 30;
            return;
        }
    }
    public function render()
    {
        $this->estadoFiltro = '';
        if ($this->tipo_reporte == 2) {
            $this->estadoFiltro = 'PENDIENTE';
        } else if ($this->tipo_reporte == 3) {
            $this->estadoFiltro = 'ENTREGADO';
        } else if ($this->tipo_reporte == 4) {
            $this->estadoFiltro = 'CANCELADO';
        }
        $solicitudes = EntregaEncajes::select(
            'entrega_encajes.*',
            DB::raw("CONCAT(customer.nombres, ' ', customer.apellidos) as nombre_completo"),
            'customer.numero_documento',
            'customer.id as numeroSocio',
            'customer_tipo_ahorros.codigo as codigoCuenta',
        )
            ->join('customer', 'entrega_encajes.customer_id', '=', 'customer.id')
            ->leftjoin('customer_tipo_ahorros', 'entrega_encajes.customer_tipo_ahorro_id', '=', 'customer_tipo_ahorros.id')
            ->where('entrega_encajes.status',  $this->estadoFiltro)
            ->where(function ($query) {
                $query->where('customer.nombres', 'like', '%' . $this->search . '%')
                    ->orWhere('customer.apellidos', 'like', '%' . $this->search . '%')
                    ->orWhere('customer.numero_documento', 'like', '%' . $this->search . '%');
            })
            ->when($this->estadoFiltro == 'PENDIENTE', function ($query) {
                $query->whereBetween('entrega_encajes.date_created', [$this->fechaInicio, $this->fechaFin]);
            })
            ->when($this->estadoFiltro == 'ENTREGADO', function ($query) {
                $query->whereBetween('entrega_encajes.date_entrega', [$this->fechaInicio, $this->fechaFin]);
            })
            ->when($this->estadoFiltro == 'CANCELADO', function ($query) {
                $query->whereBetween('entrega_encajes.date_cancel', [$this->fechaInicio, $this->fechaFin]);
            })
            ->paginate(50);


        $cuentas =  CustomerTipoAhorros::select(
            'customer_tipo_ahorros.*',
            DB::raw("CONCAT(customer.nombres, ' ', customer.apellidos) as nombre_completo"),
            'customer.numero_documento',
            'customer.id as numeroSocio',
            'tipo_ahorros.porcentaje_encaje as porcentajeEncaje'
        )
            ->join('customer', 'customer_tipo_ahorros.customer_id', '=', 'customer.id')
            ->leftjoin('tipo_ahorros', 'customer_tipo_ahorros.tipo_ahorros_id', '=', 'tipo_ahorros.id')
            ->where(function ($query) {
                $query->where('customer.nombres', 'like', '%' . $this->search . '%')
                    ->orWhere('customer.apellidos', 'like', '%' . $this->search . '%')
                    ->orWhere('customer.numero_documento', 'like', '%' . $this->search . '%');
            })
            ->where('tipo_ahorros.cuenta_encaje', true)
            ->paginate(50);
        foreach ($cuentas as $key => $cuent) {
            $ingresos = CustomerHistorial::join('type_transactions', 'customer_historials.type_transaction_id', '=', 'type_transactions.id')
                ->whereIn('type_transactions.name_corto', ['IN', 'SC', 'SOL', 'PVP', 'IFN', 'DCD', 'DFJ', 'DNC'])
                ->where('customer_historials.customer_tipo_ahorro_id', $cuent->id)
                ->where('customer_historials.status', true)
                ->sum('customer_historials.valor_movimiento');
            $egresos = CustomerHistorial::join('type_transactions', 'customer_historials.type_transaction_id', '=', 'type_transactions.id')
                ->whereIn('type_transactions.name_corto', ['EG', 'DEA', 'CVN', 'DND'])
                ->where('customer_historials.customer_tipo_ahorro_id', $cuent->id)
                ->where('customer_historials.status', true)
                ->sum('customer_historials.valor_movimiento');
            $valor = $ingresos - $egresos;
            $cuent->saldo = number_format(round($valor, 2), 2, '.', '');
            $cuent->cantSolicitudes =  EntregaEncajes::where('customer_tipo_ahorro_id', $cuent->id)->where('status', 'PENDIENTE')->count();
        }
        $clientesTodos = Customer::all();
        return view('livewire.solicitud-encaje.solicitud-encaje-component', compact('cuentas', 'solicitudes', 'clientesTodos'));
    }
}
