<?php

namespace App\Http\Livewire\Prestamos;

use App\Models\Company;
use App\Models\Prestamos;
use App\Models\RecurrenciaPrestamos;
use App\Models\PlanCuentas;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PrestamosComponent extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $tipoFiltro = '';
    public function updatingSearch() { $this->resetPage(); }
    public function updatingTipoFiltro() { $this->resetPage(); }

    public $seleccionado = '';
    public $name = '';
    public $interes = '';
    public $interes_anual = '';
    public $fondo_desgravamen = '';
    public $tipo = '';
    public $plan_cuenta_id;
    public $valor_minimo = '';
    public $valor_maximo = '';
    public $edad_minima = '';
    public $edad_maxima = '';
    public $diario = '';
    public $periodo_id = '';

    public $lleva_contabilidad = true;

    public $administrativo_porcentaje_valor = '';
    public $gasto_administrativo = '';
    public $encaje = 0;
    public $ahorro = 0;
    public $suma_valores_gastos_prestamo = 0;
    public $encaje_credito_cuenta = '';
    public $encaje_porcentaje_valor = '';
    public $encaje_cantidad = '';

    public $primer_gasto = '';
    public $segundo_gasto = '';
    public $tercer_gasto = '';
    public $porcentaje_primer_gasto = 0;
    public $porcentaje_segundo_gasto = 0;
    public $porcentaje_tercer_gasto = 0;
    public $calculo_simple = 0;
    public $letra_credito = '';


    public function creditoSelect($id)
    {
        $this->seleccionado = $id;
        if ($id == 0) {
            $this->name = '';
            $this->interes = '';
            $this->interes_anual = '';
            $this->fondo_desgravamen = '';
            $this->tipo = '';
            $this->plan_cuenta_id = '';
            $this->valor_minimo = '';
            $this->valor_maximo = '';
            $this->edad_minima = '';
            $this->edad_maxima = '';
            $this->diario = '';
            $this->periodo_id = '';
            $this->lleva_contabilidad = true;
            $this->gasto_administrativo = '';
            $this->administrativo_porcentaje_valor = '';
            $this->encaje = 0;
            $this->suma_valores_gastos_prestamo = 0;
            $this->encaje_credito_cuenta = '';
            $this->encaje_porcentaje_valor = '';
            $this->encaje_cantidad = '';
            $this->primer_gasto = '';
            $this->segundo_gasto = '';
            $this->tercer_gasto = '';
            $this->porcentaje_primer_gasto = 0;
            $this->porcentaje_segundo_gasto = 0;
            $this->porcentaje_tercer_gasto = 0;
            $this->ahorro = '';
            $this->calculo_simple = 0;
            $this->letra_credito = '';
        } else {
            $prestamo = Prestamos::find($id);
            $this->name = $prestamo->name;
            $this->interes = $prestamo->interes;
            $this->interes_anual = $prestamo->interes_anual;
            $this->fondo_desgravamen = $prestamo->fondo_desgravamen;
            $this->tipo = $prestamo->tipo;
            $this->plan_cuenta_id = $prestamo->plan_cuenta_id;
            $this->valor_minimo = $prestamo->valor_minimo;
            $this->valor_maximo = $prestamo->valor_maximo;
            $this->edad_minima = $prestamo->edad_minima;
            $this->edad_maxima = $prestamo->edad_maxima;
            $this->diario = $prestamo->diario;
            $this->periodo_id = $prestamo->periodo_id;
            $this->lleva_contabilidad = (bool) $prestamo->lleva_contabilidad;
            $this->gasto_administrativo = $prestamo->gasto_administrativo;
            $this->administrativo_porcentaje_valor = $prestamo->administrativo_porcentaje_valor;
            $this->encaje = $prestamo->encaje;
            $this->encaje_credito_cuenta = $prestamo->encaje_credito_cuenta;
            $this->encaje_porcentaje_valor = $prestamo->encaje_porcentaje_valor;
            $this->encaje_cantidad = $prestamo->encaje_cantidad;
            $this->primer_gasto = $prestamo->primer_gasto;
            $this->segundo_gasto = $prestamo->segundo_gasto;
            $this->tercer_gasto = $prestamo->tercer_gasto;

            $this->porcentaje_primer_gasto = $prestamo->porcentaje_primer_gasto;
            $this->porcentaje_segundo_gasto = $prestamo->porcentaje_segundo_gasto;
            $this->porcentaje_tercer_gasto = $prestamo->porcentaje_tercer_gasto;

            $this->ahorro = $prestamo->ahorro;
            $this->suma_valores_gastos_prestamo = $prestamo->suma_valores_gastos_prestamo;
            $this->calculo_simple = $prestamo->calculo_simple;
            $this->letra_credito = $prestamo->letra_credito;
        }
    }
    public function guardarCredito()
    {

        $this->validate(
            [
                'name' => 'required',
                'interes' => 'required',
                'interes_anual' => 'required',
                //'plan_cuenta_id' => 'required|numeric',
                'tipo' => 'required',
                'valor_minimo' => 'required',
                'valor_maximo' => 'required',
                'edad_minima' => 'required',
                'edad_maxima' => 'required',
                'diario' => 'required',
                'periodo_id' => 'required',
                'lleva_contabilidad' => 'required',
                'encaje' => 'required',
                'encaje_credito_cuenta' => ($this->encaje == 1) ? 'required' : '',
                'encaje_porcentaje_valor' => ($this->encaje == 1) ? 'required' : '',
                'encaje_cantidad' => ($this->encaje == 1) ? 'required' : '',
                'administrativo_porcentaje_valor' => 'required',
                'porcentaje_primer_gasto' => 'required',
                'porcentaje_segundo_gasto' => 'required',
                'porcentaje_tercer_gasto' => 'required',
                'letra_credito' => ($this->suma_valores_gastos_prestamo == 1) ? 'required' : '',
            ],
            [
                'name.required' => 'Debe Ingresar un nombre del prestamo.',
                'interes.required' => 'Debe ingresar el interes mensual.',
                'interes_anual.required' => 'Debe ingresar el interes anual.',
                'tipo.required' => 'Debe seleccionar el tipo de prestamo .',
                'valor_minimo.required' => 'Debe ingresar el valor minimo que se puede prestar.',
                'valor_maximo.required' => 'Debe ingresar el valor maximo que se puede prestar.',
                'edad_minima.required' => 'Debe ingresar la edad minima para adquirir el prestamo.',
                'edad_maxima.required' => 'Debe ingresar la edad maxima para adquirir el prestamo.',
                'diario.required' => 'seleccione el tipo de cobranza.',
                'periodo_id.required' => 'Debe seleccionar el preiodo de las letras.',
                'encaje.required' => 'Debe seleccionar si el crédito requiere encaje.',
                'encaje_credito_cuenta.required' => 'Debe seleccionar si el encaje debe ser del credito o de los ahorros.',
                'encaje_porcentaje_valor.required' => 'Debe ingresar si es por valor o por un porcentaje del crédito.',
                'encaje_cantidad.required' => 'Debe ingresar el valor del encaje.',
                'administrativo_porcentaje_valor.required' => 'Debe seleccionar entre valor o porcentaje del gasto administrativo.',

                'porcentaje_primer_gasto.required' => 'Debe seleccionar si el gasto es porcentaje o valor.',
                'porcentaje_segundo_gasto.required' => 'Debe seleccionar si el gasto es porcentaje o valor.',
                'porcentaje_tercer_gasto.required' => 'Debe seleccionar si el gasto es porcentaje o valor.',
                'letra_credito.required' => 'Debe seleccionar si el gasto se suma al crédito o a la letra.',

            ]
        );
        
        if ($this->administrativo_porcentaje_valor == "PORCENTAJE" && $this->gasto_administrativo >= 100) {
            $color = 'danger';
            $mensaje = 'Cuando el gasto es admisnitrativo es por porcentaje no puede ser igual o mañor a 100';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }
        if ($this->encaje_porcentaje_valor == "PORCENTAJE" && $this->encaje_cantidad >= 100) {
            $color = 'danger';
            $mensaje = 'Cuando el encaje es por porcentaje no puede ser igual o mañor a 100';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }
        if ($this->seleccionado == 0) {
            $prestamo = new Prestamos();
            $prestamo->company_id = Auth::user()->company_id;
        } else {
            $prestamo = Prestamos::find($this->seleccionado);
        }
        $prestamo->name = strtoupper($this->name);
        $prestamo->interes = $this->interes;
        $prestamo->interes_anual = $this->interes_anual;
        $prestamo->fondo_desgravamen = $this->fondo_desgravamen;
        $prestamo->tipo = $this->tipo;
        $prestamo->plan_cuenta_id = $this->plan_cuenta_id;
        $prestamo->valor_minimo = $this->valor_minimo;
        $prestamo->valor_maximo = $this->valor_maximo;
        $prestamo->edad_minima = $this->edad_minima;
        $prestamo->edad_maxima = $this->edad_maxima;
        $prestamo->diario = $this->diario;
        $prestamo->periodo_id = $this->periodo_id;
        $prestamo->lleva_contabilidad = $this->lleva_contabilidad;
        $prestamo->gasto_administrativo = $this->gasto_administrativo;
        $prestamo->administrativo_porcentaje_valor = $this->administrativo_porcentaje_valor;
        $prestamo->encaje = $this->encaje;
        $prestamo->encaje_credito_cuenta = ($this->encaje) ? $this->encaje_credito_cuenta : null;
        $prestamo->encaje_porcentaje_valor = ($this->encaje) ? $this->encaje_porcentaje_valor : null;
        $prestamo->encaje_cantidad = ($this->encaje) ? $this->encaje_cantidad : 0;
        $prestamo->primer_gasto = $this->primer_gasto;
        $prestamo->segundo_gasto = $this->segundo_gasto;
        $prestamo->tercer_gasto = $this->tercer_gasto;

        $prestamo->porcentaje_primer_gasto = $this->porcentaje_primer_gasto;
        $prestamo->porcentaje_segundo_gasto = $this->porcentaje_segundo_gasto;
        $prestamo->porcentaje_tercer_gasto = $this->porcentaje_tercer_gasto;

        $prestamo->ahorro = $this->ahorro;
        $prestamo->suma_valores_gastos_prestamo = $this->suma_valores_gastos_prestamo;
        $prestamo->calculo_simple = $this->calculo_simple;
        $prestamo->letra_credito = $this->letra_credito;
        $prestamo->lleva_contabilidad = $this->lleva_contabilidad;
        $prestamo->save();

        $this->dispatchBrowserEvent('closeModal');
    }

    public function cambioLetra($id)
    {
        $tipo = Prestamos::find($id);
        $tipo->letra_cambio = ($tipo->letra_cambio) ? false : true;
        $tipo->save();
    }

    public function cambioPagare($id)
    {
        $tipo = Prestamos::find($id);
        $tipo->pagare = ($tipo->pagare) ? false : true;
        $tipo->save();
    }

    public function cambioContrato($id)
    {
        $tipo = Prestamos::find($id);
        $tipo->contrato = ($tipo->contrato) ? false : true;
        $tipo->save();
    }
    public function render()
    {
        $company = Company::find(Auth::user()->company_id);
        $prestamos = Prestamos::where('status', 'A')->where('company_id', Auth::user()->company_id)->where('name', 'like', '%' . trim($this->search) . '%')->when(in_array($this->tipoFiltro, ['A', 'F'], true), function ($query) { $query->where('tipo', $this->tipoFiltro); })->orderBy('name')->orderBy('id')->paginate(10);
        $recurrencia = RecurrenciaPrestamos::where('company_id', Auth::user()->company_id)->get();
        // Obtener todas las cuentas marcadas como préstamo (pueden ser padres o hijos)
        $cuentasMarcadas = PlanCuentas::where('company_id', Auth::user()->company_id)
            ->where('prestamo', 1)
            ->select('nivel1', 'nivel2', 'nivel3')
            ->distinct()
            ->get();

        // Buscar las cuentas PADRES (Nivel 3) que coincidan con la estructura de las marcadas
        // Esto asegura que si marcaron un hijo (Nivel 4), mostremos al padre.
        $planCuentas = PlanCuentas::where('company_id', Auth::user()->company_id)
            ->whereNull('nivel4') // Solo queremos padres (Nivel 3)
            ->whereNotNull('nivel3') // Asegurar que sea Nivel 3
            ->where(function ($query) use ($cuentasMarcadas) {
                foreach ($cuentasMarcadas as $cuenta) {
                    $query->orWhere(function ($sub) use ($cuenta) {
                        $sub->where('nivel1', $cuenta->nivel1)
                            ->where('nivel2', $cuenta->nivel2)
                            ->where('nivel3', $cuenta->nivel3);
                    });
                }
            })
            ->get();
        return view('livewire.prestamos.prestamos-component', compact('prestamos', 'recurrencia', 'company', 'planCuentas'));
    }
}
