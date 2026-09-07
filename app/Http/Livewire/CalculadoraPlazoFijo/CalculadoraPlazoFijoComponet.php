<?php

namespace App\Http\Livewire\CalculadoraPlazoFijo;

use App\Models\TipoAhorrosProgramadosDetalle;
use App\Models\Company;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalculadoraPlazoFijoComponet extends Component {

    public $valoPlazo = '';
    public $interesPlazo = '';
    public $pagoPlazo = '';
    public $resultado = '';
    public $fechaInicio = '';
    protected $rules = [
        'fechaInicio' => 'date|after_or_equal:today',
    ];
    protected $messages = [
        'fechaInicio.after_or_equal' => 'La fecha no puede ser anterior a la fecha actual, se colocará automaticamente la fecha actual.',
    ];
    public $montoVer = '';
    public $interesVer = '';
    public $plazoVer = '';
    public $valorGanadoVer = '';
    public $penalidadVer = '';
    public $totalPagarVer = '';
    public $mensualizados = [];
    public $fechaPago = '';
    public $diasManual = '';
    public $usarManual = '';
    public $activarManual = false;

    public function updated($propertyName) {
        $this->validateOnly($propertyName);
    }

    public function generarPlazo() {
        $this->validate(
                [
                    'valoPlazo' => 'required',
                    'interesPlazo' => 'required',
                    'pagoPlazo' => 'required',
                    'fechaInicio' => 'required',
                    'diasManual' => ($this->usarManual == true) ? 'required' : '',
                ],
                [
                    'valoPlazo.required' => 'Necesita ingresar un valor.',
                    'interesPlazo.required' => 'Necesita seleccionar un plazo.',
                    'pagoPlazo.required' => 'Necesita seleccionar la forma de desembolso.',
                    'fechaInicio.required' => 'Necesita seleccionar una fecha para calcular los desembolsos.',
                    'diasManual.required' => 'Usted selecciono ingresar el plazo manual este campo es obligatorio.',
                ]
        );
        $plazos = TipoAhorrosProgramadosDetalle::find($this->interesPlazo);
        $calculo = ($this->valoPlazo) * ($plazos->interes / 100);

        $diasAlCalculo = $plazos->rango_max;
        if ($this->usarManual) {
            $diasAlCalculo = $this->diasManual;
        }

        $fecha = \Carbon\Carbon::parse($this->fechaInicio);
        $fechaMasDias = $fecha->addDays($diasAlCalculo);
        $fechaPago = $fechaMasDias->toDateString();

        $this->montoVer = number_format($this->valoPlazo, 2);
        $this->interesVer = number_format($plazos->interes, 2);
        $this->plazoVer = $diasAlCalculo . ' Días';
        $entero = intdiv($diasAlCalculo, 30);

        $this->valorGanadoVer = (($calculo / 12) * $entero );
        $company = Company::find(Auth::user()->company_id);
        $penalizado = 0;
        if ($company->comercial_name != "CAJA SAN JOSE ALTO") {
            $penalizado = $this->valorGanadoVer * (2 / 100);
        }

        $this->penalidadVer = number_format($penalizado, 2);
        $this->totalPagarVer = number_format(($this->valorGanadoVer - $this->penalidadVer), 2);
        $this->fechaPago = $fechaPago;
        $this->mensualizados = [];
        if ($this->pagoPlazo != 'C') {
            $data = array();
            $entero = intdiv($diasAlCalculo, 30);
            $interes = $this->interesVer / $entero;
            $ganado = number_format($this->valorGanadoVer / $entero, 2);
            $penali = 0;
            if ($company->comercial_name != "CAJA SAN JOSE ALTO") {
                $penali = number_format($penalizado / $entero, 2);
            }

            $fechaPago = $this->fechaInicio;
            for ($i = 1; $i <= $entero; $i++) {
                $fecha = \Carbon\Carbon::parse($fechaPago);
                $fechaMasDias = $fecha->addMonths(1);
                $fechaPago = $fechaMasDias->toDateString();
                $data [] = [
                    'interes' => number_format($interes, 2),
                    'ganado' => $ganado,
                    'penali' => $penali,
                    'totalPagarVer' => ($ganado - $penali),
                    'fechaPago' => $fechaPago,
                ];
            }
            $this->mensualizados = $data;
        }
    }

    public function toggleUsarManual() {
        if ($this->usarManual) {
            $this->activarManual = true;
        } else {
            $this->activarManual = false;
        }
    }

    public function onDiasManualChange() {
        if ($this->diasManual < 30) {
            $color = 'danger';
            $mensaje = 'El valor no puede ser inferior a 30 días, se colocara automáticamente el valor mínimo.';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            $this->diasManual = 30;
            return;
        }
    }

    public function render() {
        if ($this->fechaInicio == '') {
            $this->fechaInicio = date('Y-m-d');
        } else if ($this->fechaInicio < date('Y-m-d')) {
            $this->fechaInicio = date('Y-m-d');
        }
        $plazos = TipoAhorrosProgramadosDetalle::where('company_id', Auth::user()->company_id)
                        ->where('status', 1)->get();
        return view('livewire.calculadora-plazo-fijo.calculadora-plazo-fijo-componet', compact('plazos'));
    }
}
