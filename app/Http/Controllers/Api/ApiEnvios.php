<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Company;
use App\Models\Country;
use App\Models\Customer;
use App\Models\WhaEnvios;
use Illuminate\Http\Request;
use Response;

class ApiEnvios extends Controller
{
    public function enviosInmediatos(Request $request)
    {
        $data = [];
        $company = Company::find($request->input('empresa'));
        if ($company->count() > 0) {

            $company = Company::find($request->input('empresa'));
            $existe = WhaEnvios::where('company_id', $company->id)
                ->whereIn('estado', ["PROGRAMADO", "PENDIENTE"])
                ->where('envio_ahora', true);

            if ($existe->count()) {
                $detalles = WhaEnvios::where('company_id', $company->id)
                    ->whereIn('estado', ["PROGRAMADO", "PENDIENTE"])
                    ->where('envio_ahora', true)
                    ->orderBy('id', 'ASC')
                    ->take(10)
                    ->get();


                foreach ($detalles as $key => $value) {
                    $detalleIndividual = WhaEnvios::find($value->id);
                    $detalleIndividual->envio_ahora = false;
                    $detalleIndividual->fecha_envio = date('Y-m-d H:i:s');
                    $detalleIndividual->save();

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

                    $customer = Customer::find($value->customer_id);
                    if ($customer->country->id == 1) {
                        if (substr($customer->customer_celular, 0, 1) === '0') {
                            $telefonoSinCero = substr($customer->telefono, 1);
                        } else {
                            $telefonoSinCero = $customer->telefono;
                        }
                        $celular = $customer->country->codigo_llamada . $telefonoSinCero;
                    } else {
                        $celular = $customer->country->codigo_llamada . $customer->telefono;
                    }



                    //VALIDAR CELULAR SOLO ECUADOR 9 DIGITOS 
                    // if (strlen($value->phone)  == 9) {
                    $detalleEnvio = WhaEnvios::find($value->id);
                    $detalleEnvio->estado = "ENVIADO";
                    $detalleEnvio->save();
                    $data[] = [
                        'id' => $detalleEnvio->id,
                        'celular' => $celular,
                        'mensaje' => $value->whatsapp
                    ];
                    // } else {
                    //     $detalleEnvio = WhaEnvios::find($value->id);
                    //     $detalleEnvio->estado = "FALLIDO";
                    //     $detalleEnvio->save();
                    // }
                }
                $data = [
                    'code' => 200,
                    'msg' => "Consulta exitosa",
                    'data' => $data,
                ];
            } else {
                $data = [
                    'code' => 300,
                    'msg' => "No existe nada a enviar",
                    'data' => false,
                ];
            }
        } else {
            $data = [
                'code' => 404,
                'msg' => "No existe Empresa",
                'data' => false,
            ];
        }
        return $data;
    }
}
