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
    public $bovedaNombre = '';
    public $nuevoNombre = '';
    public $nuevaDescripcion = '';
    public $nuevaPrincipal = false;
    public $detalleCarga = [];

    public function verDetalleCarga($id)
    {
        abort_unless(Auth::check() && Auth::user()->company_id, 403);
        $this->detalleCarga = [];
        $company = Auth::user()->company_id;
        $carga = DB::table('descargo_bovedas_header as d')
            ->join('operaciones_descargo_bovedas as o', function ($join) use ($company) {
                $join->on('o.id', '=', 'd.operaciones_descargo_bovedas_id')->where('o.company_id', $company);
            })
            ->leftJoin('bancos as b', function ($join) use ($company) {
                $join->on('b.id', '=', 'd.bancos_id')->where('b.company_id', $company);
            })
            ->leftJoin('bovedas as v', function ($join) use ($company) {
                $join->on('v.id', '=', 'd.boveda_origen_id')->where('v.company_id', $company);
            })
            ->where('d.company_id', $company)->where('d.id', $id)
            ->whereIn('o.nombre_corto', ['CAREM', 'CARCLI'])
            ->select('d.id', 'd.fecha_creacion', 'd.created_at', 'd.valor', 'd.estado', 'd.status',
                'd.observacion', 'o.nombre as operacion', 'b.nombre as banco',
                'b.numero_cuenta as cuenta', 'v.nombre as boveda')->first();
        abort_unless($carga, 404);
        $this->detalleCarga = (array) $carga;
        $this->dispatchBrowserEvent('bovedas-open', ['tipo' => 'detalle-carga']);
    }

    public function abrirCrearBoveda()
    {
        abort_unless(Auth::check() && Auth::user()->company_id, 403);
        $this->reset(['nuevoNombre', 'nuevaDescripcion', 'nuevaPrincipal']);
        $this->resetValidation();
        $this->dispatchBrowserEvent('bovedas-open', ['tipo' => 'crear']);
    }

    public function storeBoveda()
    {
        abort_unless(Auth::check() && Auth::user()->company_id, 403);
        $this->nuevoNombre = trim($this->nuevoNombre);
        $this->nuevaDescripcion = trim($this->nuevaDescripcion);
        $this->validate([
            'nuevoNombre' => 'required|string|max:255',
            'nuevaDescripcion' => 'required|string|max:255',
            'nuevaPrincipal' => 'boolean',
        ], [], [
            'nuevoNombre' => 'nombre',
            'nuevaDescripcion' => 'descripción',
            'nuevaPrincipal' => 'bóveda principal',
        ]);

        $boveda = new Bovedas();
        $boveda->company_id = Auth::user()->company_id;
        $boveda->fecha_creacion = now();
        $boveda->nombre = $this->nuevoNombre;
        $boveda->descripcion = $this->nuevaDescripcion;
        $boveda->principal = (bool) $this->nuevaPrincipal;
        $boveda->boveda = true;
        $boveda->caja = false;
        $boveda->status = true;
        $boveda->save();

        $this->bovedasTransferencia = Bovedas::where('company_id', Auth::user()->company_id)->where('status', true)->get()->toArray();
        $this->reset(['nuevoNombre', 'nuevaDescripcion', 'nuevaPrincipal']);
        $this->dispatchBrowserEvent('closeModal');
        $this->dispatchBrowserEvent('alerta', [
            'titulo' => 'Notificación', 'color' => 'success', 'mensaje' => 'Bóveda creada correctamente',
        ]);
    }

    public function abrirOperacion($id, $tipo)
    {
        abort_unless(in_array($tipo, ['carga', 'gasto', 'transferencia'], true), 404);
        $boveda = Bovedas::where('company_id', Auth::user()->company_id)->where('status', true)->findOrFail($id);
        abort_if($tipo !== 'transferencia' && !$boveda->principal, 403);
        $this->limpiarFormulario();
        $this->boveda_id = $boveda->id;
        $this->bovedaNombre = $boveda->nombre;
        $this->dispatchBrowserEvent('bovedas-open', ['tipo' => $tipo]);
    }

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
        $this->reset(['trans_boveda_id', 'trans_banco_id', 'trans_observacion', 'trans_valor', 'bovedaNombre']);
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
        $saldosBancos = [];
        foreach ($bovedas->where('principal', true) as $boveda) {
            foreach ($bancosValores as $banco) {
                $saldosBancos[$boveda->id][$banco->id] = DescargoBovedasHeaderController::valorVancoTotal($banco->id, $boveda->id);
            }
        }
        $transferencias = DB::table('descargo_bovedas_header as movimiento')
            ->join('operaciones_descargo_bovedas as operacion', 'movimiento.operaciones_descargo_bovedas_id', '=', 'operacion.id')
            ->leftJoin('bovedas as origen', 'movimiento.boveda_origen_id', '=', 'origen.id')
            ->leftJoin('bovedas as destino', 'movimiento.boveda_destino_id', '=', 'destino.id')
            ->where('movimiento.company_id', Auth::user()->company_id)
            ->where('operacion.nombre_corto', 'TRANENV')
            ->select('movimiento.*', 'origen.nombre as origen_nombre', 'destino.nombre as destino_nombre')
            ->orderByDesc('movimiento.id')->limit(10)->get();
        
        return view('livewire.descargo-bovedas-header.descargo-bovedas-header-component', compact('bovedas', 'valoresInicialesEmpresa', 'gastosIniciales', 'bancosValores', 'saldosBancos', 'transferencias'));
    }
}
