<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Base\BaseController;
use App\Http\Livewire\CustomerSimulador\CustomerSimuladorComponent;
use App\Models\{CustomerTipoAhorros, CustomerMovimiento, CustomerMovimientoSolicitud, Bancos, CreditFolderHeader, CreditFolderDetail, RegistroFormasPago, FormasPago, Prestamos, RecurrenciaPrestamos, TerminosUso, TerminosUsoClientes};
use App\Services\PortalTransfer;
use App\Support\CustomerPortal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash, Storage};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PortalActionsController extends Controller
{
    private function customer(Request $request)
    {
        $customer = CustomerPortal::customer($request->user());
        abort_unless($customer && $customer->status, 403);
        return $customer;
    }

    private function owned($model, $customer)
    {
        return $model::where('company_id', $customer->company_id)->where('customer_id', $customer->id);
    }

    private function accounts($customer)
    {
        return $this->owned(CustomerTipoAhorros::class, $customer)->where('status', true);
    }

    private function banks($customer)
    {
        return Bancos::where('company_id', $customer->company_id)->where('status', true);
    }

    public function movements(Request $request, $account)
    {
        $customer = $this->customer($request);
        $account = $this->owned(CustomerTipoAhorros::class, $customer)->findOrFail($account);
        $request->validate(['desde' => 'nullable|date_format:Y-m-d', 'hasta' => 'nullable|date_format:Y-m-d|after_or_equal:desde']);
        $query = $this->owned(CustomerMovimiento::class, $customer)->where('customer_tipo_ahorro_id', $account->id)->where('status', true);
        if ($request->filled('desde')) $query->whereDate('date_created', '>=', $request->desde);
        if ($request->filled('hasta')) $query->whereDate('date_created', '<=', $request->hasta);
        $movements = $query->orderByDesc('date_created')->orderByDesc('id')->paginate(20);
        $balance = app(PortalTransfer::class)->balance($account) / 100;
        return view('portal.movements', compact('customer', 'account', 'movements', 'balance'));
    }

    public function requests(Request $request)
    {
        $customer = $this->customer($request);
        $accounts = $this->accounts($customer)->get();
        $banks = $this->banks($customer)->get();
        $requests = $this->owned(CustomerMovimientoSolicitud::class, $customer)->orderByDesc('id')->paginate(15);
        $editing = $request->filled('editar') ? $this->owned(CustomerMovimientoSolicitud::class, $customer)
            ->where('estado', 'PENDIENTE')->findOrFail($request->editar) : null;
        return view('portal.requests', compact('customer', 'accounts', 'banks', 'requests', 'editing'));
    }

    public function saveRequest(Request $request)
    {
        $customer = $this->customer($request);
        $input = $request->validate([
            'id' => 'nullable|integer', 'cuenta' => 'required|integer', 'banco' => 'required|integer',
            'valor' => 'required|numeric|min:0.01|max:99999|regex:/^\d+(\.\d{1,2})?$/',
            'numero_deposito' => 'required|digits_between:1,60', 'observacion' => 'nullable|string|max:255',
            'archivo' => ($request->filled('id') ? 'nullable' : 'required') . '|file|mimes:pdf,jpg,jpeg,png|max:1024',
        ]);
        $account = $this->accounts($customer)->findOrFail($input['cuenta']);
        $this->banks($customer)->findOrFail($input['banco']);
        DB::transaction(function () use ($request, $customer, $input, $account) {
            $row = $request->filled('id') ? $this->owned(CustomerMovimientoSolicitud::class, $customer)
                ->where('estado', 'PENDIENTE')->lockForUpdate()->findOrFail($input['id']) : new CustomerMovimientoSolicitud();
            $row->fill(['company_id' => $customer->company_id, 'customer_id' => $customer->id,
                'user_id' => $request->user()->id, 'customer_tipo_ahorro_id' => $account->id,
                'banco_id' => $input['banco'], 'valor' => $input['valor'], 'numero_deposito' => $input['numero_deposito'],
                'observacion' => $input['observacion'] ?? '', 'estado' => 'PENDIENTE']);
            if (!$row->exists) $row->fecha_creacion = now();
            if ($request->hasFile('archivo')) {
                // Retain the previous attachment until the replacement has been saved successfully.
                $row->archivo = $request->file('archivo')->store('public/solicitudes');
                $row->path = Storage::url($row->archivo);
            }
            $row->save();
        });
        return redirect()->route('portal.requests')->with('message', 'Solicitud guardada. La caja revisará el comprobante antes de acreditar el valor.');
    }

    public function deleteRequest(Request $request, $id)
    {
        $customer = $this->customer($request);
        DB::transaction(function () use ($customer, $id) {
            $this->owned(CustomerMovimientoSolicitud::class, $customer)->where('estado', 'PENDIENTE')->lockForUpdate()->findOrFail($id)->delete();
        });
        return redirect()->route('portal.requests')->with('message', 'Solicitud pendiente eliminada.');
    }

    public function transfers(Request $request)
    {
        $customer = $this->customer($request);
        $accounts = $this->accounts($customer)->get();
        foreach ($accounts as $account) $account->balance = app(PortalTransfer::class)->balance($account) / 100;
        $request->validate(['buscar' => 'nullable|string|min:3|max:100']);
        $destinations = collect();
        if ($request->filled('buscar')) {
            $term = $request->buscar;
            $destinations = CustomerTipoAhorros::join('customer as c', 'c.id', '=', 'customer_tipo_ahorros.customer_id')
                ->where('customer_tipo_ahorros.company_id', $customer->company_id)->where('c.company_id', $customer->company_id)
                ->where('customer_tipo_ahorros.status', true)->where('c.status', true)
                ->where(function ($query) use ($term) {
                    $query->where('customer_tipo_ahorros.codigo', $term)->orWhere('c.nombres', 'like', '%' . $term . '%')
                        ->orWhere('c.apellidos', 'like', '%' . $term . '%');
                })->limit(10)->get(['customer_tipo_ahorros.codigo', 'c.nombres', 'c.apellidos']);
        }
        $operation = (string) Str::uuid();
        return view('portal.transfers', compact('customer', 'accounts', 'operation', 'destinations'));
    }

    public function transfer(Request $request)
    {
        $customer = $this->customer($request);
        $input = $request->validate(['cuenta' => 'required|integer', 'destino' => 'required|string|max:100',
            'valor' => 'required|numeric|min:0.01|max:99999|regex:/^\d+(\.\d{1,2})?$/',
            'descripcion' => 'required|string|max:255', 'operacion' => 'required|uuid', 'confirmar' => 'accepted']);
        $reference = app(PortalTransfer::class)->send($customer, $request->user(), $input);
        return redirect()->route('portal.transfers')->with('message', 'Transferencia procesada. Referencia: ' . $reference);
    }

    public function credit(Request $request, $id)
    {
        $customer = $this->customer($request);
        $credit = $this->owned(CreditFolderHeader::class, $customer)->findOrFail($id);
        $details = CreditFolderDetail::where('company_id', $customer->company_id)->where('code_folder_header', $credit->code)
            ->orderBy('date_vencimiento')->orderBy('id')->paginate(20);
        $banks = $this->banks($customer)->whereIn('tipo_cuenta_id', [1, 2])->get();
        $files = \App\Models\CreditFiles::where('company_id', $customer->company_id)->where('credit_header_id', $credit->id)->get();
        return view('portal.credit', compact('customer', 'credit', 'details', 'banks', 'files'));
    }

    public function creditAttachment(Request $request, $id, $detail)
    {
        $customer = $this->customer($request);
        $credit = $this->owned(CreditFolderHeader::class, $customer)->findOrFail($id);
        $row = CreditFolderDetail::where('company_id', $customer->company_id)->where('code_folder_header', $credit->code)->findOrFail($detail);
        if ($row->status === 'PAGADA') {
            return app(\App\Http\Controllers\Credit\CreditController::class)->pdfCuotaVer($row->id, $customer->id);
        }
        return $this->download($row->path);
    }

    public function creditFile(Request $request, $id, $file)
    {
        $customer = $this->customer($request);
        $credit = $this->owned(CreditFolderHeader::class, $customer)->findOrFail($id);
        $row = \App\Models\CreditFiles::where('company_id', $customer->company_id)->where('credit_header_id', $credit->id)->findOrFail($file);
        return $this->download($row->path);
    }

    public function requestAttachment(Request $request, $id)
    {
        $row = $this->owned(CustomerMovimientoSolicitud::class, $this->customer($request))->findOrFail($id);
        return $this->download($row->path);
    }

    private function download($path)
    {
        // Legacy attachments use either /storage URLs or public uploads paths.
        $path = ltrim((string) $path, '/');
        abort_if($path === '' || strpos($path, '..') !== false || strpos($path, ':') !== false || strpos($path, '\\') !== false, 404);
        $relative = preg_replace('#^(storage/|public/)#', '', $path);
        foreach ([Storage::disk('public')->path(''), public_path('uploads')] as $root) {
            $suffix = $root === public_path('uploads') ? preg_replace('#^uploads/#', '', $relative) : $relative;
            $resolvedRoot = realpath($root);
            $file = realpath($root . DIRECTORY_SEPARATOR . $suffix);
            if ($resolvedRoot && $file && is_file($file) && strpos($file, $resolvedRoot . DIRECTORY_SEPARATOR) === 0) {
                return response()->download($file, basename($file), ['X-Content-Type-Options' => 'nosniff']);
            }
        }
        abort(404, 'No se encontró el archivo adjunto.');
    }

    public function pay(Request $request, $id, $detail)
    {
        $customer = $this->customer($request);
        $credit = $this->owned(CreditFolderHeader::class, $customer)->findOrFail($id);
        $input = $request->validate(['banco' => 'required|integer', 'numero_comprobante' => 'required|string|max:100',
            'fecha_comprobante' => 'required|date_format:Y-m-d|before_or_equal:today', 'hora_comprobante' => 'required|date_format:H:i',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120']);
        $this->banks($customer)->whereIn('tipo_cuenta_id', [1, 2])->findOrFail($input['banco']);
        $paymentType = FormasPago::where('nombre', 'TRANSFERENCIA')->first();
        if (!$paymentType) throw ValidationException::withMessages(['banco' => 'La caja debe configurar la forma de pago TRANSFERENCIA.']);
        DB::transaction(function () use ($request, $customer, $credit, $detail, $input, $paymentType) {
            $row = CreditFolderDetail::where('company_id', $customer->company_id)->where('code_folder_header', $credit->code)
                ->lockForUpdate()->findOrFail($detail);
            if ($row->status !== 'PENDIENTE') throw ValidationException::withMessages(['archivo' => 'Esta cuota ya fue pagada o tiene un comprobante en revisión.']);
            if (CreditFolderDetail::where('company_id', $customer->company_id)->where('banco_id', $input['banco'])
                ->where('numero_comprobante', $input['numero_comprobante'])->where('id', '!=', $row->id)->exists()) {
                throw ValidationException::withMessages(['numero_comprobante' => 'Ese comprobante ya se registró para otra cuota.']);
            }
            $interest = $row->date_vencimiento < date('Y-m-d') ? BaseController::calculoInteresMoraLetraValorMensual($row->id) : 0;
            $path = $request->file('archivo')->store('uploads/comprobantes_pagos', 'public');
            RegistroFormasPago::create(['company_id' => $customer->company_id, 'customer_id' => $customer->id,
                'letra_id' => $row->id, 'prestamo_id' => $credit->id, 'forma_pago_id' => $paymentType->id,
                'forma_pago' => 'TRANSFERENCIA', 'banco_id' => $input['banco'], 'valor' => round($row->valor_cuota + $interest, 2),
                'numero_comprobante' => $input['numero_comprobante'], 'fecha_comprobante' => $input['fecha_comprobante'],
                'hora_comprobante' => $input['hora_comprobante'], 'date_create' => date('Y-m-d'), 'hour_create' => date('H:i:s'),
                'user_id' => $request->user()->id, 'user_name' => trim($request->user()->firstname . ' ' . $request->user()->lastname),
                'status' => 3, 'solicitado' => 3]);
            $row->status = 'STAND BY'; $row->tipo_pago = 'TRANSFERENCIA'; $row->banco_id = $input['banco'];
            $row->numero_comprobante = $input['numero_comprobante']; $row->interes_mora = $interest; $row->path = $path; $row->save();
        });
        return redirect()->route('portal.credit', $credit->id)->with('message', 'Comprobante enviado. El pago queda pendiente de revisión por la caja.');
    }

    public function password(Request $request)
    {
        $customer = $this->customer($request);
        if ($request->isMethod('post')) {
            $input = $request->validate(['actual' => 'required|string', 'password' => 'required|string|min:8|max:100|confirmed']);
            if (!Hash::check($input['actual'], $request->user()->password)) throw ValidationException::withMessages(['actual' => 'La contraseña actual no coincide.']);
            $user = $request->user(); $user->password = Hash::make($input['password']);
            $user->token = ''; $user->remember_token = Str::random(60); $user->save();
            $request->session()->regenerate();
            return redirect()->route('portal.password')->with('message', 'Contraseña actualizada.');
        }
        return view('portal.password', compact('customer'));
    }

    public function simulator(Request $request)
    {
        $customer = $this->customer($request);
        $products = Prestamos::where('company_id', $customer->company_id)->where('status', 'A')->get();
        $rows = [];
        if ($request->isMethod('post')) {
            $input = $request->validate(['producto' => 'required|integer', 'valor' => 'required|numeric|min:1|max:99999',
                'cuotas' => 'required|integer|min:1|max:360', 'fecha' => 'required|date_format:Y-m-d']);
            $product = $products->firstWhere('id', $input['producto']);
            abort_unless($product, 404);
            $period = RecurrenciaPrestamos::where('company_id', $customer->company_id)->find($product->periodo_id);
            if (!$product->calculo_simple && (!$period || !in_array($period->code, ['D', 'S', 'M']) || $period->separacion < 1
                || $period->separacion > 12 || $product->interes <= 0 || ($product->diario && $product->tipo !== 'F'))) {
                throw ValidationException::withMessages(['producto' => 'El producto no tiene una configuración válida para este simulador.']);
            }
            // Use the source calculation routines only. Never invoke its credit-creation or client-selection actions.
            $simulator = new CustomerSimuladorComponent();
            $simulator->prestamo_simulador = $product->id; $simulator->valor_simulador = $input['valor'];
            $simulator->cuotas_simulador = $input['cuotas']; $simulator->fecha_prestamo = $input['fecha'];
            if ($product->calculo_simple) $simulator->generarSimple();
            elseif ($product->diario) $simulator->generarDiario();
            else $simulator->generarNormal();
            $rows = $simulator->listaLetras;
        }
        return view('portal.simulator', compact('customer', 'products', 'rows'));
    }

    public function terms(Request $request)
    {
        $customer = $this->customer($request);
        $terms = TerminosUso::where('company_id', $customer->company_id)->where('status', true)->get();
        if ($request->isMethod('post')) {
            $input = $request->validate(['termino' => 'required|integer', 'aceptar' => 'accepted']);
            $term = $terms->firstWhere('id', $input['termino']); abort_unless($term, 404);
            TerminosUsoClientes::firstOrCreate(['company_id' => $customer->company_id, 'customer_id' => $customer->id,
                'user_id' => $request->user()->id, 'terminos_usos_id' => $term->id],
                ['periodo' => date('Y'), 'date_create' => date('Y-m-d'), 'hour_create' => date('H:i:s')]);
            return redirect()->route('portal.terms')->with('message', 'Aceptación registrada.');
        }
        $accepted = $this->owned(TerminosUsoClientes::class, $customer)->where('user_id', $request->user()->id)->pluck('terminos_usos_id')->all();
        return view('portal.terms', compact('customer', 'terms', 'accepted'));
    }
}
