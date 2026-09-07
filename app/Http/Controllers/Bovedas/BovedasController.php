<?php

namespace App\Http\Controllers\Bovedas;

use App\Http\Controllers\Controller;
use App\Models\CreditFolderHeader;
use App\Models\DescargoBovedasHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BovedasController extends Controller
{
    public function index()
    {
        return view('bovedas.index');
    }

    public static function saldoBovedas($id)
    {
        $saldo = DB::table('descargo_bovedas_header')
            ->join('operaciones_descargo_bovedas', 'descargo_bovedas_header.operaciones_descargo_bovedas_id', '=', 'operaciones_descargo_bovedas.id')
            ->where('descargo_bovedas_header.company_id', Auth::user()->company_id)
            ->where('descargo_bovedas_header.boveda_origen_id', $id)
            ->where('descargo_bovedas_header.estado', 'FINALIZADO')
            ->selectRaw('SUM(CASE WHEN operaciones_descargo_bovedas.accion = "S" THEN descargo_bovedas_header.valor ELSE -descargo_bovedas_header.valor END) as total')
            ->value('total') ?? 0;
        return number_format($saldo, 2, '.', '');
    }

    public static function creditosVigentes()
    {
        $vigente = CreditFolderHeader::where('company_id', Auth::user()->company_id)->whereIn('status', ['ENTREGADO'])->sum('valor_solicitado') ?? 0;
        return number_format($vigente, 2, '.', '');
    }
}
