<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Base\BaseController;
use App\Models\CustomerHistorial;
use App\Models\CustomerTarifa;
use App\Models\CartolaHeader;
use App\Models\CartolaDetail;
use App\Models\Country;
use App\Models\CreditFolderHeader;
use App\Models\CustomerMovimiento;
use App\Models\TipoAhorrosProgramadosDetalle;
use App\Models\TypeTransaction;
use App\Models\CustomerMovimientoDetalleCalculadora;
use App\Models\HeaderCalculadora;
use Response;
use PDF;
use Luecano\NumeroALetras\NumeroALetras;

class CustomerController extends Controller
{

    public function index()
    {
        $country = Country::firstOrCreate(
            ['id' => 1],
            [
                'nombre' => 'ECUADOR',
                'codigo_llamada' => '+593',
            ]
        );

        $eeuu = Country::firstOrCreate(
            ['id' => 2],
            [
                'nombre' => 'ESTADOS UNIDOS',
                'codigo_llamada' => '+1',
            ]
        );
        $clientes = Customer::where('company_id', Auth::user()->company_id)->where('status', true)->get();
        $cantidad = count($clientes);
        return view('customer/index')
            ->with('clientes', $clientes)
            ->with('cantidad', $cantidad);
    }

    public function create()
    {
        $tarifas = CustomerTarifa::all();
        $country = Country::all();
        return view('customer/create')
            ->with('tarifas', $tarifas)
            ->with('country', $country);
    }

    public function store(Request $request)
    {
        $tabla = 'customer';
        $code = BaseController::generarCodigo($tabla, 9);
        $tarifa = CustomerTarifa::find($request->input('tarifa'));
        $data = [
            "code" => $code,
            "nombres" => strtoupper($request->input('nombres')),
            "apellidos" => strtoupper($request->input('apellidos')),
            "documento" => $request->input('documento'),
            "numero_documento" => $request->input('numero_documento'),
            "direccion" => strtoupper($request->input('direccion')),
            "parentesco_customer" => strtoupper($request->input('parentesco')),
            "name_parentesco" => strtoupper($request->input('nombres_parentesco')),
            //            "numero_identificacion_parentesco" => $request->input('identificacion_parentesto'),
            "country_id" => $request->input('codigo'),
            "telefono_parentesco" => $request->input('telefono_parentesco'),
            "estado_civil" => strtoupper($request->input('estado_civil')),
            "latitud" => $request->input('latitud'),
            "longitud" => $request->input('longitud'),
            "telefono" => $request->input('telefono'),
            "correo" => $request->input('correo'),
            "company_id" => Auth::user()->company_id,
            "customer_tarifa_id" => $tarifa->id,
            "customer_tarifa_name" => strtoupper($tarifa->name),
            "customer_tarifa_interes" => $tarifa->porcentaje,
            "conyugue_nombre" => ($request->input('estado_civil') != 'SOLTERO/A' && $request->input('estado_civil') != 'VIUDO/A') ? strtoupper($request->input('nombres_conyugue')) : null,
            "conyugue_identificacion" => ($request->input('estado_civil') != 'SOLTERO/A' && $request->input('estado_civil') != 'VIUDO/A') ? $request->input('identificacion_conyugue') : null,
            "conyugue_telefono" => ($request->input('estado_civil') != 'SOLTERO/A' && $request->input('estado_civil') != 'VIUDO/A') ? $request->input('telefono_conyugue') : null,
        ];
        $cliente = Customer::create($data);
        return redirect('customer')->with('mesaje', 'Pedido creado con exito');
    }

    public function show($id)
    {
        $cliente = Customer::find($id);
        return view('customer/show')
            ->with('cliente', $cliente);
    }

    public function edit(Customer $customer)
    {
        $tarifas = CustomerTarifa::all();
        $country = Country::all();
        return view('customer/edit')
            ->with('customer', $customer)
            ->with('country', $country)
            ->with('tarifas', $tarifas);
    }

