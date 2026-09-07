<?php

namespace App\Http\Livewire\ReporteGastosAdministrativos;

use App\Models\Company;
use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\Customer;
use App\Models\CustomerMovimiento;
use App\Models\CustomerTipoAhorros;
use App\Models\Prestamos;
use App\Models\TipoAhorros;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Gastos\GastosExport;

class ReporteGastosAdministrativosComponent extends Component
{

    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $cuenta_prestamo = 0;


    public function mount()
    {
        $this->fecha_inicio = date('Y-m-d');
        $this->fecha_fin = date('Y-m-d');
    }

    public function obtenerDatos()
    {
        $this->fecha_inicio = $this->fecha_inicio ?? '';
        $this->fecha_fin = $this->fecha_fin ?? '';
        $this->cuenta_prestamo = $this->cuenta_prestamo ?? '';
    }

    public function export()
    {
        if ($this->cuenta_prestamo == 0) {
            $color = 'danger';
            $mensaje = 'Debe seleccionar un tipo de reporte para exportar';
            $data = [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
                'color' => $color,
                'mensaje' => $mensaje
            ];

            $this->dispatchBrowserEvent('alerta', $data);
        } else if ($this->cuenta_prestamo == 1) {
            $nombre = 'Reporte de cuentas';
            return Excel::download(new GastosExport($this->fecha_inicio, $this->fecha_fin,  $this->cuenta_prestamo), $nombre . '.xlsx');
        } else {
            $nombre = 'Reporte de Créditos';
            return Excel::download(new GastosExport($this->fecha_inicio, $this->fecha_fin,  $this->cuenta_prestamo), $nombre . '.xlsx');
        }
    }


    public function render()
    {
        $inicio = $this->fecha_inicio;
        $fin = $this->fecha_fin;
        $company = Company::find(Auth::user()->company_id);
        $creditosTotales = CreditFolderHeader::where('status', 'ENTREGADO')
            ->where('date_created', '>=', $inicio)
            ->where('date_created', '<=', $fin)
            ->orderBy('code', 'asc');
        $creditos = CreditFolderHeader::where('status', 'ENTREGADO')
            ->where('date_created', '>=', $inicio)
            ->where('date_created', '<=', $fin)
            ->orderBy('code', 'asc')
            ->paginate(10);
        foreach ($creditos as $val) {
            $customer = Customer::find($val->customer_id);
            $val->customerIdentificacion =  $customer->numero_documento;
            $val->customerNombre =  $customer->apellidos . ' '  . $customer->nombres;
            $pretamo = Prestamos::find($val->tipo_prestamo);
            $val->prestamoNombre = '';
            $val->prestamoInteres = '';
            if ($pretamo) {
                $val->prestamoNombre = $pretamo->name;
                $val->prestamoInteres = $pretamo->interes;
            }
        }
        $cuentasTotales = CustomerMovimiento::select('customer_tipo_ahorro_id', 'customer_name', 'customer_ruc', 'observation', 'date_created', DB::raw('SUM(valor_movimiento) as total_valor_movimiento'))
            ->whereNotNull('customer_tipo_ahorro_id')
            ->where('date_created', '>=', $this->fecha_inicio)
            ->where('date_created', '<=', $this->fecha_fin)
            ->whereIn('type_transaction_id', [20, 21])
            ->groupBy('customer_tipo_ahorro_id');
        $totalValorMovimiento = 0;
        foreach ($cuentasTotales->get() as $valCuenta) {
            $totalValorMovimiento += $valCuenta->total_valor_movimiento;
        }

        $cuentas = CustomerMovimiento::select('customer_tipo_ahorro_id', 'customer_name', 'customer_ruc', 'observation', 'date_created', DB::raw('SUM(valor_movimiento) as total_valor_movimiento'))
            ->whereNotNull('customer_tipo_ahorro_id')
            ->where('date_created', '>=', $this->fecha_inicio)
            ->where('date_created', '<=', $this->fecha_fin)
            ->whereIn('type_transaction_id', [20, 21])
            ->groupBy('customer_tipo_ahorro_id')
            ->paginate(10);

        foreach ($cuentas  as $valCuenta) {
            $cuatomerTipoAhorro =  CustomerTipoAhorros::find($valCuenta->customer_tipo_ahorro_id);
            $valCuenta->numeroCuenta =  $cuatomerTipoAhorro->codigo;
            $ahorr =  TipoAhorros::find($cuatomerTipoAhorro->tipo_ahorros_id);
            $valCuenta->nombreHorro = $ahorr->name;
        }

        return view('livewire.reporte-gastos-administrativos.reporte-gastos-administrativos-component', compact('creditos', 'cuentas',  'company', 'creditosTotales', 'cuentasTotales', 'totalValorMovimiento'));
    }
}
