<?php

namespace App\Http\Livewire\Cartera;

use App\Models\CarteraEnviosDetalle;
use App\Models\CarteraEnviosHeader;
use App\Models\CarteraReglas;
use App\Models\CreditFolderDetail;
use App\Models\Customer;
use App\Models\Meses;
use App\Models\WhaEnvios;
use CreateCreditFolderDetailsTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CarteraComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $mes;
    public $anio;
    public $anios = [];
    public $search = '';
    public $search2 = '';
    public $finalizarInteres = false;
    public $regla = null;
    public $headerId = null;

    public function __construct()
    {
        $this->mes = date('m');
        $this->anio = date('Y');
    }

    public function finalizarRegla()
    {
        $header = CarteraEnviosHeader::find($this->headerId);
        $detalles = CarteraEnviosDetalle::where('cartera_envios_header_id', $header->id)->get();
        foreach ($detalles as $key => $value) {



            $detalles = CreditFolderDetail::select(
                'credit_folder_details.id',
                'customer.id as customer_id',
                'customer.nombres',
                'customer.apellidos',
                'customer.telefono',
            )
                ->join('credit_folder_headers', 'credit_folder_details.code_folder_header', '=', 'credit_folder_headers.code')
                ->join('customer', 'credit_folder_headers.customer_id', '=', 'customer.id')
                ->where('credit_folder_details.company_id', Auth::user()->company_id)
                ->where('credit_folder_details.id', $value->credit_folder_detalle_id)
                ->first();

            $regla = CarteraReglas::find($this->regla);
            $whatsapp = new WhaEnvios();
            $whatsapp->company_id = Auth::user()->company_id;
            $whatsapp->envio_ahora = true;
            $whatsapp->es_transacion = false;
            $whatsapp->inmediato = true;
            $whatsapp->description = 'ENVIO DE WHATSAPP COBRO DE CARTERA';
            $whatsapp->customer_id = $detalles->customer_id;
            $whatsapp->customer_name = $detalles->nombres . ' ' . $detalles->apellidos;
            $whatsapp->customer_celular = $detalles->telefono;
            $whatsapp->whatsapp = $regla->mensaje;
            $whatsapp->estado = 'PENDIENTE';
            $whatsapp->fecha_creacion = date('Y-m-d H:i:s');
            $whatsapp->fecha_envio = date('Y-m-d H:i:s');
            $whatsapp->save();

            $creditFolderDetail = CarteraEnviosDetalle::find($value->id);
            $creditFolderDetail->estado = 'FINALIZADO';
            $creditFolderDetail->save();
        }
        //Finaliza cabecera
        $header->status = 'FINALIZADO';
        $header->user_calcuado = Auth::user()->id;
        $header->save();
    }

    public function mount()
    {
        $anioActual = date('Y');
        $aniosAnteriores = [];
        for ($i = 0; $i < 5; $i++) {
            $aniosAnteriores[] = $anioActual - $i;
        }
        $this->anios = array_reverse($aniosAnteriores);
    }

    public function vaciarValores()
    {
        $this->regla = null;
        $this->headerId = null;
    }

    public function calcular($id)
    {
        $header = CarteraEnviosHeader::where('cartera_reglas_id', $id)->where('anio', $this->anio)->where('mes', $this->mes);
        if ($header->count()) {
            $header = CarteraEnviosHeader::where('cartera_reglas_id', $id)->where('anio', $this->anio)->where('mes', $this->mes)->first();
        } else {
            $header = new CarteraEnviosHeader();
            $header->company_id = Auth::user()->company_id;
            $header->cartera_reglas_id = $id;
            $header->fecha_creacion = date('Y-m-d H:i:s');
            $header->anio = $this->anio;
            $header->mes = $this->mes;
            $header->user_created = Auth::user()->id;
            $header->status = 'PENDIENTE';
            $header->save();
        }
        $reglas = CarteraReglas::find($id);
        if ($header->status == 'PENDIENTE') {
            CarteraEnviosDetalle::where('company_id', Auth::user()->company_id)
                ->where('cartera_envios_header_id', $header->id)
                ->delete();

            $vencidas = CreditFolderDetail::select(
                'credit_folder_details.id',
                DB::raw('DATEDIFF(NOW(), credit_folder_details.date_vencimiento) AS dias_transcurridos')
            )
                ->join('credit_folder_headers', 'credit_folder_details.code_folder_header', '=', 'credit_folder_headers.code')
                ->join('customer', 'credit_folder_headers.customer_id', '=', 'customer.id')
                ->where('credit_folder_details.company_id', Auth::user()->company_id)
                ->where('credit_folder_details.date_vencimiento', '<=', date('Y-m-d'))
                ->where('credit_folder_details.status', 'PENDIENTE')
                ->orderBy('credit_folder_details.id')
                ->groupBy('customer.id')
                ->having('dias_transcurridos', $reglas->comparacion, $reglas->dias_mora)
                ->get();

            foreach ($vencidas as $key => $value) {
                $detail = new CarteraEnviosDetalle();
                $detail->company_id = Auth::user()->company_id;
                $detail->cartera_envios_header_id = $header->id;
                $detail->credit_folder_detalle_id = $value->id;
                $detail->mensaje = $reglas->mensaje;
                $detail->estado = 'PENDIENTE';
                $detail->save();
            }
        }
        $this->headerId = $header->id;
        $this->regla = $id;
        $this->finalizarInteres = true;
    }

    public function ver($id)
    {
        $header = CarteraEnviosHeader::where('cartera_reglas_id', $id)->where('anio', $this->anio)->where('mes', $this->mes)->first();
        $reglas = CarteraReglas::find($id);
        $detalles = CarteraEnviosDetalle::where('company_id', Auth::user()->company_id)
            ->where('cartera_envios_header_id', $header->id)
            ->get();
        $this->headerId = $header->id;
        $this->regla = $id;
        $this->finalizarInteres = false;
    }

    public function render()
    {
        $ultimoDiaMes = date('Y-m-t', strtotime($this->anio . '-' . $this->mes . '-01'));
        $reglas = CarteraReglas::where('company_id', Auth::user()->company_id)
            ->where('created_at', '<=', $ultimoDiaMes)
            ->where('nombre', 'like', '%' . $this->search . '%')
            ->where('status', true)
            ->get();

        foreach ($reglas as $key => $reg) {
            $existe = CarteraEnviosHeader::where('company_id', Auth::user()->company_id)
                ->where('cartera_reglas_id', $reg->id)
                ->where('anio', $this->anio)
                ->where('mes', $this->mes);

            if ($existe->count() > 0) {
                $header = CarteraEnviosHeader::where('company_id', Auth::user()->company_id)
                    ->where('cartera_reglas_id', $reg->id)
                    ->where('anio', $this->anio)
                    ->where('mes', $this->mes)
                    ->first();
                $reg->estadoHeader = $header->status;
            } else {
                $reg->estadoHeader = 'PENDIENTE';
            }
        }

        $detalles = CarteraEnviosDetalle::select(
            'cartera_envios_detalle.*',
            'customer.*',
            'credit_folder_details.date_vencimiento',
            DB::raw('DATEDIFF(NOW(), credit_folder_details.date_vencimiento) AS dias_transcurridos')
        )
            ->join('credit_folder_details', 'cartera_envios_detalle.credit_folder_detalle_id', '=', 'credit_folder_details.id')
            ->join('credit_folder_headers', 'credit_folder_details.code_folder_header', '=', 'credit_folder_headers.code')
            ->join('customer', 'credit_folder_headers.customer_id', '=', 'customer.id')
            ->where('cartera_envios_detalle.company_id', Auth::user()->company_id)
            ->where('cartera_envios_detalle.cartera_envios_header_id', $this->headerId)
            ->where(function ($query) {
                $query->where('customer.nombres', 'like', '%' . $this->search2 . '%')
                    ->orWhere('customer.apellidos', 'like', '%' . $this->search2 . '%');
            })
            ->paginate(20);

        $meses = Meses::where('company_id', Auth::user()->company_id)->where('status', true)->orderBy('codigo')->get();
        return view('livewire.cartera.cartera-component', compact('meses', 'reglas', 'detalles'));
    }
}
