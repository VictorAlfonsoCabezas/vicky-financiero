<?php

namespace App\Http\Livewire\CustomerMovimientoSolicitud;

use App\Models\Bancos;
use App\Models\Customer;
use App\Models\CustomerMovimientoSolicitud;
use App\Models\CustomerTipoAhorros;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\FormasPago;
use Illuminate\Validation\Rule;



class CustomerMovimientoSolicitudComponent extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $valor = '';
    public $observacion = '';
    public $path = '';
    public $cuenta_selec = '';
    public $archivo;
    public $fecha_creacion = '';
    public $previewUrl;
    public $pagoTipoBanco = false;
    public $formasPago = 0;
    public $banco_id = '';
    public $comprobante = 0;
    public $forma_pago_id = '';
    public $numero_deposito = '';

    public $bancos = [];










    public function updatedArchivo()
    {
        if ($this->archivo) {
            $this->validate([
                'archivo' => 'image|max:2048', // 2MB max
            ]);

            $this->previewUrl = $this->archivo->temporaryUrl();
        }
    }

    /*public function abrirModal($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();
        if ($this->id_seleccionado !== 0) {
            $solocitud = CustomerMovimientoSolicitud::find($this->id_seleccionado);
            $this->cuenta_selec = $solocitud->customer_tipo_ahorro_id;
            $this->valor = $solocitud->valor;
            $this->comprobante = '';
            $this->banco_id = 1;
            $this->forma_pago_id = 1;

            $this->$this->observacion = $solocitud->observacion;
            $this->path = $solocitud->path;
            $this->fecha_creacion = $solocitud->fecha_creacion;
        }
    }

    public function cambioFormaPago($id)
    {
        $formaPago = FormasPago::find($id);
        $this->pagoTipoBanco = false;
        if (trim($formaPago->nombre) == 'TRANSFERENCIA' || trim($formaPago->nombre) == 'CHEQUE') {
            $this->pagoTipoBanco = true;
        }
    }

    public function mount()
    {
        $this->formasPago = FormasPago::all();
    }


    // /*/

    public function abrirModal($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();

        if ($this->id_seleccionado !== 0) {
            $solicitud = CustomerMovimientoSolicitud::find($this->id_seleccionado);

            $this->cuenta_selec = $solicitud->customer_tipo_ahorro_id;
            $this->valor = $solicitud->valor;
            //$this->comprobante = $solicitud->comprobante;
            $this->banco_id = $solicitud->banco_id ?? null;
            //$this->forma_pago_id = $solicitud->forma_pago_id ?? null;
            $this->observacion = $solicitud->observacion;
            $this->numero_deposito = $solicitud->numero_deposito;
            $this->path = $solicitud->path;
            $this->fecha_creacion = $solicitud->fecha_creacion;

            /*if ($this->forma_pago_id) {
                $this->cambioFormaPago($this->forma_pago_id);
            }*/
        }
    }

    /*public function cambioFormaPago($id)
    {
        $formaPago = FormasPago::find($id);
        $this->pagoTipoBanco = false;

        if ($formaPago && in_array(trim($formaPago->nombre), ['TRANSFERENCIA', 'CHEQUE'])) {
            $this->pagoTipoBanco = true;
            $this->bancos = Bancos::all();
        }
    }*/

    public function mount()
    {
        //$this->formasPago = FormasPago::all();
        $this->bancos = collect();
        $this->bancos = Bancos::all();
    }



    //ELIMINAR SOLICITUD

    public function eliminarSolicitud($id)
    {
        $solicitud = CustomerMovimientoSolicitud::find($id);

        if ($solicitud) {
            // Solo permitimos eliminar si está pendiente
            if ($solicitud->estado == 'PENDIENTE') {
                $solicitud->delete();
                session()->flash('message', 'Solicitud eliminada correctamente.');
            } else {
                session()->flash('error', 'Solo se pueden eliminar solicitudes pendientes.');
            }
        }
    }




    /////

    public function selecionaCuenta($id)
    {
        $this->cuenta_selec = $id;
    }

    private function limpiarFormulario()
    {
        $this->valor = '';
        $this->cuenta_selec = '';
        //$this->comprobante = '';
        $this->banco_id = '';
        //$this->forma_pago_id = '';
        $this->numero_deposito = '';
        $this->archivo = null;
        //$this->pagoTipoBanco = false;
        $this->reset(['observacion']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();
        if ($this->id_seleccionado !== 0) {
            $solicitud = CustomerMovimientoSolicitud::find($this->id_seleccionado);
            //Borrar imagen 
            if ($solicitud->archivo) {
                Storage::delete($solicitud->archivo);
            }
            $color = 'warning';
            $mensaje = 'La Solicitud se ha modificado correctamente';
        } else {
            $solicitud = new CustomerMovimientoSolicitud();
            $solicitud->company_id = Auth::user()->company_id;
            $solicitud->user_id = Auth::user()->id;
            $customer = Customer::where('company_id', Auth::user()->company_id)->where('user_id', Auth::user()->id)->first();
            $solicitud->customer_id = $customer->id;
            $solicitud->fecha_creacion = date('Y-m-d H:i:s');
            $color = 'success';
            $mensaje = 'La Solicitud se ha ingresado correctamente';
        }
        //Guardar imagen
        $ruta = $this->archivo->store('public/solicitudes');
        $url = Storage::url($ruta);
        $solicitud->customer_tipo_ahorro_id = $this->cuenta_selec;
        $solicitud->valor = $this->valor;
        $solicitud->banco_id = $this->banco_id ?? null;
        //$solicitud->comprobante = $this->comprobante;
        $solicitud->numero_deposito = $this->numero_deposito;

        //$solicitud->forma_pago_id = $this->forma_pago_id ?? null;
        $solicitud->observacion = $this->observacion;
        $solicitud->path = $url;
        $solicitud->archivo = $ruta;
        $solicitud->save();
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('closeModal');
        $this->dispatchBrowserEvent('alerta', $data);
    }

    protected function rules()
    {
        return [
            'valor' => ['required', 'regex:/^\d+(\.\d{1,2})?$/', 'numeric', 'min:0.01'],
            'cuenta_selec' => 'required',
            'banco_id' => 'required|exists:bancos,id',
            //'forma_pago_id' => 'required|exists:formas_pago,id',
            'archivo' => 'required|mimes:pdf,jpg,jpeg,png|max:1024',

            /*'comprobante' => [
                Rule::requiredIf(function () {
                    return in_array($this->forma_pago_id, [2, 3]);
                }),
                'string',
                'max:255'
            ],*/

            'numero_deposito' => [
                'required',
                'string',
                'max:255',
                'regex:/^[0-9]+$/',
            ],



            /*'banco_id' => [
                Rule::requiredIf(function () {
                    return in_array($this->forma_pago_id, [2, 3]);
                }),
                'exists:bancos,id'
            ],*/
        ];
    }

    protected function messages()
    {
        return [
            'valor.required' => 'El campo valor es obligatorio.',
            'valor.regex' => 'El valor debe tener máximo 2 decimales.',
            'valor.numeric' => 'El valor debe ser numérico.',
            'valor.min' => 'El valor debe ser mayor a 0.',

            'cuenta_selec.required' => 'Debe seleccionar una cuenta.',

            //'forma_pago_id.required' => 'Debe seleccionar una forma de pago.',
            //'forma_pago_id.exists' => 'La forma de pago seleccionada no es válida.',


            'archivo.required' => 'Debe subir un archivo.',
            'archivo.mimes' => 'El archivo debe ser PDF o imagen (jpg, jpeg, png).',
            'archivo.max' => 'El archivo no debe superar 1 MB.',

            //'comprobante.required' => 'El comprobante es obligatorio cuando la forma de pago es Cheque o Transferencia.',
            'numero_deposito.required' => 'El número de depósito es obligatorio',
            'banco_id.required' => 'Debe seleccionar un banco',
            'banco_id.exists' => 'El banco seleccionado no es válido.',
        ];
    }
    
    public function cuenta()
    {
        return $this->belongsTo(\App\Models\CustomerTipoAhorros::class, 'customer_tipo_ahorro_id');
    }



    public function render()
    {
        $movimientos = CustomerMovimientoSolicitud::where('company_id', Auth::user()->company_id)
            ->where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->paginate(20);
        $customer = Customer::where('company_id', Auth::user()->company_id)
            ->where('user_id', Auth::user()->id)
            ->first();
        $cuentas = CustomerTipoAhorros::where('company_id', Auth::user()->company_id)
            ->where('customer_id', $customer->id)
            ->get();
        return view('livewire.customer-movimiento-solicitud.customer-movimiento-solicitud-component', compact('movimientos', 'cuentas'));
    }
}
