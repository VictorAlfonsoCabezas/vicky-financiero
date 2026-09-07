<?php

namespace App\Http\Livewire\TipoAhorros;

use App\Models\Bancos;
use App\Models\HistoricoTransaccional;
use App\Models\TipoAhorros;
use App\Models\TipoAhorrosDetalle;
use App\Models\TipoAhorrosProgramadosDetalle;
use App\Models\TransaccionesCuentas;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TipoAhorrosComponent extends Component
{
    public $search = '';
    public $name = '';
    public $descargo_creditos = 0;
    public $programado = 0;
    public $cuenta_certificado = 0;
    public $ahorro_prestamo = 0;
    public $cuenta_certificado_valor_max = 0.00;
    public $valor_periodico = 0.00;
    public $description = '';
    public $edad_min = '';
    public $edad_max = '';
    public $interes = '';
    public $rango_valor = '';
    public $rango_tiempo = '';
    public $class = '';
    public $id_seleccionado = 0;
    public $detalle_tipo = 0;
    public $detalle_nombre = '';
    public $detalle_valor = 0.00;
    public $detalle_siglas = '';
    public $detalle_bloqueado = 0;
    public $detalle_suma = 0;
    public $interes_programado = 0;
    public $rango_min = 0;
    public $rango_max = 0;
    public $cuenta_encaje = 0;
    public $porcentaje_encaje = 0;
    public $cuentaPrincipal = 0;
    public $cuentaTransaccional = 0;
    public $administrativo_procentaje = 0;
    public $administrativo_valor = 0;
    public $administrativo_fecha = "";
    public $activarAdministrativo = "disabled";

    public $concepto = "";
    public $dia_mes = "";
    public $porcentaje_recaudacion = "";
    public $valor_recaudacion = "";
    public $dias_multa = "";
    public $porcentaje_multa = "";
    public $valor_multa = "";
    public $tipo_ahorro_id = "";
    public $banco_id = "";
    public $reglasAhorro = [];
    public $mostrarReglas = false;


    function tipoAhorro($id)
    {
        $this->id_seleccionado = $id;
        $this->resetInput();
        if ($id > 0) {
            $tipo = TipoAhorros::find($id);
            $this->programado = $tipo->programado;
            $this->descargo_creditos = $tipo->descargo_creditos;
            $this->cuenta_certificado = $tipo->cuenta_certificado;
            $this->cuenta_certificado_valor_max = $tipo->cuenta_certificado_valor_max;
            $this->name = $tipo->name;
            $this->description = $tipo->description;
            $this->edad_min = $tipo->edad_min;
            $this->edad_max = $tipo->edad_max;
            $this->interes = $tipo->interes;
            $this->rango_valor = $tipo->rango_valor;
            $this->rango_tiempo = $tipo->rango_tiempo;
            $this->class = $tipo->class;
            $this->valor_periodico = $tipo->valor_periodico;
            $this->ahorro_prestamo = $tipo->ahorro_prestamo;
            $this->cuenta_encaje = $tipo->cuenta_encaje;
            $this->porcentaje_encaje = $tipo->porcentaje_encaje;
            $this->cuentaPrincipal = ($tipo->cuenta_transaccional == 1) ?  0 : 1;
            $this->cuentaTransaccional =  ($tipo->cuenta_transaccional == 1) ?  1 : 0;
            $this->activarAdministrativo =  ($tipo->cuenta_transaccional == 0) ?  "disabled" : "";
            $this->administrativo_procentaje = $tipo->administrativo_procentaje;
            $this->administrativo_valor = $tipo->administrativo_valor;
            $this->administrativo_fecha = $tipo->administrativo_fecha;
            $reglasAhorro = TransaccionesCuentas::where('tipo_ahorro_id', $id)->get();
            foreach ($reglasAhorro as $val) {
                $banco =  Bancos::find($val->banco_id);
                $val->banco_nombre = $banco ? $banco->nombre : 'No asignado';
                $val->banco_numero = $banco ? $banco->numero_cuenta : 'No asignado';
            }

            $this->reglasAhorro = $reglasAhorro;
            $this->mostrarReglas = true;
        }
    }

    public function resetInput()
    {
        $this->programado = 0;
        $this->descargo_creditos = 0;
        $this->cuenta_certificado = 0;
        $this->cuenta_certificado_valor_max = 0.00;
        $this->ahorro_prestamo = 0;
        $this->name = '';
        $this->description = '';
        $this->edad_min = '';
        $this->edad_max = '';
        $this->interes = '';
        $this->rango_valor = '';
        $this->rango_tiempo = '';
        $this->cuenta_encaje = 0;
        $this->porcentaje_encaje = 0;
        $this->class = 'info';

        $this->cuentaPrincipal = 0;
        $this->cuentaTransaccional = 0;
        $this->activarAdministrativo =  "";
        $this->administrativo_procentaje = 0;
        $this->administrativo_valor = 0;
        $this->administrativo_fecha =  "";
        $this->reglasAhorro =  [];

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function actualizarCuentaEncaje()
    {
        if ($this->cuenta_encaje) {
            $this->programado = 0;
            $this->descargo_creditos = 0;
            $this->cuenta_certificado = 0;
            $this->cuenta_certificado_valor_max = 0.00;
            $this->ahorro_prestamo = 0;
        } else {
            $this->porcentaje_encaje = 0;
        }
    }
    public function desactivarCuentaEncaje()
    {
        $this->cuenta_encaje = 0;
        $this->porcentaje_encaje = 0;
    }

    public function activarDesactivarPrincipal()
    {
        //if ($this->cuentaPrincipal) {
        //    $this->cuentaTransaccional = 0;
        //    $this->administrativo_valor = 0;
        //    $this->administrativo_fecha = "";
        //    $this->activarAdministrativo = "disabled";
        //}
    }
    public function activarDesactivarTransaccional()
    {

        //if ($this->cuentaTransaccional) {
        //    $this->cuentaPrincipal = 0;
        //    $numeroPrincipales = TipoAhorros::where('company_id', Auth::user()->company_id)->where('cuenta_transaccional', 0)->count();
        //    if ($numeroPrincipales  == 0) {
        //        $this->cuentaTransaccional = 0;
        //        $this->administrativo_procentaje = 0;
        //        $this->administrativo_valor = 0;
        //        $this->activarAdministrativo = "disabled";
        //        $titulo = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia';
        //        $mensaje = "Debe tener al menos un tipo de ahorro principal";
        //        $this->dispatchBrowserEvent('alertaGrande', [
        //            'titulo' => $titulo,
        //            'color' => 'danger',
        //            'mensaje' => $mensaje
        //        ]);
        //        return;
        //    } else {
        //        $this->activarAdministrativo = "";
        //    }
        //} else {
        //    $this->cuentaTransaccional = 0;
        //    $this->administrativo_procentaje = 0;
        //    $this->administrativo_valor = 0;
        //    $this->activarAdministrativo = "disabled";
        //}
    }

    public function store()
    {
        $this->validate([
            "programado" => "required",
            "descargo_creditos" => "required",
            "cuenta_certificado" => "required",
            "name" => "required|min:3|max:50",
            "edad_min" => "required|numeric",
            "edad_max" => "required|numeric",
            "interes" => "required|numeric",
            "class" => "required",
        ]);

        if ($this->id_seleccionado > 0) {
            $tipo = TipoAhorros::find($this->id_seleccionado);
        } else {
            $tipo = new TipoAhorros();
        }
        $tipo->company_id = Auth::user()->company_id;
        $tipo->programado = $this->programado;
        $tipo->descargo_creditos = $this->descargo_creditos;
        $tipo->cuenta_certificado = $this->cuenta_certificado;
        $tipo->cuenta_certificado_valor_max = $this->cuenta_certificado_valor_max;
        $tipo->ahorro_prestamo = $this->ahorro_prestamo;
        $tipo->name = $this->name;
        $tipo->description = $this->description;
        $tipo->edad_min = $this->edad_min;
        $tipo->edad_max = $this->edad_max;
        $tipo->interes = $this->interes;
        $tipo->rango_valor = $this->rango_valor;
        $tipo->rango_tiempo = $this->rango_tiempo;
        $tipo->class = $this->class;
        $tipo->valor_periodico = $this->valor_periodico;
        $tipo->cuenta_encaje = $this->cuenta_encaje;
        $tipo->porcentaje_encaje = $this->porcentaje_encaje;
        if ($this->cuentaTransaccional) {
            $tipo->cuenta_transaccional = 1;
            $tipo->administrativo_procentaje = $this->administrativo_procentaje;
            $tipo->administrativo_valor = $this->administrativo_valor;
            $tipo->administrativo_fecha = $this->administrativo_fecha;
        } else {
            $tipo->cuenta_transaccional = 0;
            $tipo->administrativo_valor = 0;
            $tipo->administrativo_fecha = "";
        }
        $tipo->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function cambioEstado($id)
    {
        $tipo = TipoAhorros::find($id);
        $tipo->status = ($tipo->status) ? false : true;
        $tipo->save();
    }

    public function ver($id)
    {
        $this->resetInput2();
        $this->id_seleccionado = $id;
    }

    public function calculadora($id)
    {
        $this->resetInput3();
        $this->id_seleccionado = $id;
    }

    public function guardarValor()
    {
        $this->validate([
            "detalle_tipo" => "required|min:1",
            "detalle_nombre" => "required",
            "detalle_valor" => "required|numeric",
            "detalle_siglas" => "required",
            "detalle_bloqueado" => "required",
            "detalle_suma" => "required",
        ]);
        $tipo = new TipoAhorrosDetalle();
        $tipo->company_id = Auth::user()->company_id;
        $tipo->tipo_ahorros_id = $this->id_seleccionado;
        $tipo->afecta = $this->detalle_tipo;
        $tipo->nombre = $this->detalle_nombre;
        $tipo->valor = $this->detalle_valor;
        $tipo->siglas = $this->detalle_siglas;
        $tipo->bloqueado = $this->detalle_bloqueado;
        $tipo->suma = $this->detalle_suma;
        $tipo->save();
        $this->resetInput2();
    }

    public function guardarProgramado()
    {
        $this->validate([
            "interes_programado" => "required|min:1",
            "rango_min" => "required|numeric",
            "rango_max" => "required|numeric",
        ]);
        $tipo = new TipoAhorrosProgramadosDetalle();
        $tipo->company_id = Auth::user()->company_id;
        $tipo->tipo_ahorros_id = $this->id_seleccionado;
        $tipo->interes = $this->interes_programado;
        $tipo->rango_min = $this->rango_min;
        $tipo->rango_max = $this->rango_max;
        $tipo->status = true;
        $tipo->save();
        $this->resetInput3();
    }

    public function resetInput2()
    {
        $this->detalle_tipo = 0;
        $this->detalle_nombre = '';
        $this->detalle_valor = 0.00;
        $this->detalle_siglas = '';
        $this->detalle_bloqueado = 0;
        $this->detalle_suma = 0;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function resetInput3()
    {
        $this->interes_programado = '';
        $this->rango_min = '';
        $this->rango_max = '';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function eliminarDetalle($id)
    {
        $tipo = TipoAhorrosDetalle::find($id)->delete();
        $color = 'danger';
        $mensaje = 'Se Eliminó correctamente';
        $data = [
            'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function eliminarProgramado($id)
    {
        $tipo = TipoAhorrosProgramadosDetalle::find($id)->delete();
        $color = 'danger';
        $mensaje = 'Se Eliminó correctamente';
        $data = [
            'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function borrarTipo($id)
    {
        $tipo = TipoAhorros::find($id)->delete();
    }

    public function cambioBloqueado($id)
    {
        $tipoAhorroDetalle = TipoAhorrosDetalle::find($id);
        $tipoAhorroDetalle->bloqueado = !$tipoAhorroDetalle->bloqueado;
        $tipoAhorroDetalle->save();
    }

    public function cambioSuma($id)
    {
        $tipoAhorroDetalle = TipoAhorrosDetalle::find($id);
        $tipoAhorroDetalle->suma = !$tipoAhorroDetalle->suma;
        $tipoAhorroDetalle->save();
    }

    public function agregarReglaCuentaTransaacional()
    {
        $this->validate(
            [
                "concepto" => "required|min:3|max:50",
                "dia_mes" => "required|numeric",
                "porcentaje_recaudacion" => "required|numeric",
                "valor_recaudacion" => "required|numeric",
                "dias_multa" => "required|numeric",
                "porcentaje_multa" => "required|numeric",
                "valor_multa" => "required|numeric",

                "banco_id" => "required|numeric",
            ],
            [
                'concepto.required' => 'Debe ingresar un concepto para la regla.',
                'dia_mes.required' => 'Debe ingresar un día del mes para iniciar la regla.',
                'porcentaje_recaudacion.required' => 'Debe seleccionar el tipo de calculo para el inicio ce la regla.',
                'valor_recaudacion.required' => 'Debe ingresar valor para la regla.',
                'dias_multa.required' => 'Debe ingresar una cantidad de días para la multa de laregla.',
                'porcentaje_multa.required' => 'Debe seleccionar el tipo de calculo para la multa ce la regla.',
                'valor_multa.required' => 'Debe ingresar valor de la multa para la regla.',

                'banco_id.required' => 'Debe seleccionar a que banco estara unida la regla.',
            ]
        );


        $transaccion = new TransaccionesCuentas();
        $transaccion->concepto = $this->concepto;
        $transaccion->dia_mes = $this->dia_mes;
        $transaccion->porcentaje_recaudacion = $this->porcentaje_recaudacion;
        $transaccion->valor_recaudacion = $this->valor_recaudacion;
        $transaccion->dias_multa = $this->dias_multa;
        $transaccion->porcentaje_multa = $this->porcentaje_multa;
        $transaccion->valor_multa = $this->valor_multa;
        $transaccion->tipo_ahorro_id = $this->id_seleccionado;
        $transaccion->banco_id = $this->banco_id;
        $transaccion->save();

        $this->concepto = '';
        $this->dia_mes = '';
        $this->porcentaje_recaudacion = '';
        $this->valor_recaudacion = '';
        $this->dias_multa = '';
        $this->porcentaje_multa = '';
        $this->valor_multa = '';
        $this->banco_id = '';
        $reglasAhorro = TransaccionesCuentas::where('tipo_ahorro_id', $this->id_seleccionado)->get();
        foreach ($reglasAhorro as $val) {
            $banco =  Bancos::find($val->banco_id);
            $val->banco_nombre = $banco ? $banco->nombre : 'No asignado';
            $val->banco_numero = $banco ? $banco->numero_cuenta : 'No asignado';
        }
        $this->reglasAhorro =  $reglasAhorro;
    }

    public function eliminarFormaPagarLiquidar($id)
    {
        $existe = HistoricoTransaccional::where('transacciones_cuentas_id', $id)->count();
        if ($existe  == 0) {
            $elimnado =  TransaccionesCuentas::find($id)->delete();
        } else {
            $titulo = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Esta regla no puede ser eliminada</b>';
            $mensaje = 'No se puede eliminar esta regla por que ya esta en uso';
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => $titulo,
                'color' => 'danger',
                'mensaje' => $mensaje
            ]);
            return;
        }

        $reglasAhorro = TransaccionesCuentas::where('tipo_ahorro_id', $this->id_seleccionado)->get();
        foreach ($reglasAhorro as $val) {
            $banco =  Bancos::find($val->banco_id);
            $val->banco_nombre = $banco ? $banco->nombre : 'No asignado';
            $val->banco_numero = $banco ? $banco->numero_cuenta : 'No asignado';
        }
        $this->reglasAhorro =  $reglasAhorro;
    }


    public function render()
    {
        $tipos = TipoAhorros::where('company_id', Auth::user()->company_id)->where('name', 'like', '%' . $this->search . '%')->paginate(10);
        $detalle = TipoAhorrosDetalle::where('company_id', Auth::user()->company_id)->where('tipo_ahorros_id', $this->id_seleccionado)->get();
        $detalleProgramado = TipoAhorrosProgramadosDetalle::where('company_id', Auth::user()->company_id)->where('tipo_ahorros_id', $this->id_seleccionado)->get();
        $bancos = Bancos::where('tipo_cuenta_id', '>', 0)->where('numero_cuenta', '!=', '')->where('status', true)->get();
        return view('livewire.tipo-ahorros.tipo-ahorros-component', compact('tipos', 'detalle', 'detalleProgramado', 'bancos'));
    }
}
