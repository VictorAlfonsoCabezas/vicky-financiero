<?php

namespace App\Http\Livewire\CustomerTransferencias;

use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Cartola\CartolaController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Models\Customer;
use App\Models\CustomerMovimiento;
use App\Models\CustomerTipoAhorros;
use App\Models\CustomerTransferencias;
use App\Models\TypeTransaction;
use App\Models\WhaEnvios;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomerTransferenciasComponent extends Component
{

    public $cuenta_origen = '';
    public $customer_destino = '';
    public $valor = '0.00';
    public $search = '';
    public $fin = 0;
    public $descripcion = '';

    public function seleccionarCliente($id)
    {
        $this->customer_destino = $id;
    }

    public function confirmarTransferencia()
    {
        $this->fin = 1;
        if ($this->valor <= 0) {
            $color = 'danger';
            $mensaje = 'Ingrese un valor mayor a cero';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;

        } else if ($this->descripcion == '') {
            $color = 'danger';
            $mensaje = 'debe ingresar la descripcion de la transferencia';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;

        } else {

            $customerOrigen = Customer::where('company_id', Auth::user()->company_id)->where('user_id', Auth::user()->id)->first();
            $nobreCortoEnvia = 'TRE';
            $transactionEnvia = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', $nobreCortoEnvia)
                ->first();

            $tabla = 'customer_movimientos';
            $code = BaseController::generarCodigo($tabla, 9);
            $valorTotal = BaseController::valorTotal();
            $data = [
                "code" => $code,
                "company_id" => Auth::user()->company_id,
                "customer_id" => $customerOrigen->id,
                "customer_code" => $customerOrigen->code,
                "afecta" => $transactionEnvia->afecta,
                "customer_name" => $customerOrigen->nombres . ' ' . $customerOrigen->apellidos,
                "customer_ruc" => $customerOrigen->numero_documento,
                "customer_address" => $customerOrigen->direccion,
                "customer_telefono" => $customerOrigen->telefono,
                "customer_tipo_ahorro_id" => $this->cuenta_origen,
                "type_transaction_id" => $transactionEnvia->id,
                "type_transaction_name" => $transactionEnvia->name,
                "type_transaction_action" => $transactionEnvia->action,
                "valor_movimiento" => $this->valor,
                "saldo_general" => $valorTotal + $this->valor,
                "observation" => $this->descripcion,
                "user_created_id" => Auth::user()->id,
                "date_created" => date('Y-m-d'),
                "hour_created" => date("H:i:s"),
            ];
            $movimientos = CustomerMovimiento::create($data);
            CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transactionEnvia->id, $this->valor, $customerOrigen->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
            CartolaController::ingresoCartola($movimientos->customer_code, $movimientos->type_transaction_id, $movimientos->valor_movimiento, date('Y-m-d'));
            $whatsappEnviar = 'Estimad@, *' . $customerOrigen->nombres . ' ' . $customerOrigen->apellidos . '* tu transacción ha sido procesada exitosamente, por el monto de: *' . $this->valor . '$* Gracias por usar nuestros servicios de ' . Auth::user()->company->comercial_name;
            $whatsapp = new WhaEnvios();
            $whatsapp->company_id = Auth::user()->company_id;
            $whatsapp->envio_ahora = true;
            $whatsapp->es_transacion = true;
            $whatsapp->inmediato = true;
            $whatsapp->description = 'TRANSACCIONES ENTRE SOCIOS DE LA CAJA';
            $whatsapp->type_transacction_id = $transactionEnvia->id;
            $whatsapp->type_transacction_name = $transactionEnvia->name;
            $whatsapp->proviene_id = $movimientos->id;
            $whatsapp->customer_id = $customerOrigen->id;
            $whatsapp->customer_name = $customerOrigen->nombres . ' ' . $customerOrigen->apellidos;
            $whatsapp->customer_celular = $customerOrigen->telefono;
            $whatsapp->whatsapp = $whatsappEnviar;
            $whatsapp->estado = 'PENDIENTE';
            $whatsapp->fecha_creacion = date('Y-m-d H:i:s');
            $whatsapp->fecha_envio = date('Y-m-d H:i:s');
            $whatsapp->save();




            $cuentaDestino = CustomerTipoAhorros::where('company_id', Auth::user()->company_id)->where('customer_id', $this->customer_destino)->first();
            $customerDestino = Customer::find($cuentaDestino->customer_id);
            $nobreCortoRecibe = 'TRR';
            $transactionRecibe = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', $nobreCortoRecibe)
                ->first();

            $tabla = 'customer_movimientos';
            $code = BaseController::generarCodigo($tabla, 9);
            $valorTotal = BaseController::valorTotal();
            $data = [
                "code" => $code,
                "company_id" => Auth::user()->company_id,
                "customer_id" => $customerDestino->id,
                "customer_code" => $customerDestino->code,
                "afecta" => $transactionRecibe->afecta,
                "customer_name" => $customerDestino->nombres . ' ' . $customerDestino->apellidos,
                "customer_ruc" => $customerDestino->numero_documento,
                "customer_address" => $customerDestino->direccion,
                "customer_telefono" => $customerDestino->telefono,
                "customer_tipo_ahorro_id" => $cuentaDestino->id,
                "type_transaction_id" => $transactionRecibe->id,
                "type_transaction_name" => $transactionRecibe->name,
                "type_transaction_action" => $transactionRecibe->action,
                "valor_movimiento" => $this->valor,
                "saldo_general" => $valorTotal + $this->valor,
                "observation" => $this->descripcion,
                "user_created_id" => Auth::user()->id,
                "date_created" => date('Y-m-d'),
                "hour_created" => date("H:i:s"),
            ];
            $movimientos = CustomerMovimiento::create($data);
            CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transactionRecibe->id, $this->valor, $customerDestino->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
            CartolaController::ingresoCartola($movimientos->customer_code, $movimientos->type_transaction_id, $movimientos->valor_movimiento, date('Y-m-d'));
            $whatsappEnviar = 'Estimad@, *' . $customerDestino->nombres . ' ' . $customerDestino->apellidos . '* tu transacción ha sido procesada exitosamente, por el monto de: *' . $this->valor . '$* Gracias por usar nuestros servicios de ' . Auth::user()->company->comercial_name;
            $whatsapp = new WhaEnvios();
            $whatsapp->company_id = Auth::user()->company_id;
            $whatsapp->envio_ahora = true;
            $whatsapp->es_transacion = true;
            $whatsapp->inmediato = true;
            $whatsapp->description = 'TRANSACCIONES ENTRE SOCIOS DE LA CAJA';
            $whatsapp->type_transacction_id = $transactionRecibe->id;
            $whatsapp->type_transacction_name = $transactionRecibe->name;
            $whatsapp->proviene_id = $movimientos->id;
            $whatsapp->customer_id = $customerDestino->id;
            $whatsapp->customer_name = $customerDestino->nombres . ' ' . $customerDestino->apellidos;
            $whatsapp->customer_celular = $customerDestino->telefono;
            $whatsapp->whatsapp = $whatsappEnviar;
            $whatsapp->estado = 'PENDIENTE';
            $whatsapp->fecha_creacion = date('Y-m-d H:i:s');
            $whatsapp->fecha_envio = date('Y-m-d H:i:s');
            $whatsapp->save();

            $this->limpiarValores();
        }
    }

    public function obtenerDatosSaldo()
    {
        $valorIngresado = $this->valor;
        $cuentaOrigen = $this->cuenta_origen;
        if ($cuentaOrigen != null && $cuentaOrigen != '') {
            $saldoCuenta = BaseController::verSaldoTipoAhorro($cuentaOrigen);
            if ($saldoCuenta <= 0) {
                $this->valor = 0;
                $color = 'danger';
                $mensaje = 'La cuenta a seleccionada no dispone de saldo, seleccione otra cuenta';
                $data = [
                    'titulo' => 'Notificación',
                    'color' => $color,
                    'mensaje' => $mensaje
                ];
                $this->dispatchBrowserEvent('alerta', $data);
                return;
            } else {
                $color = 'info';
                $mensaje = 'Cuenta seleecionada';
                $data = [
                    'titulo' => 'Notificación',
                    'color' => $color,
                    'mensaje' => $mensaje
                ];
                $this->dispatchBrowserEvent('alerta', $data);
                return;
            }


        } else {
            $color = 'danger';
            $mensaje = 'Primero debe seleccionar una cuenta de porigen';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }
    }
    public function limpiarValores()
    {
        $this->search = '';
        $this->valor = 0.00;
        $this->customer_destino = '';
        $this->cuenta_origen = '';
        $this->descripcion = '';
    }

    public function render()
    {
        $customer_origen = Customer::where('company_id', Auth::user()->company_id)->where('user_id', Auth::user()->id)->first();
        if (isset($customer_origen->id)) {
            $customer_cuenta_origen = CustomerTipoAhorros::where('company_id', Auth::user()->company_id)->where('customer_id', $customer_origen->id)->get();
        } else {
            $customer_cuenta_origen = [];
        }

        $customer_cuenta_destino = CustomerTipoAhorros::select(
            'customer_tipo_ahorros.id',
            'customer_tipo_ahorros.customer_id',
            'customer.nombres',
            'customer.apellidos',
            'customer.direccion',
            'customer.numero_documento',
            'customer.telefono',
            'customer.correo'
        )
            ->join('customer', 'customer_tipo_ahorros.customer_id', '=', 'customer.id')
            //->where('customer_tipo_ahorros.codigo', $this->search)
            ->where(function ($query) {
                $query->where('customer_tipo_ahorros.codigo', $this->search)
                    ->orWhere('customer.nombres', 'like', '%' . $this->search . '%')
                    ->orWhere('customer.apellidos', 'like', '%' . $this->search . '%');
            })
            ->first();
        return view('livewire.customer-transferencias.customer-transferencias-component', compact('customer_origen', 'customer_cuenta_origen', 'customer_cuenta_destino'));
    }
}
