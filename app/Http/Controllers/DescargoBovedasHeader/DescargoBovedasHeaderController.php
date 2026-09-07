<?php

namespace App\Http\Controllers\DescargoBovedasHeader;

use App\Http\Controllers\Controller;
use App\Models\AsientosHeader;
use App\Models\Company;
use App\Models\DescargoBovedasHeader;
use App\Models\OperacionesDescargoBovedas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DescargoBovedasHeaderController extends Controller
{
    public function index()
    {
        return view('descargo-bovedas-header/index');
    }

    public static function agregarDescargoBovedasHeader(
        $banco = null,
        $caja = null,
        $operacion,
        $bovedaOrigen,
        $bovedaDestino = null,
        $valor,
        $observacion = null,
        $estado = 'PENDIENTE'
    ) {
        $descargoBovedasHeader = new DescargoBovedasHeader();
        $descargoBovedasHeader->company_id = Auth::user()->company_id;
        $descargoBovedasHeader->bancos_id = $banco;
        $descargoBovedasHeader->cajas_id = $caja;
        $descargoBovedasHeader->operaciones_descargo_bovedas_id = $operacion;
        $descargoBovedasHeader->boveda_origen_id = $bovedaOrigen;
        $descargoBovedasHeader->boveda_destino_id = $bovedaDestino;
        $descargoBovedasHeader->valor = $valor;
        $descargoBovedasHeader->fecha_creacion = date('Y-m-d H:i:s');
        $descargoBovedasHeader->estado = $estado;
        $descargoBovedasHeader->status = true;
        $descargoBovedasHeader->observacion = $observacion;
        $descargoBovedasHeader->save();

        //Si es transferencia se crea el movimiento en la boveda
        if ($bovedaDestino !== null) {
            $transferencia = OperacionesDescargoBovedas::where('company_id', Auth::user()->company_id)->where('nombre_corto', 'TRANRECI')->firstOrFail();
            $descargoBovedasHeaderDestino = new DescargoBovedasHeader();
            $descargoBovedasHeaderDestino->company_id = Auth::user()->company_id;
            $descargoBovedasHeaderDestino->bancos_id = $banco;
            $descargoBovedasHeaderDestino->cajas_id = $caja;
            $descargoBovedasHeaderDestino->operaciones_descargo_bovedas_id = $transferencia->id;
            $descargoBovedasHeaderDestino->boveda_origen_id = $bovedaDestino;
            $descargoBovedasHeaderDestino->boveda_destino_id = null;
            $descargoBovedasHeaderDestino->valor = $valor;
            $descargoBovedasHeaderDestino->fecha_creacion = date('Y-m-d H:i:s');
            $descargoBovedasHeaderDestino->estado = $estado;
            $descargoBovedasHeaderDestino->status = true;
            $descargoBovedasHeaderDestino->observacion = $observacion;
            $descargoBovedasHeaderDestino->save();
        }

        //Crear asientos
        $company = Company::find(Auth::user()->company_id);
        if ($company && $company->puedeContabilizar($descargoBovedasHeader->fecha_creacion)) {
            AsientosHeader::recalcularAsientosBovedas($descargoBovedasHeader->id);
        }

        return true;
    }

    public static function aprobarOperacion($id)
    {
        $descargoBovedasHeader = DescargoBovedasHeader::find($id);
        $descargoBovedasHeader->estado = 'FINALIZADO';
        $descargoBovedasHeader->save();
        return true;
    }

    public static function valorVancoTotal($banco, $boveda)
    {
        $valor = 0;
        $operacionIngreso =  OperacionesDescargoBovedas::where('nombre_corto', 'TRANRECI')->first();
        $operacionInicial =  OperacionesDescargoBovedas::where('nombre_corto', 'CAREM')->first();
        $operacionInicialCliente =  OperacionesDescargoBovedas::where('nombre_corto', 'CARCLI')->first();
        $operacionDepositoCuentaYnotasCredito =  OperacionesDescargoBovedas::where('nombre_corto', 'INVA')->first();
        $valoresRecibidos = DescargoBovedasHeader::where('estado', 'FINALIZADO')
            ->whereIn('operaciones_descargo_bovedas_id', [$operacionIngreso->id,  $operacionInicial->id,  $operacionInicialCliente->id, $operacionDepositoCuentaYnotasCredito->id])
            ->where('bancos_id', $banco)
            ->where('boveda_origen_id', $boveda)
            ->sum('valor');
        $operacionEnvia =  OperacionesDescargoBovedas::where('nombre_corto', 'TRANENV')->first();
        $valoresEnviados = DescargoBovedasHeader::where('estado', 'FINALIZADO')
            ->where('operaciones_descargo_bovedas_id', $operacionEnvia->id)
            ->where('bancos_id', $banco)
            ->where('boveda_origen_id', $boveda)
            ->sum('valor');
        $operacionEntregaCredito =  OperacionesDescargoBovedas::where('nombre_corto', 'EVC')->first();
        $valoresRetirados = DescargoBovedasHeader::where('estado', 'FINALIZADO')
            ->where('operaciones_descargo_bovedas_id', $operacionEntregaCredito->id)
            ->where('bancos_id', $banco)
            ->where('boveda_origen_id', $boveda)
            ->sum('valor');
        $valor = number_format($valoresRecibidos, 2, '.', '') - number_format($valoresEnviados, 2, '.', '') - number_format($valoresRetirados, 2, '.', '');
        return number_format($valor, 2, '.', '');
    }
}
