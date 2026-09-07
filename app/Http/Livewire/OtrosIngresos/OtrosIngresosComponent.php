<?php

namespace App\Http\Livewire\OtrosIngresos;

use App\Models\OtrosIngresos;
use App\Models\Customer;
use App\Models\TypeTransaction;
use App\Models\CustomerMovimiento;
use App\Models\CustomerHistorial;
use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Models\FormasPago;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class OtrosIngresosComponent extends Component
{
    public $id_seleccionado = 0;
    public $name = '';
    public $valor = 0;
    public $descripcion = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }
    private function limpiarFormulario()
    {
        $this->reset(['name', 'valor', 'descripcion']);
        $this->resetErrorBag();
        $this->resetValidation();
    }
    public function storeFormas()
    {
        $this->validate(
            [

                'name' => 'required',
                'valor' => 'required',
                'descripcion' => 'required',

            ],
            [

                'name.required' => 'Necesita ingresar una razón.',
                'valor.required' => 'Necesita ingresar un valor.',
                'descripcion.required' => 'Necesita ingresar una descripción.',

            ]
        );
        if ($this->id_seleccionado > 0) {
            $ingresos = OtrosIngresos::find($this->id_seleccionado);
        } else {
            $ingresos = new OtrosIngresos();
            $ingresos->company_id = Auth::user()->company_id;
            $ingresos->date_create = date('Y-m-d');
            $ingresos->hour_create = date('H:i:s');
            $ingresos->user_id = Auth::user()->id;
            $ingresos->user_name = Auth::user()->username;
            $ingresos->valor = $this->valor;

            $movimiento = $this->generarMovimiento();
            $ingresos->transaction_id = $movimiento->type_transaction_id;
            $ingresos->movimiento_id = $movimiento->id;
        }
        $ingresos->name = strtoupper($this->name);
        $ingresos->descripcion = $this->descripcion;

        $ingresos->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function generarMovimiento()
    {
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'IOV')
            ->first();
        $valorTotal = BaseController::valorTotal();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $formaPago = FormasPago::where('descripcion', 'EFECTIVO ')->first();
        $idFormaPago = null;
        $NombreFormaPago = null;
        if ($formaPago) {
            $idFormaPago = $formaPago->id;
            $NombreFormaPago = $formaPago->nombre;
        }
        $data = [
            "code" => $code,
            "company_id" => Auth::user()->company_id,
            "customer_code" => ' ',
            "afecta" => $transaction->afecta,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $this->valor,
            "saldo_general" => $valorTotal + $this->valor,
            "observation" =>  $transaction->description,
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
            "forma_pago_id" => $idFormaPago,
            "forma_pago_name" => $NombreFormaPago,
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $this->valor, ' ', $movimientos->saldo_general, date('Y-m-d'));
        return $movimientos;
    }

    public function borrarValor($id)
    {
        $valor = OtrosIngresos::find($id);
        $movimiento = CustomerMovimiento::find($valor->movimiento_id);
        $historial = CustomerHistorial::where('customer_movimiento_code', $movimiento->code)->first();

        CustomerHistorial::find($historial->id)->delete();
        CustomerMovimiento::find($movimiento->id)->delete();
        OtrosIngresos::find($valor->id)->delete();
    }

    public function render()
    {
        $valores = OtrosIngresos::where('company_id', Auth::user()->company_id)->get();
        return view('livewire.otros-ingresos.otros-ingresos-component', compact('valores'));
    }
}
