<?php

namespace App\Http\Livewire\TodosModulos;

use App\Models\AsientosDetalle;
use App\Models\AsientosHeader;
use App\Models\Conceptos;
use App\Models\CustomerMovimiento;
use App\Models\PlanCuentas;
use App\Models\Sedes;
use App\Models\SedesCentroCostos;
use App\Models\TipoConcepto;
use App\Models\TypeTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class TodosModulosComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado;
    public $modulo_id = 0;
    public $fechaInicio;
    public $fechaFin;
    public $conceptos;
    public $centro_costos;
    public $movimiento_manual_id;
    public $fecha_manual;
    public $concepto_id;
    public $descripcion;
    public $search = '';
    public $asientos = [];
    public $buscarMovimiento = '';
    public $estadoContable = '';
    public $porPagina = 20;

    public function updated($property)
    {
        if (in_array($property, ['fechaInicio', 'fechaFin', 'modulo_id', 'buscarMovimiento', 'estadoContable', 'porPagina'], true)) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros()
    {
        $this->fechaInicio = date('Y-01-01');
        $this->fechaFin = date('Y-m-d');
        $this->modulo_id = 0;
        $this->buscarMovimiento = '';
        $this->estadoContable = '';
        $this->porPagina = 20;
        $this->resetPage();
    }

    public function __construct()
    {
        $this->fechaInicio = date('Y-01-01');
        $this->fechaFin = date('Y-m-d');
        $this->fecha_manual = date('Y-m-d\TH:i');
    }

    public function calcularTodosAsientos()
    {
        $this->validate(['fechaInicio' => 'required|date', 'fechaFin' => 'required|date|after_or_equal:fechaInicio']);
        $companyId = Auth::user()->company_id;
        $fechaInicio = \Carbon\Carbon::parse($this->fechaInicio)->format('Y-m-d');
        $fechaFin = \Carbon\Carbon::parse($this->fechaFin)->format('Y-m-d');
        $totalRecalculo = 0;
        $customerMovimientoIds = CustomerMovimiento::select('id')
            ->where('company_id', $companyId)
            ->where(function ($query) { $query->whereNull('status')->orWhere('status', 1); })
            ->when($this->modulo_id != 0, function ($query) {
                return $query->where('type_transaction_id', $this->modulo_id);
            })
            ->whereBetween(
                DB::raw('DATE(date_created)'),
                [$fechaInicio, $fechaFin]
            )
            ->pluck('id');
        $generalesAsientosIdsQuery = AsientosHeader::select('asientos_header.id')
            ->where('asientos_header.company_id', $companyId)
            ->where('asientos_header.status', 1)
            ->whereNull('asientos_header.customer_movimiento_id')
            ->whereBetween(DB::raw('DATE(asientos_header.fecha_contable)'), [$fechaInicio, $fechaFin]);
        // Aplicar el filtro de módulo SÓLO si hay un módulo seleccionado (y es un asiento general)
        if ($this->modulo_id != 0) {
            $generalesAsientosIdsQuery
                ->join('config_plan_header', 'asientos_header.config_plan_header_id', '=', 'config_plan_header.id')
                ->where('config_plan_header.type_transaction_id', $this->modulo_id);
        }
        $generalesAsientosHeaderIds = $generalesAsientosIdsQuery
            ->distinct()
            ->pluck('id');
        $totalRecalculo = $customerMovimientoIds->count() + $generalesAsientosHeaderIds->count();
        $typeTransaction = TypeTransaction::where('id', $this->modulo_id)->first();
        foreach ($customerMovimientoIds as $customerMovimientoId) {
            $respuesta = AsientosHeader::recalcularAsientos($customerMovimientoId);
            $color = !empty($respuesta['warning']) ? 'warning' : (($respuesta['code'] == '200') ? 'success' : 'danger');
            $mensaje = $respuesta['msg'];
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
        }
        foreach ($generalesAsientosHeaderIds as $asientoHeaderId) {
            $tipoGeneral = $typeTransaction ?? optional(AsientosHeader::where('company_id', $companyId)->find($asientoHeaderId)->configPlanHeader)->typeTransaction;
            $respuesta = AsientosHeader::recalcularAsientos($asientoHeaderId, 'customer_movimientos', true, $tipoGeneral);
            $color = !empty($respuesta['warning']) ? 'warning' : (($respuesta['code'] == '200') ? 'success' : 'danger');
            $mensaje = $respuesta['msg'];
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
        }
        $data = [
            'titulo' => 'Notificación',
            'color' => 'warning',
            'mensaje' => 'Se Término el recalculo de ' . $totalRecalculo . ' asientos.'
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function recalcularAsientoGeneral($id_asiento_header, $type_transaction_id)
    {
        $typeTransaction = TypeTransaction::where('id', $type_transaction_id)->first();
        $respuesta = AsientosHeader::recalcularAsientos($id_asiento_header, 'customer_movimientos', true, $typeTransaction);
        $color = !empty($respuesta['warning']) ? 'warning' : (($respuesta['code'] == '200') ? 'success' : 'danger');
        $mensaje = $respuesta['msg'];
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function recalcularAsiento($id)
    {
        $respuesta = AsientosHeader::recalcularAsientos($id);
        $color = !empty($respuesta['warning']) ? 'warning' : (($respuesta['code'] == '200') ? 'success' : 'danger');
        $mensaje = $respuesta['msg'];
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function abrirModal($id)
    {
        $this->limpiarFormulario();
        $this->id_seleccionado = AsientosHeader::where('company_id', Auth::user()->company_id)->findOrFail($id)->id;
    }

    public function abrirModalManual($movimientoId)
    {
        $this->limpiarFormularioManual();
        $movimiento = CustomerMovimiento::where('company_id', Auth::user()->company_id)->find($movimientoId);

        if (!$movimiento) {
            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Advertencia',
                'color' => 'danger',
                'mensaje' => 'No existe el movimiento seleccionado.'
            ]);
            return;
        }

        $this->movimiento_manual_id = $movimiento->id;
        $this->fecha_manual = date('Y-m-d\TH:i', strtotime($movimiento->date_created . ' ' . $movimiento->hour_created));
        $this->descripcion = 'ASIENTO MANUAL DEL MODULO ' . $movimiento->type_transaction_name . ' MOVIMIENTO #' . $movimiento->id;
    }

    public function agregarCuenta($id)
    {
        $planCuentas = PlanCuentas::where('company_id', Auth::user()->company_id)->find($id);
        if (!$planCuentas) {
            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Advertencia',
                'color' => 'danger',
                'mensaje' => 'La cuenta contable seleccionada no existe.'
            ]);
            return;
        }

        $this->asientos[] = [
            'cuenta_id' => $planCuentas->id,
            'cuenta_codigo' => $planCuentas->codigo,
            'cuenta_nombre' => $planCuentas->nombre,
            'sedes_centro_costos' => 0,
            'debe_haber' => true,
            'debe' => '0.00',
            'haber' => '0.00',
        ];

        $this->search = '';
    }

    public function elminarCuenta($key)
    {
        unset($this->asientos[$key]);
        $this->asientos = array_values($this->asientos);
    }

    public function actualizarDebHaber($key)
    {
        $this->asientos[$key]['debe_haber'] = !$this->asientos[$key]['debe_haber'];
        $this->asientos[$key]['debe'] = '0.00';
        $this->asientos[$key]['haber'] = '0.00';
    }

    public function actualizarValores($key, $valor)
    {
        if ($this->asientos[$key]['debe_haber']) {
            $this->asientos[$key]['debe'] = $valor;
            $this->asientos[$key]['haber'] = '0.00';
        } else {
            $this->asientos[$key]['debe'] = '0.00';
            $this->asientos[$key]['haber'] = $valor;
        }
    }

    public function actualizarCentro($key, $valor)
    {
        $this->asientos[$key]['sedes_centro_costos'] = $valor;
    }

    public function storeManual()
    {
        $this->validate([
            'movimiento_manual_id' => 'required',
            'fecha_manual' => 'required',
            'concepto_id' => 'required',
            'descripcion' => 'required',
        ], [
            'concepto_id.required' => 'El concepto es obligatorio.',
            'descripcion.required' => 'La descripcion es obligatoria.',
        ]);

        if (count($this->asientos) < 2) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Asiento incompleto</b>',
                'color' => 'danger',
                'mensaje' => 'El asiento debe tener al menos dos cuentas contables.'
            ]);
            return;
        }

        // Los asientos descuadrados se conservan para revision.

        foreach ($this->asientos as $asiento) {
            if (empty($asiento['sedes_centro_costos'])) {
                $this->dispatchBrowserEvent('alertaGrande', [
                    'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Sin centro de costos</b>',
                    'color' => 'danger',
                    'mensaje' => 'El centro de costos es obligatorio para todas las cuentas.'
                ]);
                return;
            }
        }

        if (!Auth::user()->company->puedeContabilizar(date('Y-m-d', strtotime($this->fecha_manual)))) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Periodo no permitido</b>',
                'color' => 'danger',
                'mensaje' => 'La fecha seleccionada no esta habilitada para contabilizar.'
            ]);
            return;
        }

        DB::transaction(function () {
            $movimiento = CustomerMovimiento::find($this->movimiento_manual_id);

            $header = new AsientosHeader();
            $header->company_id = Auth::user()->company_id;
            $header->manual = true;
            $header->fecha_contable = date('Y-m-d H:i:s', strtotime($this->fecha_manual));
            $header->fecha_creacion = date('Y-m-d H:i:s');
            $header->user_created = Auth::user()->id;
            $header->concepto_id = $this->concepto_id;
            $header->descripcion = $this->descripcion;
            $header->customer_movimiento_id = $this->movimiento_manual_id;
            $header->gasto_id = $movimiento->gastos_id ?? null;
            $header->status = true;
            $header->save();

            foreach ($this->asientos as $asiento) {
                $detalle = new AsientosDetalle();
                $detalle->company_id = Auth::user()->company_id;
                $detalle->asientos_header_id = $header->id;
                $detalle->plan_cuentas_id = $asiento['cuenta_id'];
                $detalle->sedes_centro_costos_id = $asiento['sedes_centro_costos'];
                $detalle->debe_haber = $asiento['debe_haber'];
                $detalle->valor = $asiento['debe_haber'] ? $asiento['debe'] : $asiento['haber'];
                $detalle->fecha_contable = $header->fecha_contable;
                $detalle->fecha_creacion = date('Y-m-d H:i:s');
                $detalle->user_created = Auth::user()->id;
                $detalle->observacion = 'ASIENTO MANUAL DESDE TODOS MODULOS';
                $detalle->status = true;
                $detalle->save();
            }
            if (!AsientosHeader::estaCuadrado($header->id)) {
                AsientosHeader::advertirDescuadre($header);
            }
        });

        $this->dispatchBrowserEvent('alerta', [
            'titulo' => 'Notificacion',
            'color' => 'success',
            'mensaje' => 'Asiento manual creado correctamente.'
        ]);
        $this->dispatchBrowserEvent('closeModal');
        $this->limpiarFormularioManual();
    }

    private function limpiarFormulario()
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }

    private function limpiarFormularioManual()
    {
        $this->reset(['movimiento_manual_id', 'concepto_id', 'descripcion', 'search']);
        $this->fecha_manual = date('Y-m-d\TH:i');
        $this->asientos = [];
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function mount()
    {
        $this->conceptos = Conceptos::with('tipoConcepto')
            ->where('company_id', Auth::user()->company_id)
            ->get();

        $this->centro_costos = SedesCentroCostos::with(['sede', 'centroCostos'])
            ->where('company_id', Auth::user()->company_id)
            ->get();
    }

    private function consultaModulos($companyId, $fechaInicio, $fechaFin)
    {
        $sumDebe = "(SELECT COALESCE(SUM(valor), 0) FROM asientos_detalle WHERE asientos_detalle.asientos_header_id = asientos_header.id AND asientos_detalle.company_id = asientos_header.company_id AND asientos_detalle.status = 1 AND asientos_detalle.debe_haber = 1)";
        $sumHaber = "(SELECT COALESCE(SUM(valor), 0) FROM asientos_detalle WHERE asientos_detalle.asientos_header_id = asientos_header.id AND asientos_detalle.company_id = asientos_header.company_id AND asientos_detalle.status = 1 AND asientos_detalle.debe_haber = 0)";
        $movimientos = CustomerMovimiento::select(
            'customer_movimientos.id', 'customer_movimientos.date_created', 'customer_movimientos.hour_created',
            'customer_movimientos.customer_ruc', 'customer_movimientos.customer_name',
            'customer_movimientos.type_transaction_name', 'customer_movimientos.valor_movimiento',
            'asientos_header.id as asiento_header_id', 'asientos_header.descripcion as asiento_descripcion',
            DB::raw($sumDebe . ' as suma_debe'), DB::raw($sumHaber . ' as suma_haber'),
            DB::raw("'MODULO' as origen"), 'customer_movimientos.type_transaction_id'
        )->leftJoin('asientos_header', function ($join) use ($companyId) {
            $join->on('customer_movimientos.id', '=', 'asientos_header.customer_movimiento_id')
                ->where('asientos_header.company_id', $companyId)->where('asientos_header.status', 1);
        })->where('customer_movimientos.company_id', $companyId)
            ->where(function ($query) { $query->whereNull('customer_movimientos.status')->orWhere('customer_movimientos.status', 1); })
            ->whereBetween(DB::raw('DATE(customer_movimientos.date_created)'), [$fechaInicio, $fechaFin])
            ->when($this->modulo_id != 0, function ($query) { $query->where('customer_movimientos.type_transaction_id', $this->modulo_id); });

        $generales = AsientosHeader::select(
            DB::raw('NULL as id'), DB::raw('DATE(asientos_header.fecha_contable) as date_created'),
            DB::raw('TIME(asientos_header.fecha_contable) as hour_created'), DB::raw("'' as customer_ruc"),
            DB::raw("'Asiento contable general' as customer_name"), DB::raw("'Asiento general' as type_transaction_name"),
            DB::raw($sumDebe . ' as valor_movimiento'), 'asientos_header.id as asiento_header_id',
            'asientos_header.descripcion as asiento_descripcion', DB::raw($sumDebe . ' as suma_debe'),
            DB::raw($sumHaber . ' as suma_haber'), DB::raw("'GENERAL' as origen"),
            'config_plan_header.type_transaction_id'
        )->leftJoin('config_plan_header', function ($join) use ($companyId) {
            $join->on('asientos_header.config_plan_header_id', '=', 'config_plan_header.id')->where('config_plan_header.company_id', $companyId);
        })->where('asientos_header.company_id', $companyId)->where('asientos_header.status', 1)
            ->whereNull('asientos_header.customer_movimiento_id')
            ->whereBetween(DB::raw('DATE(asientos_header.fecha_contable)'), [$fechaInicio, $fechaFin])
            ->when($this->modulo_id != 0, function ($query) { $query->where('config_plan_header.type_transaction_id', $this->modulo_id); });

        $estadoSql = "CASE WHEN asiento_header_id IS NULL THEN 'sin_asiento' WHEN asiento_descripcion LIKE '[PENDIENTE CONTABLE] %' OR ROUND(COALESCE(suma_debe, 0), 2) <> ROUND(COALESCE(suma_haber, 0), 2) OR COALESCE(suma_debe, 0) <= 0 THEN 'pendiente' ELSE 'cuadrado' END";
        $consulta = DB::query()->fromSub($movimientos->unionAll($generales), 'registros')
            ->select('registros.*')->selectRaw($estadoSql . ' as estado_contable');
        if (trim($this->buscarMovimiento) !== '') {
            $buscar = '%' . trim($this->buscarMovimiento) . '%';
            $consulta->where(function ($query) use ($buscar) {
                foreach (['customer_name', 'customer_ruc', 'type_transaction_name', 'id', 'asiento_header_id'] as $campo) {
                    $query->orWhere($campo, 'like', $buscar);
                }
            });
        }
        return DB::query()->fromSub($consulta, 'resultados');
    }

    public function render()
    {
        $companyId = Auth::user()->company_id;
        $fechaInicio = \Carbon\Carbon::parse($this->fechaInicio)->format('Y-m-d');
        $fechaFin = \Carbon\Carbon::parse($this->fechaFin)->format('Y-m-d');

        $tipoTransacciones = TypeTransaction::where('company_id', $companyId)->where('status', true)->get();
        $tipoConceptos = TipoConcepto::where('company_id', $companyId)->where('status', true)->get();
        $sedes = Sedes::where('company_id', $companyId)->where('status', true)->get();
        $planCuentasBuscador = $this->search !== ''
            ? PlanCuentas::where('company_id', $companyId)
                ->where('status', true)
                ->where(function ($q) {
                    $q->where('nombre', 'like', "%{$this->search}%")
                        ->orWhere('codigo', 'like', "%{$this->search}%");
                })
                ->orderBy('codigo')
                ->limit(10)
                ->get()
            : collect();
        $consulta = $this->consultaModulos($companyId, $fechaInicio, $fechaFin);
        $resumen = (clone $consulta)->selectRaw("COUNT(*) as total, SUM(CASE WHEN estado_contable = 'cuadrado' THEN 1 ELSE 0 END) as cuadrados, SUM(CASE WHEN estado_contable = 'pendiente' THEN 1 ELSE 0 END) as pendientes, SUM(CASE WHEN estado_contable = 'sin_asiento' THEN 1 ELSE 0 END) as sin_asiento")->first();
        if (in_array($this->estadoContable, ['cuadrado', 'pendiente', 'sin_asiento'], true)) {
            $consulta->where('estado_contable', $this->estadoContable);
        }
        $limite = in_array((int) $this->porPagina, [20, 50, 100], true) ? (int) $this->porPagina : 20;
        $modulos = $consulta->orderBy('date_created', 'desc')->orderBy('hour_created', 'desc')
            ->orderBy('asiento_header_id', 'desc')->orderBy('id', 'desc')->paginate($limite);
        $asientosHeader = null;
        $detalle = collect();

        if ($this->id_seleccionado) {
            $asientosHeader = AsientosHeader::where('company_id', $companyId)->find($this->id_seleccionado);
            $detalle = AsientosDetalle::select(
                'asientos_detalle.*',
                'plan_cuentas.nombre as plan_nombre',
                'plan_cuentas.codigo as plan_codigo',
                'sedes.name as sedes_nombre',
                'centro_costos.name as centro_costos_nombre',
                'asientos_detalle.asientos_header_id as header_id'
            )
                ->leftjoin('plan_cuentas', 'asientos_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
                ->leftjoin('sedes_centro_costos', 'asientos_detalle.sedes_centro_costos_id', '=', 'sedes_centro_costos.id')
                ->leftjoin('centro_costos', 'sedes_centro_costos.centro_costos_id', '=', 'centro_costos.id')
                ->leftjoin('sedes', 'sedes_centro_costos.sede_id', '=', 'sedes.id')
                ->where('asientos_detalle.company_id', $companyId)
                ->where('asientos_detalle.status', 1)
                ->where('asientos_detalle.asientos_header_id', $this->id_seleccionado)
                ->orderBy('asientos_detalle.debe_haber', 'DESC')
                ->orderBy('asientos_detalle.id')
                ->get();
        }

        return view('livewire.todos-modulos.todos-modulos-component', compact(
            'modulos',
            'resumen',
            'tipoTransacciones',
            'asientosHeader',
            'detalle',
            'tipoConceptos',
            'sedes',
            'planCuentasBuscador'
        ));
    }
}