    public function update(Request $request, Customer $customer)
    {
        $tarifa = CustomerTarifa::find($request->input('tarifa'));
        $customer = Customer::find($customer->id);
        $customer->nombres = strtoupper($request->input('nombres'));
        $customer->apellidos = strtoupper($request->input('apellidos'));
        $customer->documento = $request->input('documento');
        $customer->numero_documento = $request->input('numero_documento');
        $customer->direccion = strtoupper($request->input('direccion'));
        $customer->parentesco_customer = strtoupper($request->input('parentesco'));
        $customer->name_parentesco = strtoupper($request->input('nombres_parentesco'));
        //        $customer->numero_identificacion_parentesco = $request->input('identificacion_parentesto');
        $customer->country_id = $request->input('codigo');
        $customer->telefono_parentesco = $request->input('telefono_parentesco');
        $customer->estado_civil = strtoupper($request->input('estado_civil'));
        $customer->latitud = $request->input('latitud');
        $customer->longitud = $request->input('longitud');
        $customer->telefono = $request->input('telefono');
        $customer->correo = $request->input('correo');
        $customer->company_id = Auth::user()->company_id;
        $customer->customer_tarifa_id = $tarifa->id;
        $customer->customer_tarifa_name = strtoupper($tarifa->name);
        $customer->customer_tarifa_interes = $tarifa->porcentaje;
        $customer->conyugue_nombre = ($request->input('estado_civil') != 'SOLTERO/A' && $request->input('estado_civil') != 'VIUDO/A') ? strtoupper($request->input('nombres_conyugue')) : null;
        $customer->conyugue_identificacion = ($request->input('estado_civil') != 'SOLTERO/A' && $request->input('estado_civil') != 'VIUDO/A') ? $request->input('identificacion_conyugue') : null;
        $customer->conyugue_telefono = ($request->input('estado_civil') != 'SOLTERO/A' && $request->input('estado_civil') != 'VIUDO/A') ? $request->input('telefono_conyugue') : null;
        $customer->save();
        return redirect('customer')
            ->with('mensaje', 'Cliente Actualizado con Éxito');
    }

    public function destroy(Customer $customer)
    {
        $numeroConsulta = $customer->numero_documento;
        $creditosNumero = CreditFolderHeader::where('customer_ruc', $numeroConsulta)->count();
        $creditos = CustomerMovimiento::where('customer_ruc', $numeroConsulta)->where('type_transaction_action', 'S')->sum('valor_movimiento');
        $retiros = CustomerMovimiento::where('customer_ruc', $numeroConsulta)->where('type_transaction_action', 'R')->sum('valor_movimiento');
        $total = $creditos - $retiros;
        if ($creditosNumero > 0) {
            return redirect('customer')
                ->with('mensaje', 'El Cliente ' . $customer->nombres . ' ' . $customer->apellidos . ' no puede ser eliminado tiene ' . $creditosNumero . (($creditosNumero > 1) ? ' Créditos...' : ' Crédito ...'));
        } else {
            if ($total != 0) {
                return redirect('customer')
                    ->with('mensaje', 'El Cliente ' . $customer->nombres . ' ' . $customer->apellidos . ' no puede ser eliminado dispone de transacciones...');
            } else {
                $customer->status = 0;
                $customer->save();
                return redirect('customer')
                    ->with('mensaje', 'Cliente Eliminado Satisfactoriamente...');
            }
        }
    }

    public function buscarCustomer(Request $request)
    {
        $name = strtoupper($request->input('term'));
        $val = Customer::where('company_id', Auth::user()->company_id)->where('numero_documento', 'LIKE', '%' . $name . '%')->where('status', true);
        if ($val->count() > 0) {
            foreach ($val->get() as $value) {
                $data[] = [
                    'value' => $value->nombres . ' ' . $value->apellidos,
                    'name' => $value->nombres . ' ' . $value->apellidos,
                    'direccion' => $value->direccion,
                    'telefono' => $value->telefono,
                    'correo' => $value->correo,
                    'ruc' => $value->numero_documento,
                    'id' => $value->id,
                ];
            }
            return Response::json($data);
        } else {
            $val = Customer::where('company_id', Auth::user()->company_id)->where('nombres', 'LIKE', '%' . $name . '%')->where('status', true);
            if ($val->count() > 0) {
                foreach ($val->get() as $value) {
                    $data[] = [
                        'value' => $value->nombres . ' ' . $value->apellidos,
                        'name' => $value->nombres . ' ' . $value->apellidos,
                        'direccion' => $value->direccion,
                        'telefono' => $value->telefono,
                        'correo' => $value->correo,
                        'ruc' => $value->numero_documento,
                        'id' => $value->id,
                    ];
                }
                return Response::json($data);
            } else {
                return Response::json('');
            }
        }
    }

