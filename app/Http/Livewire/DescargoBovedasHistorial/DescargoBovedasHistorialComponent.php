<?php

namespace App\Http\Livewire\DescargoBovedasHistorial;

use App\Models\DescargoBovedasHeader;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DescargoBovedasHistorialComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public function render()
    {
        $historial = DescargoBovedasHeader::select(
            'descargo_bovedas_header.*',
            'operaciones_descargo_bovedas.nombre as operacion_nombre',
            'bovedas.nombre as bovedas_nombre',
            'bov.nombre as bovedas_recibe_nombre',
            'bancos.nombre as banco_nombre',
            'bancos.numero_cuenta',
            'cajas.code as caja_code'
        )
            ->leftjoin('operaciones_descargo_bovedas', 'descargo_bovedas_header.operaciones_descargo_bovedas_id', '=', '.operaciones_descargo_bovedas.id')
            ->leftjoin('bovedas', 'descargo_bovedas_header.boveda_origen_id', '=', 'bovedas.id')
            ->leftjoin('bovedas as bov', 'descargo_bovedas_header.boveda_destino_id', '=', 'bov.id')
            ->leftjoin('bancos', 'descargo_bovedas_header.bancos_id', '=', 'bancos.id')
            ->leftjoin('cajas', 'descargo_bovedas_header.cajas_id', '=', 'cajas.id')
            ->where('descargo_bovedas_header.company_id', Auth::user()->company_id)
            ->orderBy('descargo_bovedas_header.created_at', 'DESC')
            ->paginate(20);
        return view('livewire.descargo-bovedas-historial.descargo-bovedas-historial-component', compact('historial'));
    }
}
