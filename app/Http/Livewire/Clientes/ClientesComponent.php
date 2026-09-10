<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Bancos;
use App\Models\Ciudad;
use App\Models\Country;
use App\Models\CreditFiles;
use App\Models\CreditFolderHeader;
use App\Models\Customer;
use App\Models\CustomerFile;
use App\Models\CustomerParentezco;
use App\Models\CustomerTipoAhorros;
use App\Models\EstadoCivil;
use App\Models\Genero;
use App\Models\NivelAcademico;
use App\Models\Parentezco;
use App\Models\Parroquia;
use App\Models\Provincia;
use App\Models\TipoCuenta;
use App\Models\TipoDocumento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Exports\Customer\CustomerExport;
use Maatwebsite\Excel\Facades\Excel;

class ClientesComponent extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    public $archivo;
    public $descrpcion = '';
    public $id_seleccionado = 0;
    public $numero_documento = '';
    public $fecha_nacimiento = '';
    public $edad = 0;
    public $nombres = '';
    public $apellidos = '';
    public $telefono = '';
    public $telefono_fijo = '';
    public $correo = '';
    public $direccion = '';
    public $conyugue_nombre = '';
    public $conyugue_identificacion = '';
    public $conyugue_telefono = '';
    public $name_parentesco = '';
    public $telefono_parentesco = '';
    public $latitudlongitud = '';
    public $latitud = '';
    public $longitud = '';
    public $created_at = '';
    public $updated_at = '';
    public $search = '';
    public $tipoDocumento = [];
    public $tipo_documento_id = '';
    public $genero = [];
    public $genero_id = '';
    public $estadoCivil = [];
    public $estado_civil_id = '';
    public $tipo_vivienda = '';
    public $tiempo_vivienda = '';
    public $paises = [];
    public $pais_id = '';
    public $niveles = [];
    public $nivel_academico_id = '';
    public $parentezco = [];
    public $parentezco_id = '';
    public $provincia = [];
    public $provincia_id = '';
    public $parroquia = [];
    public $parroquia_id = '';
    public $ciudad = [];
    public $ciudad_id = '';
    public $banco = [];
    public $banco_id = '';
    public $tipo_cuenta = [];
    public $tipo_cuenta_id = '';
    public $no_cuenta = '';
    public $valores = [];
    public $totalCuentas = 0;
    public $totalPrestamos = 0;
    public $fundador = 0;
    public $seperacion_bienes = 0;
    public $cargas_familiares = 0;
    public $conyuge_nivel_academico_id = '';
    public $conyuge_fecha_nacimiento = '';
    public $conyuge_ocupacion = '';
    public $conyuge_empresa_nombre = '';
    public $conyuge_empresa_direccion = '';
    public $conyuge_empresa_telefono = '';
    public $conyuge_tiempo_empresa = '';
    public $conyuge_cargo_empresa = '';

    public $ocupacion = 'NINGUNO';
    public $empresa_nombre = '';
    public $empresa_direccion = '';
    public $empresa_provincia_id = '';
    public $empresa_canton_id = '';
    public $empresa_parroquia_id = '';
    public $empresa_telefono = '';
    public $empresa_tiempo = '';
    public $empresa_cargo = '';
    public $negocio_nombre = '';
    public $negocio_direccion = '';
    public $negocio_provincia_id = '';
    public $negocio_canton_id = '';
    public $negocio_parroquia_id = '';
    public $negocio_telefono = '';
    public $negocio_tiempo = '';
    public $negocio_actividad = '';
    public $date_open_account = '';




    protected $listeners = [
        'selectorProvincia',
        'selectorCiudad',
        'selectorParroquia',
        'selectorPais',
        'selectorNivel',
        'selectorNivelConyuge',
        'selectorEProvincia',
        'selectorECiudad',
        'selectorEParroquia',
        'selectorNProvincia',
        'selectorNCiudad',
        'selectorNParroquia'
    ];

    public function selectorEProvincia($id_cambiado)
    {
        $this->empresa_provincia_id = $id_cambiado;
    }

    public function selectorECiudad($id_cambiado)
    {
        $this->empresa_canton_id = $id_cambiado;
    }

    public function selectorEParroquia($id_cambiado)
    {
        $this->empresa_parroquia_id = $id_cambiado;
    }

    public function selectorNProvincia($id_cambiado)
    {
        $this->negocio_provincia_id = $id_cambiado;
    }

    public function selectorNCiudad($id_cambiado)
    {
        $this->negocio_canton_id = $id_cambiado;
    }

    public function selectorNParroquia($id_cambiado)
    {
        $this->negocio_parroquia_id = $id_cambiado;
    }

    public function selectorNivelConyuge($id_cambiado)
    {
        $this->conyuge_nivel_academico_id = $id_cambiado;
    }

    public function selectorNivel($id_cambiado)
    {
        $this->nivel_academico_id = $id_cambiado;
    }

    public function selectorPais($id_cambiado)
    {
        $this->pais_id = $id_cambiado;
    }

    public function selectorProvincia($id_cambiado)
    {
        $this->provincia_id = $id_cambiado;
    }

    public function selectorCiudad($id_cambiado)
    {
        $this->ciudad_id = $id_cambiado;
    }

    public function selectorParroquia($id_cambiado)
    {
        $this->parroquia_id = $id_cambiado;
    }

    public function ocupacion($ocupacion)
    {
        $this->ocupacion = $ocupacion;
    }

    public function buscarCedula()
    {
        //Validar solo cantidad correctar de digitos
        if (strlen($this->numero_documento) == 10) {
            //Validar buscar ruc existe
            $existeCliente = Customer::where('numero_documento', $this->numero_documento)->where('company_id', Auth::user()->company_id)->first();
            if (is_null($existeCliente)) {
                $this->nombres = '';
                $this->apellidos = '';

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => "https://clinicasancho.ddns.net:8085/restful/api-eva/buscar-cedula",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_POSTFIELDS => "{\n\t\"cedula\": \"$this->numero_documento\"\n}",
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/json",
                        "User-Agent: insomnia/10.3.0"
                    ],
                ]);

                $response = curl_exec($curl);

                if ($response) {
                    $data = json_decode($response, true); // El segundo parámetro `true` convierte la respuesta a un array asociativo
                    // Validar y asignar nombres y apellidos
                    if (isset($data['nombres']) && isset($data['apellidos'])) {
                        $this->nombres = $data['nombres'];
                        $this->apellidos = $data['apellidos'];
                    }
                }

                curl_close($curl);

                if ($this->nombres !== '' && $this->apellidos !== '') {
                    $color = 'success';
                    $mensaje = 'Se buscó en un servidor Externo';
                } else {
                    $color = 'danger';
                    $mensaje = 'No se encontró información en el servidor Externo';
                }

                $data = [
                    'titulo' => 'Notificación',
                    'color' => $color,
                    'mensaje' => $mensaje
                ];

                $this->dispatchBrowserEvent('alerta', $data);
            } else {
                $this->seleccionarCliente($existeCliente->id);
            }
        }
    }

    public function quitarReferencia($id)
    {
        $customerParentezco = CustomerParentezco::find($id);
        $customerParentezco->delete();
        $this->dispatchBrowserEvent('alerta', [
            'titulo' => 'Notificación',
            'color' => 'danger',
            'mensaje' => 'Se eliminó correctamente'
        ]);
    }

    public function agregarReferencia()
    {
        $customerParentezco = new CustomerParentezco();
        $customerParentezco->company_id = Auth::user()->company_id;
        $customerParentezco->customer_id = $this->id_seleccionado;
        $customerParentezco->parentezco_id = $this->parentezco_id;
        $customerParentezco->nombres_apellidos = 'Nuevo';
        $customerParentezco->celular = 'Nuevo';
        $customerParentezco->save();

        $this->valores[] = [
            'id' => $customerParentezco->id,
            'parentezco_id' => $customerParentezco->parentezco_id,
            'nombres_apellidos' => $customerParentezco->nombres_apellidos,
            'celular' => $customerParentezco->celular
        ];
    }

    public function store()
    {
        $this->validate([
            'tipo_documento_id' => 'required',
            'numero_documento' => 'required',
            'nombres' => 'required',
            'apellidos' => 'required',
            'genero_id' => 'required',
            'estado_civil_id' => 'required',
            'pais_id' => 'required',
            'provincia_id' => 'required',
            'parroquia_id' => 'required',
            'ciudad_id' => 'required',
            'telefono' => 'required',
            'correo' => 'required',
            'direccion' => 'required',
            'parentezco_id' => 'required',
            'name_parentesco' => 'required',
            'telefono_parentesco' => 'required',
        ]);

        if ($this->id_seleccionado > 0) {
            $customer = Customer::find($this->id_seleccionado);
        } else {
            $customer = new Customer();
        }
        $customer->company_id = Auth::user()->company_id;
        $customer->tipo_documento_id = $this->tipo_documento_id;
        $customer->numero_documento = $this->numero_documento;
        $customer->fecha_nacimiento = $this->fecha_nacimiento;
        $customer->nombres = $this->nombres;
        $customer->apellidos = $this->apellidos;
        $customer->genero_id = $this->genero_id;
        $customer->estado_civil_id = $this->estado_civil_id;
        $customer->tipo_vivienda = $this->tipo_vivienda;
        $customer->tiempo_vivienda = $this->tiempo_vivienda;
        $customer->country_id = $this->pais_id;
        $customer->provincia_id = $this->provincia_id;
        $customer->parroquia_id = $this->parroquia_id;
        $customer->ciudad_id = $this->ciudad_id;
        $customer->telefono = $this->telefono;
        $customer->telefono_fijo = $this->telefono_fijo;
        $customer->correo = $this->correo;
        $customer->direccion = $this->direccion;
        $customer->nivel_academico_id = ($this->nivel_academico_id !== '') ? $this->nivel_academico_id : null;
        $customer->conyugue_nombre = $this->conyugue_nombre;
        $customer->conyugue_identificacion = $this->conyugue_identificacion;
        $customer->conyugue_telefono = $this->conyugue_telefono;
        $customer->conyuge_nivel_academico_id = ($this->conyuge_nivel_academico_id !== '') ? $this->conyuge_nivel_academico_id : null;
        $customer->conyuge_fecha_nacimiento = $this->conyuge_fecha_nacimiento;
        $customer->conyuge_ocupacion = $this->conyuge_ocupacion;
        $customer->conyuge_empresa_nombre = $this->conyuge_empresa_nombre;
        $customer->conyuge_empresa_direccion = $this->conyuge_empresa_direccion;
        $customer->conyuge_empresa_telefono = $this->conyuge_empresa_telefono;
        $customer->conyuge_tiempo_empresa = $this->conyuge_tiempo_empresa;
        $customer->conyuge_cargo_empresa = $this->conyuge_cargo_empresa;
        $customer->ocupacion = $this->ocupacion;
        $customer->empresa_nombre = $this->empresa_nombre;
        $customer->empresa_direccion = $this->empresa_direccion;
        $customer->empresa_provincia_id = ($this->empresa_provincia_id != null) ? $this->empresa_provincia_id : null;
        $customer->empresa_canton_id = ($this->empresa_canton_id != null) ? $this->empresa_canton_id : null;
        $customer->empresa_parroquia_id = ($this->empresa_parroquia_id != null) ? $this->empresa_parroquia_id : null;
        $customer->empresa_telefono = $this->empresa_telefono;
        $customer->empresa_tiempo = $this->empresa_tiempo;
        $customer->empresa_cargo = $this->empresa_cargo;
        $customer->negocio_nombre = $this->negocio_nombre;
        $customer->negocio_direccion = $this->negocio_direccion;
        $customer->negocio_provincia_id = ($this->negocio_provincia_id != null) ? $this->negocio_provincia_id : null;
        $customer->negocio_canton_id = ($this->negocio_canton_id != null) ? $this->negocio_canton_id : null;
        $customer->negocio_parroquia_id = ($this->negocio_parroquia_id != null) ? $this->negocio_parroquia_id : null;
        $customer->negocio_telefono = $this->negocio_telefono;
        $customer->negocio_tiempo = $this->negocio_tiempo;
        $customer->negocio_actividad = $this->negocio_actividad;
        $customer->parentezco_id = $this->parentezco_id;
        $customer->name_parentesco = $this->name_parentesco;
        $customer->telefono_parentesco = $this->telefono_parentesco;
        $data = $this->dividirCoordenadas($this->latitudlongitud);
        $customer->latitud = $data['latitud'];
        $customer->longitud = $data['longitud'];
        $customer->banco_id = $this->banco_id;
        $customer->tipo_cuenta_id = $this->tipo_cuenta_id;
        $customer->no_cuenta = $this->no_cuenta;
        $customer->seperacion_bienes = $this->seperacion_bienes;
        $customer->cargas_familiares = $this->cargas_familiares;
        $customer->fundador = $this->fundador;
        $customer->date_open_account = $this->date_open_account;
        $customer->save();
        $this->id_seleccionado = $customer->id;
        //Guardar Parentezco
        foreach ($this->valores as $id => $fila) {
            $customerParentezco = CustomerParentezco::find($fila['id']);
            $customerParentezco->company_id = Auth::user()->company_id;
            $customerParentezco->customer_id = $this->id_seleccionado;
            //$customerParentezco->parentezco_id = $this->valores[$id]['parentezco_id'];
            $customerParentezco->nombres_apellidos = $this->valores[$id]['nombres_apellidos'];
            $customerParentezco->celular = $this->valores[$id]['celular'];
            $customerParentezco->save();
        }

        //Alerta guardar
        $color = 'success';
        $mensaje = 'La información del cliente se guardó correctamente';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }
    public function storeLatitudlongitud()
    {
        $this->validate([
            'latitudlongitud' => 'required',
        ]);

        if ($this->id_seleccionado > 0) {
            $customer = Customer::find($this->id_seleccionado);
        } else {
            $customer = new Customer();
        }

        $data = $this->dividirCoordenadas($this->latitudlongitud);
        $customer->latitud = $data['latitud'];
        $customer->longitud = $data['longitud'];
        $customer->save();

        $this->latitud = $customer->latitud;
        $this->longitud = $customer->longitud;

        //Alerta guardar
        $color = 'success';
        $mensaje = 'La información del cliente se guardó correctamente';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);

        $this->dispatchBrowserEvent('locationUpdated', [
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
        ]);
    }

    public function dividirCoordenadas($latitudlongitud)
    {
        // Dividir coordenadas
        $latitud = null;
        $longitud = null;
        if (strpos($latitudlongitud, ',') !== false) {
            $parts = explode(',', $latitudlongitud);
            if (count($parts) === 2) {
                $latitud = $parts[0];
                $longitud = $parts[1];
            }
        }

        $data = [
            'latitud' => $latitud,
            'longitud' => $longitud,
        ];
        return $data;
    }

    public function calcularEdadVista()
    {
        $this->edad = $this->calcularEdad($this->fecha_nacimiento);
    }

    public function calcularEdad($fechaNacimiento)
    {
        $edad = ($fechaNacimiento !== null) ? date('Y') - date('Y', strtotime($fechaNacimiento)) : 0;
        return $edad;
    }

    public $activeTab = 'activity';

    public function selectTab($tab)
    {
        if (in_array($tab, ['activity', 'timeline', 'archivos'], true) && ($tab === 'activity' || $this->id_seleccionado > 0)) {
            $this->activeTab = $tab;
        }
    }

    public function updatingSearch() { $this->resetPage(); }

    public function seleccionarCliente($id)
    {
        if ($id > 0) {
            Customer::where('company_id', Auth::user()->company_id)->findOrFail($id);
        }
        $this->activeTab = 'activity';
        $this->resetErrorBag();
        $this->limpiarFormulario();
        if ($id > 0) {
            $this->id_seleccionado = $id;
            $cliente = Customer::find($id);
            $this->tipo_documento_id = $cliente->tipo_documento_id ?? $this->tipo_documento_id;
            $this->genero_id = $cliente->genero_id ?? $this->genero_id;
            $this->estado_civil_id = $cliente->estado_civil_id ?? $this->estado_civil_id;
            $this->tipo_vivienda = $cliente->tipo_vivienda;
            $this->tiempo_vivienda = $cliente->tiempo_vivienda;
            $this->pais_id = $cliente->country_id ?? $this->pais_id;
            $this->nivel_academico_id = $cliente->nivel_academico_id ?? $this->nivel_academico_id;
            $this->provincia_id = $cliente->provincia_id ?? $this->provincia_id;
            $this->parroquia_id = $cliente->parroquia_id ?? $this->parroquia_id;
            $this->ciudad_id = $cliente->ciudad_id ?? $this->ciudad_id;
            $this->numero_documento = $cliente->numero_documento;
            $this->fecha_nacimiento = $cliente->fecha_nacimiento;
            $this->edad = $this->calcularEdad($cliente->fecha_nacimiento);
            $this->nombres = $cliente->nombres;
            $this->apellidos = $cliente->apellidos;
            $this->telefono = $cliente->telefono;
            $this->telefono_fijo = $cliente->telefono_fijo;
            $this->correo = $cliente->correo;
            $this->direccion = $cliente->direccion;
            $this->conyugue_nombre = $cliente->conyugue_nombre;
            $this->conyugue_identificacion = $cliente->conyugue_identificacion;
            $this->conyugue_telefono = $cliente->conyugue_telefono;
            $this->conyuge_nivel_academico_id = $cliente->conyuge_nivel_academico_id;
            $this->conyuge_fecha_nacimiento = $cliente->conyuge_fecha_nacimiento;
            $this->conyuge_ocupacion = $cliente->conyuge_ocupacion;
            $this->conyuge_empresa_nombre = $cliente->conyuge_empresa_nombre;
            $this->conyuge_empresa_direccion = $cliente->conyuge_empresa_direccion;
            $this->conyuge_empresa_telefono = $cliente->conyuge_empresa_telefono;
            $this->conyuge_tiempo_empresa = $cliente->conyuge_tiempo_empresa;
            $this->conyuge_cargo_empresa = $cliente->conyuge_cargo_empresa;
            $this->ocupacion = $cliente->ocupacion;
            $this->empresa_nombre = $cliente->empresa_nombre;
            $this->empresa_direccion = $cliente->empresa_direccion;
            $this->empresa_provincia_id = ($cliente->empresa_provincia_id !== null) ? $cliente->empresa_provincia_id : '';
            $this->empresa_canton_id = ($cliente->empresa_canton_id !== null) ? $cliente->empresa_canton_id : '';
            $this->empresa_parroquia_id = ($cliente->empresa_parroquia_id !== null) ? $cliente->empresa_parroquia_id : '';
            $this->empresa_telefono = $cliente->empresa_telefono;
            $this->empresa_tiempo = $cliente->empresa_tiempo;
            $this->empresa_cargo = $cliente->empresa_cargo;
            $this->negocio_nombre = $cliente->negocio_nombre;
            $this->negocio_direccion = $cliente->negocio_direccion;
            $this->negocio_provincia_id = ($cliente->negocio_provincia_id !== null) ? $cliente->negocio_provincia_id : '';
            $this->negocio_canton_id = ($cliente->negocio_canton_id !== null) ? $cliente->negocio_canton_id : '';
            $this->negocio_parroquia_id = ($cliente->negocio_parroquia_id !== null) ? $cliente->negocio_parroquia_id : '';
            $this->negocio_telefono = $cliente->negocio_telefono;
            $this->negocio_tiempo = $cliente->negocio_tiempo;
            $this->negocio_actividad = $cliente->negocio_actividad;
            $this->parentezco_id = $cliente->parentezco_id ?? $this->parentezco_id;
            $this->name_parentesco = $cliente->name_parentesco;
            $this->telefono_parentesco = $cliente->telefono_parentesco;
            $this->latitudlongitud = empty($cliente->latitud) ? '' : ($cliente->latitud . ',' . $cliente->longitud);
            $this->latitud = $cliente->latitud;
            $this->longitud = $cliente->longitud;
            $this->banco_id = $cliente->banco_id;
            $this->tipo_cuenta_id = $cliente->tipo_cuenta_id;
            $this->seperacion_bienes = $cliente->seperacion_bienes;
            $this->no_cuenta = $cliente->no_cuenta;
            $this->cargas_familiares = $cliente->cargas_familiares;
            $this->fundador = $cliente->fundador;
            $this->created_at = $cliente->created_at;
            $this->updated_at = $cliente->updated_at;
            //calcular prestamos y cuentas
            $toltalCuentas = CustomerTipoAhorros::where('company_id', Auth::user()->company_id)->where('customer_id', $id)->where('status', true)->count();
            $totalPrestamos = CreditFolderHeader::where('company_id', Auth::user()->company_id)->where('customer_id', $id)->count();
            $this->totalCuentas = $toltalCuentas;
            $this->totalPrestamos = $totalPrestamos;
            $this->fundador = $cliente->fundador;
            $this->date_open_account = $cliente->date_open_account;
        } else {
            $this->id_seleccionado = 0;
        }
        $this->dispatchBrowserEvent('locationUpdated', [
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
        ]);
    }

    private function limpiarFormulario()
    {
        $this->reset([
            'nombres',
            'apellidos',
            'numero_documento',
            'fecha_nacimiento',
            'edad',
            'telefono',
            'telefono_fijo',
            'correo',
            'direccion',
            'nivel_academico_id',
            'conyugue_nombre',
            'conyugue_identificacion',
            'conyugue_telefono',
            'conyuge_nivel_academico_id',
            'conyuge_fecha_nacimiento',
            'conyuge_ocupacion',
            'conyuge_empresa_nombre',
            'conyuge_empresa_direccion',
            'conyuge_empresa_telefono',
            'conyuge_tiempo_empresa',
            'conyuge_cargo_empresa',
            'ocupacion',
            'empresa_nombre',
            'empresa_direccion',
            'empresa_provincia_id',
            'empresa_canton_id',
            'empresa_parroquia_id',
            'empresa_telefono',
            'empresa_tiempo',
            'empresa_cargo',
            'negocio_nombre',
            'negocio_direccion',
            'negocio_provincia_id',
            'negocio_canton_id',
            'negocio_parroquia_id',
            'negocio_telefono',
            'negocio_tiempo',
            'negocio_actividad',
            'name_parentesco',
            'telefono_parentesco',
            'tipo_documento_id',
            'genero_id',
            'estado_civil_id',
            'tipo_vivienda',
            'tiempo_vivienda',
            'pais_id',
            'provincia_id',
            'parroquia_id',
            'ciudad_id',
            'parentezco_id',
            'latitudlongitud',
            'latitud',
            'longitud',
            'banco_id',
            'tipo_cuenta_id',
            'no_cuenta',
            'cargas_familiares',
            'seperacion_bienes',
            'fundador',
            'created_at',
            'updated_at',
            'archivo',
            'descrpcion',
            'date_open_account'
        ]);
        $this->tipo_documento_id = TipoDocumento::where('company_id', Auth::user()->company_id)->where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->genero_id = Genero::where('company_id', Auth::user()->company_id)->where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->pais_id = Country::where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->nivel_academico_id = NivelAcademico::where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->parentezco_id = Parentezco::where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->estado_civil_id = EstadoCivil::where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->provincia_id = Provincia::where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->parroquia_id = Parroquia::where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->ciudad_id = Ciudad::where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->totalCuentas = 0;
        $this->totalPrestamos = 0;
        $this->ocupacion = 'NINGUNO';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function subirArchivo()
    {
        $this->validate([
            'archivo' => 'required|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:20240',
        ]);
        $ruta = $this->archivo->store('public/clientes');
        $extension = $this->archivo->getClientOriginalExtension();
        $url = Storage::url($ruta);
        $customerFile = new CustomerFile();
        $customerFile->company_id = Auth::user()->company_id;
        $customerFile->customer_id = $this->id_seleccionado;
        $customerFile->descripcion = $this->descrpcion ?? 'Archivo sin descripción';
        $customerFile->formato = $extension;
        $customerFile->path = $url;
        $customerFile->archivo = $ruta;
        $customerFile->save();
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('alerta', [
            'titulo' => 'Notificación',
            'color' => 'success',
            'mensaje' => 'Se guardó correctamente'
        ]);
    }

    public function eliminarFile($id)
    {
        $customerFile = CustomerFile::find($id);
        if ($customerFile->archivo) {
            Storage::delete($customerFile->archivo);
        }
        $customerFile->delete();
        $this->dispatchBrowserEvent('alerta', [
            'titulo' => 'Notificación',
            'color' => 'danger',
            'mensaje' => 'Se eliminó correctamente'
        ]);
    }

    public function descargarClientes(){
        $nombre = 'Clientes_' . date('Y-m-d_H-i-s');
        return Excel::download(new CustomerExport(), $nombre . '.xlsx');
    }

    public function mount($customer_id)
    {
        $this->tipoDocumento = TipoDocumento::where('company_id', Auth::user()->company_id)->where('status', true)->get()->toArray();
        $this->tipo_documento_id = TipoDocumento::where('company_id', Auth::user()->company_id)->where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->genero = Genero::where('company_id', Auth::user()->company_id)->where('status', true)->get()->toArray();
        $this->genero_id = Genero::where('company_id', Auth::user()->company_id)->where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->paises = Country::where('status', true)->get()->toArray();
        $this->pais_id = Country::where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->niveles = NivelAcademico::where('status', true)->get()->toArray();
        $this->nivel_academico_id = NivelAcademico::where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->parentezco = Parentezco::where('status', true)->get()->toArray();
        $this->parentezco_id = Parentezco::where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->estadoCivil = EstadoCivil::where('status', true)->get()->toArray();
        $this->estado_civil_id = EstadoCivil::where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->tipo_vivienda = '';
        $this->tiempo_vivienda = '';
        $this->provincia = Provincia::where('status', true)->get()->toArray();
        $this->provincia_id = Provincia::where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->parroquia = Parroquia::where('status', true)->get()->toArray();
        $this->parroquia_id = Parroquia::where('defecto', true)->where('status', true)->first()->id ?? '';
        $this->ciudad = Ciudad::where('status', true)->get()->toArray();
        $this->ciudad_id = Ciudad::where('defecto', true)->where('status', true)->first()->id ?? '';



        $this->banco = Bancos::where('status', true)->where('numero_cuenta', '')->get()->toArray();
        $this->banco_id = '';
        $this->tipo_cuenta = TipoCuenta::where('status', true)->get()->toArray();
        $this->tipo_cuenta_id = '';

        if ($customer_id > 0) {
            $customerFind = Customer::where('company_id', Auth::user()->company_id)->findOrFail($customer_id);
            if ($customerFind != null) {
                $this->seleccionarCliente($customer_id);
                $this->search = $customerFind->numero_documento;
            }
        }
    }

    public function customerSearchQuery()
    {
        $hoy = now()->format('Y-m-d');
        return Customer::select('*', DB::raw("DATE(customer.created_at) = '{$hoy}' as nuevo"))
            ->where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $term = '%' . trim($this->search) . '%';
                $query->where('nombres', 'like', $term)->orWhere('apellidos', 'like', $term)->orWhere('numero_documento', 'like', $term);
            })->orderBy('created_at', 'DESC')->orderBy('id', 'DESC');
    }

    public function render()
    {
        $clientes = $this->customerSearchQuery()->paginate(15);
        $cliente = Customer::find($this->id_seleccionado);
        $resultado = CustomerParentezco::where('company_id', Auth::user()->company_id)->where('customer_id', $this->id_seleccionado)->get();
        $this->valores = [];
        foreach ($resultado as $cus) {
            $parentezco = Parentezco::find($cus->parentezco_id);
            $this->valores[] = [
                'id' => $cus->id,
                'parentezco_id' => $cus->parentezco_id,
                'parentezco_nombre' => $parentezco->nombre ?? 'No definido',
                'nombres_apellidos' => $cus->nombres_apellidos,
                'celular' => $cus->celular
            ];
        }
        $valores = $this->valores;

        $filesCreditos = CreditFiles::select('credit_file.archivo', 'credit_file.descripcion', 'credit_file.path', 'credit_file.formato', 'credit_folder_headers.code')
            ->join('credit_folder_headers', 'credit_file.credit_header_id', '=', 'credit_folder_headers.id')
            ->join('customer', 'credit_folder_headers.customer_id', '=', 'customer.id')
            ->where('customer.company_id', Auth::user()->company_id)
            ->where('customer.id', $this->id_seleccionado)
            ->get();
        $filesGeneral = CustomerFile::where('company_id', Auth::user()->company_id)->where('customer_id', $this->id_seleccionado)->whereNull('customer_tipo_ahorros_id')->get();
        $filesCuentas = CustomerFile::where('company_id', Auth::user()->company_id)->where('customer_id', $this->id_seleccionado)->whereNotNull('customer_tipo_ahorros_id')->get();
        return view('livewire.clientes.clientes-component', compact('clientes', 'cliente', 'valores', 'filesCreditos', 'filesGeneral', 'filesCuentas'));
    }
}