    public function buscarCustomerModal($datos)
    {
        $val = Customer::where('company_id', Auth::user()->company_id)->where('nombres', 'LIKE', '%' . $datos . '%')->where('status', true);
        if ($val->count() > 0) {
            foreach ($val->get() as $value) {
                $caja[] = [
                    'value' => $value->nombres,
                    'id' => $value->id,
                    'ruc' => $value->numero_documento,
                    'direccion' => $value->direccion,
                    'telefono' => $value->telefono,
                    'correo' => $value->correo,
                ];
            }
            return Response::json($caja);
        } else {
            $val = Customer::where('company_id', Auth::user()->company_id)->where('numero_documento', $datos)->where('status', true);
            if ($val->count() > 0) {
                $value = $val->first();
                $caja[] = [
                    'value' => $value->nombres,
                    'id' => $value->id,
                    'ruc' => $value->numero_documento,
                    'direccion' => $value->direccion,
                    'telefono' => $value->telefono,
                    'correo' => $value->correo,
                ];
                return Response::json($caja);
            } else {
                $val = Customer::where('company_id', Auth::user()->company_id)->where('telefono', $datos)->where('status', true);
                if ($val->count() > 0) {
                    $value = $val->first();
                    $caja[] = [
                        'value' => $value->nombres,
                        'id' => $value->id,
                        'ruc' => $value->numero_documento,
                        'direccion' => $value->direccion,
                        'telefono' => $value->telefono,
                        'correo' => $value->correo,
                    ];
                    return Response::json($caja);
                } else {
                    return Response::json('');
                }
            }
        }
    }

    public function infoCustomerModal($id)
    {
        $customer = Customer::find($id);
        return Response::json($customer);
    }

    public function updateCustomerModal(Request $request, $id)
    {
        $customer = Customer::find($id);
        $customer->documento = $request['editar_tipo_customer_modal'];
        $customer->numero_documento = $request['editar_ruc_customer_modal'];
        $customer->nombres = strtoupper($request['editar_nombres_customer_modal']);
        $customer->apellidos = $strtoupper($request['editar_apellidos_customer_modal']);
        $customer->direccion = $strtoupper($request['editar_direccion_customer_modal']);
        $customer->telefono = $request['editar_telefono_customer_modal'];
        $customer->correo = $request['editar_correo_customer_modal'];
        $customer->save();
        return Response::json(true);
    }

    public function createCustomerModal(Request $request)
    {
        $customer = new Customer();
        $customer->documento = $request['crear_tipo_customer_modal'];
        $customer->numero_documento = $request['ruc_customer_modal'];
        $customer->nombres = strtoupper($request['nombres_customer_modal']);
        $customer->apellidos = strtoupper($request['apellidos_customer_modal']);
        $customer->direccion = strtoupper($request['direccion_customer_modal']);
        $customer->telefono = $request['telefono_customer_modal'];
        $customer->correo = $request['correo_customer_modal'];
        $customer->company_id = Auth::user()->company_id;
        $customer->save();
        return Response::json($customer);
    }

