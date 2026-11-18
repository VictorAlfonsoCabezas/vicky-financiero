<?php

namespace App\Http\Livewire\BalanceGeneral;

use App\Models\AsientosDetalle;
use App\Models\Company;
use App\Models\PlanCuentas;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use PDF;

class BalanceGeneralComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $fechaInicio;
    public $fechaFin;
    public $filtroSaldo = 'todos';
    public $perPage = 100;
    public $cuentaDetalleId;
    public $subcuentaDetalleId;

    protected $listeners = ['cerrarDetalleBalance'];

    private const VALOR_NETO = 'CASE WHEN asientos_detalle.debe_haber = 1 THEN asientos_detalle.valor ELSE -asientos_detalle.valor END';

    public function __construct()
    {
        $this->fechaInicio = date('Y-01-01');
        $this->fechaFin = date('Y-m-d');
    }
    //Pdf_Balance_general

    public function generarPdf()
    {
        $company = Company::find(Auth::user()->company_id);
        if ($company->photo != null && $company->photo != '') {
            app(\App\Services\CompanyLogoStorage::class)->resolve($company->photo);
            $path = 'uploads/companies/' . $company->photo;
            if (!file_exists(public_path($path))) {
                $path = 'vicky/icono-fondo-blanc.png';
            }
        } else {
            $path = 'vicky/icono-fondo-blanc.png';
        }
        $data['company'] = $company;
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);
        $data['periodoDesde'] = $this->fechaInicio;
        $data['periodoHasta'] = $this->fechaFin;
        $data['nombreDocumento'] = 'BALANCE GENERAL ' . $this->fechaInicio . ' HASTA ' . $this->fechaFin;


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
            'plan_cuentas.nivel7'
        )
            ->where('plan_cuentas.company_id', Auth::user()->company_id)
            ->where('plan_cuentas.estado_resultados', 0)
            ->get();

        foreach ($planCuentas as $key => $value) {
            $nivelMax = 1;
            if ($value->nivel1 !== null) {
                $nivelMax = 1;
            }
            if ($value->nivel1 !== null && $value->nivel2 !== null) {
                $nivelMax = 2;
            }
            if ($value->nivel1 !== null && $value->nivel2 !== null && $value->nivel3 !== null) {
                $nivelMax = 3;
            }
            if ($value->nivel1 !== null && $value->nivel2 !== null && $value->nivel3 !== null && $value->nivel4 !== null) {
                $nivelMax = 4;
            }
            if ($value->nivel1 !== null && $value->nivel2 !== null && $value->nivel3 !== null && $value->nivel4 !== null && $value->nivel5 !== null) {
                $nivelMax = 5;
            }
            if ($value->nivel1 !== null && $value->nivel2 !== null && $value->nivel3 !== null && $value->nivel4 !== null && $value->nivel5 !== null && $value->nivel6 !== null) {
                $nivelMax = 6;
            }
            if ($value->nivel1 !== null && $value->nivel2 !== null && $value->nivel3 !== null && $value->nivel4 !== null && $value->nivel5 !== null && $value->nivel6 !== null && $value->nivel7 !== null) {
                $nivelMax = 7;
            }

            switch ($nivelMax) {
                case 1:
                    $suma = AsientosDetalle::leftJoin('plan_cuentas', 'asientos_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
                        ->join('asientos_header', 'asientos_detalle.asientos_header_id', '=', 'asientos_header.id')
                        ->where('plan_cuentas.company_id', Auth::user()->company_id)
                        ->where('plan_cuentas.nivel1', $value->nivel1)
                        ->whereBetween(
                            DB::raw('DATE(asientos_header.fecha_contable)'),
                            [
                                Carbon::parse($this->fechaInicio)->format('Y-m-d'),
                                Carbon::parse($this->fechaFin)->format('Y-m-d'),
                            ]
                        )
                        ->sum(DB::raw("CASE WHEN asientos_detalle.debe_haber = 1 THEN asientos_detalle.valor ELSE -asientos_detalle.valor END"));

                    $value->saldo = $suma ?? 0;
                    $value->nivel = $nivelMax;
                    break;
                case 2:
                    $suma = AsientosDetalle::leftJoin('plan_cuentas', 'asientos_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
                        ->join('asientos_header', 'asientos_detalle.asientos_header_id', '=', 'asientos_header.id')
                        ->where('plan_cuentas.company_id', Auth::user()->company_id)
                        ->where('plan_cuentas.nivel1', $value->nivel1)
                        ->where('plan_cuentas.nivel2', $value->nivel2)
                        ->whereBetween(
                            DB::raw('DATE(asientos_header.fecha_contable)'),
                            [
                                Carbon::parse($this->fechaInicio)->format('Y-m-d'),
                                Carbon::parse($this->fechaFin)->format('Y-m-d'),
                            ]
                        )
                        ->sum(DB::raw("CASE WHEN asientos_detalle.debe_haber = 1 THEN asientos_detalle.valor ELSE -asientos_detalle.valor END"));

                    $value->saldo = $suma ?? 0;
                    $value->nivel = $nivelMax;
                    break;
                case 3:
                    $suma = AsientosDetalle::leftJoin('plan_cuentas', 'asientos_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
                        ->join('asientos_header', 'asientos_detalle.asientos_header_id', '=', 'asientos_header.id')
                        ->where('plan_cuentas.company_id', Auth::user()->company_id)
                        ->where('plan_cuentas.nivel1', $value->nivel1)
                        ->where('plan_cuentas.nivel2', $value->nivel2)
                        ->where('plan_cuentas.nivel3', $value->nivel3)
                        ->whereBetween(
                            DB::raw('DATE(asientos_header.fecha_contable)'),
                            [
                                Carbon::parse($this->fechaInicio)->format('Y-m-d'),
                                Carbon::parse($this->fechaFin)->format('Y-m-d'),
                            ]
                        )
                        ->sum(DB::raw("CASE WHEN asientos_detalle.debe_haber = 1 THEN asientos_detalle.valor ELSE -asientos_detalle.valor END"));

                    $value->saldo = $suma ?? 0;
                    $value->nivel = $nivelMax;
                    break;
                case 4:
                    $suma = AsientosDetalle::leftJoin('plan_cuentas', 'asientos_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
                        ->join('asientos_header', 'asientos_detalle.asientos_header_id', '=', 'asientos_header.id')
                        ->where('plan_cuentas.company_id', Auth::user()->company_id)
                        ->where('plan_cuentas.nivel1', $value->nivel1)
                        ->where('plan_cuentas.nivel2', $value->nivel2)
                        ->where('plan_cuentas.nivel3', $value->nivel3)
                        ->where('plan_cuentas.nivel4', $value->nivel4)
                        ->whereBetween(
                            DB::raw('DATE(asientos_header.fecha_contable)'),
                            [
                                Carbon::parse($this->fechaInicio)->format('Y-m-d'),
                                Carbon::parse($this->fechaFin)->format('Y-m-d'),
                            ]
                        )
                        ->sum(DB::raw("CASE WHEN asientos_detalle.debe_haber = 1 THEN asientos_detalle.valor ELSE -asientos_detalle.valor END"));

                    $value->saldo = $suma ?? 0;
                    $value->nivel = $nivelMax;
                    break;
                case 5:
                    $suma = AsientosDetalle::leftJoin('plan_cuentas', 'asientos_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
                        ->join('asientos_header', 'asientos_detalle.asientos_header_id', '=', 'asientos_header.id')
                        ->where('plan_cuentas.company_id', Auth::user()->company_id)
                        ->where('plan_cuentas.nivel1', $value->nivel1)
                        ->where('plan_cuentas.nivel2', $value->nivel2)
                        ->where('plan_cuentas.nivel3', $value->nivel3)
                        ->where('plan_cuentas.nivel4', $value->nivel4)
                        ->where('plan_cuentas.nivel5', $value->nivel5)
                        ->whereBetween(
                            DB::raw('DATE(asientos_header.fecha_contable)'),
                            [
                                Carbon::parse($this->fechaInicio)->format('Y-m-d'),
                                Carbon::parse($this->fechaFin)->format('Y-m-d'),
                            ]
                        )
                        ->sum(DB::raw("CASE WHEN asientos_detalle.debe_haber = 1 THEN asientos_detalle.valor ELSE -asientos_detalle.valor END"));

                    $value->saldo = $suma ?? 0;
                    $value->nivel = $nivelMax;
                    break;
                case 6:
                    $suma = AsientosDetalle::leftJoin('plan_cuentas', 'asientos_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
                        ->join('asientos_header', 'asientos_detalle.asientos_header_id', '=', 'asientos_header.id')
                        ->where('plan_cuentas.company_id', Auth::user()->company_id)
                        ->where('plan_cuentas.nivel1', $value->nivel1)
                        ->where('plan_cuentas.nivel2', $value->nivel2)
                        ->where('plan_cuentas.nivel3', $value->nivel3)
                        ->where('plan_cuentas.nivel4', $value->nivel4)
                        ->where('plan_cuentas.nivel5', $value->nivel5)
                        ->where('plan_cuentas.nivel6', $value->nivel6)
                        ->whereBetween(
                            DB::raw('DATE(asientos_header.fecha_contable)'),
                            [
                                Carbon::parse($this->fechaInicio)->format('Y-m-d'),
                                Carbon::parse($this->fechaFin)->format('Y-m-d'),
                            ]
                        )
                        ->sum(DB::raw("CASE WHEN asientos_detalle.debe_haber = 1 THEN asientos_detalle.valor ELSE -asientos_detalle.valor END"));

                    $value->saldo = $suma ?? 0;
                    $value->nivel = $nivelMax;
                    break;
                case 7:
                    $suma = AsientosDetalle::leftJoin('plan_cuentas', 'asientos_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
                        ->join('asientos_header', 'asientos_detalle.asientos_header_id', '=', 'asientos_header.id')
                        ->where('plan_cuentas.company_id', Auth::user()->company_id)
                        ->where('plan_cuentas.nivel1', $value->nivel1)
                        ->where('plan_cuentas.nivel2', $value->nivel2)
                        ->where('plan_cuentas.nivel3', $value->nivel3)
                        ->where('plan_cuentas.nivel4', $value->nivel4)
                        ->where('plan_cuentas.nivel5', $value->nivel5)
                        ->where('plan_cuentas.nivel6', $value->nivel6)
                        ->where('plan_cuentas.nivel7', $value->nivel7)
                        ->whereBetween(
                            DB::raw('DATE(asientos_header.fecha_contable)'),
                            [
                                Carbon::parse($this->fechaInicio)->format('Y-m-d'),
                                Carbon::parse($this->fechaFin)->format('Y-m-d'),
                            ]
                        )
                        ->sum(DB::raw("CASE WHEN asientos_detalle.debe_haber = 1 THEN asientos_detalle.valor ELSE -asientos_detalle.valor END"));

                    $value->saldo = $suma ?? 0;
                    $value->nivel = $nivelMax;
                    break;
            }
        }

        $planCuentas = $this->ordenarJerarquicamente($planCuentas);

        if ($this->filtroSaldo === 'diferente-de-cero') {
            $planCuentas = $planCuentas->filter(function ($plan) {
                return (float) $plan->saldo !== 0.00;
            });
        }

        $data['planCuentas'] = $planCuentas;
        $pdf = PDF::loadView('reportes.Pdf_Balance_general', $data);
        // Devuelve el PDF como descarga
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'BALANCE GENERAL ' . $this->fechaInicio . ' HASTA ' . $this->fechaFin . '.pdf');
    }

    public function cierreFom() {}
    public function updated($property)
    {
        if (
            in_array($property, [
                'fechaInicio',
                'fechaFin',
                'filtroSaldo',
            ])
        ) {
            $this->resetPage();
            $this->resetPage('detallePage');
        }
    }

    public function render()
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
            'plan_cuentas.nivel7'
        )
            ->where('plan_cuentas.company_id', Auth::user()->company_id)
            ->where('plan_cuentas.estado_resultados', 0)
            ->get();

        foreach ($planCuentas as $plan) {
            $plan->nivel = $this->nivelCuenta($plan);
            $plan->saldo = $this->movimientosCuenta($plan)->sum(DB::raw(self::VALOR_NETO));
        }

        $planCuentas = $this->ordenarJerarquicamente($planCuentas);

        if ($this->filtroSaldo === 'diferente-de-cero') {
            $planCuentas = $planCuentas->filter(function ($plan) {
                return (float) $plan->saldo !== 0.00;
            });
        }

        $planCuentas = $this->paginarColeccion($planCuentas);

        return view('livewire.balance-general.balance-general-component', [
            'planCuentas' => $planCuentas,
            'detalle' => $this->cuentaDetalleId ? $this->detalleCuenta() : null,
        ]);
    }

    public function abrirDetalleBalance(int $cuentaId)
    {
        $this->cuentaDetalleId = $this->consultarCuentaDetalle($cuentaId)->id;
        $this->seleccionarSubcuenta(null);
        $this->dispatchBrowserEvent('balance-detalle-abrir');
    }

    public function cerrarDetalleBalance()
    {
        $this->cuentaDetalleId = null;
        $this->seleccionarSubcuenta(null);
    }

    public function seleccionarSubcuenta(?int $cuentaId = null)
    {
        $this->subcuentaDetalleId = $cuentaId;
        $this->resetPage('detallePage');
    }

    private function consultarCuentaDetalle($cuentaId)
    {
        return PlanCuentas::where('company_id', Auth::user()->company_id)
            ->where('estado_resultados', 0)
            ->findOrFail($cuentaId);
    }

    private function nivelCuenta(PlanCuentas $cuenta): int
    {
        $nivel = 1;

        while ($nivel < 7 && $cuenta->{'nivel' . $nivel} !== null && $cuenta->{'nivel' . ($nivel + 1)} !== null) {
            $nivel++;
        }

        return $nivel;
    }

    private function movimientosCuenta(PlanCuentas $cuenta)
    {
        $companyId = Auth::user()->company_id;
        $consulta = AsientosDetalle::query()
            ->join('plan_cuentas', 'asientos_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
            ->join('asientos_header', 'asientos_detalle.asientos_header_id', '=', 'asientos_header.id')
            ->where('plan_cuentas.company_id', $companyId)
            ->where('asientos_header.company_id', $companyId)
            ->whereBetween(DB::raw('DATE(asientos_header.fecha_contable)'), [
                Carbon::parse($this->fechaInicio)->format('Y-m-d'),
                Carbon::parse($this->fechaFin)->format('Y-m-d'),
            ]);

        $nivelCuenta = $this->nivelCuenta($cuenta);
        for ($nivel = 1; $nivel <= $nivelCuenta; $nivel++) {
            $campo = 'nivel' . $nivel;
            $consulta->where('plan_cuentas.' . $campo, $cuenta->{$campo});
        }

        return $consulta;
    }

    private function detalleCuenta(): array
    {
        $cuenta = $this->consultarCuentaDetalle($this->cuentaDetalleId);
        $consulta = $this->movimientosCuenta($cuenta);
        $totales = (clone $consulta)->selectRaw(
            'COALESCE(SUM(CASE WHEN asientos_detalle.debe_haber = 1 THEN asientos_detalle.valor ELSE 0 END), 0) AS debe,
            COALESCE(SUM(CASE WHEN asientos_detalle.debe_haber = 1 THEN 0 ELSE asientos_detalle.valor END), 0) AS haber,
            COALESCE(SUM(' . self::VALOR_NETO . '), 0) AS neto'
        )->first();

        $agrupada = (clone $consulta)->where('plan_cuentas.id', '<>', $cuenta->id)->exists();
        $subcuenta = null;
        $subcuentas = null;
        $movimientos = null;

        if ($agrupada && $this->subcuentaDetalleId) {
            $subcuenta = (clone $consulta)->where('plan_cuentas.id', $this->subcuentaDetalleId)
                ->select('plan_cuentas.id', 'plan_cuentas.codigo', 'plan_cuentas.nombre')->first();
        }

        if ($agrupada && !$subcuenta) {
            $subcuentas = $consulta->select('plan_cuentas.id', 'plan_cuentas.codigo', 'plan_cuentas.nombre')
                ->selectRaw('SUM(' . self::VALOR_NETO . ') AS neto')
                ->groupBy('plan_cuentas.id', 'plan_cuentas.codigo', 'plan_cuentas.nombre')
                ->orderBy('plan_cuentas.codigo')
                ->orderBy('plan_cuentas.id')
                ->paginate(15, ['*'], 'detallePage');
        } else {
            if ($subcuenta) {
                $consulta->where('plan_cuentas.id', $subcuenta->id);
            }

            $movimientos = $consulta->with('asientos.concepto')
                ->select('asientos_detalle.id', 'asientos_detalle.asientos_header_id', 'asientos_detalle.debe_haber', 'asientos_detalle.valor')
                ->orderByDesc('asientos_header.fecha_contable')
                ->orderByDesc('asientos_detalle.id')
                ->paginate(15, ['*'], 'detallePage');
        }

        return compact('cuenta', 'totales', 'subcuenta', 'subcuentas', 'movimientos');
    }

    private function ordenarJerarquicamente($planCuentas)
    {
        return $planCuentas->sortBy(function ($planCuenta) {
            $segmentos = [];

            for ($nivel = 1; $nivel <= 7; $nivel++) {
                $valor = $planCuenta->{'nivel' . $nivel};
                $segmentos[] = $valor === null
                    ? ''
                    : str_pad((string) ((int) $valor), 10, '0', STR_PAD_LEFT);
            }

            $segmentos[] = str_pad((string) $planCuenta->id, 20, '0', STR_PAD_LEFT);

            return implode('.', $segmentos);
        })->values();
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
