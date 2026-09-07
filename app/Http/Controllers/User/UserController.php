<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\User;
use App\Models\Rol;
use App\Models\UsuarioRol;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Response;
use Image;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
use Auth;
use Illuminate\Support\Facades\URL;

//use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    public function index()
    {
        return view('usuarios/index');
    }

    public function indexData()
    {
        $user = User::all();
        foreach ($user as $use) {
            $rol = UsuarioRol::where('user_id', $use->id)->first();
            $rol_name = Rol::find($rol->rol_id)->nombre;
            $use->rol_name = $rol_name;
        }
        return $user;
    }

    public function create()
    {
        $roles = Rol::all();
        $empresa = Company::all();
        return view('usuarios/create')
            ->with('empresa', $empresa)
            ->with('roles', $roles);
    }

    public function store(Request $request)
    {
        foreach ($request->input('empresa') as $key => $value) {
            if ($key == 0) {
                $empresa = $value;
            }
        }
        if ($key !== 0) {
            $separado = implode(",", $request->input('empresa'));
        } else {
            $separado = '';
        }
        $usuario = new User();
        $usuario->company_id = $empresa;
        $usuario->company_varias = $separado;
        $usuario->firstname = strtoupper(request('firstname'));
        $usuario->lastname = strtoupper(request('lastname'));
        $usuario->username = request('username');
        $usuario->ruc = request('ruc');
        $usuario->email = request('email');
        $usuario->password = Hash::make(request('password'));
        $usuario->remember_token = bcrypt(request('password'));
        $usuario->token = request('password');
        $usuario->save();
        if ($request->file('photo') !== null) {
            $imagen = $request->file('photo');
            $nombre = time() . '.' . $imagen->getClientOriginalExtension();
            $destino = public_path('uploads/users');
            $request->photo->move($destino, $nombre);
            $usuario->photo = $nombre;
            $usuario->save();
        }
        $rol = new UsuarioRol();
        $rol->rol_id = request('rol');
        $rol->user_id = $usuario->id;
        $rol->status = true;
        $rol->save();
        //enviar email
        //        $datos=[
        //            'usuario' => request('username'),
        //            'clave' => request('password')
        //        ];
        //        Mail::send("email.welcome_user", $datos, function ($mensaje){
        //           $mensaje->to("pacjohn92@gmail.com", "MANUEL PEREZ")->subject("ONIX usuario y clave del sistema"); 
        //        });
        return redirect('/usuarios');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $user->rol = UsuarioRol::where('user_id', $id)->value('rol_id');
        $roles = Rol::all();
        $empresa = Company::where('status', true)->get();
        $data = [];
        if ($user->company_varias !== null && $user->company_varias !== '') {
            $empresas = explode(",", $user->company_varias);
            foreach ($empresa as $value) {
                $indice = in_array($value->id, $empresas);
                if ($indice) {
                    $data[] = [
                        'id' => $value->id,
                        'name' => $value->company_name,
                        'status' => true
                    ];
                } else {
                    $data[] = [
                        'id' => $value->id,
                        'name' => $value->company_name,
                        'status' => false
                    ];
                }
            }
        } else {
            foreach ($empresa as $value) {
                $data[] = [
                    'id' => $value->id,
                    'name' => $value->company_name,
                    'status' => false
                ];
            }
        }
        return view('usuarios/edit')
            ->with('roles', $roles)
            ->with('empresa', $empresa)
            ->with('empresas', $data)
            ->with('user', $user);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'rol' => 'required|integer|exists:rol,id',
            'empresa' => 'required|array|min:1',
            'empresa.*' => 'required|integer|exists:company,id',
        ]);
        foreach ($request->input('empresa') as $key => $value) {
            if ($key == 0) {
                $empresa = $value;
            }
        }
        if ($key !== 0) {
            $separado = implode(",", $request->input('empresa'));
        } else {
            $separado = '';
        }
        $user = User::find($id);
        $user->company_id = $empresa;
        $user->company_varias = $separado;
        $user->firstname = strtoupper(request('firstname'));
        $user->lastname = strtoupper(request('lastname'));
        $user->username = request('username');
        $user->ruc = request('ruc');
        $user->email = request('email');
        $user->password = Hash::make(request('password'));
        $user->remember_token = bcrypt(request('password'));
        $user->token = request('password');
        $user->status = true;
        $user->save();
        if ($request->file('photo') !== null) {
            $imagen = $request->file('photo');
            if ($user->photo !== null) {
                $nombre = $user->photo;
                $borrar = public_path() . '/uploads/users/' . $user->photo;
                unlink($borrar);
            } else {
                $nombre = time() . '.' . $imagen->getClientOriginalExtension();
            }
            $destino = public_path('uploads/users');
            $request->photo->move($destino, $nombre);
            $user->photo = $nombre;
            $user->save();
        }
        $rol = UsuarioRol::firstOrNew(['user_id' => $id]);
        $rol->rol_id = $request->input('rol');
        if (!$rol->exists) {
            $rol->status = true;
        }
        $rol->save();
        return redirect('/usuarios')->with('mensaje', 'Usuario Editado exitosamente');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->status = false;
        $user->save();
        return $user;
    }

    public function darUsername($nombre, $apellido)
    {
        $inicial = strtolower(substr($nombre, 0, 1) . $apellido);
        $username = User::where('username', $inicial);
        if ($username->count() !== 0) {
            $i = 0;
            $j = 1;
            while ($i == 0) {
                $valor = $inicial . $j;
                $username = User::where('username', $valor);
                if ($username->count() == 0) {
                    $i = 1;
                }
                $j++;
            }
            return Response::json($valor);
        } else {
            return Response::json($inicial);
        }
    }

    public function profile()
    {
        $rolName = '';
        $usuarioRol = UsuarioRol::where('user_id', Auth::user()->id)->first();
        if ($usuarioRol != null && $usuarioRol != '') {
            $rol = Rol::find($usuarioRol->rol_id);
            $rolName = $rol->nombre;
        }
        return view('usuarios/profile')->with('rolName', $rolName);
    }

    public function actualizarPassword(Request $request)
    {
        $data = $request->input('parametros');
        $user = User::find(Auth::user()->id);
        $user->password = Hash::make($data['nueva']);
        $user->remember_token = bcrypt($data['nueva']);
        $user->token = $data['nueva'];
        $user->save();
        return Response::json(true);
    }

    public function verDatos(Request $request)
    {
        $users = User::whereNotNull('username');
        $totalData = (clone $users)->count();
        foreach ((array) $request->input('columns', []) as $column) {
            $search = trim((string) data_get($column, 'search.value', ''));
            $field = data_get($column, 'data');
            if ($search === '') { continue; }
            if ($field === 'name') {
                foreach (preg_split('/\s+/', $search) as $term) {
                    $users->where(function ($query) use ($term) {
                        $query->where('firstname', 'like', "%{$term}%")->orWhere('lastname', 'like', "%{$term}%");
                    });
                }
            } elseif (in_array($field, ['id', 'username', 'email'], true)) {
                $users->where($field, 'like', "%{$search}%");
            }
        }
        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $users->where(function ($query) use ($search) {
                foreach (['firstname', 'lastname', 'username', 'email'] as $field) {
                    $query->orWhere($field, 'like', "%{$search}%");
                }
            });
        }
        $totalFiltered = (clone $users)->count();
        $columns = ['id', 'firstname', 'username', 'email'];
        $order = $columns[(int) $request->input('order.0.column', 0)] ?? 'id';
        $dir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $limit = max(1, min(100, (int) $request->input('length', 10)));
        $posts = $users->with('roles')->orderBy($order, $dir)
            ->offset(max(0, (int) $request->input('start', 0)))->limit($limit)->get();
        $data = array();
        if (!empty($posts)) {
            foreach ($posts as $state) {
                $nestedData['id'] = $state->id;
                $nestedData['name'] = e(trim($state->firstname . ' ' . $state->lastname));
                $nestedData['username'] = e($state->username);
                $nestedData['email'] = e($state->email);
                $nestedData['rol_name'] = e($state->roles->pluck('nombre')->implode(', ') ?: 'Sin rol');
                if ($state->status) {
                    $estado = '<label class="badge bg-info rounded-pill">Activo</label>';
                } else {
                    $estado = '<label class="badge bg-danger rounded-pill">Inactivo</label>';
                }
                $nestedData['status'] = '<a onclick="javascript:cambioEstado(' . $state->id . ')" >' . $estado . '</a>';
                $nestedData['acciones'] = '<a href="' . URL::to(('usuarios/' . $state->id . '/edit')) . '" method="GET" class="btn btn-warning btn-xs" style="color: white;"><i class="fas fa-edit"></i> Editar</a>';
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        return Response::json($json_data);
    }

    public function cambioEstado($id)
    {
        $user = User::find($id);
        if ($user->status) {
            $new = 0;
        } else {
            $new = 1;
        }
        $usuario = User::find($id);
        $usuario->status = $new;
        $usuario->save();
    }
}