    public function buscarCustomerCaja($dato)
    {
        $value = $dato;
        $res = '';
        $valores = explode(' ', $value);
        $data = Customer::where('company_id', Auth::user()->company_id)->where('numero_documento', 'like', '%' . $valores[0] . '%')->select('id', 'code', 'nombres', 'apellidos', 'documento', 'numero_documento', 'direccion', 'latitud', 'longitud', 'telefono', 'telefono_2', 'telefono_3', 'customer_tarifa_id', 'customer_tarifa_interes', 'correo', 'company_id', 'customer_tarifa_name');
        if ($data->count() > 0) {
            $res = $data->get();
        } else {
            $data = Customer::where('company_id', Auth::user()->company_id)->where('nombres', 'like', '%' . $value . '%')->select('id', 'code', 'nombres', 'apellidos', 'documento', 'numero_documento', 'direccion', 'latitud', 'longitud', 'telefono', 'telefono_2', 'telefono_3', 'customer_tarifa_id', 'customer_tarifa_interes', 'correo', 'company_id', 'customer_tarifa_name');
            if ($data->count() > 0) {
                $res = $data->get();
            } else {
                $data = Customer::where('company_id', Auth::user()->company_id)->where('apellidos', 'like', '%' . $value . '%')->select('id', 'code', 'nombres', 'apellidos', 'documento', 'numero_documento', 'direccion', 'latitud', 'longitud', 'telefono', 'telefono_2', 'telefono_3', 'customer_tarifa_id', 'customer_tarifa_interes', 'correo', 'company_id', 'customer_tarifa_name');
                if ($data->count() > 0) {
                    $res = $data->get();
                } else {
                    $data = Customer::where('company_id', Auth::user()->company_id)->where('code', 'like', '%' . $value . '%')->select('id', 'code', 'nombres', 'apellidos', 'documento', 'numero_documento', 'direccion', 'latitud', 'longitud', 'telefono', 'telefono_2', 'telefono_3', 'customer_tarifa_id', 'customer_tarifa_interes', 'correo', 'company_id', 'customer_tarifa_name');
                    if ($data->count() > 0) {
                        $res = $data->get();
                    } else {
                        $res = '';
                    }
                }
            }
        }
        return Response::json($res);
    }

