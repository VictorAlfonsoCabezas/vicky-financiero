<?php

namespace App\Http\Livewire\Asientos;

use App\Models\AsientosDetalle;
use App\Models\AsientosHeader;
use App\Models\CentroCostos;
use App\Models\Conceptos;
use App\Models\PlanCuentas;
use App\Models\Sedes;
use App\Models\SedesCentroCostos;
use App\Models\TipoConcepto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AsientosComponent extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';


    public $conceptos;
    public $centro_costos;
    public $fechaInicio = '';
    public $fechaFin = '';
    public $id_seleccionado = 0;
    public $nombre = '';
    public $cuentaDebe = '';
    public $cuentaHaber = '';
    public $fecha = '';
    public $cuenta_id;
    public $concepto_id;
    public $descripcion;
    public $asientos = [];
    public $searchAsiento = '';
    public $searchAsientoDescripcion = '';
    public $manual = '';
    public $search = '';

    protected $listeners = [
        'cuenta_id' => 'actualizarCuentaId',
        'concepto_id' => 'actualizarConceptoId',
        'elminarAsiento'
    ];

    public function actualizarCuentaId($value)
    {
        $this->cuenta_id = $value;
        $this->emit('selectoresCargar');
    }

    public function actualizarConceptoId($value)
    {
        $this->concepto_id = $value;
        // $this->emit('selectoresCargar');
    }

    public function __construct()
    {
        $this->fechaInicio = date('Y-01-01');
        $this->fechaFin = date('Y-m-d');
        $this->fecha = date('Y-m-d H:i');
    }

    public function agregarCuenta($id)
    {
        if ($id !== null) {
            $planCuentas = PlanCuentas::find($id);
            if (!$planCuentas) {
                $this->dispatchBrowserEvent('alerta', [
                    'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
                    'color' => 'danger',
                    'mensaje' => 'La cuenta contable seleccionada no existe'
                ]);
                $this->search = '';
                return;
            }

            $this->asientos[] = [
                'id' => count($this->asientos) + 1,
                'cuenta_id' => $id,
                'cuenta_codigo' => $planCuentas->codigo,
                'cuenta_nombre' => $planCuentas->nombre,
                'sedes_centro_costos' => 0,
                'debe_haber' => true,
                'debe' => '0.00',
                'haber' => '0.00',
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

        // $this->emit('selectoresCargar');
    }

    public function elminarCuenta($key)
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

    public function actualizarValores($key, $valor)
    {
        if ($this->asientos[$key]['debe_haber']) {
            $this->asientos[$key]['debe'] = $valor;
            $this->asientos[$key]['haber'] = '0.00';
        } else {
            $this->asientos[$key]['debe'] = '0.00';
            $this->asientos[$key]['haber'] = $valor;
        }
        // $this->emit('selectoresCargar');
    }

    public function actualizarDebHaber($key)
    {
        $this->asientos[$key]['debe'] = '0.00';
        $this->asientos[$key]['haber'] = '0.00';
        $this->asientos[$key]['debe_haber'] = !$this->asientos[$key]['debe_haber'];
        // $this->emit('selectoresCargar');
    }

    public function actualizarCentro($key, $valor)
    {
        $this->asientos[$key]['sedes_centro_costos'] = $valor;
        // $this->emit('selectoresCargar');
    }

    public function store()
    {
        $this->validate(
            [
                'fecha' => 'required',
                'concepto_id' => 'required',
                'descripcion' => 'required',
            ],
            [
                'fecha.required' => 'La fecha es obligatoria',
                'concepto_id.required' => 'El concepto es obligatorio.',
                'descripcion.required' => 'La Descripcion es obligatoria.',
            ]
        );

        if (count($this->asientos) < 2) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Asiento incompleto</b>',
                'color' => 'danger',
                'mensaje' => 'El asiento debe tener al menos dos cuentas contables.'
            ]);
            return;
        }

        //Validar descuadre de asiento
        $descuadrado = false;
        $totalDebe = array_reduce($this->asientos, function ($carry, $item) {
            return $carry + (float) $item['debe'];
        }, 0.00);

        $totalHaber = array_reduce($this->asientos, function ($carry, $item) {
            return $carry + (float) $item['haber'];
        }, 0.00);
        $totalDebe  = number_format($totalDebe, 2, '.', '');
        $totalHaber  = number_format($totalHaber, 2, '.', '');
        //dd($totalDebe, $totalHaber, ($totalDebe != $totalHaber));

        if ($totalDebe != $totalHaber) {
            $descuadrado = true;
            /*
            $color = 'danger';
            $mensaje = 'El asiento manual no se encuentra correctamente cuadrado, existen diferencias entre el Debe y el Haber';
            $data = [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            */


            $titulo = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>No cuadra el asiento</b>';
            $mensaje = 'El asiento manual no se encuentra correctamente cuadrado, existen diferencias entre el Debe y el Haber';
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => $titulo,
                'color' => 'danger',
                'mensaje' => $mensaje
            ]);
            return;
        }

        //Validar poner centro de costos
        $validarCentro = true;
        foreach ($this->asientos as $key => $value) {
            if ($value['sedes_centro_costos'] == null || $value['sedes_centro_costos'] == 0) {
                $validarCentro = false;
            }
        }
        if (!$validarCentro) {
            $descuadrado = true;
            /*
            $color = 'danger';
            $mensaje = 'El centro de costos es obligatorio para todos los planes de cuentas';
            $data = [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            */

            $titulo = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Sin centro de costos</b>';
            $mensaje = 'El centro de costos es obligatorio para todos los planes de cuentas';
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => $titulo,
                'color' => 'danger',
                'mensaje' => $mensaje
            ]);
            return;
        }

        //Validar inicio de la fecha contable
        if (Auth::user()->company->fecha_inicio_contable && Auth::user()->company->fecha_inicio_contable != null) {
            $fechaInicio = Auth::user()->company->fecha_inicio_contable;
            if ($this->fecha < $fechaInicio) {
                $descuadrado = true;
                /*
                $color = 'danger';
                $mensaje = 'La fecha en la que se intenta crear el asiento esta antes de la fecha de inicio contable';
                $data = [
                    'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
                    'color' => $color,
                    'mensaje' => $mensaje
                ];
                $this->dispatchBrowserEvent('alerta', $data);
                */

                $titulo = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Error en la fecha</b>';
                $mensaje = 'La fecha en la que se intenta crear el asiento esta antes de la fecha de inicio contable';
                $this->dispatchBrowserEvent('alertaGrande', [
                    'titulo' => $titulo,
                    'color' => 'danger',
                    'mensaje' => $mensaje
                ]);
                return;
            }
        }


        if (!$descuadrado) {
            if ($this->id_seleccionado == 0) {
                $header = new AsientosHeader();
                $header->company_id = Auth::user()->company_id;
                $color = 'success';
                $mensaje = 'Se crea correctamete el Asiento Manual';
                $data = [
                    'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
                    'color' => $color,
                    'mensaje' => $mensaje
                ];
            } else {
                $header = AsientosHeader::find($this->id_seleccionado);
                $color = 'warning';
                $mensaje = 'Se modificó correctamete el Asiento Manual';
                $data = [
                    'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
                    'color' => $color,
                    'mensaje' => $mensaje
                ];
            }
            $header->manual = true;
            $header->fecha_contable = $this->fecha;
            $header->fecha_creacion = date('Y-m-d H:i:s');
            $header->user_created = Auth::user()->id;
            $header->concepto_id = $this->concepto_id;
            $header->descripcion = $this->descripcion;
            $header->status = true;
            $header->save();

            //Eliminar detalles
            AsientosDetalle::where('company_id', Auth::user()->company_id)->where('asientos_header_id', $header->id)->delete();
            foreach ($this->asientos as $key => $value) {
                $detalle = new AsientosDetalle();
                $detalle->company_id = Auth::user()->company_id;
                $detalle->asientos_header_id = $header->id;
                $detalle->plan_cuentas_id = $value['cuenta_id'];
                $detalle->sedes_centro_costos_id = ($value['sedes_centro_costos'] !== 0) ? $value['sedes_centro_costos'] : null;
                $detalle->debe_haber = $value['debe_haber'];
                $detalle->valor = ($value['debe_haber']) ? $value['debe'] : $value['haber'];
                $detalle->fecha_creacion = $this->fecha;
                $detalle->user_created = Auth::user()->id;
                $detalle->save();
            }

            if (!AsientosHeader::estaCuadrado($header->id)) {
                AsientosDetalle::where('company_id', Auth::user()->company_id)->where('asientos_header_id', $header->id)->delete();
                $header->delete();

                $this->dispatchBrowserEvent('alertaGrande', [
                    'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>No cuadra el asiento</b>',
                    'color' => 'danger',
                    'mensaje' => 'El asiento no se guardo porque los totales finales de Debe y Haber no coinciden.'
                ]);
                return;
            }

            $this->dispatchBrowserEvent('alerta', $data);
            $this->dispatchBrowserEvent('closeModal');
        }
    }

    public function abrirModal($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();
        if ($this->id_seleccionado !== 0) {
            $header = AsientosHeader::find($this->id_seleccionado);
            $detalle = AsientosDetalle::where('company_id', Auth::user()->company_id)
                ->where('asientos_header_id', $this->id_seleccionado)
                ->orderBy('debe_haber', 'DESC')
                ->orderBy('id')
                ->get();
            $this->concepto_id = $header->concepto_id;
            $this->fecha = date('Y-m-d H:i', strtotime($header->fecha_creacion));
            $this->descripcion = $header->descripcion;
            foreach ($detalle as $key => $value) {
                $planCuentas = PlanCuentas::find($value->plan_cuentas_id);
                $this->asientos[] = [
                    'id' => $value->asientos,
                    'cuenta_id' => $value->plan_cuentas_id,
                    'cuenta_codigo' => optional($planCuentas)->codigo ?? 'Sin cuenta',
                    'cuenta_nombre' => optional($planCuentas)->nombre ?? 'Cuenta no encontrada',
                    'sedes_centro_costos' => $value->sedes_centro_costos_id,
                    'debe_haber' => $value->debe_haber,
                    'debe' => ($value->debe_haber) ? $value->valor : '0.00',
                    'haber' => ($value->debe_haber) ? '0.00' : $value->valor,
                ];
            }
        }
        // $this->emit('selectoresCargar');
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'cuenta_id', 'fecha', 'descripcion', 'concepto_id']);
        $this->asientos = [];
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function confirmarElminarAsiento($id)
    {
        $boveda = AsientosHeader::find($id);
        $data = [
            'id' => $id
        ];
        $this->dispatchBrowserEvent('notificarEliminar', $data);
    }

    public function elminarAsiento($id)
    {
        AsientosDetalle::where('company_id', Auth::user()->company_id)->where('asientos_header_id', $id)->delete();
        AsientosHeader::find($id)->delete();
        $color = 'danger';
        $mensaje = 'Se elimino correctamente, el Asiento Contable';
        $data = [
            'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function updated($property)
    {
        if (
            in_array($property, [
                'fechaInicio',
                'fechaFin',
                'searchAsiento',
                'searchAsientoDescripcion',
            ])
        ) {
            $this->resetPage();
        }
    }

    public function mount()
    {
        $this->conceptos = Conceptos::with('tipoConcepto')
            ->where('company_id', Auth::user()->company_id)
            ->get();

        $this->centro_costos = SedesCentroCostos::with(['sede', 'centroCostos'])
            ->where('company_id', Auth::user()->company_id)
            ->get();
        $this->emit('selectoresCargar');
    }

    public function render()
    {
        $asientosHeader = AsientosHeader::select(
            'asientos_header.*',
            DB::raw("(SELECT COALESCE(SUM(valor), 0) FROM asientos_detalle WHERE asientos_detalle.asientos_header_id = asientos_header.id AND asientos_detalle.debe_haber = true AND asientos_detalle.status = true) as suma_debe"),
            DB::raw("(SELECT COALESCE(SUM(valor), 0) FROM asientos_detalle WHERE asientos_detalle.asientos_header_id = asientos_header.id AND asientos_detalle.debe_haber = false AND asientos_detalle.status = true) as suma_haber")
        )
            ->with([
            'concepto.tipoConcepto',
            'detalles.planCuentas',
            'detalles.userCreated',
        ])
            ->where('company_id', Auth::user()->company_id)
            ->where('status', true)
            ->whereBetween(
                DB::raw('DATE(fecha_contable)'),
                [
                    Carbon::parse($this->fechaInicio)->format('Y-m-d'),
                    Carbon::parse($this->fechaFin)->format('Y-m-d'),
                ]
            )
            ->when($this->searchAsiento !== '' || $this->searchAsientoDescripcion !== '', function ($query) {
                $query->where(function ($q) {
                    if ($this->searchAsiento !== '') {
                        $q->where('id', 'like', '%' . $this->searchAsiento . '%');
                    }

                    if ($this->searchAsientoDescripcion !== '') {
                        $method = $this->searchAsiento !== '' ? 'orWhere' : 'where';
                        $q->{$method}('descripcion', 'like', '%' . $this->searchAsientoDescripcion . '%');
                    }
                });
            })
            ->when($this->manual !== '', function ($query) {
                $query->where('manual', (bool) $this->manual);
            })
            ->orderByDesc('asientos_header.fecha_creacion')
            ->orderByDesc('asientos_header.id')
            ->paginate(20);

        $tipoConceptos = TipoConcepto::where('company_id', Auth::user()->company_id)
            ->where('status', true)
            ->get();

        $planCuentas = PlanCuentas::where('company_id', Auth::user()->company_id)->where('status', true)->get();

        $sedes = Sedes::where('company_id', Auth::user()->company_id)
            ->where('status', true)
            ->get();

        $planCuentasBuscador = $this->search !== ''
            ? PlanCuentas::where('company_id', Auth::user()->company_id)
                ->where('status', true)
                ->where(
                    function ($q) {
                        return $q->where('nombre', 'like', "%{$this->search}%")
                            ->orWhere('codigo', 'like', "%{$this->search}%");
                    }
                )
                ->paginate(10)
            : collect();

        return view(
            'livewire.asientos.asientos-component',
            compact(
                'asientosHeader',
                'planCuentas',
                'tipoConceptos',
                'sedes',
                'planCuentasBuscador'
            )
        );
    }
}
