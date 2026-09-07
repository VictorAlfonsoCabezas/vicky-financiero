<?php

namespace App\Http\Livewire\CargaInicial;

use App\Models\AsientosDetalle;
use App\Models\AsientosHeader;
use App\Models\CentroCostos;
use App\Models\Conceptos;
use App\Models\PlanCuentas;
use App\Models\Sedes;
use App\Models\SedesCentroCostos;
use App\Models\TipoConcepto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CargaInicialComponent extends Component
{
    public function elminarAsiento($id)
    {
        AsientosDetalle::where('company_id', Auth::user()->company_id)->where('asientos_header_id', $id)->delete();
        AsientosHeader::find($id)->delete();
        $color = 'danger';
        $mensaje = 'Se elimino correctamente, el Asiento Contable';
        $data = [
            'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function modificartValor($detalleId, $inputValue)
    {
        $detalle = AsientosDetalle::find($detalleId);
        $detalle->valor = $inputValue;
        $detalle->save();

        $color = 'success';
        $mensaje = 'Se modifica el Asiento correctamente...';
        $data = [
            'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }


    public function render()
    {
        $detalle = AsientosDetalle::select(
            'asientos_detalle.*',
            'asientos_header.id as header_id',
            'asientos_header.manual',
            'asientos_detalle.id as detalle_id',
            DB::raw("CONCAT(users.firstname, ' ', users.lastname) as nombres"),
            'plan_cuentas.codigo',
            'plan_cuentas.nombre',
            'conceptos.nombre as concepto',
            DB::raw("(SELECT SUM(valor) FROM asientos_detalle WHERE asientos_detalle.asientos_header_id = asientos_header.id AND asientos_detalle.debe_haber = true) as suma_debe"),
            DB::raw("(SELECT SUM(valor) FROM asientos_detalle WHERE asientos_detalle.asientos_header_id = asientos_header.id AND asientos_detalle.debe_haber = false) as suma_haber")
        )
            ->join('asientos_header', 'asientos_detalle.asientos_header_id', '=', 'asientos_header.id')
            ->join('users', 'asientos_detalle.user_created', '=', 'users.id')
            ->join('plan_cuentas', 'asientos_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
            ->join('conceptos', 'asientos_header.concepto_id', '=', 'conceptos.id')
            ->join('descargo_bovedas_header', 'asientos_header.descargo_bovedas_header_id', '=', 'descargo_bovedas_header.id')
            ->join('operaciones_descargo_bovedas', 'descargo_bovedas_header.operaciones_descargo_bovedas_id', '=', 'operaciones_descargo_bovedas.id')
            ->where('asientos_detalle.company_id', Auth::user()->company_id)
            ->where('operaciones_descargo_bovedas.nombre', "CARGA INICIAL EMPRESA")
            ->orderBy('asientos_detalle.debe_haber', 'DESC')
            ->orderBy('asientos_detalle.id')
            ->get();

        $contador = $detalle->count();
        return view('livewire.carga-inicial.carga-inicial-component', compact('detalle','contador'));
    }
}
