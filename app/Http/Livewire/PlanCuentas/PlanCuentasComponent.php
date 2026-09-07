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

    public function verPlan($id)
    {
        $this->limpiarFormulario();
        $this->id_seleccionado = $id;
        if ($this->id_seleccionado !== 0) {
            $planCuentas = PlanCuentas::find($this->id_seleccionado);
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
        $this->validate([
            "nombre" => "required",
            "codigo" => "required"
        ]);

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
        $planCuentas->nombre = strtoupper($this->nombre);
        //vacir subniveles
        $planCuentas->nivel1 = null;
        $planCuentas->nivel2 = null;
        $planCuentas->nivel3 = null;
        $planCuentas->nivel4 = null;
        $planCuentas->nivel5 = null;
        $planCuentas->nivel6 = null;
        $planCuentas->nivel7 = null;
        $array = explode('.', $this->codigo);
        foreach ($array as $key => $element) {
            $nivelKey = 'nivel' . ($key + 1);
            $planCuentas->$nivelKey = $element;
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
        $mensaje = 'Se modifica el Plan de Cuentas correctamente...';
        $data = [
            'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function nuevoPlan()
    {
        $this->validate([
            "nombre" => "required",
            "codigo" => "required"
        ]);

        $codigoDuplicado = PlanCuentas::where('company_id', Auth::user()->company_id)
            ->where('codigo', $this->codigo)
            ->exists();

        if ($codigoDuplicado) {
            $this->addError('codigo', 'Ya existe una cuenta con ese codigo en esta empresa.');
            return;
        }

        $planCuentas = new PlanCuentas();
        $planCuentas->company_id = Auth::user()->company_id;
        $planCuentas->nombre = strtoupper($this->nombre);
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
        $mensaje = 'Se Creado el Plan de Cuentas correctamente...';
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

        if ($this->cuentaTieneMovimientos($id)) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Cuenta en uso</b>',
                'color' => 'danger',
                'mensaje' => 'No se puede eliminar una cuenta contable que ya tiene asientos, configuraciones o gastos asociados.'
            ]);
            return;
        }

        $planCuentas->delete();
        $color = 'danger';
        $mensaje = 'Se Creado eliminado Plan de Cuentas correctamente...';
        $data = [
            'titulo' => '<i class="fa fa-plus" aria-hidden="true"></i> Advertencia',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->id_seleccionado = 0;
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
            || DB::table('utilidads')->where('company_id', Auth::user()->company_id)->where('cuenta_pasivo_id', $id)->exists()
            ;
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
        $cuentas = PlanCuentas::select('id', 'nombre', 'codigo', 'creado_usuario', 'prestamo', 'gasto', 'utilidad', 'estado_resultados', DB::raw('REPLACE(codigo, ".", "") as codigoSolo'))
            ->where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('codigo', 'like', '%' . $this->search . '%');
            })
            ->orderBy('codigoSolo', 'ASC')
            ->paginate(50);
        $bancos = Bancos::where('company_id', Auth::user()->company_id)->where('tipo_cuenta_id', '>', 0)->where('numero_cuenta', '!=', '')->where('status', true)->get();
        return view('livewire.plan-cuentas.plan-cuentas-component', compact('cuentas', 'cuenta','bancos'));
    }
}
