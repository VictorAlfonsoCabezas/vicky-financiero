<?php

namespace App\Http\Livewire\DescargoBovedasHeader;

use App\Http\Controllers\Bovedas\BovedasController;
use App\Http\Controllers\DescargoBovedasHeader\DescargoBovedasHeaderController;
use App\Models\Bancos;
use App\Models\Bovedas;
use App\Models\CreditFolderHeader;
use App\Models\DescargoBovedasHeader;
use App\Models\OperacionesDescargoBovedas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DescargoBovedasHeaderComponent extends Component
{
    public $bancos = [];
    public $bovedasTransferencia = [];
    public $cargaInicial = [];
    public $valor = 0;
    public $banco_id = '';
    public $operacion_id = '';
    public $boveda_id = '';
    public $trans_boveda_id = '';
    public $trans_banco_id = '';
    public $trans_observacion = '';
    public $trans_valor = '';
    public $creditosVigentes = 0;
    public $valor_gasto = 0;
    public $descripcion_gasto = '';

    public function storeCarga()
    {
        $this->validate([
            'valor' => 'required|numeric',
            'banco_id' => 'required|numeric',
            'operacion_id' => 'required',
        ]);

        DescargoBovedasHeaderController::agregarDescargoBovedasHeader(
            $this->banco_id,
            null,
            $this->operacion_id,
            $this->boveda_id,
            null,
            $this->valor,
            'CARGA INICIAL',
            'FINALIZADO'
        );
        $this->limpiarFormulario();
        $color = 'success';
        $mensaje = 'Ingreso de carga inicial exitoso';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
        $this->dispatchBrowserEvent('closeModal');
    }

    public function storeGastoInicial()
    {
        $this->validate([
            'valor_gasto' => 'required|numeric',
            'descripcion_gasto' => 'required',
        ]);
        $gastoInicial = OperacionesDescargoBovedas::where('company_id', Auth::user()->company_id)->where('nombre_corto', 'GASINI')->firstOrFail();
        DescargoBovedasHeaderController::agregarDescargoBovedasHeader(
            null,
            null,
            $gastoInicial->id,
            $this->boveda_id,
            null,
            $this->valor_gasto,
            $this->descripcion_gasto,
            'FINALIZADO'
        );
        $this->limpiarFormulario();
        $color = 'success';
        $mensaje = 'Gasto Inicial exitoso';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
        $this->dispatchBrowserEvent('closeModal');
    }

    public function seleccionarBoveda($id)
    {
        $this->boveda_id = $id;
    }

    public function gastoInicial($id)
    {
        $this->boveda_id = $id;
    }

    private function limpiarFormulario()
    {
        $this->boveda_id = '';
        $this->reset(['valor', 'banco_id', 'operacion_id', 'valor_gasto', 'descripcion_gasto']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeTransferencia()
    {
        $this->validate([
            'trans_valor' => 'required|numeric',
            'trans_boveda_id' => 'required|numeric',
            'trans_banco_id' => 'required|numeric',
            'trans_observacion' => 'required',
        ]);
        $transferencia = OperacionesDescargoBovedas::where('company_id', Auth::user()->company_id)->where('nombre_corto', 'TRANENV')->firstOrFail();
        DescargoBovedasHeaderController::agregarDescargoBovedasHeader(
            $this->trans_banco_id,
            null,
            $transferencia->id,
            $this->boveda_id,
            $this->trans_boveda_id,
            $this->trans_valor,
            'TRANSFERENCIA',
            'FINALIZADO'
        );
        $this->limpiarFormulario();
        $color = 'success';
        $mensaje = 'Transferencia exitosa';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
        $this->dispatchBrowserEvent('closeModal');
    }

    public function mount()
    {
        $this->bancos = Bancos::where('company_id', Auth::user()->company_id)->where('numero_cuenta', '!=', '')->where('status', true)->get()->toArray();
        $this->bovedasTransferencia = Bovedas::where('company_id', Auth::user()->company_id)->where('status', true)->get()->toArray();
        $this->cargaInicial = OperacionesDescargoBovedas::where('company_id', Auth::user()->company_id)->whereIn('nombre_corto', ['CAREM', 'CARCLI'])->where('status', true)->get()->toArray();
    }

    public function render()
    {
        $bovedas = Bovedas::where('company_id', Auth::user()->company_id)->where('status', true)->get();
        foreach ($bovedas as $key => $value) {
            $value->saldoBoveda = BovedasController::saldoBovedas($value->id);
        }

        $valoresInicialesEmpresa = DB::table('descargo_bovedas_header')->select('descargo_bovedas_header.*', 'bancos.nombre as nombre_banco')
            ->join('operaciones_descargo_bovedas', 'descargo_bovedas_header.operaciones_descargo_bovedas_id', '=', 'operaciones_descargo_bovedas.id')
            ->join('bancos', 'descargo_bovedas_header.bancos_id', '=', 'bancos.id')
            ->where('descargo_bovedas_header.company_id', Auth::user()->company_id)
            ->whereIn('operaciones_descargo_bovedas.nombre_corto', ['CAREM', 'CARCLI'])
            ->get();

        $gastosIniciales = DB::table('descargo_bovedas_header')->select('descargo_bovedas_header.*')
            ->join('operaciones_descargo_bovedas', 'descargo_bovedas_header.operaciones_descargo_bovedas_id', '=', 'operaciones_descargo_bovedas.id')
            ->where('descargo_bovedas_header.company_id', Auth::user()->company_id)
            ->whereIn('operaciones_descargo_bovedas.nombre_corto', ['GASINI'])
            ->get();
        $this->creditosVigentes = BovedasController::creditosVigentes();
        $bancosValores = Bancos::where('company_id', Auth::user()->company_id)->where('numero_cuenta', '!=', '')->where('status', true)->get();
        
        return view('livewire.descargo-bovedas-header.descargo-bovedas-header-component', compact('bovedas', 'valoresInicialesEmpresa', 'gastosIniciales', 'bancosValores'));
    }
}
