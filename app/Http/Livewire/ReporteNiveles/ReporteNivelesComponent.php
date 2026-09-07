<?php

namespace App\Http\Livewire\ReporteNiveles;

use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Exports\ReporteNivelesExport;
use App\Models\Customer;
use Maatwebsite\Excel\Facades\Excel;

class ReporteNivelesComponent extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $detalleNiveles = [];
    public $detalleNivelesArray = array();
    public $nivel = '';
    public $textoDescarga = '';
    public $totalValores = 0;

    public function seleccionarNivel()
    {
        $diasInicio = 1;
        $diasFin = 1;
        $this->detalleNiveles = [];
        $fechaActual = date('Y-m-d');

        switch ($this->nivel) {
            case 1:
                $this->textoDescarga = 'De 1 a 30 días';
                $diasInicio = 1;
                $diasFin = 30;

                $fechaInicio = \Carbon\Carbon::parse($fechaActual);
                $fechaMasDiasInicio = $fechaInicio->subDays(1);
                $fechaInicioConsulta = $fechaMasDiasInicio->toDateString();

                $fechaFin = \Carbon\Carbon::parse($fechaActual);
                $fechaMasDiasFin = $fechaFin->subDays($diasFin);
                $fechaFinConsulta = $fechaMasDiasFin->toDateString();

                $letras = CreditFolderDetail::where('date_vencimiento', '<=', $fechaInicioConsulta)->where('date_vencimiento', '>=', $fechaFinConsulta)->where('status', 'PENDIENTE')
                    ->orderBy('date_vencimiento', 'asc')
                    ->get();
                break;
            case 2:
                $this->textoDescarga = 'De 31 a 90 días';
                $diasInicio = 31;
                $diasFin = 90;
                $fechaInicio = \Carbon\Carbon::parse($fechaActual);
                $fechaMasDiasInicio = $fechaInicio->subDays($diasInicio);
                $fechaInicioConsulta = $fechaMasDiasInicio->toDateString();

                $fechaFin = \Carbon\Carbon::parse($fechaActual);
                $fechaMasDiasFin = $fechaFin->subDays($diasFin);
                $fechaFinConsulta = $fechaMasDiasFin->toDateString();
                $letras = CreditFolderDetail::where('date_vencimiento', '<=', $fechaInicioConsulta)->where('date_vencimiento', '>=', $fechaFinConsulta)->where('status', 'PENDIENTE')
                    ->orderBy('date_vencimiento', 'asc')
                    ->get();
                break;
            case 3:
                $this->textoDescarga = 'De 91 a 180 días';
                $diasInicio = 91;
                $diasFin = 180;
                $fechaInicio = \Carbon\Carbon::parse($fechaActual);
                $fechaMasDiasInicio = $fechaInicio->subDays($diasInicio);
                $fechaInicioConsulta = $fechaMasDiasInicio->toDateString();

                $fechaFin = \Carbon\Carbon::parse($fechaActual);
                $fechaMasDiasFin = $fechaFin->subDays($diasFin);
                $fechaFinConsulta = $fechaMasDiasFin->toDateString();
                $letras = CreditFolderDetail::where('date_vencimiento', '<=', $fechaInicioConsulta)->where('date_vencimiento', '>=', $fechaFinConsulta)->where('status', 'PENDIENTE')
                    ->orderBy('date_vencimiento', 'asc')
                    ->get();
                break;
            case 4:
                $this->textoDescarga = 'De más de 360 dias';
                $diasInicio = 360;
                $diasFin = 360;
                $fechaInicio = \Carbon\Carbon::parse($fechaActual);
                $fechaMasDiasInicio = $fechaInicio->subDays($diasInicio);
                $fechaInicioConsulta = $fechaMasDiasInicio->toDateString();

                $fechaFin = \Carbon\Carbon::parse($fechaActual);
                $fechaMasDiasFin = $fechaFin->subDays($diasFin);
                $fechaFinConsulta = $fechaMasDiasFin->toDateString();

                $letras = CreditFolderDetail::where('date_vencimiento', '<=', $fechaInicioConsulta)->where('status', 'PENDIENTE')
                    ->orderBy('date_vencimiento', 'asc')
                    ->get();
                break;
        }
        $sumatoriaTotalValor = 0;
        foreach ($letras as $value) {
            $sumatoriaTotalValor += $value->valor_cuota;
            $cabecera =  CreditFolderHeader::where('code', $value->code_folder_header)->first();
            $value->cedulaCliente = '';
            $value->totalLetras = '';
            if ($cabecera) {
                $value->totalLetras = $cabecera->cuotas_pagar;
            }
            if (isset($cabecera->customer_id)) {

                $customer = Customer::find($cabecera->customer_id);
                $value->cedulaCliente = $customer->numero_documento;
            }
            if (isset($cabecera->customer_name)) {
                $value->nombresCliente = $cabecera->customer_name;
            }

            $fechaEmision = \Carbon\Carbon::parse($fechaActual);
            $fechaExpiracion = \Carbon\Carbon::parse($value->date_vencimiento);

            $value->diasDiferencia = $fechaExpiracion->diffInDays($fechaEmision);
        }
        $this->totalValores = $sumatoriaTotalValor;
        $this->detalleNiveles = $letras;
        $this->detalleNivelesArray = $letras->toArray();
    }

    public function decargarReporte()
    {

        if ($this->nivel != '') {
            $this->seleccionarNivel();
            return Excel::download(new ReporteNivelesExport($this->detalleNivelesArray, $this->totalValores), $this->textoDescarga . '.xlsx');
        } else {
            $color = 'danger';
            $mensaje = 'Debe seleccionar un nivel para descargar';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }
    }


    public function render()
    {
        return view('livewire.reporte-niveles.reporte-niveles-component');
    }
}