    public function buscarApiDatos($dato)
    {
        $maxExecutionTime = 5;
        set_time_limit($maxExecutionTime);
        $ch = curl_init();
        //Le pasamos la url a curl, y formateamos la cedula de identidad y la fecha
        curl_setopt($ch, CURLOPT_URL, 'https://srienlinea.sri.gob.ec/movil-servicios/api/v1.0/deudas/porIdentificacion/' . $dato . '/?tipoPersona=N&_=1691677932987');
        //Le pasamos a curl un useragent
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
        //Le pasamos a curl el header del idioma
        $headers = [];
        $headers[] = 'Accept-Language: es-es,en';
        $headers[] = 'Content-type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //Número máximo de segundos para ejecutar funciones curl
        curl_setopt($ch, CURLOPT_TIMEOUT, $maxExecutionTime);
        //Le pasamos True, 1, para seguir cualquier encabezado location
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //Le pasamos true, 1, para que nos devuelva el resultado en una string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //Guardar pagina
        $resultado = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch); //Cerramos la conexion CURL.
        $filas = json_decode($resultado, true);
        $nombres = '';
        $apellidos = '';
        $primerNombre = '';
        $segundoNombre = '';
        $apellidoPaterno = '';
        $apellidoMaterno = '';
        $primerApellido = '';
        $segundoApellido = '';
        if (isset($filas['contribuyente'])) {
            $datosPersonales = $filas['contribuyente'];
            $nombreArray = explode(" ", $datosPersonales['nombreComercial']);
            $primerApellido = (isset($nombreArray[0])) ? $nombreArray[0] : '';
            $segundoApellido = (isset($nombreArray[1])) ? $nombreArray[1] : '';
            $apellidos = $primerApellido . ' ' . $segundoApellido;
            $primerNombreC = (isset($nombreArray[2])) ? $nombreArray[2] : '';
            $segundoNombreC = (isset($nombreArray[3])) ? $nombreArray[3] : '';
            $nombres = $primerNombreC . ' ' . $segundoNombreC;
            $primerNombre = $primerNombreC;
            $segundoNombre = $segundoNombreC;
            $apellidoPaterno = $primerApellido;
            $apellidoMaterno = $segundoApellido;
        }
        $personales = [
            'primerNombre' => $primerNombre,
            'segundoNombre' => $segundoNombre,
            'apellidoPaterno' => $primerApellido,
            'apellidoMaterno' => $segundoApellido,
            'nombres' => $nombres,
            'apellidos' => $apellidos,
        ];
        return Response::json($personales);
    }

    public function seleccionarCustomer($id)
    {
        $customer = Customer::find($id);
        $ingresos = CustomerHistorial::where('customer_code', $customer->code)
            ->where('type_transaction_action', 'S')
            ->where('type_transaction_name', 'INGRESOS')
            ->where('status', true)
            ->sum('valor_movimiento');
        $egresos = CustomerHistorial::where('customer_code', $customer->code)
            ->where('type_transaction_action', 'R')
            ->where('type_transaction_name', 'EGRESOS')
            ->where('status', true)
            ->sum('valor_movimiento');
        $valor = $ingresos - $egresos;
        $data = [
            'customer' => $customer,
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'valor' => $valor,
        ];
        return Response::json($data);
    }

    public function generarCustomerNew(Request $request)
    {
        $tarifas = CustomerTarifa::find($request->input('tarifas'));
        $tabla = 'customer';
        $code = BaseController::generarCodigo($tabla, 9);
        $data = [
            "code" => $code,
            "nombres" => strtoupper($request->input('name')),
            "apellidos" => strtoupper($request->input('last_name')),
            "numero_documento" => $request->input('number_ship'),
            "direccion" => strtoupper($request->input('adress')),
            "telefono" => $request->input('phone'),
            "customer_tarifa_id" => $tarifas->id,
            "customer_tarifa_name" => strtoupper($tarifas->name),
            "customer_tarifa_interes" => $tarifas->porcentaje,
            "parentesco_customer" => strtoupper($request->input('parentesco')),
            "name_parentesco" => strtoupper($request->input('name_parentesco')),
            //            "numero_identificacion_parentesco" => $request->input('identificacion_parentesto'),
            "telefono_parentesco" => $request->input('telefono_parentesco'),
            "estado_civil" => strtoupper($request->input('estado_civil')),
            "documento" => '04',
            "company_id" => Auth::user()->company_id,
            "conyugue_nombre" => ($request->input('estado_civil') != 'SOLTERO/A' && $request->input('estado_civil') != 'VIUDO/A') ? strtoupper($request->input('nombres_conyugue')) : null,
            "conyugue_identificacion" => ($request->input('estado_civil') != 'SOLTERO/A' && $request->input('estado_civil') != 'VIUDO/A') ? $request->input('identificacion_conyugue') : null,
            "conyugue_telefono" => ($request->input('estado_civil') != 'SOLTERO/A' && $request->input('estado_civil') != 'VIUDO/A') ? $request->input('telefono_conyugue') : null,
        ];
        $customer = Customer::create($data);
        return Response::json($customer);
    }

    public function cartilla($code)
    {
        $numero = CustomerHistorial::where('customer_code', $code)
            ->where('company_id', Auth::user()->company_id)
            ->where('status', 1);
        if ($numero->count() > 0) {
            $datosCatola['customerHistorial'] = CustomerHistorial::where('customer_code', $code)
                ->where('company_id', Auth::user()->company_id)
                ->where('status', 1)
                ->get();
            $datosCatola['customer'] = Customer::where('code', $code)->first();
            $datosCatola['cartolaHeader'] = CartolaHeader::where('customer_code', $code)
                ->where('status', 'ACTIVA')->first();
            if ($datosCatola['cartolaHeader'] != null) {
                $datosCatola['cartolaDetalle'] = CartolaDetail::where('cartola_header_code', $datosCatola['cartolaHeader']->code)->get();
                return Response::json($datosCatola);
            } else {
                $datosCatola = '';
                return Response::json($datosCatola);
            }
        } else {
        }
    }

    public function cartillaImprimir($code)
    {
        $company = Company::find(Auth::user()->company_id);
        if ($company->photo != null && $company->photo != '') {
            $path = 'uploads/companies/' . $company->photo;
            if (!file_exists(public_path($path))) {
                $path = 'codev/negro.png';
            }
        } else {
            $path = 'codev/negro.png';
        }
        $data['company'] = $company;
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);
        $path2 = 'codev/negro.png';
        $data['cartolaHeader'] = CartolaHeader::find($code);
        $data['customer'] = Customer::where('code', $data['cartolaHeader']->customer_code)->first();
        $data['detalleCartola'] = CartolaDetail::where('cartola_header_code', $data['cartolaHeader']->code)->get();
        return PDF::loadView('reportes.cartola', compact('data'))
            ->setPaper('A5', "portrait")
            ->download('Cartola N° : ' . $data['cartolaHeader']->code . '.pdf');
    }

    public function buscarCedulas($dato)
    {
        $customer = Customer::where('numero_documento', $dato);
        if ($customer->count() == 0) {
            return Response::json(true);
        } else {
            return Response::json(false);
        }
    }

    public function epson($cliente, $cuenta, $id)
    {
        $seleccionado = CartolaDetail::where('customer_movimientos_id', $id)->first();
        $todos = CartolaDetail::where('cartola_headers_id', $seleccionado->cartola_headers_id)
            ->where('cara', $seleccionado->cara)
            ->get();
        $numeroCuenta = \App\Models\CustomerTipoAhorros::find($cuenta);
        $data = [];
        foreach ($todos as $key => $value) {
            $tipoTranc = TypeTransaction::find($value->type_transaction_id);
            $movimiento = CustomerMovimiento::find($value->customer_movimientos_id);
            $clienteDatos = Customer::find($movimiento->customer_id);
            $nombreArmado = $movimiento->customer_name;
            if ($clienteDatos) {
                $nombreArmado = $clienteDatos->apellidos . ' ' . $clienteDatos->nombres;
            }
            $data[] = [
                'lugar' => $value->posicion,
                'cara' => $value->cara,
                'id' => $value->id,
                'codigo' => $numeroCuenta->codigo,
                'nombres' => $nombreArmado,
                'customer_ruc' => $movimiento->customer_ruc,
                'tipo' => $value->type_transaction_action,
                'valor_movimiento' => $value->valor_transaction,
                'detectado' => ($value->id == $seleccionado->id) ? true : false,
                'posicion' => $value->posicion,
                'date_created' => $value->date_transaction,
                'name_corto' => ($tipoTranc->nombre_cartola != null) ? $tipoTranc->nombre_cartola : $tipoTranc->name_corto,
                'saldoTotal' => $value->saldo_transaction,
            ];
        }
        $jsonData = json_encode($data);
        return $jsonData;
    }
    public function epsonOld($cliente, $cuenta, $id)
    {
        $clientes = Customer::find($cliente);
        $detalle = CustomerMovimiento::select('customer_movimientos.*', 'customer_tipo_ahorros.codigo', 'tipo_ahorros_detalle.siglas', 'type_transactions.nombre_cartola')
            ->join('type_transactions', 'customer_movimientos.type_transaction_id', '=', 'type_transactions.id')
            ->join('customer_tipo_ahorros', 'customer_movimientos.customer_tipo_ahorro_id', '=', 'customer_tipo_ahorros.id')
            ->leftjoin('tipo_ahorros_detalle', 'customer_movimientos.tipo_ahorro_detalle_id', '=', 'tipo_ahorros_detalle.id')
            ->where('customer_movimientos.customer_code', $clientes->code)
            ->where('customer_movimientos.company_id', Auth::user()->company_id)
            ->whereIn('type_transactions.name_corto', ['IN', 'EG', 'SE', 'SC', 'DEA'])
            //->whereIn('customer_movimientos.type_transaction_id', [1, 2])
            ->where('customer_movimientos.customer_tipo_ahorro_id', $cuenta)
            ->where('customer_movimientos.status', true)
            ->orderBy('customer_movimientos.id', 'asc')
            ->get();

        $data = [];
        $dataPrint = [];
        $saldoTotal = 0;
        $linea = 0;
        $contadorA = 0;
        $contadorB = 0;
        $cara = 'A';
        $limpiar = true;
        foreach ($detalle as $key => $value) {
            // $tipoTranc = TypeTransaction::find($value->type_transaction_id);
            $dectecta = false;
            if ($value->type_transaction_action == 'S') {
                if ($value->type_transaction_name == 'SUMA CLIENTE' || $value->type_transaction_name == 'INGRESOS') {
                    $saldoTotal += $value->valor_movimiento;
                }
            } else {
                $saldoTotal -= $value->valor_movimiento;
            }
            if ($contadorA < 24) {
                $contadorA++;
                $linea = $contadorA;
                $cara = 'A';
            } else if ($contadorB < 29) {
                $contadorB++;
                $linea = $contadorB;
                $cara = 'B';
            } else {
                $contadorA = 1;
                $contadorB = 1;
                $linea = $contadorA;
                $cara = 'A';
            }

            if ($value->id == $id) {
                $dectecta = true;
                $limpiar = false;
                $data[] = [
                    'lugar' => $linea,
                    'cara' => $cara,
                    'id' => $value->id,
                    'codigo' => $value->codigo,
                    'nombres' => $value->customer_name,
                    'tipo' => $value->type_transaction_action,
                    'valor_movimiento' => $value->valor_movimiento,
                    'detectado' => $dectecta,
                    'posicion' => $linea,
                    'date_created' => $value->date_created,
                    'name_corto' => $value->siglas ? $value->siglas : $value->nombre_cartola,
                    'saldoTotal' => number_format($saldoTotal, 2),
                ];
            } else {
                $data[] = [
                    'lugar' => $linea,
                    'cara' => $cara,
                    'id' => $value->id,
                    'codigo' => $value->codigo,
                    'nombres' => $value->customer_name,
                    'tipo' => $value->type_transaction_action,
                    'valor_movimiento' => '',
                    'detectado' => $dectecta,
                    'posicion' => $linea,
                    'date_created' => $value->date_created,
                    'name_corto' => $value->siglas ? $value->siglas : $value->nombre_cartola,
                    'saldoTotal' => number_format($saldoTotal, 2),
                ];
            }
            if ($cara == "A") {
                if ($contadorA  == 24 && $limpiar  == true) {
                    $data = [];
                }
                if ($contadorA  <= 24 && $limpiar  == false) {
                    $dataPrint = $data;
                    $limpiar = true;
                }
            }
            if ($cara == "B") {
                if ($contadorB  == 29 && $limpiar  == true) {
                    $data = [];
                }
                if ($contadorB  <= 29 && $limpiar  == false) {
                    $dataPrint = $data;
                    $limpiar = true;
                }
            }
        }
        /*$chunks = array_chunk($detalle->toArray(), 20);
        $responseChunks = [];
        foreach ($chunks as $chunk) {
            $responseChunks[] = ($chunk);
        }
        $lastChunk = end($responseChunks);*/
        $jsonData = json_encode($dataPrint);
        // Retorna la cadena JSON como respuesta
        return $jsonData;
    }

    public function certificadoAhorroProgramado($id)
    {

        //try {
            $meses = array(
                'January' => 'enero',
                'February' => 'febrero',
                'March' => 'marzo',
                'April' => 'abril',
                'May' => 'mayo',
                'June' => 'junio',
                'July' => 'julio',
                'August' => 'agosto',
                'September' => 'septiembre',
                'October' => 'octubre',
                'November' => 'noviembre',
                'December' => 'diciembre'
            );
            $customerMovimientos = CustomerMovimiento::find($id);
            $headerCalculadoraPlazo = HeaderCalculadora::where('customer_movimientos_id', $id)->first();

            $customerMovimientosCalculadora = CustomerMovimientoDetalleCalculadora::where('customer_movimientos_id', $customerMovimientos->id)->get();
            $company = Company::find(Auth::user()->company_id);
            if ($company->photo != null && $company->photo != '') {
                $path = 'uploads/companies/' . $company->photo;
                if (!file_exists(public_path($path))) {
                    $path = 'codev/negro.png';
                }
            } else {
                $path = 'codev/negro.png';
            }
            $interes = $customerMovimientos->customerTipoAhorro->tipoAhorrosProgramadosDetalle->id;
            $pagoPlazo = $customerMovimientos->customerTipoAhorro->pago;
            $plazos = TipoAhorrosProgramadosDetalle::find($interes);
            $calculo = ($customerMovimientos->valor_movimiento) * ($plazos->interes / 100);
            $penalizado = $calculo * (2 / 100);
            $fechaInicio = date('Y-m-d');
            $fecha = \Carbon\Carbon::parse($fechaInicio);
            $fechaMasDias = $fecha->addDays($plazos->rango_max);
            $fechaPago = $fechaMasDias->toDateString();
            $montoVer = number_format($customerMovimientos->valor_movimiento,  2, '.', '');
            $interesVer = number_format($plazos->interes,  2, '.', '');
            $plazoVer = $plazos->rango_max . ' Dias';
            $valorGanadoVer = number_format($calculo,  2, '.', '');
            $penalidadVer = number_format($penalizado,  2, '.', '');
            $totalPagarVer = number_format($calculo - $penalizado);
            $fechaPago = $fechaPago;
            $mensualizados = [];
            $dataMensualizado = array();
            if ($pagoPlazo != 'C') {
                $entero = intdiv($plazos->rango_max, 30);
                $interes = $interesVer / $entero;
                $ganado = number_format($calculo / $entero,  2, '.', '');
                $penali = number_format($penalizado / $entero,  2, '.', '');
                $fechaPago = $fechaInicio;
                for ($i = 1; $i <= $entero; $i++) {
                    $fecha = \Carbon\Carbon::parse($fechaPago);
                    $fechaMasDias = $fecha->addMonths(1);
                    $fechaPago = $fechaMasDias->toDateString();
                    $dataMensualizado[] = [
                        'interes' => $interes,
                        'ganado' => $ganado,
                        'penali' => $penali,
                        'totalPagarVer' => number_format(($ganado - $penali),  2, '.', ''),
                        'fechaPago' => $fechaPago,
                    ];
                }
            }

            $dataCumplimiento = [
                'montoVer' => $montoVer,
                'interesVer' => $interesVer,
                'plazoVer' => $plazoVer,
                'valorGanadoVer' => $valorGanadoVer,
                'penalidadVer' => $penalidadVer,
                'totalPagarVer' => $totalPagarVer,
                'fechaPago' => $fechaPago,
            ];
            $man = file_get_contents(public_path($path));
            $data['imagen'] = base64_encode($man);
            $data['customer'] = Customer::find($customerMovimientos->customer_id);
            $data['company'] = $company;
            $data['movimiento'] = $customerMovimientos;
            $data['movimientoCalculadora'] = $customerMovimientosCalculadora;
            $data['cumplimiento'] = $dataCumplimiento;
            $data['mensualizado'] = $dataMensualizado;
            $data['pagoPlazo'] = $pagoPlazo;
            $data['detallePlazo'] = $plazos;
            $data['beneficiarioProgramado'] = '';
            if (isset($headerCalculadoraPlazo->beneficiarioProgramado)) {
                if ($headerCalculadoraPlazo->beneficiarioProgramado != "") {

                    $customerBeneficiarioVer = Customer::find($headerCalculadoraPlazo->beneficiarioProgramado);
                    if($customerBeneficiarioVer){

                        $data['beneficiarioProgramado'] = $customerBeneficiarioVer->nombres . " " . $customerBeneficiarioVer->apellidos;
                    }
                }
            }

            $formatter = new NumeroALetras();
            $data['cantidadLetras'] = $formatter->toMoney($data['movimiento']->valor_movimiento, 2, 'DÓLARES', 'CENTAVOS');

            $fechaInicio = \Carbon\Carbon::parse($customerMovimientos->date_created);
            $fechaInicializada = $fechaInicio;
            // Obten la fecha formateada en inglés
            $fechaFormateadaEnIngles = $fechaInicializada->format('d F Y');
            // Realiza la sustitución de los nombres de los meses
            $fechaFormateadaEnEspanol = preg_replace_callback('/\b(\d+) (\w+) (\d+)\b/u', function ($match) use ($meses) {
                $mes = $meses[$match[2]];
                return "{$match[1]} de $mes del {$match[3]}";
            }, $fechaFormateadaEnIngles);
            // Actualiza la variable $data con la fecha formateada en español
            $data['fechaFormateadaInicio'] = $fechaFormateadaEnEspanol;
            //        fecha fin
            $fechaFinPlazo = $fechaInicializada->addDays($data['movimiento']->plazo_dias_programado);
            // Obten la fecha formateada en inglés
            $fechaFormateadaEnIngles = $fechaFinPlazo->format('d F Y');
            // Realiza la sustitución de los nombres de los meses
            $fechaFormateadaEnEspanol = preg_replace_callback('/\b(\d+) (\w+) (\d+)\b/u', function ($match) use ($meses) {
                $mes = $meses[$match[2]];
                return "{$match[1]} de $mes del {$match[3]}";
            }, $fechaFormateadaEnIngles);
            // Actualiza la variable $data con la fecha formateada en español
            $data['fechaFormateadaFin'] = $fechaFormateadaEnEspanol;
            return PDF::loadView('reportes.certificadoAhorroProgramado', compact('data', 'dataCumplimiento'))
                ->download('Certificado de Ahorro.pdf');


        //} catch (\Exception $e) {
        //    return redirect()->back()->with('message', '¡Se produjo un error!' . $e->getMessage());
        //}
    }
}
