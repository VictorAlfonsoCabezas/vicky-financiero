<?php

namespace App\Http\Livewire\LibroMayor;

use App\Models\AsientosHeader;
use App\Models\PlanCuentas;
use App\Models\Sedes;
use App\Models\TipoConcepto;
use Auth;
use DB;
use Livewire\Component;
use Livewire\WithPagination;

class LibroMayorComponent extends Component
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
    public $manual = '';
    public $search = '';
    public $cuentaFiltro = '';
    public $cuentaFiltroTexto = '';
    public $searchCuentaPlan = '';
    public $nombreCuenta = '';
    public $codigoComparar = '';

    public function mount()
    {
        $this->fechaInicio = date('Y-01-01');
        $this->fechaFin = date('Y-m-d');
        $this->fecha = date('Y-m-d H:i');
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['fechaInicio', 'fechaFin', 'searchAsiento', 'cuentaFiltro', 'manual'])) {
            $this->resetPage();
        }
    }

    public function updatedSearch()
    {
        if ($this->cuentaFiltroTexto !== '' && $this->search !== $this->cuentaFiltroTexto) {
            $this->cuentaFiltro = '';
            $this->cuentaFiltroTexto = '';
            $this->resetPage();
        }
    }

    public function seleccionarCuentaFiltro($cuentaId)
    {
        $cuenta = PlanCuentas::where('company_id', Auth::user()->company_id)
            ->where('id', $cuentaId)
            ->first();

        if (!$cuenta) {
            return;
        }

        $this->cuentaFiltro = $cuenta->id;
        $this->cuentaFiltroTexto = $cuenta->codigo . ' - ' . $cuenta->nombre;
        $this->nombreCuenta = $cuenta->codigo . ' - ' . $cuenta->nombre;

        $this->codigoComparar = $cuenta->codigo;

        $this->search = $this->cuentaFiltroTexto;
        $this->resetPage();
    }

    public function limpiarCuentaFiltro()
    {
        $this->cuentaFiltro = '';
        $this->cuentaFiltroTexto = '';
        $this->search = '';
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->fechaInicio = date('Y-01-01');
        $this->fechaFin = date('Y-m-d');
        $this->searchAsiento = '';
        $this->cuentaFiltro = '';
        $this->cuentaFiltroTexto = '';
        $this->search = '';
        $this->searchCuentaPlan = '';
        $this->manual = '';
        $this->resetPage();
    }

    public function render()
    {
        $queryAsientosHeader = AsientosHeader::select(
            'asientos_header.*',
            DB::raw("(SELECT COALESCE(SUM(valor), 0) FROM asientos_detalle WHERE asientos_detalle.asientos_header_id = asientos_header.id AND asientos_detalle.debe_haber = true AND asientos_detalle.status = true) as suma_debe"),
            DB::raw("(SELECT COALESCE(SUM(valor), 0) FROM asientos_detalle WHERE asientos_detalle.asientos_header_id = asientos_header.id AND asientos_detalle.debe_haber = false AND asientos_detalle.status = true) as suma_haber")
        )
            ->with([
                'concepto.tipoConcepto',
                'customerMovimiento.typeTransaction',
                'detalles.planCuentas',
                'detalles.userCreated',
            ])
            ->where('company_id', Auth::user()->company_id)
            ->where('status', true)
            ->whereBetween(
                DB::raw('DATE(fecha_contable)'),
                [
                    \Carbon\Carbon::parse($this->fechaInicio)->format('Y-m-d'),
                    \Carbon\Carbon::parse($this->fechaFin)->format('Y-m-d'),
                ]
            )
            ->when(trim($this->searchAsiento) !== '', function ($query) {
                $asientos = collect(preg_split('/[\s,;]+/', trim($this->searchAsiento)))
                    ->filter()
                    ->values();

                if ($asientos->count() > 1 && $asientos->every(function ($asiento) {
                    return is_numeric($asiento);
                })) {
                    $query->whereIn('asientos_header.id', $asientos->all());
                } else {
                    $query->where('asientos_header.id', 'like', '%' . trim($this->searchAsiento) . '%');
                }
            })
            ->when($this->cuentaFiltro !== '', function ($query) {
                $query->whereHas('detalles', function ($detalle) {
                    $detalle->where('plan_cuentas_id', $this->cuentaFiltro)
                        ->where('status', true);
                });
            })
            ->when($this->manual !== '', function ($query) {
                $query->where('manual', $this->manual);
            })
            ->orderByDesc('asientos_header.fecha_creacion')
            ->orderByDesc('asientos_header.id');

        $asientosHeader = $queryAsientosHeader->paginate(20);
        $totalDebePagina = $asientosHeader->getCollection()->sum('suma_debe');
        $totalHaberPagina = $asientosHeader->getCollection()->sum('suma_haber');


        $tipoConceptos = TipoConcepto::where('company_id', Auth::user()->company_id)->where('status', true)->get();
        $planCuentas = PlanCuentas::select(
            'id',
            'codigo',
            'nombre',
            DB::raw('REPLACE(codigo, ".", "") as codigoSolo')
        )
            ->where('company_id', Auth::user()->company_id)
            ->where('status', true)
            ->when(trim($this->searchCuentaPlan) !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('nombre', 'like', '%' . trim($this->searchCuentaPlan) . '%')
                        ->orWhere('codigo', 'like', '%' . trim($this->searchCuentaPlan) . '%');
                });
            })
            ->orderBy('codigoSolo', 'ASC')
            ->limit(80)
            ->get();
        $sedes = Sedes::where('company_id', Auth::user()->company_id)->where('status', true)->get();

        if (trim($this->search) !== '' && $this->cuentaFiltro === '') {
            $planCuentasBuscador = PlanCuentas::where('company_id', Auth::user()->company_id)
                ->where(function ($query) {
                    $query->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('codigo', 'like', '%' . $this->search . '%');
                })
                ->orderBy('codigo')
                ->limit(10)
                ->get();
        } else {
            $planCuentasBuscador = collect();
        }
        return view('livewire.libro-mayor.libro-mayor-component', compact('asientosHeader', 'planCuentas', 'tipoConceptos', 'sedes', 'planCuentasBuscador', 'totalDebePagina', 'totalHaberPagina'));
    }
}
