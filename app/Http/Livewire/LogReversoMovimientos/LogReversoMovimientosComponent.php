<?php

namespace App\Http\Livewire\LogReversoMovimientos;

use App\Models\LogReversoMovimiento;
use Livewire\Component;
use Livewire\WithPagination;

class LogReversoMovimientosComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['generarReverso'];

    public $fechaInicio;
    public $search = '';
    public $detalleLog = [];

    public function __construct()
    {
        $this->fechaInicio = date('Y-m-d');
    }

    public function verLogs($id)
    {
        $this->detalleLog = [];
        $log = LogReversoMovimiento::find($id);
        $this->detalleLog = LogReversoMovimiento::where('customer_id', $log->customer_id)
            ->orderBy('date_create', 'asc')
            ->get();
           
    }

    public function render()
    {
        $detalle = LogReversoMovimiento::select('log_reverso_movimientos.*')
            ->where('log_reverso_movimientos.date_create', $this->fechaInicio)
            ->where(function ($query) {
                $query->where('log_reverso_movimientos.customer_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('log_reverso_movimientos.date_create', 'asc')
            ->groupBy('log_reverso_movimientos.customer_id')
            ->paginate(15);
        return view('livewire.log-reverso-movimientos.log-reverso-movimientos-component', compact('detalle'));
    }
}
