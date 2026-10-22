<?php

namespace App\Http\Livewire\PlanCuentas;

use App\Http\Controllers\PlanCuentas\PlanCuentasController;
use App\Models\AsientosDetalle;
use App\Models\Bancos;
use App\Models\ConfigPlanDetalle;
use App\Models\GastosPlanCuentas;
use App\Models\PlanCuentas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class PlanCuentasComponent extends Component
{
    use WithFileUploads;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $archivo = '';
    public $id_seleccionado = 0;
    public $nombre = '';
    public $codigo = '';
    public $prestamo = false;
    public $tiempo_inicio = '';
    public $tiempo_fin = '';
    public $gasto = false;
    public $utilidad = false;
    public $estado_resultados = false;
    public $saldo = false;
    public $search = '';
    public $banco_id = '';
    public $accion_cuenta = '';
    public $filtroUso = '';
    public $filtroOrigen = '';
    public $porPagina = 25;
    public $id_eliminar;

    public function updated($property)
    {
        if (in_array($property, ['search', 'filtroUso', 'filtroOrigen', 'porPagina'], true)) $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'filtroUso', 'filtroOrigen', 'porPagina']);
        $this->resetPage();
    }

    public function confirmarEliminar($id)
    {
        $this->id_eliminar = PlanCuentas::where('company_id', Auth::user()->company_id)
            ->where('creado_usuario', true)->findOrFail($id)->id;
        $this->dispatchBrowserEvent('pc-confirmar-eliminar');
    }

    public function cancelarEliminar()
    {
        $this->id_eliminar = null;
        $this->dispatchBrowserEvent('pc-cerrar-eliminar');
    }

    private function validarCuenta()
    {
        $this->nombre = trim($this->nombre);
        $this->codigo = trim($this->codigo);
        $cuentaActual = $this->id_seleccionado ? PlanCuentas::where('company_id', Auth::user()->company_id)->find($this->id_seleccionado) : null;
        $reglasCodigo = ['required', 'string', 'max:60'];
        if (!$cuentaActual || $cuentaActual->codigo !== $this->codigo) {
            $reglasCodigo[] = 'regex:/^[0-9]{1,2}(\.[0-9]{1,2}){0,6}$/';
        }
        $this->validate([
            'nombre' => 'required|string|max:60',
            'codigo' => $reglasCodigo,
            'tiempo_inicio' => 'nullable|integer|min:0',
            'tiempo_fin' => 'nullable|integer|min:0' . ($this->tiempo_inicio !== '' && $this->tiempo_inicio !== null ? '|gte:tiempo_inicio' : ''),
            'banco_id' => ['nullable', \Illuminate\Validation\Rule::exists('bancos', 'id')->where('company_id', Auth::user()->company_id)],
            'accion_cuenta' => 'nullable|in:SUMA,RESTA',
        ], [
            'codigo.regex' => 'Usa hasta 7 niveles de uno o dos dígitos separados por puntos (ejemplo: 1.01.02).',
            'tiempo_fin.gte' => 'El plazo final debe ser mayor o igual al inicial.'
        ]);
        foreach (['tiempo_inicio', 'tiempo_fin', 'banco_id', 'accion_cuenta'] as $campo) {
            if ($this->$campo === '') $this->$campo = null;
        }
    }

    public function verPlan($id)
    {
        $this->limpiarFormulario();
        $this->id_seleccionado = $id;
        if ($this->id_seleccionado !== 0) {
            $planCuentas = PlanCuentas::where('company_id', Auth::user()->company_id)->findOrFail($this->id_seleccionado);
            $this->nombre = $planCuentas->nombre;
            $this->codigo = $planCuentas->codigo;
            $this->saldo = $planCuentas->saldo;
            $this->prestamo = $planCuentas->prestamo;
            $this->tiempo_inicio = $planCuentas->tiempo_inicio;
            $this->tiempo_fin = $planCuentas->tiempo_fin;
            $this->gasto = $planCuentas->gasto;
            $this->utilidad = $planCuentas->utilidad;
            $this->estado_resultados = $planCuentas->estado_resultados;
            $this->banco_id = $planCuentas->banco_id;
            $this->accion_cuenta = $planCuentas->accion_cuenta;
        }
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'codigo', 'saldo', 'prestamo', 'gasto', 'utilidad', 'estado_resultados', 'tiempo_inicio', 'tiempo_fin', 'banco_id', 'accion_cuenta']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function actualizarPlan()
    {
        $this->validarCuenta();

        $codigoDuplicado = PlanCuentas::where('company_id', Auth::user()->company_id)
            ->where('codigo', $this->codigo)
            ->where('id', '!=', $this->id_seleccionado)
            ->exists();

        if ($codigoDuplicado) {
            $this->addError('codigo', 'Ya existe una cuenta con ese codigo en esta empresa.');
            return;
        }

        $planCuentas = PlanCuentas::find($this->id_seleccionado);
        if (!$planCuentas || $planCuentas->company_id != Auth::user()->company_id) {
            $this->dispatchBrowserEvent('alerta', [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
                'color' => 'danger',
                'mensaje' => 'La cuenta contable seleccionada no existe para esta empresa.'
            ]);
            return;
        }

        $planCuentas->company_id = Auth::user()->company_id;
        $planCuentas->nombre = mb_strtoupper($this->nombre, 'UTF-8');
        if ($planCuentas->codigo !== $this->codigo) {
            // Reconstruir la jerarquía únicamente cuando cambia el código.
            foreach (range(1, 7) as $nivel) $planCuentas->{'nivel' . $nivel} = null;
            foreach (explode('.', $this->codigo) as $key => $element) {
                $nivelKey = 'nivel' . ($key + 1);
                $planCuentas->$nivelKey = $element;
            }
        }
        $planCuentas->company_id = Auth::user()->company_id;
        $planCuentas->codigo = $this->codigo;
        $planCuentas->prestamo = $this->prestamo;
        $planCuentas->tiempo_inicio = $this->tiempo_inicio;
        $planCuentas->tiempo_fin = $this->tiempo_fin;
        $planCuentas->gasto = $this->gasto;
        $planCuentas->utilidad = $this->utilidad;
        $planCuentas->estado_resultados = $this->estado_resultados;
        $planCuentas->banco_id = $this->banco_id;
        $planCuentas->accion_cuenta = $this->accion_cuenta;
        $planCuentas->save();

        //Alerta
        $color = 'success';
        $mensaje = 'Cuenta contable actualizada correctamente.';
        $data = [
            'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function nuevoPlan()
    {
        $this->validarCuenta();

        $codigoDuplicado = PlanCuentas::where('company_id', Auth::user()->company_id)
            ->where('codigo', $this->codigo)
            ->exists();

        if ($codigoDuplicado) {
            $this->addError('codigo', 'Ya existe una cuenta con ese codigo en esta empresa.');
            return;
        }

        $planCuentas = new PlanCuentas();
        $planCuentas->company_id = Auth::user()->company_id;
        $planCuentas->nombre = mb_strtoupper($this->nombre, 'UTF-8');
        $array = explode('.', $this->codigo);
        foreach ($array as $key => $element) {
            $nivelKey = 'nivel' . ($key + 1);
            $planCuentas->$nivelKey = $element;
        }
        $planCuentas->codigo = $this->codigo;
        $planCuentas->creado_usuario = true;
        $planCuentas->prestamo = $this->prestamo;
        $planCuentas->tiempo_inicio = $this->tiempo_inicio;
        $planCuentas->tiempo_fin = $this->tiempo_fin;
        $planCuentas->gasto = $this->gasto;
        $planCuentas->utilidad = $this->utilidad;
        $planCuentas->estado_resultados = $this->estado_resultados;
        $planCuentas->banco_id = $this->banco_id;
        $planCuentas->accion_cuenta = $this->accion_cuenta;
        $planCuentas->save();

        $color = 'success';
        $mensaje = 'Cuenta contable creada correctamente.';
        $data = [
            'titulo' => '<i class="fa fa-plus" aria-hidden="true"></i> Advertencia',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->id_seleccionado = $planCuentas->id;
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function eliminarPlan($id)
    {
        $planCuentas = PlanCuentas::where('company_id', Auth::user()->company_id)->find($id);

        if (!$planCuentas) {
            $this->dispatchBrowserEvent('alerta', [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
                'color' => 'danger',
                'mensaje' => 'La cuenta contable no existe para esta empresa.'
            ]);
            return;
        }

        if (!$planCuentas->creado_usuario || $this->cuentaTieneMovimientos($id)) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Cuenta en uso</b>',
                'color' => 'danger',
                'mensaje' => 'Solo se pueden eliminar cuentas personalizadas sin asientos, configuraciones ni operaciones asociadas.'
            ]);
            return;
        }

        $planCuentas->delete();
        $color = 'success';
        $mensaje = 'Cuenta contable eliminada correctamente.';
        $data = [
            'titulo' => '<i class="fa fa-plus" aria-hidden="true"></i> Advertencia',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->id_seleccionado = 0;
        $this->id_eliminar = null;
        $this->dispatchBrowserEvent('pc-cerrar-eliminar');
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('alerta', $data);
    }

    private function cuentaTieneMovimientos($id)
    {
        return AsientosDetalle::where('company_id', Auth::user()->company_id)
            ->where('plan_cuentas_id', $id)
            ->exists()
            || ConfigPlanDetalle::where('company_id', Auth::user()->company_id)
            ->where('plan_cuentas_id', $id)
            ->exists()
            || GastosPlanCuentas::where('company_id', Auth::user()->company_id)
            ->where('plan_cuentas_id', $id)
            ->exists()
            || DB::table('prestamos')->where('company_id', Auth::user()->company_id)->where('plan_cuenta_id', $id)->exists()
            || DB::table('proveedores')->where('company_id', Auth::user()->company_id)->where('plan_cuenta_id', $id)->exists()
            || DB::table('credit_folder_headers')->where('company_id', Auth::user()->company_id)->where('cuenta_contable_id', $id)->exists()
            || DB::table('utilidads')->where('company_id', Auth::user()->company_id)->where('cuenta_pasivo_id', $id)->exists();
    }

    public function reiniciarNuevo()
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
    }

    // public function subirArchivo()
    // {
    //     $this->validate([
    //         'archivo' => 'required|mimes:xlsx|max:1024',
    //     ]);

    //     // Obtener la ruta temporal del archivo subido

    //     $rutaTemporal = $this->archivo->getRealPath();

    //     $data = Excel::toArray([], $rutaTemporal);

    //     foreach ($data[0] as $key => $row) {
    //         if ($key == 0) {
    //         } else {
    //             if ($row[0]) {
    //                 $plan = PlanCuentas::find($row[0]);
    //             } else {
    //                 $plan = new PlanCuentas();
    //             }
    //             $plan->company_id = Auth::user()->company_id;

    //             $plan->codigo = $row[1];

    //             $arrayResultante = explode(".", $row[1]);
    //             $arrayLimpio = array_filter($arrayResultante, function ($valor) {
    //                 return $valor !== "";
    //             });
    //             $plan->nivel1 = null;
    //             $plan->nivel2 = null;
    //             $plan->nivel3 = null;
    //             $plan->nivel4 = null;
    //             $plan->nivel5 = null;
    //             $plan->nivel6 = null;
    //             $plan->nivel7 = null;

    //             foreach ($arrayLimpio as $key => $value) {
    //                 if ($key == 0) {
    //                     $plan->nivel1 = $value;
    //                 } else if ($key == 1) {
    //                     $plan->nivel2 = $value;
    //                 } else if ($key == 2) {
    //                     $plan->nivel3 = $value;
    //                 } else if ($key == 3) {
    //                     $plan->nivel4 = $value;
    //                 } else if ($key == 4) {
    //                     $plan->nivel5 = $value;
    //                 } else if ($key == 5) {
    //                     $plan->nivel6 = $value;
    //                 } else if ($key == 6) {
    //                     $plan->nivel7 = $value;
    //                 }
    //             }
    //             $plan->nombre = strtoupper($row[2]);
    //             $plan->saldo = $row[3];
    //             $plan->save();
    //         }
    //     }
    // }

    public function render()
    {
        $cuenta = PlanCuentas::where('company_id', Auth::user()->company_id)->find($this->id_seleccionado);
        $resumen = PlanCuentas::where('company_id', Auth::user()->company_id)
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN prestamo = 1 THEN 1 ELSE 0 END) as prestamos, SUM(CASE WHEN gasto = 1 THEN 1 ELSE 0 END) as gastos, SUM(CASE WHEN utilidad = 1 THEN 1 ELSE 0 END) as utilidades')->first();
        $cuentas = PlanCuentas::select('id', 'nombre', 'codigo', 'creado_usuario', 'prestamo', 'gasto', 'utilidad', 'estado_resultados')
            ->where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('codigo', 'like', '%' . $this->search . '%');
            })
            ->when(in_array($this->filtroUso, ['prestamo', 'gasto', 'utilidad', 'estado_resultados'], true), function ($query) {
                $query->where($this->filtroUso, true);
            })
            ->when(in_array($this->filtroOrigen, ['sistema', 'personalizada'], true), function ($query) {
                $query->where('creado_usuario', $this->filtroOrigen === 'personalizada');
            });
        foreach (range(1, 7) as $nivel) $cuentas->orderByRaw('CAST(nivel' . $nivel . ' AS DECIMAL(10,0)) ASC');
        $cuentas = $cuentas->orderBy('codigo')->paginate(in_array((int) $this->porPagina, [25, 50, 100], true) ? (int) $this->porPagina : 25);
        $cuentaEliminar = $this->id_eliminar ? PlanCuentas::where('company_id', Auth::user()->company_id)->find($this->id_eliminar) : null;
        $bancos = Bancos::where('company_id', Auth::user()->company_id)->where(function ($query) {
            $query->where(function ($activos) {
                $activos->where('tipo_cuenta_id', '>', 0)->where('numero_cuenta', '!=', '')->where('status', true);
            });
            if ($this->banco_id) $query->orWhere('id', $this->banco_id);
        })->orderBy('nombre')->get();
        return view('livewire.plan-cuentas.plan-cuentas-component', compact('cuentas', 'cuenta', 'bancos', 'resumen', 'cuentaEliminar'));
    }
}
