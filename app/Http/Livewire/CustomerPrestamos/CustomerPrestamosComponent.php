<?php

namespace App\Http\Livewire\CustomerPrestamos;

use App\Http\Controllers\Credit\CreditController;
use App\Http\Controllers\Base\BaseController;
use App\Models\Bancos;
use App\Models\CreditFiles;
use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\Customer;
use App\Models\FondoHeader;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\FormasPago;


use Livewire\WithFileUploads;
use App\Models\RegistroFormasPago;
use Illuminate\Support\Facades\DB;


class CustomerPrestamosComponent extends Component
{
    use WithPagination, WithFileUploads;
    protected $paginationTheme = 'bootstrap';


    public $detalleSeleccionado;

    public $registroPago;

    public $banco_id = '';
    public $numero_comprobante = '';
    public $comprobante;

    public $fecha_comprobante;
    public $hora_comprobante;
    //public $pagoTipoBanco = false;

    //public $forma_pago_id = '';

    public $mostrarBanco = false;

    public $valorInteres = 0;
    public $valorTotal = 0;

    public $code_header = 0;
    public $id_seleccionado = 0;
    public $cuenta_selec = 0;

    public function selecionaPrestamos($id)
    {
        $this->cuenta_selec = $id;
        $this->id_seleccionado = $id;
        $header = CreditFolderHeader::find($id);
        $this->code_header = $header->code;
    }

    public function descargarComprobante($id)
    {
        $detalle = CreditFolderDetail::find($id);
        if ($detalle->path != null && $detalle->path != '') {
            $link = $detalle->path;
        } else {
            $vaucher = CreditController::crearVaucherNuevo($id);
            $letra = CreditFolderDetail::find($id);
            $link = '/uploads/comprobante/' . $letra->path;
        }
        if (Storage::exists($link)) {
            return Storage::download($link);
        }
    }

