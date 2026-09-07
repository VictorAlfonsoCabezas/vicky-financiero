<?php

namespace App\Http\Livewire\CustomerMovimientoAprobacion;

use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Models\Bancos;
use App\Models\Bovedas;
use App\Models\Customer;
use App\Models\CustomerHistorial;
use App\Models\CustomerMovimiento;
use App\Models\CustomerMovimientoSolicitud;
use App\Models\CustomerTipoAhorros;
use App\Models\DescargoBovedasHeader;
use App\Models\FormasPago;
use App\Models\OperacionesDescargoBovedas;
use App\Models\TipoAhorrosDetalle;
use App\Models\TypeTransaction;
use App\Models\WhaEnvios;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomerMovimientoAprobacionComponent extends Component
{
    public $id_seleccionado = 0;
    public $valor = 0;
    public $observacion = '';
    public $path = '';
    public $fecha_creacion = '';
    public $user_nombres = '';
    public $search = '';
    public $razon_rechazo = '';
    public $movimiento = '';
    public $banco = '';
    public $numero_cuenta = '';
    public $observacionTransferencia = '';
    public $comprobante = '';
    public $numero_deposito = '';
    public $cuenta_selec = '';


    public function abrirModal($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();
        if ($this->id_seleccionado !== 0) {
            $solocitud = CustomerMovimientoSolicitud::find($this->id_seleccionado);
            $this->valor = $solocitud->valor;
            $this->observacion = $solocitud->observacion;
            $this->path = $solocitud->path;
            $this->fecha_creacion = $solocitud->fecha_creacion;
            $this->user_nombres = $solocitud->user->firstname . ' ' . $solocitud->user->lastname;
            if ($solocitud->banco_id != null && $solocitud->banco_id != 0 && $solocitud->banco_id != '') {
                $this->banco = Bancos::find($solocitud->banco_id)->nombre;
                $this->numero_cuenta = Bancos::find($solocitud->banco_id)->numero_cuenta;
            } else {
                $this->banco = "";
                $this->numero_cuenta = "";
            }

            $this->observacionTransferencia = $solocitud->observacion;
            $this->comprobante = $solocitud->comprobante;
            $this->numero_deposito = $solocitud->numero_deposito;
        }
    }

    private function limpiarFormulario()
    {
        $this->valor = 0;
        $this->reset(['observacion', 'path', 'fecha_creacion', 'user_nombres', 'razon_rechazo']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function aceptar($id)
    {
        $bovedaPrincipal = Bovedas::where('company_id', Auth::user()->company_id)
            ->where('principal', 1)
            ->first();
        if (!$bovedaPrincipal) {
            $color = 'danger';
            $mensaje = 'No tiene una boveda configurada como principal para continuar con la aprobación';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }
        $solicitud = CustomerMovimientoSolicitud::find($id);
        $customer = Customer::find($solicitud->customer_id);
        $transaccion = 'SOL';

        // Si viene desde Depositos buscar
        if (!empty($solicitud->type_transaction_id)) {
            $transactionBuscar = TypeTransaction::find($solicitud->type_transaction_id);
            $transaccion = $transactionBuscar->name_corto;
        }

        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', $transaccion)
            ->first();
        $transaccionBoveda = 'INVA';
        $transactionBoveda = OperacionesDescargoBovedas::where('company_id', Auth::user()->company_id)
            ->where('nombre_corto', $transaccionBoveda)
            ->first();

        // Dividir Pagos
        $dividirPagos = false;
        $cuenta = CustomerTipoAhorros::find($solicitud->customer_tipo_ahorro_id);
        $cliente = Customer::find($solicitud->customer_id);
        $sumaValores = TipoAhorrosDetalle::where('tipo_ahorros_id', $cuenta->tipo_ahorros_id)->sum('valor');
        if (($this->saldoEnCuenta($cliente->id, $cuenta->id)) == 0 && $sumaValores != 0) {
            $customerTipoAhorro = CustomerTipoAhorros::find($cuenta->id);
            $cliente = Customer::find($cliente->id);
            $saldoCuenta = $this->saldoEnCuenta($cliente->id, $cuenta->id);
            $saldo = $saldoCuenta + $solicitud->valor;
            if ($sumaValores != $saldo) {
                $mensaje = "<b>Cuenta de con valores iniciales: </b> la transacción que ingresa es <b> {$solicitud->valor} $</b> y lo que se debe ingresar es:<b> {$sumaValores} $</b>";
                $data = [
                    'titulo' => 'Adevertencia',
                    'color' => 'danger',
                    'mensaje' => $mensaje
                ];
                $this->dispatchBrowserEvent('alerta', $data);
                return;
            } else {
                $dividirPagos = true;
            }
        }

        if (!$dividirPagos) {
            $tabla = 'customer_movimientos';
            $code = BaseController::generarCodigo($tabla, 9);
            $valorTotal = BaseController::valorTotal();
            $data = [
                "code" => $code,
                "comprobante" => $solicitud->comprobante,
                // Nuevos
                "banco_id" => ($solicitud->forma_pago_id !== 1) ? $solicitud->banco_id : null,
                "numero_deposito" => ($solicitud->forma_pago_id !== 1) ? $solicitud->numero_deposito : null,
                "forma_pago_id" => $solicitud->forma_pago_id,
                "forma_pago_name" => (FormasPago::find($solicitud->forma_pago_id) != null) ?  FormasPago::find($solicitud->forma_pago_id)->nombre : '',

                "company_id" => Auth::user()->company_id,
                "customer_id" => $customer->id,
                "customer_code" => $customer->code,
                "afecta" => $transaction->afecta,
                "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                "customer_ruc" => $customer->numero_documento,
                "customer_address" => $customer->direccion,
                "customer_telefono" => $customer->telefono,
                "customer_tipo_ahorro_id" => $solicitud->customer_tipo_ahorro_id,
                "type_transaction_id" => $transaction->id,
                "type_transaction_name" => $transaction->name,
                "type_transaction_action" => $transaction->action,
                "valor_movimiento" => $solicitud->valor,
                "saldo_general" => $valorTotal + $solicitud->valor,
                "observation" => $solicitud->observacion,
                "user_created_id" => Auth::user()->id,
                "date_created" => date('Y-m-d'),
                "hour_created" => date("H:i:s"),
            ];
            $movimientos = CustomerMovimiento::create($data);
            $dataBov = [
                "company_id" => Auth::user()->company_id,
                "bancos_id" => $solicitud->banco_id ?? null,
                "operaciones_descargo_bovedas_id" => $transactionBoveda->id,
                "boveda_origen_id" => $bovedaPrincipal->id,
                "boveda_destino_id" => $bovedaPrincipal->id,
                "valor" => $movimientos->valor_movimiento,
                "observacion" => $transactionBoveda->descripcion . ' , del cliente ' . $movimientos->customer_name . 'por el valor de $' . $movimientos->valor_movimiento,
                "fecha_creacion" => date('Y-m-d H:i:s'),
                "estado" => 'FINALIZADO',
                "customer_movimiento_solicitud_id" => $id,
                "customer_movimiento_id" => $movimientos->id,
            ];
            DescargoBovedasHeader::create($dataBov);
            CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $solicitud->valor, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
            BaseController::guardarValoresCartola($solicitud->customer_tipo_ahorro_id, $customer->id, $movimientos, $transaction);

            $whatsappEnviar = 'Estimad@, *' . $customer->nombres . ' ' . $customer->apellidos . '* tu transacción ha sido procesada exitosamente, por el monto de: *' . $solicitud->valor . '$* Gracias por usar nuestros servicios de Caja de ' . Auth::user()->company->comercial_name;
            $whatsapp = new WhaEnvios();
            $whatsapp->company_id = Auth::user()->company_id;
            $whatsapp->envio_ahora = true;
            $whatsapp->es_transacion = true;
            $whatsapp->inmediato = true;
            $whatsapp->description = 'ENVIO DE WHATSAPP DESDE TRANSACCIONES';
            $whatsapp->type_transacction_id = $transaction->id;
            $whatsapp->type_transacction_name = $transaction->name;
            $whatsapp->proviene_id = $movimientos->id;
            $whatsapp->customer_id = $customer->id;
            $whatsapp->customer_name = $customer->nombres . ' ' . $customer->apellidos;
            $whatsapp->customer_celular = $customer->telefono;
            $whatsapp->whatsapp = $whatsappEnviar;
            $whatsapp->estado = 'PENDIENTE';
            $whatsapp->fecha_creacion = date('Y-m-d H:i:s');
            $whatsapp->fecha_envio = date('Y-m-d H:i:s');
            $whatsapp->save();
            //Aprobar transaction
            $solicitud->estado = "APROBADO";
            $solicitud->save();
            $color = 'success';
            $mensaje = 'Transaccion aprobada con Éxito';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
        } else {
            $cuenta = CustomerTipoAhorros::find($solicitud->customer_tipo_ahorro_id);
            $detalle = TipoAhorrosDetalle::where('tipo_ahorros_id', $cuenta->tipoAhorros->id)->get();
            $customer = Customer::find($solicitud->customer_id);
            foreach ($detalle as $key => $value) {
                if ($value->afecta == 'E') {
                    $transaccion = 'SE';
                } else {
                    $transaccion = 'SC';
                }
                $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                    ->where('name_corto', $transaccion)
                    ->first();
                $tabla = 'customer_movimientos';
                $code = BaseController::generarCodigo($tabla, 9);
                $valorTotal = BaseController::valorTotal();
                $data = [
                    "code" => $code,
                    "comprobante" => $solicitud->comprobante,
                    // Nuevos
                    "banco_id" => ($solicitud->forma_pago_id !== 1) ? $solicitud->banco_id : null,
                    "numero_deposito" => ($solicitud->forma_pago_id !== 1) ? $solicitud->numero_deposito : null,
                    "forma_pago_id" => $solicitud->forma_pago_id,
                    "forma_pago_name" => (FormasPago::find($solicitud->forma_pago_id) != null) ? FormasPago::find($solicitud->forma_pago_id)->nombre : '',

                    "company_id" => Auth::user()->company_id,
                    "customer_id" => $customer->id,
                    "customer_code" => $customer->code,
                    "afecta" => $transaction->afecta,
                    "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                    "customer_ruc" => $customer->numero_documento,
                    "customer_address" => $customer->direccion,
                    "customer_telefono" => $customer->telefono,
                    "customer_tipo_ahorro_id" => $cuenta->id,
                    "tipo_ahorro_detalle_id" => $value->id,
                    "type_transaction_id" => $transaction->id,
                    "type_transaction_name" => $transaction->name,
                    "type_transaction_action" => $transaction->action,
                    "valor_movimiento" => $value->valor,
                    "saldo_general" => $valorTotal + $value->valor,
                    "observation" => $solicitud->observacion,
                    "user_created_id" => Auth::user()->id,
                    "date_created" => date('Y-m-d'),
                    "hour_created" => date("H:i:s"),
                ];
                $movimientos = CustomerMovimiento::create($data);
                $dataBov = [
                    "company_id" => Auth::user()->company_id,
                    "bancos_id" => $solicitud->banco_id ?? null,
                    "operaciones_descargo_bovedas_id" => $transactionBoveda->id,
                    "boveda_origen_id" => $bovedaPrincipal->id,
                    "boveda_destino_id" => $bovedaPrincipal->id,
                    "valor" => $movimientos->valor_movimiento,
                    "observacion" => $transactionBoveda->descripcion . ' , del cliente ' . $movimientos->customer_name . 'por el valor de $' . $movimientos->valor_movimiento,
                    "fecha_creacion" => date('Y-m-d H:i:s'),
                    "estado" => 'FINALIZADO',
                    "customer_movimiento_solicitud_id" => $id,
                    "customer_movimiento_id" => $movimientos->id,
                ];
                DescargoBovedasHeader::create($dataBov);
                CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $solicitud->valor, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
                BaseController::guardarValoresCartola($solicitud->customer_tipo_ahorro_id, $customer->id, $movimientos, $transaction);

                $whatsappEnviar = 'Estimad@, *' . $customer->nombres . ' ' . $customer->apellidos . '* tu transacción ha sido procesada exitosamente, por el monto de: *' . $solicitud->valor . '$* Gracias por usar nuestros servicios de ' . Auth::user()->company->comercial_name;
                $whatsapp = new WhaEnvios();
                $whatsapp->company_id = Auth::user()->company_id;
                $whatsapp->envio_ahora = true;
                $whatsapp->es_transacion = true;
                $whatsapp->inmediato = true;
                $whatsapp->description = 'ENVIO DE WHATSAPP DESDE TRANSACCIONES';
                $whatsapp->type_transacction_id = $transaction->id;
                $whatsapp->type_transacction_name = $transaction->name;
                $whatsapp->proviene_id = $movimientos->id;
                $whatsapp->customer_id = $customer->id;
                $whatsapp->customer_name = $customer->nombres . ' ' . $customer->apellidos;
                $whatsapp->customer_celular = $customer->telefono;
                $whatsapp->whatsapp = $whatsappEnviar;
                $whatsapp->estado = 'PENDIENTE';
                $whatsapp->fecha_creacion = date('Y-m-d H:i:s');
                $whatsapp->fecha_envio = date('Y-m-d H:i:s');
                $whatsapp->save();
            }
            //Aprobar transaction
            $solicitud->estado = "APROBADO";
            $solicitud->save();
            $color = 'success';
            $mensaje = 'Transaccion aprobada con Éxito';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
        }
    }

    public function saldoEnCuenta($cliente_id, $cuenta_id)
    {
        $cliente = Customer::find($cliente_id);
        $ingresos = CustomerHistorial::where('customer_code', $cliente->code)
            ->where('type_transaction_action', 'S')
            // ->where('type_transaction_name', 'INGRESOS')
            ->where('customer_tipo_ahorro_id', $cuenta_id)
            ->where('status', true)
            ->sum('valor_movimiento');
        $egresos = CustomerHistorial::where('customer_code', $cliente->code)
            ->where('type_transaction_action', 'R')
            // ->where('type_transaction_name', 'EGRESOS')
            ->where('customer_tipo_ahorro_id', $cuenta_id)
            ->where('status', true)
            ->sum('valor_movimiento');
        $valor = $ingresos - $egresos;
        $saldoCuenta = number_format(round($valor, 2), 2, '.', '');
        return $saldoCuenta;
    }

    public function seleccionar($id)
    {
        $this->movimiento = $id;
    }

    public function storeRechazar()
    {
        $this->validate([
            "razon_rechazo" => "required",
        ]);

        $solicitud = CustomerMovimientoSolicitud::find($this->movimiento);
        $solicitud->estado = "RECHAZADO";
        $solicitud->razon_rechazado = $this->razon_rechazo;
        $solicitud->fecha_rechazado = date('Y-m-d H:i:s');
        $solicitud->user_rechazado = Auth::user()->id;
        $solicitud->save();
        $color = 'success';
        $mensaje = 'Solicitud Rechazada correctamemte';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('alerta', $data);
        $this->dispatchBrowserEvent('closeModal');
    }

    public function render()
    {
        $movimientos = CustomerMovimientoSolicitud::select(
            'customer_movimiento_solicitud.*',
            'customer.numero_documento',
            'customer.nombres',
            'customer.apellidos',
            'bancos.nombre as banco',
            'bancos.numero_cuenta as numero_cuenta',
            'cta.codigo as cuenta_numero',
            'cta.tipo_ahorros_id as cuenta_tipo_id'
        )
            ->leftJoin('customer', 'customer_movimiento_solicitud.customer_id', '=', 'customer.id')
            ->leftJoin('bancos', 'customer_movimiento_solicitud.banco_id', '=', 'bancos.id')
            ->leftJoin('customer_tipo_ahorros as cta', 'customer_movimiento_solicitud.customer_tipo_ahorro_id', '=', 'cta.id')
            ->where('customer.company_id', Auth::user()->company_id)
            ->where('customer_movimiento_solicitud.estado', 'PENDIENTE')
            ->where(function ($query) {
                $query->where('customer.numero_documento', 'like', '%' . $this->search . '%')
                    ->orWhere('customer.nombres', 'like', '%' . $this->search . '%')
                    ->orWhere('customer.apellidos', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('livewire.customer-movimiento-aprobacion.customer-movimiento-aprobacion-component', compact('movimientos'));
    }
}
