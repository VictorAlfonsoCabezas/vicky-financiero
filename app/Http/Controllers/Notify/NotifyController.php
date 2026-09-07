<?php

namespace App\Http\Controllers\Notify;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;

class NotifyController extends Controller
{
    public function getNotify(){
        $pedidos = OrderHeader::where('company_assigned_id', Auth::user()->company_id)->where('status', 'ENVIADO')->orderBy('id', 'DESC');
        if($pedidos->count() !== 0){
            foreach ($pedidos->get() as $value) {
                $orden[]=[
                    'id' => $value->id,
                    'nombres' => $value->customer_name,
                    'ruc' => $value->customer_ruc,
                    'address' => $value->customer_address,
                    'total' => $value->total
                ];
            }
            $datos =[
                'ordenes' => $orden,
                'cantidad' => $pedidos->count(),
            ];
            return Response::json($datos);
        }else{
            return Response::json(false);
        }
    }
}
