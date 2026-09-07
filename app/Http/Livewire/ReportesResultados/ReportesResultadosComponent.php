<?php

namespace App\Http\Livewire\ReportesResultados;

use App\Models\AsientosDetalle;
use App\Models\Company;
use App\Models\PlanCuentas;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use PDF;

class ReportesResultadosComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $fechaInicio;
    public $fechaFin;
    public $filtroSaldo;
    public $perPage = 100;
    public $totalIngresos = 0;
    public $totalGastos = 0;
    public $resultadoPeriodo = 0;

    public function mount()
    {
        $this->fechaInicio = $this->fechaInicio ?: date('Y-01-01');
        $this->fechaFin = $this->fechaFin ?: date('Y-m-d');
        $this->filtroSaldo = $this->filtroSaldo ?: 'todos';
    }

    public function updated($property)
    {
        if (in_array($property, ['fechaInicio', 'fechaFin', 'filtroSaldo'])) {
            $this->resetPage();
        }
    }

    public function generarPdf()
    {
        $company = Company::find(Auth::user()->company_id);
        if ($company->photo != null && $company->photo != '') {
            $path = 'uploads/companies/' . $company->photo;
            if (!file_exists(public_path($path))) {
                $path = 'codev/negro.png';
            }
        } else {
            $path = 'codev/negro.png';
        }

        $data['company'] = $company;
        $data['imagen'] = base64_encode(file_get_contents(public_path($path)));
        $data['periodoDesde'] = $this->fechaInicio;
        $data['periodoHasta'] = $this->fechaFin;
        $data['nombreDocumento'] = 'ESTADO DE RESULTADOS DESDE ' . $this->fechaInicio . ' HASTA ' . $this->fechaFin;
        $data['planCuentas'] = $this->obtenerPlanCuentasConSaldos();
        $data['totalIngresos'] = $this->totalIngresos;
        $data['totalGastos'] = $this->totalGastos;
        $data['resultadoPeriodo'] = $this->resultadoPeriodo;

        $pdf = PDF::loadView('reportes.Pdf_Estado_resultados', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'ESTADO DE RESULTADOS DESDE ' . $this->fechaInicio . ' HASTA ' . $this->fechaFin . '.pdf');
    }

    public function cierreFom()
    {
        $this->dispatchBrowserEvent('alertaGrande', [
            'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Cierre contable pendiente</b>',
            'color' => 'warning',
            'mensaje' => 'Primero debe crearse la tabla de periodos contables para bloquear modificaciones por mes o anio.'
        ]);
    }

    public function render()
    {
        $planCuentas = $this->paginarColeccion($this->obtenerPlanCuentasConSaldos());

        return view('livewire.reportes-resultados.reportes-resultados-component', compact('planCuentas'));
    }

    private function obtenerPlanCuentasConSaldos()
    {
        $planCuentas = PlanCuentas::select(
            'plan_cuentas.id',
            'plan_cuentas.codigo',
            'plan_cuentas.nombre',
            'plan_cuentas.nivel1',
            'plan_cuentas.nivel2',
            'plan_cuentas.nivel3',
            'plan_cuentas.nivel4',
            'plan_cuentas.nivel5',
            'plan_cuentas.nivel6',
            'plan_cuentas.nivel7',
            'plan_cuentas.accion_cuenta'
        )
            ->where('plan_cuentas.company_id', Auth::user()->company_id)
            ->where('plan_cuentas.estado_resultados', 1)
            ->orderBy('plan_cuentas.codigo')
            ->get();

        foreach ($planCuentas as $planCuenta) {
            $nivelMax = $this->nivelMaximo($planCuenta);
            $planCuenta->saldo = number_format($this->saldoPorNivel($planCuenta, $nivelMax), 2, '.', '');
            $planCuenta->nivel = $nivelMax;
        }

        $this->calcularResumen($planCuentas);

        if ($this->filtroSaldo === 'diferente-de-cero') {
            $planCuentas = $planCuentas->filter(function ($plan) {
                return (float) $plan->saldo !== 0.00;
            });
        }

        return $planCuentas;
    }

    private function nivelMaximo($planCuenta)
    {
        $nivelMax = 1;

        for ($nivel = 1; $nivel <= 7; $nivel++) {
            if ($this->nivelTieneValor($planCuenta->{'nivel' . $nivel})) {
                $nivelMax = $nivel;
            }
        }

        return $nivelMax;
    }

    private function saldoPorNivel($planCuenta, $nivelMax)
    {
        $query = AsientosDetalle::leftJoin('plan_cuentas', 'asientos_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
            ->join('asientos_header', 'asientos_detalle.asientos_header_id', '=', 'asientos_header.id')
            ->where('plan_cuentas.company_id', Auth::user()->company_id)
            ->where('asientos_detalle.company_id', Auth::user()->company_id)
            ->where('asientos_header.company_id', Auth::user()->company_id)
            ->where('asientos_detalle.status', true)
            ->where('asientos_header.status', true)
            ->whereBetween(
                DB::raw('DATE(asientos_header.fecha_contable)'),
                [
                    Carbon::parse($this->fechaInicio)->format('Y-m-d'),
                    Carbon::parse($this->fechaFin)->format('Y-m-d'),
                ]
            );

        for ($nivel = 1; $nivel <= $nivelMax; $nivel++) {
            if ($this->nivelTieneValor($planCuenta->{'nivel' . $nivel})) {
                $query->where('plan_cuentas.nivel' . $nivel, $planCuenta->{'nivel' . $nivel});
            }
        }

        $debeMenosHaber = $query->sum(DB::raw("CASE WHEN asientos_detalle.debe_haber = 1 THEN asientos_detalle.valor ELSE -asientos_detalle.valor END"));

        return $this->saldoEstadoResultados($planCuenta->nivel1, $debeMenosHaber);
    }

    private function calcularResumen($planCuentas)
    {
        $ingresos = $planCuentas->first(function ($plan) {
            return (string) $plan->nivel1 === '5' && $plan->nivel === 1;
        });

        $gastos = $planCuentas->first(function ($plan) {
            return (string) $plan->nivel1 === '4' && $plan->nivel === 1;
        });

        $this->totalIngresos = round($ingresos ? (float) $ingresos->saldo : $this->saldoPorNivelUno('5'), 2);
        $this->totalGastos = round($gastos ? (float) $gastos->saldo : $this->saldoPorNivelUno('4'), 2);
        $this->resultadoPeriodo = round($this->totalIngresos - $this->totalGastos, 2);
    }

    private function saldoPorNivelUno($nivel1)
    {
        $debeMenosHaber = AsientosDetalle::leftJoin('plan_cuentas', 'asientos_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
            ->join('asientos_header', 'asientos_detalle.asientos_header_id', '=', 'asientos_header.id')
            ->where('plan_cuentas.company_id', Auth::user()->company_id)
            ->where('asientos_detalle.company_id', Auth::user()->company_id)
            ->where('asientos_header.company_id', Auth::user()->company_id)
            ->where('plan_cuentas.nivel1', $nivel1)
            ->where('asientos_detalle.status', true)
            ->where('asientos_header.status', true)
            ->whereBetween(
                DB::raw('DATE(asientos_header.fecha_contable)'),
                [
                    Carbon::parse($this->fechaInicio)->format('Y-m-d'),
                    Carbon::parse($this->fechaFin)->format('Y-m-d'),
                ]
            )
            ->sum(DB::raw("CASE WHEN asientos_detalle.debe_haber = 1 THEN asientos_detalle.valor ELSE -asientos_detalle.valor END"));

        return $this->saldoEstadoResultados($nivel1, $debeMenosHaber);
    }

    private function saldoEstadoResultados($nivel1, $debeMenosHaber)
    {
        if ((string) $nivel1 === '5') {
            return abs($debeMenosHaber * -1);
        }

        if ((string) $nivel1 === '4') {
            return abs($debeMenosHaber);
        }

        return $debeMenosHaber;
    }

    private function nivelTieneValor($valor)
    {
        return $valor !== null && trim((string) $valor) !== '';
    }

    private function paginarColeccion($coleccion)
    {
        $currentPage = Paginator::resolveCurrentPage();
        $currentItems = $coleccion->slice(($currentPage - 1) * $this->perPage, $this->perPage)->all();

        return new LengthAwarePaginator(
            $currentItems,
            $coleccion->count(),
            $this->perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );
    }
}
