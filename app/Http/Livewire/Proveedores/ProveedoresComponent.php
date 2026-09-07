<?php

namespace App\Http\Livewire\Proveedores;

use App\Models\CentroCostos;
use App\Models\PlanCuentas;
use App\Models\Proveedores;
use App\Models\ProveedoresContacto;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ProveedoresComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $id_seleccionado = 0;
    public $nombre = '';
    public $descripcion = '';
    public $ruc = '';
    public $direccion = '';
    public $telefono = '';
    public $email = '';
    public $nombre_contacto = '';
    public $telefono_contacto = '';
    public $email_contacto = '';
    public $plan_cuenta_id = '';
    public $centro_costos_id = '';
    public $status = true;

    public $tipo_factura = '';
    public $numero_autorizacion = '';
    public $fecha_caducidad = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();
        if ($this->id_seleccionado !== 0) {
            $prove = Proveedores::find($id);
            $this->nombre = strtoupper($prove->nombre);
            $this->descripcion = strtoupper($prove->descripcion);
            $this->ruc = $prove->ruc;
            $this->direccion = $prove->direccion;
            $this->telefono = $prove->telefono;
            $this->email = $prove->email;
            $this->plan_cuenta_id = $prove->plan_cuenta_id;
            $this->centro_costos_id = $prove->centro_costos_id;
            $this->status = $prove->status;

            $this->tipo_factura = $prove->tipo_factura;
            $this->numero_autorizacion = $prove->numero_autorizacion;
            $this->fecha_caducidad = $prove->fecha_caducidad;
        }
    }

    public function updatedTipoFactura()
    {
        if ($this->tipo_factura !== 'fisica') {
            $this->numero_autorizacion = null;
            $this->fecha_caducidad = null;

            $this->resetValidation([
                'numero_autorizacion',
                'fecha_caducidad'
            ]);
        }
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, [
            'tipo_factura',
            'numero_autorizacion',
            'fecha_caducidad'
        ])) {
            $this->validateOnly($propertyName, [
                'tipo_factura' => 'nullable|in:fisica,electronica',

                'numero_autorizacion' => [
                    'nullable',
                    'required_if:tipo_factura,fisica',
                    'digits:10',
                ],

                /*'tipo_factura' => 'nullable|in:fisica,electronica',

                'numero_autorizacion' => [
                    'nullable',
                    'required_if:tipo_factura,fisica,electronica',
                    function ($attribute, $value, $fail) {
                        if ($this->tipo_factura === 'fisica' && strlen($value) !== 10) {
                            $fail('Debe tener exactamente 10 caracteres.');
                        }

                        if ($this->tipo_factura === 'electronica' && strlen($value) !== 49) {
                            $fail('Debe tener exactamente 49 caracteres.');
                        }
                    }
                ],*/

                'fecha_caducidad' =>
                //'nullable|required_if:tipo_factura,fisica,electronica|date',
                'nullable|required_if:tipo_factura,fisica|date',
            ]);
        }
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'descripcion', 'ruc', 'direccion', 'telefono', 'email', 'plan_cuenta_id', 'centro_costos_id', 'status', 'tipo_factura', 'numero_autorizacion', 'fecha_caducidad',]);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeProveedores()
    {
        $this->validate([
            'nombre' => 'required',
            'ruc' => 'required|digits:13',
            'direccion' => 'required',

            'tipo_factura' => 'nullable|in:fisica,electronica',

            'numero_autorizacion' => [
                'nullable',
                'required_if:tipo_factura,fisica',
                'digits:10',
            ],

            /*'tipo_factura' => 'nullable|in:fisica,electronica',

            'numero_autorizacion' => [
                'nullable',
                'required_if:tipo_factura,fisica,electronica',
                'required_if:tipo_factura,fisica',
                function ($attribute, $value, $fail) {
                    if ($this->tipo_factura === 'fisica' && strlen($value) !== 10) {
                        $fail('Para factura física el número debe tener exactamente 10 caracteres.');
                    }

                    if ($this->tipo_factura === 'electronica' && strlen($value) !== 49) {
                        $fail('Para factura electrónica el número debe tener exactamente 49 caracteres.');
                    }
                }
            ],*/

            'fecha_caducidad' =>
            //'nullable|required_if:tipo_factura,fisica,electronica|date',
            'nullable|required_if:tipo_factura,fisica|date',
        ]);


        if ($this->id_seleccionado > 0) {
            $proveedores = Proveedores::find($this->id_seleccionado);
        } else {
            $proveedores = new Proveedores();
            $proveedores->company_id = Auth::user()->company_id;
        }
        $proveedores->nombre = strtoupper($this->nombre);
        $proveedores->descripcion = strtoupper($this->descripcion);
        $proveedores->ruc = $this->ruc;
        $proveedores->direccion = $this->direccion;
        $proveedores->telefono = $this->telefono;
        $proveedores->email = $this->email;
        $proveedores->plan_cuenta_id = $this->plan_cuenta_id;
        $proveedores->centro_costos_id = $this->centro_costos_id;
        $proveedores->status = $this->status;

        $proveedores->tipo_factura = $this->tipo_factura;
        $proveedores->numero_autorizacion = $this->numero_autorizacion;
        $proveedores->fecha_caducidad = $this->fecha_caducidad;

        $proveedores->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function agregarContacto()
    {
        if ($this->nombre_contacto !== '' && $this->telefono_contacto !== '') {
            $contacto = new ProveedoresContacto();
            $contacto->company_id = Auth::user()->company_id;
            $contacto->proveedor_id = $this->id_seleccionado;
            $contacto->nombre_contacto = $this->nombre_contacto;
            $contacto->telefono = $this->telefono_contacto;
            $contacto->email = $this->email_contacto;
            $contacto->save();
            $this->reset(['nombre_contacto', 'telefono_contacto', 'email_contacto']);

            $color = 'success';
            $mensaje = 'Contacto agregado...';
            $data = [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
        } else {
            $color = 'danger';
            $mensaje = 'Nombres y Teléfono...';
            $data = [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
        }
    }

    public function quitarContacto($id)
    {
        $contacto = ProveedoresContacto::find($id)->delete();
        $color = 'danger';
        $mensaje = 'Se elimino el contacto...';
        $data = [
            'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function render()
    {
        $proveedores = Proveedores::where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('ruc', 'like', '%' . $this->search . '%');
            })
            ->paginate(9);

        $contactos = ProveedoresContacto::where('company_id', Auth::user()->company_id)
            ->where('proveedor_id', $this->id_seleccionado)
            ->get();

        $planGastos = PlanCuentas::where('company_id', Auth::user()->company_id)
            ->where('gasto', true)
            ->where('status', true)
            ->get();

        $centroCostos = CentroCostos::where('company_id', Auth::user()->company_id)
            ->where('status', true)
            ->get();

        return view('livewire.proveedores.proveedores-component', compact('proveedores', 'contactos', 'planGastos', 'centroCostos'));
    }
}
