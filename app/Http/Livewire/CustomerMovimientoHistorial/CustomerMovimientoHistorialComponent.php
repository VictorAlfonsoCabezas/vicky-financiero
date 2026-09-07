<?php

namespace App\Http\Livewire\CustomerMovimientoHistorial;

use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Models\Bovedas;
use App\Models\Customer;
use App\Models\CustomerMovimiento;
use App\Models\CustomerMovimientoSolicitud;
use App\Models\DescargoBovedasHeader;
use App\Models\OperacionesDescargoBovedas;
use App\Models\TypeTransaction;
use App\Models\WhaEnvios;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerMovimientoHistorialComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $valor = 0;
    public $observacion = '';
    public $path = '';
    public $fecha_creacion = '';
    public $user_nombres = '';
    public $search = '';
    public $razon_rechazo = '';
    public $movimiento = '';
    public $fechaInicio = '';
    public $fechaFin = '';

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
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', $transaccion)
            ->first();
        $transaccionBoveda = 'INVA';
        $transactionBoveda = OperacionesDescargoBovedas::where('company_id', Auth::user()->company_id)
            ->where('nombre_corto', $transaccionBoveda)
            ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
        $data = [
            "code" => $code,
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
            "operaciones_descargo_bovedas_id" => $transactionBoveda->id,
            "boveda_origen_id" => $bovedaPrincipal->id,
            "boveda_destino_id" => $bovedaPrincipal->id,
            "valor" => $movimientos->valor_movimiento,
            "observacion" => $transactionBoveda->descripcion . ' , del clinete ' . $movimientos->customer_name . 'por el valor de $' . $movimientos->valor_movimiento,
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
        if($this->fechaInicio != '' && $this->fechaFin !=''){

            $movimientos = CustomerMovimientoSolicitud::select('customer_movimiento_solicitud.*', 'customer.numero_documento', 'customer.nombres', 'customer.apellidos')
            ->join('customer', 'customer_movimiento_solicitud.customer_id', '=', 'customer.id')
            ->where('customer.company_id', Auth::user()->company_id)
            ->where('customer_movimiento_solicitud.fecha_creacion', ">=", $this->fechaInicio)
            ->where('customer_movimiento_solicitud.fecha_creacion', "<=", $this->fechaFin)
            ->where(function ($query) {
                $query->where('customer.numero_documento', 'like', '%' . $this->search . '%')
                ->orWhere('customer.nombres', 'like', '%' . $this->search . '%')
                ->orWhere('customer.apellidos', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(20);
        }else{
            $movimientos = CustomerMovimientoSolicitud::select('customer_movimiento_solicitud.*', 'customer.numero_documento', 'customer.nombres', 'customer.apellidos')
            ->join('customer', 'customer_movimiento_solicitud.customer_id', '=', 'customer.id')
            ->where('customer.company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('customer.numero_documento', 'like', '%' . $this->search . '%')
                ->orWhere('customer.nombres', 'like', '%' . $this->search . '%')
                ->orWhere('customer.apellidos', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(20);
        }
        return view('livewire.customer-movimiento-historial.customer-movimiento-historial-component', compact('movimientos'));
    }
}
