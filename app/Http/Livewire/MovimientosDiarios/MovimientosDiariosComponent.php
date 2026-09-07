<?php

namespace App\Http\Livewire\MovimientosDiarios;

use App\Http\Controllers\Base\BaseController;
use App\Models\CustomerMovimiento;
use App\Models\CustomerHistorial;
use App\Models\TypeTransaction;
use App\Models\LogReversoMovimiento;
use App\Models\CartolaDetail;
use App\Models\CustomerMovimientoSolicitud;
use App\Models\DescargoBovedasHeader;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class MovimientosDiariosComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['generarReverso'];

    public $fechaInicio;
    public $search = '';

    public function __construct()
    {
        $this->fechaInicio = date('Y-m-d');
    }

    public function notificarRecerso($id)
    {
        $movimiento = CustomerMovimiento::find($id);
        $data = [
            'id' => $id,
            'cliente' => $movimiento->customer_name,
            'valor' => $movimiento->valor_movimiento,
        ];
        $this->dispatchBrowserEvent('notificarAccion', $data);
    }

    public function generarReverso($id, $observacion)
    {
        $movimiento = CustomerMovimiento::find($id);
        $cuenta = $movimiento->customer_tipo_ahorro_id;
        $cliente = $movimiento->customer_id;
        $historial = CustomerHistorial::where('customer_movimiento_code', $movimiento->code)
            ->where('type_transaction_id', $movimiento->type_transaction_id)
            ->where('valor_movimiento', $movimiento->valor_movimiento)
            ->first();
        $cartolaDetalle = CartolaDetail::where('customer_movimientos_id', $movimiento->id)->first();
        $trasanccion = TypeTransaction::find($movimiento->type_transaction_id);
        $data = [
            "company_id" => $movimiento->company_id,
            "date_create" => date('Y-m-d'),
            "hour_create" => date('H:i:s'),
            "detalle" => 'SE REVERSA LATRANSACCION ' . $movimiento->code . ' (' . $movimiento->type_transaction_name . ') DEL CLIENTE' . $movimiento->customer_name . 'POR EL VALOR $' . $movimiento->valor_movimiento,
            "user_id" => Auth::user()->id,
            "user_name" =>  Auth::user()->username,
            "date_movimiento" => $movimiento->date_created,
            "hour_movimiento" => $movimiento->hour_created,
            "customer_id" => $movimiento->customer_id,
            "customer_name" => $movimiento->customer_name,
            "observacion" => $observacion,

        ];
        LogReversoMovimiento::create($data);
        //buscar si tiene valores de boveda y de solicitudes
        $boveda = DescargoBovedasHeader::where('customer_movimiento_id', $id)->first();
        if ($boveda) {
            $idBoveda = $boveda->id;
            if ($boveda->customer_movimiento_solicitud_id) {
                $solicitudA = CustomerMovimientoSolicitud::find($boveda->customer_movimiento_solicitud_id);
                $solicitudA->estado = 'PENDIENTE';
                $solicitudA->save();
            }
        }
        CustomerMovimiento::find($id)->delete();
        CustomerHistorial::find($historial->id)->delete();
        CartolaDetail::find($cartolaDetalle->id)->delete();
        if ($boveda) {
            DescargoBovedasHeader::find($idBoveda)->delete();
        }
        BaseController::recalcularCartolas($cuenta, $cliente);
    }

    public function render()
    {
        $detalle = CustomerMovimiento::select('customer_movimientos.*', 'type_transactions.name_corto')
            ->join('type_transactions', 'customer_movimientos.type_transaction_id', '=', 'type_transactions.id')
            ->where('customer_movimientos.company_id', Auth::user()->company_id)
            ->whereIn('type_transactions.name_corto', ['IN', 'EG', 'SE', 'SC', 'DEA', 'SOL', 'CVN'])
            ->where('customer_movimientos.status', true)
            ->where('customer_movimientos.date_created', $this->fechaInicio)
            ->where(function ($query) {
                $query->where('customer_movimientos.customer_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('date_created', 'asc')
            ->paginate(15);

        return view('livewire.movimientos-diarios.movimientos-diarios-component', compact('detalle'));
    }
}
