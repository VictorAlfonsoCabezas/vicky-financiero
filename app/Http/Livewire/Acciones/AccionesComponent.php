<?php

namespace App\Http\Livewire\Acciones;

use App\Models\AccionesDetalle;
use App\Models\AccionesDetalleValores;
use App\Models\AccionesHeader;
use App\Models\TypeTransaction;
use App\Models\CustomerMovimiento;
use App\Models\CustomerHistorial;
use App\Models\Customer;
use App\Models\Meses;
use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AccionesComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = '';
    public $nombre = '';
    public $descripcion = '';
    public $anio = '';
    public $mes = '';
    public $capital = '';
    public $certificado = '';
    public $anios = [];

    public  function abrirModal($id)
    {
        $this->limpiarFormulario();
        $this->id_seleccionado = $id;
        if ($this->id_seleccionado > 0) {
            $header = AccionesHeader::find($id);
            $this->nombre = $header->nombre;
            $this->descripcion = $header->descripcion;
            $this->anio = $header->anio;
            $this->mes = $header->mes;
            $this->capital = $header->capital;
        }
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre']);
        $this->reset(['descripcion']);
        $this->reset(['anio']);
        $this->reset(['mes']);
        $this->reset(['capital']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function eliminar($header)
    {
//        $cantidad = AccionesHeader::where('company_id', Auth::user()->company_id)->first();
//        if($cantidad->id  == $header){
//            $this->generarSalida($header); 
//        }
        AccionesDetalleValores::where('company_id', Auth::user()->company_id)->where('acciones_header_id', $header)->delete();
        AccionesDetalle::where('company_id', Auth::user()->company_id)->where('acciones_header_id', $header)->delete();
        AccionesHeader::find($header)->delete();
    }

    public function generarSalida($header){
        $header = AccionesHeader::find($header);
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'RU')
                ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
        $data = [
            "code" => $code,
            "company_id" => Auth::user()->company_id,
            "afecta" => $transaction->afecta,       
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $header->capital,
            "saldo_general" => $valorTotal - $header->capital,
            "observation" => $transaction->name,
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $this->capital, '', $movimientos->saldo_general, date('Y-m-d'));
    }
    public function storeHeader()
    {
        
        $this->validate([
            "nombre" => "required",
            "anio" => "required",
            "mes" => "required",
            "capital" => "required",
            "certificado" => "required",
        ]);


        if ($this->id_seleccionado > 0) {
            $header = AccionesHeader::find($this->id_seleccionado);
        } else {
            $header = new AccionesHeader();
            $header->company_id = Auth::user()->company_id;
            //color aleatorio
            $clases = ['bg-info', 'bg-success', 'bg-warning', 'bg-danger'];
            $indiceAleatorio = array_rand($clases);
            $header->class = $clases[$indiceAleatorio];
        }
        $header->nombre = $this->nombre;
        $header->descripcion = $this->descripcion;
        $header->anio = $this->anio;
        $header->mes = $this->mes;
        $header->capital = $this->capital;
        $header->certificado = $this->certificado;
        $header->save();
        $this->dispatchBrowserEvent('closeModal');
        $cantidad = AccionesHeader::where('company_id', Auth::user()->company_id)->count();
        if($cantidad  == 1){
//            $this->generarMovimiento();    
        }
        
    }

    public function mount()
    {
        $anioActual = date('Y');
        $aniosAnteriores = [];
        for ($i = 0; $i < 5; $i++) {
            $aniosAnteriores[] = $anioActual - $i;
        }
        $this->anios = array_reverse($aniosAnteriores);
    }

    public function generarMovimiento()
    {
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'CVS')
                ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
        $data = [
            "code" => $code,
            "company_id" => Auth::user()->company_id,
            "afecta" => $transaction->afecta,       
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $this->capital,
            "saldo_general" => $valorTotal + $this->capital,
            "observation" => $transaction->name,
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $this->capital, '', $movimientos->saldo_general, date('Y-m-d'));

        
    }

    public function render()
    {
        $header = AccionesHeader::where('company_id', Auth::user()->company_id)->get();
        $meses = Meses::where('company_id', Auth::user()->company_id)->where('status', true)->orderBy('codigo')->get();
        return view('livewire.acciones.acciones-component', compact('header', 'meses'));
    }
}
