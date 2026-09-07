<?php

namespace App\Http\Livewire\ConfiguracionCuenta;

use App\Models\Conceptos;
use App\Models\ConfigPlanDetalle;
use App\Models\ConfigPlanHeader;
use App\Models\Operacion;
use App\Models\OperacionesDescargoBovedas;
use App\Models\PlanCuentas;
use App\Models\Sedes;
use App\Models\TipoConcepto;
use App\Models\TypeTransaction;
use Doctrine\DBAL\Schema\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ConfiguracionCuentaComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $conceptos;
    public $id_seleccionado;
    public $id_header = 0;
    public $asientos = [];
    public $type_transaction_id;
    public $concepto_id;
    public $cuenta_id;
    public $search = '';

    protected $listeners = [
        'moduloChanged' => 'updateModuloId',
        'conceptoChanged' => 'updateConceptoId',
        'cuentaChanged' => 'updateCuentaId',
        'elminarConfigCompleta'
    ];

    public function updateModuloId($type_transaction_id)
    {
        $this->type_transaction_id = $type_transaction_id;
    }

    public function updateConceptoId($concepto_id)
    {
        $this->concepto_id = $concepto_id;
    }

    public function updateCuentaId($cuenta_id)
    {
        $this->cuenta_id = $cuenta_id;
    }

    public function abrirModal($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();
        if ($this->id_seleccionado !== 0) {
            $header = ConfigPlanHeader::find($this->id_seleccionado);
            //Cargar modulo de bovedas o  diarios
            $header->type_transaction_id = ($header->type_transaction_id !== null) ? '1-' . $header->type_transaction_id : '2-' . $header->operaciones_descargo_bovedas_id;

            $detalle = ConfigPlanDetalle::select('config_plan_detalle.*')
                ->join('plan_cuentas', 'config_plan_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
                ->where('config_plan_detalle.company_id', Auth::user()->company_id)
                ->where('config_plan_detalle.config_plan_header_id', $this->id_seleccionado)
                ->orderBy('config_plan_detalle.debe_haber', 'DESC')
                ->orderBy('plan_cuentas.codigo', 'ASC')
                ->get();
            $this->type_transaction_id = $header->type_transaction_id;
            $this->concepto_id = $header->concepto_id;
            foreach ($detalle as $key => $value) {
                $planCuentas = PlanCuentas::find($value->plan_cuentas_id);
                $this->asientos[] = [
                    'id' => $value->asientos,
                    'cuenta_id' => $value->plan_cuentas_id,
                    'cuenta_codigo' => $planCuentas->codigo,
                    'cuenta_nombre' => $planCuentas->nombre,
                    'sede_id' => $value->sede_id,
                    'debe_haber' => $value->debe_haber,
                    'operacion_id' => $value->operacion_id,
                ];
            }
        }
        $this->emit('selectoresCargar');
    }

    public function abrirModalAsiento($id)
    {
        $this->id_header = $id;
    }

    private function limpiarFormulario()
    {
        $this->reset(['type_transaction_id', 'concepto_id', 'cuenta_id']);
        $this->asientos = [];
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function agregarCuenta($id)
    {
        if ($id !== '') {
            $planCuentas = PlanCuentas::find($id);
            $this->asientos[] = [
                'id' => count($this->asientos) + 1,
                'cuenta_id' => $id,
                'cuenta_codigo' => $planCuentas->codigo,
                'cuenta_nombre' => $planCuentas->nombre,
                'sede_id' => 0,
                'operacion_id' => 0,
                'debe_haber' => true,
            ];
        } else {
            $color = 'danger';
            $mensaje = 'Debe Seleccionar una Cuenta Contable';
            $data = [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
        }
        $this->search = '';
    }

    public function actualizarDebHaber($key)
    {
        $this->asientos[$key]['debe_haber'] = !$this->asientos[$key]['debe_haber'];
        $this->emit('selectoresCargar');
    }

    public function actualizarOperacion($key, $valor)
    {
        $this->asientos[$key]['operacion_id'] = $valor;
        $this->emit('selectoresCargar');
    }

    public function elminarConfig($key)
    {
        unset($this->asientos[$key]);
        $color = 'success';
        $mensaje = 'Item Eliminado correctamente';
        $data = [
            'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function store()
    {
        $this->validate(
            [
                'type_transaction_id' => 'required',
                'concepto_id' => 'required',
            ],
            [
                'type_transaction_id.required' => 'El módulo es obligatorio',
                'concepto_id.required' => 'El concepto es obligatorio.',
            ]
        );

        if (count($this->asientos) < 2) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Configuracion incompleta</b>',
                'color' => 'danger',
                'mensaje' => 'La configuracion contable debe tener al menos una cuenta al Debe y una cuenta al Haber.'
            ]);
            return;
        }

        $tieneDebe = collect($this->asientos)->contains('debe_haber', true);
        $tieneHaber = collect($this->asientos)->contains('debe_haber', false);
        if (!$tieneDebe || !$tieneHaber) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Debe y Haber requeridos</b>',
                'color' => 'danger',
                'mensaje' => 'Debe configurar al menos una cuenta en Debe y una cuenta en Haber.'
            ]);
            return;
        }

        if ($this->id_seleccionado == 0) {
            $header = new ConfigPlanHeader();
            $header->company_id = Auth::user()->company_id;
            $color = 'success';
            $mensaje = 'Se crea correctamente la configuración';
            $data = [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
                'color' => $color,
                'mensaje' => $mensaje
            ];
        } else {
            $header = ConfigPlanHeader::find($this->id_seleccionado);
            $color = 'warning';
            $mensaje = 'Se modificó correctamete el Asiento Manual';
            $data = [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
                'color' => $color,
                'mensaje' => $mensaje
            ];
        }

        //dividir partes
        list($parte1, $parte2) = explode('-', $this->type_transaction_id);
        $tipo = (int) $parte1;
        $identificador = (int) $parte2;
        if ($tipo == 1) {
            $header->type_transaction_id = $identificador;
            $header->operaciones_descargo_bovedas_id = null;
        } else {
            $header->type_transaction_id = null;
            $header->operaciones_descargo_bovedas_id = $identificador;
        }
        $header->concepto_id = $this->concepto_id;
        $header->status = true;
        $header->save();

        $procesadosIds = [];

        $detallesActuales = ConfigPlanDetalle::where('company_id', Auth::user()->company_id)
            ->where('config_plan_header_id', $header->id)
            ->get();

        foreach ($this->asientos as $key => $value) {
            $detalle = $detallesActuales->firstWhere('plan_cuentas_id', $value['cuenta_id']);

            if ($detalle) {
                $detalle->sede_id = null;
                $detalle->debe_haber = $value['debe_haber'];
                $detalle->campo_foraneo = $detalle->campo_foraneo ?: 'valor_movimiento';
                $detalle->status = true;
                $detalle->save();

                $procesadosIds[] = $detalle->id;
            } else {
                $nuevoDetalle = new ConfigPlanDetalle();
                $nuevoDetalle->company_id = Auth::user()->company_id;
                $nuevoDetalle->config_plan_header_id = $header->id;
                $nuevoDetalle->plan_cuentas_id = $value['cuenta_id'];
                $nuevoDetalle->sede_id = null;
                $nuevoDetalle->debe_haber = $value['debe_haber'];
                $nuevoDetalle->campo_foraneo = 'valor_movimiento';
                $nuevoDetalle->status = true;
                $nuevoDetalle->save();

                $procesadosIds[] = $nuevoDetalle->id;
            }
        }

        ConfigPlanDetalle::where('company_id', Auth::user()->company_id)
            ->where('config_plan_header_id', $header->id)
            ->whereNotIn('id', $procesadosIds)
            ->delete();

        $this->dispatchBrowserEvent('alerta', $data);
        $this->dispatchBrowserEvent('closeModal');
    }

    public function guardarConfigCampoForaneo($id, $value)
    {
        $detalle = ConfigPlanDetalle::find($id);
        $detalle->campo_foraneo = $value;
        $detalle->save();
    }

    public function guardarConfigTabla($id, $value)
    {
        $detalle = ConfigPlanDetalle::find($id);
        $detalle->tabla = $value;
        $detalle->save();
    }

    public function guardarConfigCampo($id, $value)
    {
        $detalle = ConfigPlanDetalle::find($id);
        $detalle->campo = $value;
        $detalle->save();
    }

    public function confirmarElminarConfiguracion($id)
    {
        $data = [
            'id' => $id
        ];
        $this->dispatchBrowserEvent('notificarEliminar', $data);
    }

    public function elminarConfigCompleta($id)
    {
        ConfigPlanDetalle::where('company_id', Auth::user()->company_id)->where('config_plan_header_id', $id)->delete();
        ConfigPlanHeader::find($id)->delete();
        $color = 'danger';
        $mensaje = 'Se elimino correctamente, el Asiento Contable';
        $data = [
            'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function mount()
    {
        $this->conceptos = Conceptos::with('tipoConcepto')->get();
    }

    public function render()
    {
        $detalle = ConfigPlanDetalle::select(
            'config_plan_detalle.*',
            'plan_cuentas.nombre as plan_nombre',
            'plan_cuentas.codigo as plan_codigo',
            DB::raw('COALESCE(type_transactions.name, operaciones_descargo_bovedas.nombre) as type_transactions_nombre'),
            'conceptos.nombre as conceptos_nombre'
        )
            ->join('config_plan_header', 'config_plan_detalle.config_plan_header_id', '=', 'config_plan_header.id')
            ->leftjoin('type_transactions', 'config_plan_header.type_transaction_id', '=', 'type_transactions.id')
            ->leftjoin('operaciones_descargo_bovedas', 'config_plan_header.operaciones_descargo_bovedas_id', '=', 'operaciones_descargo_bovedas.id')
            ->join('conceptos', 'config_plan_header.concepto_id', '=', 'conceptos.id')
            ->join('plan_cuentas', 'config_plan_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
            ->where('config_plan_detalle.company_id', Auth::user()->company_id)
            ->orderBy('config_plan_header.id', 'ASC')
            ->orderBy('config_plan_detalle.debe_haber', 'DESC')
            ->orderBy('plan_cuentas.codigo', 'ASC')
            ->paginate(200);

        $tipoConceptos = TipoConcepto::where('company_id', Auth::user()->company_id)->where('status', true)->get();
        $planCuentas = PlanCuentas::where('company_id', Auth::user()->company_id)->get();
        $conceptos = Conceptos::where('company_id', Auth::user()->company_id)->where('status', true)->get();
        $typeTransaction = TypeTransaction::where('company_id', Auth::user()->company_id)->where('status', true)->get();
        $operacionesDescargoBovedas = OperacionesDescargoBovedas::where('company_id', Auth::user()->company_id)->where('status', true)->get();
        $detallesConfig = ConfigPlanDetalle::select('config_plan_detalle.*')
            ->join('plan_cuentas', 'config_plan_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
            ->where('config_plan_detalle.config_plan_header_id', $this->id_header)
            ->where('config_plan_detalle.company_id', Auth::user()->company_id)
            ->orderBy('config_plan_detalle.debe_haber', 'DESC')
            ->orderBy('plan_cuentas.codigo', 'ASC')
            ->get();

        //Buscar transacciones (Nuevo)
        $headers = ConfigPlanHeader::find($this->id_header);
        // dd($header->type_transaction_id);
        if (isset($headers->type_transaction_id) && $headers->type_transaction_id !== null) {
            $columns = DB::select("SHOW COLUMNS FROM customer_movimientos");
            $columnNombres = array_map(function ($col) {
                return $col->Field;
            }, $columns);
        } else {
            $columns = DB::select("SHOW COLUMNS FROM descargo_bovedas_header");
            $columnNombres = array_map(function ($col) {
                return $col->Field;
            }, $columns);
        }

        //Tablas prestamos
        $columnTablas = ['credit_folder_details'];

        //Buscar transacciones (Nuevo)
        $columnsPres = DB::select("SHOW COLUMNS FROM credit_folder_details");
        $columnPrestamos = array_map(function ($colP) {
            return $colP->Field;
        }, $columnsPres);

        if ($this->search !== '') {
            $planCuentasBuscador = PlanCuentas::where('company_id', Auth::user()->company_id)
                ->where(function ($query) {
                    $query->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('codigo', 'like', '%' . $this->search . '%');
                })
                ->paginate(10);
        } else {
            $planCuentasBuscador = PlanCuentas::where('company_id', Auth::user()->company_id)
                ->where(function ($query) {
                    $query->where('nombre', 'like', '%null%')
                        ->orWhere('codigo', 'like', '%null%');
                })
                ->paginate(1);
        }

        return view('livewire.configuracion-cuenta.configuracion-cuenta-component', compact('headers', 'detalle', 'tipoConceptos', 'planCuentas', 'conceptos', 'typeTransaction', 'operacionesDescargoBovedas', 'detallesConfig', 'columnNombres', 'columnTablas', 'columnPrestamos', 'planCuentasBuscador'));
    }
}
