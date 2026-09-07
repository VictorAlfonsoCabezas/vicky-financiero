<?php

namespace App\Http\Livewire\CustomerUser;

use App\Models\Country;
use App\Models\Customer;
use App\Models\Genero;
use App\Models\Rol;
use App\Models\UsuarioRol;
use App\Models\WhaEnvios;
use App\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerUserComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $usuario = '';
    public $clave = '';
    public $id_seleccionado = 0;
    public $search = '';
    public $opcion = 0;

    public function cargarDatosModal($id)
    {
        $customer = Customer::find($id);
        $this->usuario = $customer->numero_documento;
        $this->clave = $customer->numero_documento;
        $this->id_seleccionado = $id;
    }

    public function enviarClaveWhatsapp($id)
    {
        $customer = Customer::find($id);
        $clave = $this->generarClave(8);
        $usuario = User::find($customer->user_id);
        $usuario->password = Hash::make($clave);
        $usuario->remember_token = bcrypt($clave);
        $usuario->token = $clave;
        $usuario->save();

        $whatsappEnviar = 'Estimad@, *' . $customer->nombres . ' ' . $customer->apellidos . '* su nueva clave temporal es: *' . $clave . '*, por favor recuerde ingresar a la pagina de acceso y cambiarla respectivamente. ';
        $whatsapp = new WhaEnvios();
        $whatsapp->company_id = Auth::user()->company_id;
        $whatsapp->envio_ahora = false;
        $whatsapp->es_transacion = false;
        $whatsapp->inmediato = true;
        $whatsapp->description = 'ENVIO PASSWORD CAJA WEB';
        $whatsapp->customer_id = $customer->id;
        $whatsapp->customer_name = $customer->nombres . ' ' . $customer->apellidos;

        $country = Country::find($customer->country_id);
        if ($country->codigo_pais == '593') {
            $celular = '593' . substr($customer->telefono, 1);
        } else {
            $celular = $country->codigo_pais  . $customer->telefono;
        }

        $whatsapp->customer_celular = $celular;
        $whatsapp->whatsapp = $whatsappEnviar;
        $whatsapp->estado = 'ENVIADO';
        $whatsapp->fecha_creacion = date('Y-m-d H:i:s');
        $whatsapp->fecha_envio = date('Y-m-d H:i:s');
        $whatsapp->save();

        $whatsappEnvio = WhaEnvios::find($whatsapp->id);


        $data = [
            'code' => '200',
            'telefono' =>  $celular,
            'mensaje' => $whatsappEnvio->whatsapp,
        ];
        $this->dispatchBrowserEvent('whatsapp', $data);
    }

    private function generarClave($longitud = 8)
    {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $claveGenerada = '';
        for ($i = 0; $i < $longitud; $i++) {
            $claveGenerada .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        return $claveGenerada;
    }

    public function guardarUsuario()
    {
        $this->validate([
            "usuario" => "required",
            "clave" => "required",
        ]);

        $customer = Customer::find($this->id_seleccionado);
        $usuario = new User();
        $usuario->company_id = Auth::user()->company_id;
        $usuario->company_varias = Auth::user()->company_id;
        $usuario->firstname = strtoupper($customer->nombres);
        $usuario->lastname = strtoupper($customer->apellidos);
        $usuario->username = $this->usuario;
        $usuario->ruc = $customer->numero_documento;
        $usuario->email = 'test' . $customer->id . '@hotmail.com';
        $usuario->password = Hash::make($this->clave);
        $usuario->remember_token = bcrypt($this->clave);
        $usuario->token = $this->clave;
        $usuario->save();

        //Crear ROL cliente si no existe
        $rolCliente = Rol::firstOrCreate(
            ['nombre' => 'CLIENTE'],
            [
                'user_create' => Auth::user()->id,
                'nombre' => 'CLIENTE',
                'observation' => 'ROL CREADO POR EL SISTEMA PARA LOS CLIENTES',
            ]
        );

        $userRol = new UsuarioRol();
        $userRol->rol_id = $rolCliente->id;
        $userRol->user_id = $usuario->id;
        $userRol->status = true;
        $userRol->save();

        //empatar customer con usuario
        $customer->user_id = $usuario->id;
        $customer->save();
        $this->dispatchBrowserEvent('closeModal');
        $color = 'success';
        $mensaje = 'Usuario Creado correctamente';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function render()
    {
        if ($this->opcion == 0) {
            $customer = Customer::where('customer.company_id', Auth::user()->company_id)
                ->where('numero_documento', 'like', '%' . $this->search . '%')
                ->orwhere('nombres', 'like', '%' . $this->search . '%')
                ->orwhere('apellidos', 'like', '%' . $this->search . '%')
                ->paginate(10);
        } elseif ($this->opcion == 1) {
            $customer = Customer::where('customer.company_id', Auth::user()->company_id)
                ->where(function ($query) {
                    $query->where('numero_documento', 'like', '%' . $this->search . '%')
                        ->orWhere('nombres', 'like', '%' . $this->search . '%')
                        ->orWhere('apellidos', 'like', '%' . $this->search . '%');
                })
                ->whereNotNull('customer.user_id')
                ->paginate(10);
        } else {
            $customer = Customer::where('company_id', Auth::user()->company_id)
                ->where(function ($query) {
                    $query->where('numero_documento', 'like', '%' . $this->search . '%')
                        ->orWhere('nombres', 'like', '%' . $this->search . '%')
                        ->orWhere('apellidos', 'like', '%' . $this->search . '%');
                })
                ->whereNull('user_id')
                ->paginate(10);
        }
        return view('livewire.customer-user.customer-user-component', compact('customer'));
    }
}