    public function descargarArchivo($id)
    {
        $detalle = CreditFolderDetail::find($id);
        if ($detalle->path != null && $detalle->path != '') {
            $link = $detalle->path;
        } else {
            $vaucher = CreditController::crearVaucherNuevo($id);
            $letra = CreditFolderDetail::find($id);
            $link = '/uploads/comprobante/' . $letra->path;
        }
        $rutaArchivo = public_path('uploads/comprobante/' . $link);
        if (file_exists($rutaArchivo)) {
            return response()->stream(
                function () use ($rutaArchivo) {
                    readfile($rutaArchivo);
                },
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="archivo.pdf"',
                ]
            );
        } else {
            // Manejar el caso en que el archivo no existe
            // Puedes emitir una alerta o un mensaje de error
        }
    }

    private function calcularInteres($detalle)
    {
        $fechaactual = date("Y-m-d");

        if ($detalle->date_vencimiento < $fechaactual) {
            return BaseController::calculoInteresMoraLetraValorMensual($detalle->id);
        }

        return 0;
    }


    public function render()
    {
        $prestamos = CreditFolderHeader::select('credit_folder_headers.*')
            ->join('customer', 'credit_folder_headers.customer_id', '=', 'customer.id')
            ->where('credit_folder_headers.company_id', Auth::user()->company_id)
            ->where('customer.user_id', Auth::user()->id)
            ->get();
        $header = CreditFolderHeader::find($this->id_seleccionado);

        $detalle = CreditFolderDetail::where('company_id', Auth::user()->company_id)->where('code_folder_header', $this->code_header)->paginate(10);
        foreach ($detalle as $det) {

            /*$interesMora = BaseController::calculoInteresMoraLetraValorMensual($det->id);
            $fechaactual = date("Y-m-d");

            $det->valorInteres = number_format(0, 2, '.', '');
            if ($det->date_vencimiento < $fechaactual) {
                $det->valorInteres = $interesMora;
            }*/

            $det->valorInteres = $this->calcularInteres($det);

            $cabecera = CreditFolderHeader::where('code', $det->code_folder_header)->first();
            $det->customerID = $cabecera->customer_id;

            $det->ultimoPago = RegistroFormasPago::where('letra_id', $det->id)
                ->latest('id')
                ->first();
        }
        $files = CreditFiles::where('company_id', Auth::user()->company_id)->where('credit_header_id', $this->id_seleccionado)->get();
        $formasPago = FormasPago::all();
        $bancos = Bancos::whereIn('tipo_cuenta_id', [1, 2])->get();

        return view('livewire.customer-prestamos.customer-prestamos-component', compact('prestamos', 'header', 'detalle', 'files', 'formasPago', 'bancos'));
    }

    public function subirPago($id)
    {
        $detalle = CreditFolderDetail::find($id);

        if (!$detalle) {
            return;
        }

        $this->detalleSeleccionado = $detalle;

        $valorInteres = $this->calcularInteres($detalle);

        $this->valorInteres = $valorInteres;
        $this->valorTotal = $detalle->valor_cuota + $valorInteres;

        //$this->forma_pago_id = '';
        $this->fecha_comprobante = date('Y-m-d');
        $this->hora_comprobante = date('H:i');

        $this->banco_id = '';
        $this->numero_comprobante = '';
        $this->comprobante = null;

        $this->dispatchBrowserEvent('show-modal-pago');
    }

    /*public function cambioFormaPago($id)
    {
        $formaPago = FormasPago::find($id);

        $this->pagoTipoBanco = false;

        if ($formaPago) {
            if (
                trim(strtoupper($formaPago->nombre)) == 'TRANSFERENCIA' ||
                trim(strtoupper($formaPago->nombre)) == 'CHEQUE'
            ) {
                $this->pagoTipoBanco = true;
            }
        }
    }*/

    public function guardarPago()
    {
        $this->resetErrorBag();
        $this->resetValidation();

        try {

            $this->validate([
                'banco_id'            => 'required',
                'fecha_comprobante'   => 'required|date',
                'hora_comprobante'    => 'required',
                'numero_comprobante'  => 'required',
                'comprobante'         => 'required|file|max:5120',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            $this->dispatchBrowserEvent('show-modal-pago');

            throw $e;
        }

        if (!$this->detalleSeleccionado) {
            return;
        }

        $detalle = CreditFolderDetail::find($this->detalleSeleccionado->id);

        if (!$detalle) {
            return;
        }

        DB::beginTransaction();

        try {

            //Subir Comprobante

            $nombreArchivo = time() . '_' . $this->comprobante->getClientOriginalName();

            $this->comprobante->storeAs(
                'uploads/comprobantes_pagos',
                $nombreArchivo,
                'public'
            );

            //revisar si hay un Registro

            $registro = RegistroFormasPago::where('letra_id', $detalle->id)
                ->where('forma_pago', 'TRANSFERENCIA')
                ->where('solicitado', 2)
                ->latest('id')
                ->first();

            //Forma de Pago

            $formaPago = FormasPago::where('nombre', 'TRANSFERENCIA')->first();

            //Registrar solicitud de pago

            if (!$registro) {

                $registro = new RegistroFormasPago();

                $registro->company_id = Auth::user()->company_id;

                $cabecera = CreditFolderHeader::find($this->id_seleccionado);

                $registro->customer_id = $cabecera->customer_id;
                $registro->letra_id = $detalle->id;
                $registro->prestamo_id = $this->id_seleccionado;

                $registro->forma_pago_id = $formaPago->id;
                $registro->forma_pago = 'TRANSFERENCIA';

                $registro->user_id = Auth::user()->id;
                $registro->user_name = Auth::user()->lastname . ' ' . Auth::user()->firstname;
            }

            $registro->banco_id = $this->banco_id;

            $registro->valor = $this->valorTotal;

            $registro->numero_comprobante = $this->numero_comprobante;

            $registro->fecha_comprobante = $this->fecha_comprobante;
            $registro->hora_comprobante = $this->hora_comprobante;

            $registro->date_create = date('Y-m-d');
            $registro->hour_create = date('H:i:s');

            $registro->status = 3;
            $registro->solicitado = 3;

            // limpiar datos del rechazo anterior
            $registro->usuario_solicitud = null;
            $registro->usuario_id_solicitud = null;
            $registro->fecha_solicitud = null;
            $registro->hora_solicitud = null;

            $registro->save();

            /*$registro = new RegistroFormasPago();

            $registro->company_id = Auth::user()->company_id;

            $cabecera = CreditFolderHeader::find($this->id_seleccionado);

            $registro->customer_id = $cabecera->customer_id;
            //$registro->customer_id = $detalle->customer_id ?? 0;
            $registro->letra_id = $detalle->id;
            $registro->prestamo_id = $this->id_seleccionado;

            $registro->forma_pago_id = $formaPago->id;
            $registro->forma_pago = 'TRANSFERENCIA';

            $registro->banco_id = $this->banco_id;

            $registro->valor = $this->valorTotal;

            $registro->numero_comprobante = $this->numero_comprobante;

            $registro->date_create = date('Y-m-d');
            $registro->hour_create = date('H:i:s');

            $registro->user_id = Auth::user()->id;
            $registro->user_name = Auth::user()->lastname . ' ' . Auth::user()->firstname;

            $registro->status = 3;

            $registro->solicitado = 3;

            $registro->fecha_comprobante = $this->fecha_comprobante;
            $registro->hora_comprobante = $this->hora_comprobante;

            $registro->save();*/



            //Actualizar cuota

            $detalle->status = 'STAND BY';

            $detalle->tipo_pago = 'TRANSFERENCIA';

            $detalle->banco_id = $this->banco_id;
            $detalle->numero_comprobante = $this->numero_comprobante;

            $detalle->interes_mora = $this->valorInteres;

            $detalle->path = 'uploads/comprobantes_pagos/' . $nombreArchivo;

            $detalle->save();

            DB::commit();

            $this->reset([
                'banco_id',
                'numero_comprobante',
                'comprobante',
                'fecha_comprobante',
                'hora_comprobante',
                'detalleSeleccionado',
                'valorInteres',
                'valorTotal'
            ]);

            $this->dispatchBrowserEvent('close-modal-pago');

            session()->flash('message', 'Comprobante enviado correctamente.');
        } catch (\Exception $e) {

            DB::rollBack();

            session()->flash('error', $e->getMessage());
        }
    }

    public function verPago($id)
    {
        $this->detalleSeleccionado = CreditFolderDetail::find($id);

        $this->registroPago = RegistroFormasPago::where('letra_id', $id)
            ->latest('id')
            ->first();

        $this->dispatchBrowserEvent('show-modal-ver-pago');
    }
}
