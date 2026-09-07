<?php

namespace App\Http\Livewire\Utilidades;

use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Models\AsientosDetalle;
use App\Models\AsientosHeader;
use App\Models\Company;
use App\Models\ConfigPlanDetalle;
use App\Models\ConfigPlanHeader;
use App\Models\CreditFolderDetail;
use App\Models\Customer;
use App\Models\CustomerMovimiento;
use App\Models\CustomerMovimientoSolicitud;
use App\Models\CustomerTipoAhorros;
use App\Models\PlanCuentas;
use App\Models\TipoAhorros;
use App\Models\TypeTransaction;
use App\Models\Utilidad;
use App\Models\WhaEnvios;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;

class UtilidadesComponent extends Component
{
    // Propiedades del Modelo Utilidad
    public $numero_socios;
    public $porcentaje = 0.00;
    public $pago_automatico = false;
    public $fecha_pago_automatico;
    public $minDate;
    public $tipo_ahorros_id;
    public $cuenta_pasivo_id;
    /** @var \Illuminate\Support\Collection $tiposAhorrosOpciones */
    public $tiposAhorrosOpciones = [];
    /** @var \Illuminate\Support\Collection|\App\Models\ConfigPlanDetalle[] $cuentasPasivoOpciones */
    public $cuentasPasivoOpciones = [];

    // Propiedades de Cálculo
    public $anio_calculo;
    public $valor_total_utilidad = 0.00;
    public $valor_por_socio = 0.00;

    // PROPIEDADES AÑADIDAS PARA GESTIÓN MASIVA
    public $movimientosUtilidadesPendientes;
    protected $movimientosAgrupadosPorLote;

    // Propiedades para rechazo masivo (usadas en el modal)
    public $ids_movimientos_a_rechazar; // String de IDs separados por coma (ej: '1,2,3')
    public $razon_rechazo_masivo = '';
    public $totalSolicitudesRechazo = 0;

    private int $typeTransactionDepositoId;
    private $transactionEUS; // Almacena el objeto TypeTransaction 'EUS'
    public $config_modificada = false;


    public function mount()
    {
        $this->anio_calculo = Carbon::now()->year - 1;
        $this->numero_socios = $this->getNumeroSocios();
        $tomorrow = Carbon::tomorrow()->toDateString();
        $this->minDate = $tomorrow;
        $this->cargarTiposAhorrosOpciones();
        $this->cuentasPasivoOpciones = $this->obtenerCuentasActivoPasivo(2, false);
        $this->cargarConfiguracionUtilidad();
        $this->calcularUtilidades();
        $this->transactionEUS = $this->transactionEUS();
    }

