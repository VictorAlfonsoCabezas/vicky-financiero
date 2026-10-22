<?php

namespace App\Http\Livewire\ConfiguracionCuenta;

use App\Models\{AsientosHeader, Conceptos, ConfigPlanDetalle, ConfigPlanHeader, OperacionesDescargoBovedas, PlanCuentas, TipoConcepto, TypeTransaction};
use Illuminate\Support\Facades\{Auth, DB, Schema};
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ConfiguracionCuentaComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $conceptos;
    public $id_seleccionado = 0;
    public $id_header = 0;
    public $id_eliminar;
    public $asientos = [];
    public $type_transaction_id = '';
    public $concepto_id = '';
    public $cuenta_id;
    public $search = '';
    public $buscarConfiguracion = '';
    public $filtroOrigen = '';
    public $filtroConcepto = '';
    public $porPagina = 12;
    public $parametroGuardado = '';

    protected $listeners = [
        'moduloChanged' => 'updateModuloId',
        'conceptoChanged' => 'updateConceptoId',
        'cuentaChanged' => 'updateCuentaId',
        'elminarConfigCompleta'
    ];

    private function cabeceras()
    {
        return ConfigPlanHeader::where('company_id', Auth::user()->company_id);
    }

    private function detalles()
    {
        return ConfigPlanDetalle::where('company_id', Auth::user()->company_id);
    }

    public function mount()
    {
        $this->conceptos = Conceptos::where('company_id', Auth::user()->company_id)->get();
    }

    public function updated($property)
    {
        if (in_array($property, ['buscarConfiguracion', 'filtroOrigen', 'filtroConcepto', 'porPagina'], true)) $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->reset(['buscarConfiguracion', 'filtroOrigen', 'filtroConcepto', 'porPagina']);
        $this->resetPage();
    }

    public function updateModuloId($id)
    {
        $this->type_transaction_id = $id;
    }
    public function updateConceptoId($id)
    {
        $this->concepto_id = $id;
    }
    public function updateCuentaId($id)
    {
        $this->cuenta_id = $id;
    }

    public function abrirModal($id)
    {
        $this->reset(['type_transaction_id', 'concepto_id', 'cuenta_id', 'asientos', 'search']);
        $this->resetValidation();
        $this->id_seleccionado = (int) $id;
        if ($id) {
            $header = $this->cabeceras()->findOrFail($id);
            $this->type_transaction_id = $header->type_transaction_id !== null
                ? '1-' . $header->type_transaction_id : '2-' . $header->operaciones_descargo_bovedas_id;
            $this->concepto_id = $header->concepto_id;
            foreach ($this->detalles()->with('planCuentas')->where('config_plan_header_id', $id)->orderByDesc('debe_haber')->orderBy('id')->get() as $detalle) {
                $this->asientos[] = [
                    'id' => $detalle->id,
                    'cuenta_id' => $detalle->plan_cuentas_id,
                    'cuenta_codigo' => optional($detalle->planCuentas)->codigo,
                    'cuenta_nombre' => optional($detalle->planCuentas)->nombre ?: 'Cuenta no disponible',
                    'sede_id' => $detalle->sede_id,
                    'operacion_id' => $detalle->operacion_id,
                    'debe_haber' => (bool) $detalle->debe_haber
                ];
            }
        }
        $this->dispatchBrowserEvent('cc-abrir-modal', ['id' => 'modalGeneral']);
    }

    public function abrirModalAsiento($id)
    {
        $this->cabeceras()->findOrFail($id);
        $this->id_header = (int) $id;
        $this->parametroGuardado = '';
        $this->resetValidation();
        $this->dispatchBrowserEvent('cc-abrir-modal', ['id' => 'modalGeneral2']);
    }

    public function agregarCuenta($id)
    {
        if (!$id) {
            $this->addError('search', 'Selecciona una cuenta del buscador.');
            return;
        }
        $cuenta = PlanCuentas::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $this->asientos[] = [
            'id' => null,
            'cuenta_id' => $cuenta->id,
            'cuenta_codigo' => $cuenta->codigo,
            'cuenta_nombre' => $cuenta->nombre,
            'sede_id' => null,
            'operacion_id' => null,
            'debe_haber' => true
        ];
        $this->search = '';
        $this->resetValidation('asientos');
    }

    public function actualizarDebHaber($key)
    {
        if (isset($this->asientos[$key])) $this->asientos[$key]['debe_haber'] = !$this->asientos[$key]['debe_haber'];
    }

    public function actualizarOperacion($key, $valor)
    {
        if (isset($this->asientos[$key])) $this->asientos[$key]['operacion_id'] = $valor;
    }

    public function elminarConfig($key)
    {
        unset($this->asientos[$key]);
        $this->asientos = array_values($this->asientos);
    }

    public function store()
    {
        $empresa = Auth::user()->company_id;
        $this->validate([
            'type_transaction_id' => ['required', 'regex:/^[12]-[1-9][0-9]*$/'],
            'concepto_id' => ['required', Rule::exists('conceptos', 'id')->where('company_id', $empresa)],
            'asientos' => 'required|array|min:2',
            'asientos.*.cuenta_id' => ['required', Rule::exists('plan_cuentas', 'id')->where('company_id', $empresa)],
            'asientos.*.debe_haber' => 'required|boolean',
        ], [
            'type_transaction_id.required' => 'Selecciona un módulo.',
            'type_transaction_id.regex' => 'Selecciona un módulo válido.',
            'concepto_id.required' => 'Selecciona un concepto.',
            'asientos.min' => 'Agrega al menos una cuenta al Debe y una al Haber.',
            'asientos.required' => 'Agrega las cuentas del Debe y del Haber.'
        ]);
        if (!collect($this->asientos)->contains('debe_haber', true) || !collect($this->asientos)->contains('debe_haber', false)) {
            $this->addError('asientos', 'La configuración debe incluir al menos una cuenta al Debe y una al Haber.');
            return;
        }
        [$tipo, $origen] = explode('-', $this->type_transaction_id);
        $modelo = $tipo === '1' ? TypeTransaction::class : OperacionesDescargoBovedas::class;
        if (!$modelo::where('company_id', $empresa)->where('id', $origen)->exists()) {
            $this->addError('type_transaction_id', 'El módulo no pertenece a la empresa activa.');
            return;
        }
        DB::transaction(function () use ($empresa, $tipo, $origen) {
            $header = $this->id_seleccionado ? $this->cabeceras()->lockForUpdate()->findOrFail($this->id_seleccionado) : new ConfigPlanHeader;
            $header->fill([
                'company_id' => $empresa,
                'type_transaction_id' => $tipo === '1' ? $origen : null,
                'operaciones_descargo_bovedas_id' => $tipo === '2' ? $origen : null,
                'concepto_id' => $this->concepto_id,
                'status' => true
            ]);
            $header->save();
            $actuales = $this->detalles()->where('config_plan_header_id', $header->id)->get();
            $ids = [];
            foreach ($this->asientos as $fila) {
                $detalle = !empty($fila['id']) ? $actuales->firstWhere('id', $fila['id']) : null;
                if (!$detalle) $detalle = new ConfigPlanDetalle(['company_id' => $empresa, 'config_plan_header_id' => $header->id]);
                $detalle->plan_cuentas_id = $fila['cuenta_id'];
                $detalle->debe_haber = $fila['debe_haber'];
                // Se conservan los parámetros, la sede y la operación de cada detalle existente.
                $detalle->campo_foraneo = $detalle->campo_foraneo ?: ($tipo === '2' ? 'valor' : 'valor_movimiento');
                $detalle->status = true;
                $detalle->save();
                $ids[] = $detalle->id;
            }
            $this->detalles()->where('config_plan_header_id', $header->id)->whereNotIn('id', $ids)->delete();
            $this->id_seleccionado = $header->id;
        });
        $this->dispatchBrowserEvent('alerta', ['titulo' => 'Configuración guardada', 'color' => 'success', 'mensaje' => 'La configuración contable se guardó correctamente.']);
        $this->dispatchBrowserEvent('cc-cerrar-modal', ['id' => 'modalGeneral']);
    }

    private function guardarParametro($id, $atributo, $valor)
    {
        $this->resetValidation();
        $this->parametroGuardado = '';
        $detalle = $this->detalles()->where('config_plan_header_id', $this->id_header)->findOrFail($id);
        $header = $this->cabeceras()->findOrFail($detalle->config_plan_header_id);
        $origen = $header->type_transaction_id !== null ? 'customer_movimientos' : 'descargo_bovedas_header';
        $permitidos = $atributo === 'campo_foraneo' ? Schema::getColumnListing($origen)
            : ($atributo === 'tabla' ? ['', 'credit_folder_details'] : array_merge([''], Schema::getColumnListing('credit_folder_details')));
        if (
            !in_array($valor, $permitidos, true) || ($origen === 'descargo_bovedas_header' && $atributo !== 'campo_foraneo' && $valor !== '')
            || ($atributo === 'campo' && $valor !== '' && $detalle->tabla !== 'credit_folder_details')
        ) {
            $this->addError('parametros', 'Selecciona un campo válido para el origen y la tabla de esta cuenta.');
            return;
        }
        $detalle->$atributo = $valor === '' ? null : $valor;
        if ($atributo === 'tabla' && !$valor) $detalle->campo = null;
        $detalle->save();
        $this->parametroGuardado = 'Cambio guardado en la cuenta ' . optional($detalle->planCuentas)->codigo . '.';
    }

    public function guardarConfigCampoForaneo($id, $valor)
    {
        $this->guardarParametro($id, 'campo_foraneo', $valor);
    }
    public function guardarConfigTabla($id, $valor)
    {
        $this->guardarParametro($id, 'tabla', $valor);
    }
    public function guardarConfigCampo($id, $valor)
    {
        $this->guardarParametro($id, 'campo', $valor);
    }

    public function confirmarElminarConfiguracion($id)
    {
        $this->id_eliminar = $this->cabeceras()->findOrFail($id)->id;
        $this->dispatchBrowserEvent('cc-abrir-modal', ['id' => 'ccEliminarModal']);
    }

    public function elminarConfigCompleta($id)
    {
        $header = $this->cabeceras()->findOrFail($id);
        if (AsientosHeader::where('company_id', Auth::user()->company_id)->where('config_plan_header_id', $id)->exists()) {
            $this->dispatchBrowserEvent('alerta', ['titulo' => 'Configuración en uso', 'color' => 'warning', 'mensaje' => 'Esta configuración tiene asientos asociados y no se puede eliminar.']);
            return;
        }
        DB::transaction(function () use ($header) {
            $this->detalles()->where('config_plan_header_id', $header->id)->delete();
            $header->delete();
        });
        $this->id_eliminar = null;
        if ((int) $this->id_header === (int) $id) $this->id_header = 0;
        $this->dispatchBrowserEvent('cc-cerrar-modal', ['id' => 'ccEliminarModal']);
        $this->dispatchBrowserEvent('alerta', ['titulo' => 'Configuración eliminada', 'color' => 'success', 'mensaje' => 'La configuración se eliminó correctamente.']);
    }

    public function render()
    {
        $empresa = Auth::user()->company_id;
        $consulta = $this->cabeceras()->with(['typeTransaction', 'operacionesDescargoBovedas', 'concepto', 'detalles' => function ($query) use ($empresa) {
            $query->where('company_id', $empresa)->with('planCuentas')->orderByDesc('debe_haber')->orderBy('id');
        }]);
        if (trim($this->buscarConfiguracion) !== '') {
            $buscar = '%' . trim($this->buscarConfiguracion) . '%';
            $consulta->where(function ($query) use ($buscar, $empresa) {
                $query->whereHas('typeTransaction', function ($q) use ($buscar) {
                    $q->where('name', 'like', $buscar);
                })
                    ->orWhereHas('operacionesDescargoBovedas', function ($q) use ($buscar) {
                        $q->where('nombre', 'like', $buscar);
                    })
                    ->orWhereHas('concepto', function ($q) use ($buscar) {
                        $q->where('nombre', 'like', $buscar);
                    })
                    ->orWhereHas('detalles', function ($q) use ($buscar, $empresa) {
                        $q->where('company_id', $empresa)->whereHas('planCuentas', function ($cuenta) use ($buscar, $empresa) {
                            $cuenta->where('company_id', $empresa)->where(function ($texto) use ($buscar) {
                                $texto->where('codigo', 'like', $buscar)->orWhere('nombre', 'like', $buscar);
                            });
                        });
                    });
            });
        }
        if ($this->filtroOrigen === 'diarias') $consulta->whereNotNull('type_transaction_id');
        if ($this->filtroOrigen === 'bovedas') $consulta->whereNotNull('operaciones_descargo_bovedas_id');
        if ($this->filtroConcepto) $consulta->where('concepto_id', $this->filtroConcepto);
        $configuraciones = $consulta->orderBy('id')->paginate(in_array((int) $this->porPagina, [12, 24, 48], true) ? (int) $this->porPagina : 12);
        $resumen = $this->cabeceras()->selectRaw('COUNT(*) as total, SUM(CASE WHEN type_transaction_id IS NOT NULL THEN 1 ELSE 0 END) as diarias, SUM(CASE WHEN operaciones_descargo_bovedas_id IS NOT NULL THEN 1 ELSE 0 END) as bovedas')->first();
        $conceptos = Conceptos::where('company_id', $empresa)->orderBy('nombre')->get();
        $typeTransaction = TypeTransaction::where('company_id', $empresa)->orderBy('name')->get();
        $operacionesDescargoBovedas = OperacionesDescargoBovedas::where('company_id', $empresa)->orderBy('nombre')->get();
        $headers = $this->id_header ? $this->cabeceras()->with(['typeTransaction', 'operacionesDescargoBovedas', 'concepto'])->find($this->id_header) : null;
        $detallesConfig = $headers ? $this->detalles()->with('planCuentas')->where('config_plan_header_id', $headers->id)->orderByDesc('debe_haber')->orderBy('id')->get() : collect();
        $columnNombres = $headers ? Schema::getColumnListing($headers->type_transaction_id !== null ? 'customer_movimientos' : 'descargo_bovedas_header') : [];
        $columnTablas = ['credit_folder_details'];
        $columnPrestamos = $headers ? Schema::getColumnListing('credit_folder_details') : [];
        $planCuentasBuscador = trim($this->search) === '' ? collect() : PlanCuentas::where('company_id', $empresa)
            ->where(function ($query) {
                $query->where('nombre', 'like', '%' . trim($this->search) . '%')->orWhere('codigo', 'like', '%' . trim($this->search) . '%');
            })
            ->orderBy('codigo')->limit(12)->get();
        $cuentaEliminar = $this->id_eliminar ? $this->cabeceras()->with(['typeTransaction', 'operacionesDescargoBovedas', 'concepto'])->find($this->id_eliminar) : null;
        return view('livewire.configuracion-cuenta.configuracion-cuenta-component', compact('configuraciones', 'resumen', 'conceptos', 'typeTransaction', 'operacionesDescargoBovedas', 'headers', 'detallesConfig', 'columnNombres', 'columnTablas', 'columnPrestamos', 'planCuentasBuscador', 'cuentaEliminar'));
    }
}
