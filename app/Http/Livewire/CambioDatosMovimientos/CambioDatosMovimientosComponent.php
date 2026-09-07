<?php

namespace App\Http\Livewire\CambioDatosMovimientos;

use App\Models\CartolaDetail;
use App\Models\CreditFolderDetail;
use App\Models\CustomerHistorial;
use App\Models\CustomerMovimiento;
use App\User;
use Livewire\Component;
use Livewire\WithPagination;

class CambioDatosMovimientosComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['debitoAutomatico'];

    public $fechaFin;
    public $valorBusqueda;
    public $search = '';

    public function cambiarfecha($fecha, $id)
    {

        $movimiento = CustomerMovimiento::find($id);
        $movimiento->date_created = $fecha;
        $movimiento->save();
        $detalleCartola = CartolaDetail::where('customer_movimientos_id', $movimiento->id)->first();
        if($detalleCartola){
            $detalleCartola->date_transaction = $fecha;
            $detalleCartola->save();

        }
        $historico = CustomerHistorial::where('customer_movimiento_code', $movimiento->code)->first();
        $historico->date_created = $fecha;
        $historico->save();

        $letra = CreditFolderDetail::find($movimiento->credit_folder_details_id);
  
        if($letra){
                $letra->date_pay = $fecha;
                $letra->save();
        }


    }
    public function render()
    {

        $movimientos =  CustomerMovimiento::where('status', true)
            ->where(function ($query) {
                $query->where('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_ruc', 'like', '%' . $this->search . '%');
            })
            ->where(function ($query) {
                $query->where('date_created', $this->fechaFin);
            })
            ->paginate(30);
            foreach( $movimientos as $value){
                $usurio = User::find($value->user_created_id);
                $value->usuarioCreo = $usurio->username;
                
            }
        return view('livewire.cambio-datos-movimientos.cambio-datos-movimientos-component', compact('movimientos'));
    }
}
