<?php

namespace App\Http\Livewire\DebitoAutomatico;

use App\Http\Controllers\Base\BaseController;
use App\Models\CreditFolderHeader;
use App\Models\CreditFolderDetail;
use App\Models\TypeTransaction;
use App\Models\WhaEnvios;
use App\Models\Customer;
use App\Models\CustomerHistorial;
use App\Models\CustomerTipoAhorros;
use App\Models\TipoAhorrosDetalle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class DebitoAutomaticoComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['debitoAutomatico'];

    public $fechaInicio;
    public $fechaFin;
    public $search = '';


    public function __construct()
    {
        $this->fechaInicio = date('Y-01-01');
        $this->fechaFin = date('Y-m-d');
    }

    public function debitoAutomaticonotificacion($id, $customer_tipo_id, $mora)
    {
        $letra = CreditFolderDetail::find($id);
        $data = [
            'id' => $id,
            'customer_tipo_id' => $customer_tipo_id,
            'mora' => $mora,
            'letra' => $letra,
            'cabecera' => CreditFolderHeader::where('code', $letra->code_folder_header)->first(),
        ];
        $this->dispatchBrowserEvent('notificarAccion', $data);
    }

    public function debitoAutomatico($id, $customer_tipo_id, $mora)
    {
        $calculoMora = 0;
        $saldo = \App\Http\Controllers\Base\BaseController::saldoCuentaCliente($customer_tipo_id);
        if ($saldo > 0) {
            if ($mora) {
                $calculoMora = \App\Http\Controllers\Base\BaseController::calculoInteresMoraLetraValorMensual($id);
            }
            $letra = CreditFolderDetail::find($id);
            $valor = $letra->valor_cuota + $calculoMora;
            if ($saldo >= $valor) {

                $resouestaDebito = \App\Http\Controllers\Base\BaseController::descargoAutomaticoCuentaCredito($valor, $customer_tipo_id, $id);
                $respuesta = \App\Http\Controllers\Base\BaseController::pagoAutomaticoLetraCredito($valor, $id, $calculoMora);
                if ($respuesta) {
                    $color = 'success';
                    $mensaje = 'EL debito Automático se realizó correctamente';
                } else {
                    $color = 'danger';
                    $mensaje = 'Existe un error en el debito Automático';
                }
            } else {
                $color = 'danger';
                $mensaje = 'No tiene saldo suficiente para realizar el debito automático..';
            }
        } else {
            $color = 'danger';
            $mensaje = 'No tiene saldo suficiente para realizar el debito automático.';
        }
        $data = [
            'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function envioWhatsapp($id)
    {
        $letra = CreditFolderDetail::find($id);
        $header = CreditFolderHeader::where('code', $letra->code_folder_header)->first();
        $customer = Customer::find($header->customer_id);
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'NTC')
            ->first();
        $whatsappEnviar = 'Estimad@, *' . $customer->nombres . ' ' . $customer->apellidos . '* la fecha de pago de su letra *#' . $letra->numero_cuota . '* correspondiente al préstamo  *#' . $header->code . '* es o fue el *' . $letra->date_vencimiento . '*. Esperamos se acerque a realizar su pago en caso de ya haberlo hecho omita el presente mensaje.';
        $whatsapp = new WhaEnvios();
        $whatsapp->company_id = Auth::user()->company_id;
        $whatsapp->envio_ahora = true;
        $whatsapp->es_transacion = true;
        $whatsapp->inmediato = true;
        $whatsapp->description = 'ENVIO DE WHATSAPP DESDE TRANSACCIONES';
        $whatsapp->type_transacction_id = $transaction->id;
        $whatsapp->type_transacction_name = $transaction->name;
        $whatsapp->customer_id = $customer->id;
        $whatsapp->customer_name = $customer->nombres . ' ' . $customer->apellidos;
        $whatsapp->customer_celular = $customer->telefono;
        $whatsapp->whatsapp = $whatsappEnviar;
        $whatsapp->estado = 'PENDIENTE';
        $whatsapp->fecha_creacion = date('Y-m-d H:i:s');
        $whatsapp->fecha_envio = date('Y-m-d H:i:s');
        $whatsapp->save();

        $whatsappEnvio = WhaEnvios::find($whatsapp->id);
        $data = [
            'code' => '200',
            'telefono' => '593' . strval($whatsappEnvio->customer_celular),
            'mensaje' => $whatsappEnvio->whatsapp,
        ];
        $this->dispatchBrowserEvent('whatsapp', $data);
    }

    public function render()
    {
        $detalle = CreditFolderDetail::select(
            'credit_folder_details.*',
            'credit_folder_headers.id as credit_folder_headers_id',
            DB::raw("CONCAT(customer.nombres, ' ', customer.apellidos) as nombre_completo"),
            'customer.numero_documento',
            DB::raw("DATEDIFF(NOW(), credit_folder_details.date_vencimiento) as dias_mora"),
            'tipo_ahorros.name as tipo_ahorro_name',
            'tipo_ahorros.class as tipo_ahorro_class',
            'customer_tipo_ahorros.id as customer_tipo_ahorros_id',
        )
            ->leftjoin('credit_folder_headers', 'credit_folder_details.code_folder_header', '=', 'credit_folder_headers.code')
            ->leftjoin('customer', 'credit_folder_headers.customer_id', '=', 'customer.id')
            ->leftjoin('customer_tipo_ahorros', 'customer.id', '=', 'customer_tipo_ahorros.customer_id')
            ->leftjoin('tipo_ahorros', 'customer_tipo_ahorros.tipo_ahorros_id', '=', 'tipo_ahorros.id')
            ->where('credit_folder_details.company_id', Auth::user()->company_id)
            ->where('credit_folder_details.status', 'PENDIENTE')
            ->where('credit_folder_headers.status', 'ENTREGADO')
            ->whereBetween('credit_folder_details.date_vencimiento', [$this->fechaInicio, $this->fechaFin])
            ->where(function ($query) {
                $query->where('customer.nombres', 'like', '%' . $this->search . '%')
                    ->orWhere('customer.apellidos', 'like', '%' . $this->search . '%')
                    ->orWhere('customer.numero_documento', 'like', '%' . $this->search . '%');
            })

            ->where(function ($query) {
                $query->whereRaw("tipo_ahorros.descargo_creditos = true OR tipo_ahorros.descargo_creditos IS NULL");
            })
            ->orderBy('dias_mora', 'desc')
            ->groupBy('credit_folder_details.id')
            ->paginate(50);
        return view('livewire.debito-automatico.debito-automatico-component', compact('detalle'));
    }
}