    private function cargarTiposAhorrosOpciones()
    {
        $this->tiposAhorrosOpciones = TipoAhorros::where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('name', 'AHORROS A LA VISTA')
                    ->orWhere('cuenta_certificado', 1);
            })
            ->select('id', 'name')
            ->get();
        if ($this->tiposAhorrosOpciones->isNotEmpty()) {
            $this->tipo_ahorros_id = $this->tiposAhorrosOpciones->first()->id;
        } else {
            $this->tipo_ahorros_id = null;
        }
    }

    // Método principal para el cálculo del valor total
    private function getUtilidadTotal()
    {
        $totalUtilidad = 0;
        // Definir el rango de fechas para el año
        $fechaInicio = Carbon::create($this->anio_calculo, 1, 1)->startOfDay();
        $fechaFin = Carbon::create($this->anio_calculo, 12, 31)->endOfDay();
        $companyId = Auth::user()->company_id;
        $company = Company::find(Auth::user()->company_id);
        $gasto_cobranza = 0.00;
        if ($company && $company->genera_gastos_cobranza == 1) {
            $gasto_cobranza = (float) $company->valor_notificado;
        }

        // Obtener cuentas de PlanCuentas marcadas para utilidad
        $cuentasUtilidadJerarquia = PlanCuentas::where('company_id', Auth::user()->company_id)
            ->where('utilidad', 1) // CAMPO CLAVE: Cuentas marcadas para el cálculo
            ->select('nivel1', 'nivel2', 'nivel3')
            ->distinct()
            ->get();
        // ->pluck('id'); // Obtengo los ids de la cuentas por ejemplo para 4.4.02.20 el id es 424

        // dd("Cuentas Utilidad Ids: ", $cuentasUtilidadJerarquia);

        $codigosPadresBuscados = $cuentasUtilidadJerarquia->map(function ($jerarquia) {
            return $jerarquia->nivel1 . '.' . $jerarquia->nivel2 . '.' . $jerarquia->nivel3;
        })->unique()->filter()->toArray();

        $idsPadresEncontrados = PlanCuentas::where('company_id', Auth::user()->company_id)
            // Buscamos los IDs cuya columna 'codigo' sea igual a los códigos padre que construimos.
            ->whereIn('codigo', $codigosPadresBuscados)
            ->pluck('id');

        // 2. Obtener el ID de la transacción 'PC' (Pago Crédito)
        // PC es un valor quemado pero aqui es donde deberia ir los valores
        // que queremos hacer la consulta
        /*
        $typeTransactionIdPC = DB::table('type_transactions')
            ->where('name_corto', 'PC')
            ->value('id'); //3

        if (!$typeTransactionIdPC) {
            Log::error("Error: No se encontró el ID de la transacción 'PC' (Pago Crédito).");
            return 0.00;
        }

        $rawExpression = sprintf(
            'cfd.interes_periodo + cfd.interes_mora + (cfd.notificado * %f)',
            $gasto_cobranza
        );
        $totalUtilidad = DB::table('customer_movimientos as cm')
            ->join('credit_folder_details as cfd', 'cfd.id', '=', 'cm.credit_folder_details_id')
            ->where('cm.company_id', $companyId)
            ->where('cm.type_transaction_id', $typeTransactionIdPC)
            ->whereBetween('cm.created_at', [$fechaInicio, $fechaFin])
            ->where('cfd.status', 'PAGADA')
            ->sum(DB::raw($rawExpression));
        */
        $interesMora = CreditFolderDetail::where('status', 'PAGADA')
            ->whereBetween('date_pay', [$fechaInicio, $fechaFin])
            ->sum('interes_mora');
        $interesNormal = CreditFolderDetail::where('status', 'PAGADA')
            ->whereBetween('date_pay', [$fechaInicio, $fechaFin])
            ->sum('interes_periodo');



        $interesMora   = (float) str_replace(',', '', $interesMora);
        $interesNormal = (float) str_replace(',', '', $interesNormal);

        $totalUtilidad = $interesMora + $interesNormal;
        return number_format($totalUtilidad, 2, '.', '');
    }

    public function calcularUtilidades()
    {
        // Calcular valor utilidades por Socio
        $this->numero_socios = $this->getNumeroSocios();
        $this->valor_total_utilidad = $this->getUtilidadTotal();
        if ($this->numero_socios > 0) {
            $utilidad_a_repartir = $this->valor_total_utilidad * ($this->porcentaje / 100);
            $this->valor_por_socio = $utilidad_a_repartir / $this->numero_socios;
        } else {
            $this->valor_por_socio = 0.00;
        }
    }

    private function getNumeroSocios()
    {
        $numero_socios = Customer::where('company_id', Auth::user()->company_id)
            ->where('fundador', 1)
            ->whereNotNull('date_open_account')
            ->count();
        return max(0, $numero_socios);
    }

    // Se dispara cuando se modifica cualquier propiedad en el front-end
    public function updated($propertyName)
    {
        // Recalcular si se cambia porcentaje
        if (in_array($propertyName, ['porcentaje'])) {
            $this->porcentaje = (float)$this->porcentaje;
            if ($this->porcentaje > 100) $this->porcentaje = 100;
            if ($this->porcentaje < 0) $this->porcentaje = 0;
            $this->calcularUtilidades();
        }
        if (in_array($propertyName, ['porcentaje', 'pago_automatico', 'fecha_pago_automatico', 'tipo_ahorros_id', 'cuenta_pasivo_id'])) {
            $this->config_modificada = $this->_checkConfigChanged();
        }
    }

    public function guardarConfiguracion()
    {
        $this->numero_socios = $this->getNumeroSocios();
        $this->porcentaje = str_replace(',', '.', $this->porcentaje);
        $tomorrowDate = Carbon::tomorrow()->toDateString();
        $this->validate([
            'porcentaje' => 'required|numeric|min:0|max:100',
            //'fecha_pago_automatico' => 'required|date|after_or_equal:' . $tomorrowDate,
            'fecha_pago_automatico' => 'required',
            'tipo_ahorros_id' => 'required|exists:tipo_ahorros,id',
            'cuenta_pasivo_id' => 'required|exists:plan_cuentas,id',
        ]);
        Utilidad::updateOrCreate(
            ['company_id' => Auth::user()->company_id],
            [
                'numero_socios' => $this->numero_socios,
                'porcentaje' => $this->porcentaje,
                'pago_automatico' => $this->pago_automatico,
                'fecha_pago_automatico' => $this->fecha_pago_automatico,
                'tipo_ahorros_id' => $this->tipo_ahorros_id,
                'cuenta_pasivo_id' => $this->cuenta_pasivo_id,
            ]
        );
        $this->config_modificada = false;
        session()->flash('message', 'Configuración de utilidad guardada exitosamente.');
    }

    // --- LÓGICA DE GENERACIÓN DE SOLICITUDES DE UTILIDAD ---
    private function cargarConfiguracionUtilidad()
    {
        $ultimaUtilidad = Utilidad::where('company_id', Auth::user()->company_id)
            ->latest()
            ->first();
        $defaultDate = Carbon::create($this->anio_calculo, 12, 31)->toDateString();
        if ($ultimaUtilidad) {
            $this->porcentaje = $ultimaUtilidad->porcentaje;
            $this->pago_automatico = $ultimaUtilidad->pago_automatico;
            $this->tipo_ahorros_id = $ultimaUtilidad->tipo_ahorros_id;
            $this->fecha_pago_automatico = Carbon::parse($ultimaUtilidad->fecha_pago_automatico)->toDateString();
            $this->cuenta_pasivo_id = $ultimaUtilidad->cuenta_pasivo_id;
            Log::info('Configuración de Utilidad cargada desde DB.', [
                'porcentaje' => $this->porcentaje,
                'pago_automatico' => $this->pago_automatico,
                'fecha_pago_automatico' => $this->fecha_pago_automatico
            ]);
        } else {
            $this->fecha_pago_automatico = $defaultDate;
            Log::info('No se encontró configuración previa, usando valores por defecto. Fecha Pago: ' . $defaultDate);
        }
    }

    private function _getCurrentConfigValues(): array
    {
        $porcentaje = (float)str_replace(',', '.', $this->porcentaje);

        $fecha_pago = $this->fecha_pago_automatico ?? Carbon::create($this->anio_calculo, 12, 31)->toDateString();
        return [
            'porcentaje' => round($porcentaje, 2),
            'pago_automatico' => (bool)$this->pago_automatico,
            'fecha_pago_automatico' => $fecha_pago,
            'tipo_ahorros_id' => intval($this->tipo_ahorros_id),
            'cuenta_pasivo_id' => $this->cuenta_pasivo_id,
        ];
    }

    private function _checkConfigChanged(): bool
    {
        $ultimaUtilidad = Utilidad::where('company_id', Auth::user()->company_id)->latest()->first();

        if (!$ultimaUtilidad) {
            return !empty($this->porcentaje) ||
                !empty($this->fecha_pago_automatico) ||
                !empty($this->tipo_ahorros_id) ||
                !empty($this->cuenta_pasivo_id);
        }

        $dbValues = [
            'porcentaje' => round((float)$ultimaUtilidad->porcentaje, 2),
            'pago_automatico' => (bool)$ultimaUtilidad->pago_automatico,
            'fecha_pago_automatico' => Carbon::parse($ultimaUtilidad->fecha_pago_automatico)->toDateString(),
            'tipo_ahorros_id' => intval($ultimaUtilidad->tipo_ahorros_id),
            'cuenta_pasivo_id' => $ultimaUtilidad->cuenta_pasivo_id,
        ];

        $currentValues = $this->_getCurrentConfigValues();

        return $dbValues !== $currentValues;
    }

    private function _procesarPagoAutomatico()
    {
        $companyId = Auth::user()->company_id;
        $transaction = TypeTransaction::where('company_id', $companyId)
            ->where('name_corto', 'EUS') // EUS: Entrega de Utilidades a Socios
            ->first();
        if (!$transaction) {
            session()->flash('error', "Advertencia: El tipo de transacción con name_corto EUS no se encontró. No se pueden generar solicitudes.");
            return;
        }
        $this->typeTransactionDepositoId = $transaction->id;
        // Recalcular para asegurar que el valor y socios estén actualizados
        $this->calcularUtilidades();
        $montoPorSocio = number_format($this->valor_por_socio, 2, '.', '');

        if ($montoPorSocio <= 0) {
            session()->flash('warning', 'El valor a repartir por socio es cero o negativo (Monto: $' . number_format($montoPorSocio, 2) . '). No se pueden generar solicitudes.');
            return;
        }
        try {
            // Obtener el Tipo de Ahorro destino (AP CAPITAL o AHORROS A LA VISTA)
            $tipoAhorroId = $this->tipo_ahorros_id;
            $socios = Customer::where('company_id', $companyId)
                ->where('fundador', 1)
                ->whereNotNull('date_open_account')
                ->get();
            if ($socios->isEmpty()) {
                session()->flash('warning', 'No se encontraron socios fundadores para generar solicitudes.');
                return;
            }
            $totalSolicitudes = 0;
            DB::beginTransaction();
            // CREACIÓN DE SOLICITUDES INDIVIDUALES PARA CADA SOCIO
            foreach ($socios as $socio) {
                // Encontrar o crear la cuenta de ahorro del socio
                $customerTipoAhorro = CustomerTipoAhorros::firstOrCreate(
                    [
                        'company_id' => $companyId,
                        'customer_id' => $socio->id,
                        'tipo_ahorros_id' => $tipoAhorroId,
                    ],
                    [
                        'codigo' => BaseController::generarCodigo('customer_tipo_ahorros', 9),
                        'valor' => 0,
                        'cumplimiento' => 0,
                        'status' => 1,
                    ]
                );

                $fechaInicio = Carbon::parse($socio->date_open_account);
                $fechaFin    = Carbon::create($this->anio_calculo, 12, 31)->endOfDay();

                $numeroMeses = $fechaInicio->diffInMonths($fechaFin);
                $porcentajePago = 0;
                if($numeroMeses >= 12){
                    $valorPagar = $montoPorSocio;
                }else{
                    $porcentajePago=($numeroMeses*100) / 11;
                    $valorPagar = ($porcentajePago*$montoPorSocio) / 100;
                }
                
                // Crear el registro de CustomerMovimientoSolicitud
                CustomerMovimientoSolicitud::create([
                    'company_id' => $companyId,
                    'customer_id' => $socio->id,
                    'customer_tipo_ahorro_id' => $customerTipoAhorro->id,
                    // Se dejan nulos los campos bancarios, ya que es un abono interno
                    'banco_id' => null,
                    'comprobante' => null,
                    'numero_deposito' => null,
                    'type_transaction_id' => $transaction->id,
                    'forma_pago_id' => null,
                    'user_id' => Auth::user()->id,
                    'fecha_creacion' => now(),
                    'valor' => $valorPagar,
                    'observacion' => 'SOLICITUD DE ABONO DE UTILIDAD AÑO ' . $this->anio_calculo . ' (Reparto Automático)' . ' CLIENTE ' . $socio->nombres . ' ' . $socio->apellidos,
                    'estado' => 'PENDIENTE',
                ]);
                $totalSolicitudes++;
            }
            DB::commit();
            session()->flash('message', '¡Se han generado exitosamente ' . $totalSolicitudes . ' solicitudes de abono de utilidad para los socios! Estas solicitudes están pendientes de aprobación.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al generar las solicitudes de utilidad: ' . $e->getMessage());
            Log::error('Error en Generación de Solicitudes de Utilidades: ' . $e->getMessage());
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => 'Error en Pago Automatico',
                'color' => 'danger',
                'mensaje' => $e->getMessage()
            ]);
            return;
        }
    }


    public function ejecutarPagoAutomaticoForzado()
    {
        $this->_procesarPagoAutomatico();
    }

    public function ejecutarPagoAutomatico($cron = false)
    {
        if ($cron) {
            $this->cargarConfiguracionUtilidad();
        } else {
            if ($this->_checkConfigChanged()) {
                $this->dispatchBrowserEvent('openConfigWarningModal');
                return;
            }
        }
        $this->_procesarPagoAutomatico();
    }

    public function transactionEUS()
    {
        $this->transactionEUS = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'EUS')
            ->first();
        return $this->transactionEUS;
    }

    public function obtenerCuentasActivoPasivo($concepto, $debe_haber)
    {
        $cuentas =  array();
        $transactionEUS = TypeTransaction::where('name_corto', 'EUS')->first();
        if ($transactionEUS) {
            $header = ConfigPlanHeader::where('type_transaction_id', $transactionEUS->id)
            ->where('concepto_id', $concepto)
            ->first();
            //dd($transactionEUS,$concepto, $debe_haber,  $header);
            if ($header) {
                $cuentas = ConfigPlanDetalle::where('config_plan_header_id', $header->id)
                    ->where('debe_haber', $debe_haber) // Filtrar por HABER
                    ->with('planCuentas')
                    ->get();
            }
        }
        return $cuentas;
    }

    // LÓGICA DE APROBACIÓN/RECHAZO MASIVO
    public function seleccionarRechazoMasivo(string $ids_movimientos_a_rechazar)
    {
        $this->ids_movimientos_a_rechazar = $ids_movimientos_a_rechazar;
        $this->razon_rechazo_masivo = ''; // Limpiar la razón anterior
        $this->totalSolicitudesRechazo = count(explode(',', $ids_movimientos_a_rechazar));
    }

    public function aceptarMasivo($loteId)
    {
        $companyId = Auth::user()->company_id;
        $company = Company::find($companyId);
        if (!$company->contabilidad) {
            $titulo = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Contabilidad no esta Activa</b>';
            $mensaje = "Advertencia:Contabilidad no esta Activa para esta empresa. Asegúrese de activar la contabilidad.";
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => $titulo,
                'color' => 'danger',
                'mensaje' => $mensaje
            ]);
            return;
        }
        if (!$company->puedeContabilizar(date('Y-m-d'))) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Fecha contable no valida</b>',
                'color' => 'danger',
                'mensaje' => 'Configure la fecha de inicio contable antes de aprobar utilidades.'
            ]);
            return;
        }
        $this->transactionEUS = $this->transactionEUS();
        try {
            DB::beginTransaction();
            $ids = explode(',', $loteId);
            $solicitudes = CustomerMovimientoSolicitud::whereIn('id', $ids)
                ->where('estado', 'PENDIENTE')
                ->where('company_id', $companyId)
                ->get();
            if ($solicitudes->isEmpty()) {
                DB::rollBack();
                session()->flash('error', 'No hay solicitudes pendientes en este lote para aprobar.');
                return;
            }
            $aprobadasCount = 0;
            $transactionEUS = $this->transactionEUS;
            $asientoDescription = 'Pago automático de Utilidades (Reparto Total) Año ' . $this->anio_calculo;
            $configHeader = ConfigPlanHeader::where('company_id', $companyId)
                ->where('type_transaction_id', $transactionEUS->id)
                ->where('status', 1)
                ->where('concepto_id', 2)
                ->first();
            $configHeaderGeneral = ConfigPlanHeader::where('company_id', $companyId)
                ->where('type_transaction_id', $transactionEUS->id)
                ->where('status', 1)
                ->where('concepto_id', 1)
                ->first();

            if (!$configHeader || !$configHeaderGeneral) {

                //alerta de color rojo
                /*
                $color = 'danger';
                $mensaje = "Advertencia: No se encontró la configuración contable (ConfigPlanHeader) para la transacción 'EUS'. Asegúrese de que existe y está activa.";
                $data = [
                    'titulo' => 'Notificación',
                    'color' => $color,
                    'mensaje' => $mensaje
                ];
                $this->dispatchBrowserEvent('alerta', $data);
                return;*/

                //segunda alerta pop up grande
                $titulo = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>No se encontró la configuración contable</b>';
                $mensaje = "Advertencia: No se encontró la configuración contable (ConfigPlanHeader) para la transacción 'EUS'. Asegúrese de que existe y está activa.";
                $this->dispatchBrowserEvent('alertaGrande', [
                    'titulo' => $titulo,
                    'color' => 'danger',
                    'mensaje' => $mensaje
                ]);
                return;
                /*
                throw new \Exception("Advertencia: No se encontró la configuración contable (ConfigPlanHeader) para la transacción 'EUS'. Asegúrese de que existe y está activa.");
                */
            }
            $detalleDEBE = ConfigPlanDetalle::where('config_plan_header_id', $configHeader->id)
                ->where('debe_haber', true)
                ->where('status', 1)
                ->first();
            $detalleHABER = ConfigPlanDetalle::where('config_plan_header_id', $configHeader->id)
                ->where('debe_haber', false)
                ->where('plan_cuentas_id', $this->cuenta_pasivo_id)
                ->where('status', 1)
                ->first();
            $detalleDEBEGeneral = ConfigPlanDetalle::where('config_plan_header_id', $configHeaderGeneral->id)
                ->where('debe_haber', true)
                ->where('status', 1)
                ->first();
            $detalleHABERGeneral = ConfigPlanDetalle::where('config_plan_header_id', $configHeaderGeneral->id)
                ->where('debe_haber', false)
                ->where('status', 1)
                ->first();
            foreach ($solicitudes as $solicitud) {
                $customer = Customer::find($solicitud->customer_id);
                if (!$customer) {
                    DB::rollBack();
                    Log::error("Solicitud ID {$solicitud->id} falló: Cliente no encontrados.");
                    return;
                }
                $tabla = 'customer_movimientos';
                $code = BaseController::generarCodigo($tabla, 9);
                $valorTotal = BaseController::valorTotal();
                $dataMovimiento = [
                    "code" => $code,
                    "comprobante" => null,
                    "banco_id" => null,
                    "numero_deposito" => null,
                    "forma_pago_id" => null,
                    "forma_pago_name" => null,
                    "company_id" => $solicitud->company_id,
                    "customer_id" => $customer->id,
                    "customer_code" => $customer->code,
                    "afecta" => $transactionEUS->afecta,
                    "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                    "customer_ruc" => $customer->numero_documento,
                    "customer_address" => $customer->direccion,
                    "customer_telefono" => $customer->telefono,
                    "customer_tipo_ahorro_id" => $solicitud->customer_tipo_ahorro_id,
                    "type_transaction_id" => $transactionEUS->id,
                    "type_transaction_name" => $transactionEUS->name,
                    "type_transaction_action" => $transactionEUS->action,
                    "valor_movimiento" => $solicitud->valor,
                    "saldo_general" => $valorTotal + $solicitud->valor,
                    "observation" => $solicitud->observacion,
                    "user_created_id" => Auth::user()->id,
                    "date_created" => date('Y-m-d'),
                    "hour_created" => date("H:i:s"),
                ];
                $movimientos = CustomerMovimiento::create($dataMovimiento);
                $headerAsiento = AsientosHeader::create([
                    'company_id' => $companyId,
                    'manual' => 0,
                    'fecha_contable' => $movimientos->date_created . ' ' . $movimientos->hour_created,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'user_created' => Auth::user()->id,
                    'concepto_id' => $configHeader->concepto_id,
                    'config_plan_header_id' => $configHeader->id,
                    'descripcion' => $asientoDescription,
                    'customer_movimiento_id' => $movimientos->id,
                    // 'gasto_id'
                    'descargo_bovedas_header_id' => $configHeader->operaciones_descargo_bovedas_id,
                    'status' => 1,
                ]);
                // Crear detalles del asiento usando ConfigPlanDetalle
                // DETALLE DEBE
                AsientosDetalle::create([
                    'company_id' => $companyId,
                    'asientos_header_id' => $headerAsiento->id,
                    'plan_cuentas_id' => $detalleDEBE->plan_cuentas_id,
                    'valor' => $movimientos->valor_movimiento,
                    'sedes_centro_costos_id' => $detalleDEBE->sede_id ?? AsientosHeader::buscarSedesCentroCostos(Auth::user()->id),
                    'debe_haber' => true, // 1=DEBE, 0=HABER
                    'fecha_contable' => $movimientos->date_created . ' ' . $movimientos->hour_created,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'user_created' => Auth::user()->id,
                    'observacion' => $asientoDescription,
                    'status' => 1,
                ]);
                // DETALLE HABER (Pasivo/Abono: Cuenta seleccionada por el usuario)
                AsientosDetalle::create([
                    'company_id' => $companyId,
                    'asientos_header_id' => $headerAsiento->id,
                    'plan_cuentas_id' => $this->cuenta_pasivo_id, // Cuenta HABER seleccionada (Pasivo)
                    'valor' => $movimientos->valor_movimiento,
                    'sedes_centro_costos_id' => $detalleHABER->sede_id ?? AsientosHeader::buscarSedesCentroCostos(Auth::user()->id),
                    'debe_haber' => false, // HABER (0)
                    'fecha_contable' => $movimientos->date_created . ' ' . $movimientos->hour_created,
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'user_created' => Auth::user()->id,
                    'observacion' => $asientoDescription,
                    'status' => 1,
                ]);

                CustomerHistorialController::guardarHistorialAutomatica(
                    $movimientos->code,
                    $transactionEUS->id,
                    $solicitud->valor,
                    $customer->code,
                    $movimientos->saldo_general,
                    date('Y-m-d'),
                    $movimientos->customer_tipo_ahorro_id
                );
                $whatsappEnviar = 'Estimad@, *' . $customer->nombres . ' ' . $customer->apellidos . '* tu abono de utilidad ha sido procesada exitosamente, por el monto de: *' . $solicitud->valor . '$* Gracias por usar nuestros servicios.';
                $whatsapp = new WhaEnvios();
                $whatsapp->company_id = Auth::user()->company_id;
                $whatsapp->envio_ahora = true;
                $whatsapp->es_transacion = true;
                $whatsapp->inmediato = true;
                $whatsapp->description = 'ENVIO DE WHATSAPP DESDE APROBACIÓN UTILIDADES';
                $whatsapp->type_transacction_id = $transactionEUS->id;
                $whatsapp->type_transacction_name = $transactionEUS->name;
                $whatsapp->proviene_id = $movimientos->id;
                $whatsapp->customer_id = $customer->id;
                $whatsapp->customer_name = $customer->nombres . ' ' . $customer->apellidos;
                $whatsapp->customer_celular = $customer->telefono;
                $whatsapp->whatsapp = $whatsappEnviar;
                $whatsapp->estado = 'PENDIENTE';
                $whatsapp->fecha_creacion = date('Y-m-d H:i:s');
                $whatsapp->fecha_envio = date('Y-m-d H:i:s');
                $whatsapp->save();
                $solicitud->estado = 'APROBADO';
                $solicitud->save();
                $aprobadasCount++;
            }

            // CREACIÓN DEL ASIENTO CONTABLE (Uno general por la suma total)
            $headerAsiento = AsientosHeader::create([
                'company_id' => $companyId,
                'manual' => 0,
                'fecha_contable' => $movimientos->date_created . ' ' . $movimientos->hour_created,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'user_created' => Auth::user()->id,
                'concepto_id' => $configHeaderGeneral->concepto_id,
                'config_plan_header_id' => $configHeaderGeneral->id,
                'descripcion' => $asientoDescription,
                // 'customer_movimiento_id'
                // 'gasto_id'
                'descargo_bovedas_header_id' => $configHeaderGeneral->operaciones_descargo_bovedas_id,
                'status' => 1,
            ]);
            // Crear detalles del asiento usando ConfigPlanDetalle
            // DETALLE DEBE
            AsientosDetalle::create([
                'company_id' => $companyId,
                'asientos_header_id' => $headerAsiento->id,
                'plan_cuentas_id' => $detalleDEBEGeneral->plan_cuentas_id,
                'valor' => $solicitudes->first()->valor * $solicitudes->count(),
                'sedes_centro_costos_id' => $detalleDEBEGeneral->sede_id ?? AsientosHeader::buscarSedesCentroCostos(Auth::user()->id),
                'debe_haber' => true, // 1=DEBE, 0=HABER
                'fecha_contable' => $movimientos->date_created . ' ' . $movimientos->hour_created,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'user_created' => Auth::user()->id,
                'observacion' => $asientoDescription,
                'status' => 1,
            ]);
            // DETALLE HABER (Pasivo/Abono: Cuenta seleccionada por el usuario)
            AsientosDetalle::create([
                'company_id' => $companyId,
                'asientos_header_id' => $headerAsiento->id,
                'plan_cuentas_id' => $detalleHABERGeneral->plan_cuentas_id, // Cuenta HABER seleccionada (Pasivo)
                'valor' => $solicitudes->first()->valor * $solicitudes->count(),
                'sedes_centro_costos_id' => $detalleHABERGeneral->sede_id ?? AsientosHeader::buscarSedesCentroCostos(Auth::user()->id),
                'debe_haber' => false, // HABER (0)
                'fecha_contable' => $movimientos->date_created . ' ' . $movimientos->hour_created,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'user_created' => Auth::user()->id,
                'observacion' => $asientoDescription,
                'status' => 1,
            ]);

            DB::commit();
            session()->flash('message', '¡Las ' . $aprobadasCount . ' solicitudes de Utilidades han sido **APROBADAS** exitosamente y los abonos realizados!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al aprobar el lote de solicitudes: ' . $e->getMessage());
            Log::error('Error al aprobar lote masivo de utilidades (asientos_header_id: $loteId): ' . $e->getMessage());
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => 'Error en Aceptar Masivo',
                'color' => 'danger',
                'mensaje' => $e->getMessage()
            ]);
            return;
        }
    }

    public function RechazarMasivo()
    {
        $this->validate([
            'razon_rechazo_masivo' => 'required|min:5|max:250',
        ]);
        $ids = explode(',', $this->ids_movimientos_a_rechazar);
        if (empty($ids) || $this->ids_movimientos_a_rechazar === null) {
            session()->flash('error', 'No se han seleccionado movimientos para rechazar. Intente de nuevo.');
            $this->dispatch('close-modal', ['modalId' => 'modalRechazoMasivo']);
            return;
        }
        $this->transactionEUS = $this->transactionEUS();
        try {
            DB::beginTransaction();
            $movimientos = CustomerMovimientoSolicitud::whereIn('id', $ids)
                ->where('estado', 'PENDIENTE')
                ->where('type_transaction_id', $this->transactionEUS->id)
                ->get();
            if ($movimientos->isEmpty()) {
                session()->flash('warning', 'Las solicitudes del lote ya no están pendientes o no son de tipo EUS.');
                DB::rollBack();
                $this->dispatch('close-modal', ['modalId' => 'modalRechazoMasivo']);
                return;
            }
            $countRechazadas = 0;
            $asientosHeaderId = $movimientos->first()->asientos_header_id;
            foreach ($movimientos as $movimiento) {
                if ($movimiento->estado !== 'PENDIENTE') {
                    continue;
                }
                // Actualizar el estado de la Solicitud a RECHAZADA
                $movimiento->estado = 'RECHAZADA';
                $movimiento->razon_rechazado = $this->razon_rechazo_masivo;
                $movimiento->user_rechazado = Auth::id();
                $movimiento->fecha_rechazado = Carbon::now();
                $movimiento->save();
                $countRechazadas++;
            }
            DB::commit();
            session()->flash('message', '¡Las solicitudes han sido rechazadas masivamente con éxito!');
            $this->razon_rechazo_masivo = null;
            $this->ids_movimientos_a_rechazar = null;
            $this->totalSolicitudesRechazo = 0;
            $this->dispatchBrowserEvent('close-modal', ['modalId' => 'modalRechazoMasivo']);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al rechazar masivamente las solicitudes: ' . $e->getMessage());
            Log::error('Error en Rechazo Masivo de Utilidades: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $this->transactionEUS();
        $companyId = Auth::user()->company_id;
        $EUS_ID = $this->transactionEUS->id ?? null;
        $this->movimientosUtilidadesPendientes = CustomerMovimientoSolicitud::select(
            'customer_movimiento_solicitud.*',
            'c.nombres',
            'c.apellidos',
            'c.numero_documento',
        )
            ->join('customer as c', 'customer_movimiento_solicitud.customer_id', '=', 'c.id')
            ->where('customer_movimiento_solicitud.company_id', $companyId)
            ->where('customer_movimiento_solicitud.type_transaction_id', $EUS_ID)
            ->where('customer_movimiento_solicitud.estado', 'PENDIENTE')
            ->with([
                'customerTipoAhorro.tipoAhorros'
            ])
            ->get();
        $this->movimientosAgrupadosPorLote = $this->movimientosUtilidadesPendientes->groupBy('fecha_creacion'); //Se crea un lote de lotes por fecha_creacion

        return view('livewire.utilidades.utilidades-component', [
            'movimientosUtilidadesPendientes' => $this->movimientosUtilidadesPendientes,
            'movimientosAgrupadosPorLote' => $this->movimientosAgrupadosPorLote,
        ]);
    }
}
