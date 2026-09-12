<?php

namespace App\Http\Livewire\UserNew;

use App\Models\Company;
use App\Models\LogSession;
use App\Models\Rol;
use App\Models\Sedes;
use App\Models\UsuarioRol;
use App\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UserNewEditComponent extends Component
{
    public $id_selected;
    public $firstname;
    public $lastname;
    public $username;
    public $email;
    public $token;
    public $ruc;
    public $admin;
    public $permiso_caja_valor;
    public $permiso_credito_aprobar;
    public $reversar_cajas;
    public $reversar_movimientos;
    public $cierre_caja;
    public $permiso_reversar_creditos;
    public $permiso_numero_cuentas;
    public $status;
    public $company_id;
    public $sede_id;
    public $rol_id;
    public $rol_name;
    public $tab = 'settings';
    public $totalLogs = 0;

    public function createUpdateUser()
    {
        $this->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'username' => 'required|unique:users,username,' . $this->id_selected,
            'rol_id' => 'required',
            'company_id' => 'required',
            'sede_id' => 'required',
            'email' => 'required|email',
            'token' => 'required|min:6',
            'ruc' => 'required'
        ]);

        if ($this->id_selected > 0) {
            $user = User::find($this->id_selected);
        } else {
            $user = new User();
        }
        $user->company_id = $this->company_id;
        $user->sede_id = $this->sede_id;
        $user->company_varias = $this->company_id;
        $user->firstname = strtoupper($this->firstname);
        $user->lastname = strtoupper($this->lastname);
        $user->username = $this->username;
        $user->ruc = $this->ruc;
        $user->email = $this->email;

        //Guardar solo si se cambia la informacion
        $user->token = $this->token;
        $user->password = Hash::make($this->token);
        $user->remember_token = bcrypt($this->token);

        $user->save();

        //Vaciar roles y crear nuevo
        UsuarioRol::where('user_id', $user->id)->delete();
        $rol = new UsuarioRol();
        $rol->rol_id = $this->rol_id;
        $rol->user_id = $user->id;
        $rol->status = true;
        $rol->save();

        return redirect()->to('/user-new/' . $user->id);
    }

    public function updateLastname()
    {
        if ($this->id_selected == 0) {
            $initial = substr($this->firstname, 0, 1);
            $lastnames = explode(' ', $this->lastname);
            $first_lastname = $lastnames[0];
            $result = $initial . $first_lastname;
            $this->username = strtolower($result);
            $this->token = strtolower($result);
        }
    }

    public function cambiaAdmin()
    {
        $user = User::find($this->id_selected);
        $user->admin = !$user->admin;
        $user->save();
    }

    public function cambiaPermisoCajaValor()
    {
        $user = User::find($this->id_selected);
        $user->permiso_caja_valor = !$user->permiso_caja_valor;
        $user->save();
    }

    public function cambiaPermisoCreditoAprobar()
    {
        $user = User::find($this->id_selected);
        $user->permiso_credito_aprobar = !$user->permiso_credito_aprobar;
        $user->save();
    }

    public function cambiaReversarCajas()
    {
        $user = User::find($this->id_selected);
        $user->reversar_cajas = !$user->reversar_cajas;
        $user->save();
    }

    public function cambiaReversarMovimientos()
    {
        $user = User::find($this->id_selected);
        $user->reversar_movimientos = !$user->reversar_movimientos;
        $user->save();
    }

    public function cambiaCierreCaja()
    {
        $user = User::find($this->id_selected);
        $user->cierre_caja = !$user->cierre_caja;
        $user->save();
    }

    public function cambiaReversoCredito()
    {
        $user = User::find($this->id_selected);
        $user->permiso_reversar_creditos = !$user->permiso_reversar_creditos;
        $user->save();
    }

    public function cambiaNumeroCuenta()
    {
        $user = User::find($this->id_selected);
        $user->permiso_numero_cuentas = !$user->permiso_numero_cuentas;
        $user->save();
    }

    public function cambioTab($tab)
    {
        $this->tab = $tab;
    }

    public function mount($id)
    {
        if ($id > 0) {
            $this->id_selected = $id;
            $user = User::findOrFail($id);
            $this->company_id = $user->company_id;
            $this->sede_id = $user->sede_id;
            $assignment = UsuarioRol::where('user_id', $id)->first();
            $this->rol_id = $assignment ? $assignment->rol_id : '';
            $this->rol_name = optional(Rol::find($this->rol_id))->nombre ?: 'Sin rol asignado';
            $this->firstname = $user->firstname;
            $this->lastname = $user->lastname;
            $this->username = $user->username;
            $this->email = $user->email;
            $this->token = $user->token;
            $this->ruc = $user->ruc;
            $this->admin = $user->admin;
            $this->permiso_caja_valor = $user->permiso_caja_valor;
            $this->permiso_credito_aprobar = $user->permiso_credito_aprobar;
            $this->reversar_cajas = $user->reversar_cajas;
            $this->reversar_movimientos = $user->reversar_movimientos;
            $this->cierre_caja = $user->cierre_caja;
            $this->permiso_reversar_creditos = $user->permiso_reversar_creditos;
            $this->permiso_numero_cuentas = $user->permiso_numero_cuentas;
            $this->status = $user->status;
            $this->totalLogs = LogSession::where('user_id', $this->id_selected)->count();
        } else {
            $this->sede_id = '';
            $this->company_id = '';
            $this->rol_id = '';
            $this->rol_name = '';
            $this->firstname = '';
            $this->lastname = '';
            $this->username = '';
            $this->email = '';
            $this->token = '';
            $this->ruc = '';
            $this->admin = 0;
            $this->permiso_caja_valor = 0;
            $this->permiso_credito_aprobar = 0;
            $this->reversar_cajas = 0;
            $this->reversar_movimientos = 0;
            $this->cierre_caja = 0;
            $this->permiso_reversar_creditos = 0;
            $this->permiso_numero_cuentas = 0;
            $this->status = 1;
            $this->totalLogs = 0;
        }
    }

    public function render()
    {
        $empresas = Company::where('status', true)->get();
        $sedes = Sedes::where('status', true)->get();
        $roles = Rol::where('status', true)->get();
        $logSession = LogSession::where('user_id', $this->id_selected)->orderBy('fecha_creacion', 'desc')->get();
        return view('livewire.user-new.user-new-edit-component', compact('empresas', 'sedes', 'roles', 'logSession'));
    }
}
