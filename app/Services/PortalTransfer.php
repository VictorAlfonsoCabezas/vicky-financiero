<?php

namespace App\Services;

use App\Models\{Company, Customer, CustomerTipoAhorros, CustomerMovimiento, CustomerHistorial, TypeTransaction, CartolaHeader, CartolaDetail};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PortalTransfer
{
    public function balance($account)
    {
        $query = CustomerMovimiento::join('type_transactions as t', 't.id', '=', 'customer_movimientos.type_transaction_id')
            ->where('customer_movimientos.company_id', $account->company_id)
            ->where('t.company_id', $account->company_id)
            ->where('customer_movimientos.customer_tipo_ahorro_id', $account->id)
            ->where('customer_movimientos.status', true);
        $income = (clone $query)->whereIn('t.name_corto', ['IN', 'SC', 'SOL', 'PVP', 'IFN', 'DCD', 'DFJ', 'DNC', 'TRR'])->sum('valor_movimiento');
        $expense = (clone $query)->whereIn('t.name_corto', ['EG', 'DEA', 'CVN', 'DND', 'DCT', 'TRE'])->sum('valor_movimiento');
        return (int) round($income * 100) - (int) round($expense * 100);
    }

    public function send($customer, $user, array $input)
    {
        return DB::transaction(function () use ($customer, $user, $input) {
            // Serialize portal transfers in this company, including retries from other sessions.
            Company::whereKey($customer->company_id)->lockForUpdate()->firstOrFail();
            $origin = CustomerTipoAhorros::where('company_id', $customer->company_id)
                ->where('customer_id', $customer->id)->where('status', true)->whereKey($input['cuenta'])->lockForUpdate()->firstOrFail();
            $destination = CustomerTipoAhorros::where('company_id', $customer->company_id)
                ->where('codigo', $input['destino'])->where('status', true)->lockForUpdate()->firstOrFail();
            $recipient = Customer::where('company_id', $customer->company_id)->where('status', true)->findOrFail($destination->customer_id);
            if ($origin->id === $destination->id) $this->fail('Elige una cuenta de destino diferente.');
            $reference = 'WEB-' . $input['operacion'];
            $existing = CustomerMovimiento::where('company_id', $customer->company_id)->where('code', $reference . '-E')->first();
            if ($existing) {
                if ($existing->customer_id != $customer->id) abort(409);
                $received = CustomerMovimiento::where('company_id', $customer->company_id)->where('code', $reference . '-R')->first();
                if (!$received || $existing->customer_tipo_ahorro_id != $origin->id || $received->customer_tipo_ahorro_id != $destination->id
                    || (int) round($existing->valor_movimiento * 100) !== (int) round($input['valor'] * 100)
                    || $existing->observation !== $input['descripcion']) {
                    $this->fail('Esta referencia ya corresponde a otra transferencia. Abre de nuevo el formulario para realizar una operación diferente.');
                }
                return $reference;
            }
            $cents = (int) round($input['valor'] * 100);
            if ($cents <= 0 || $cents > $this->balance($origin)) $this->fail('La cuenta no dispone de saldo suficiente.');
            $send = TypeTransaction::where('company_id', $customer->company_id)->where('status', true)->where('name_corto', 'TRE')->first();
            $receive = TypeTransaction::where('company_id', $customer->company_id)->where('status', true)->where('name_corto', 'TRR')->first();
            if (!$send || !$receive || $send->action !== 'R' || $receive->action !== 'S') {
                $this->fail('La caja debe configurar las transacciones TRE (resta) y TRR (suma) antes de transferir.');
            }
            $histories = CustomerHistorial::where('company_id', $customer->company_id)->where('status', true)
                ->whereNotNull('customer_movimiento_code')->where('customer_movimiento_code', '!=', '');
            $general = (clone $histories)->where('type_transaction_action', 'S')->sum('valor_movimiento')
                - (clone $histories)->where('type_transaction_action', 'R')->sum('valor_movimiento');
            foreach ([[$origin, $customer, $send, '-E', -1], [$destination, $recipient, $receive, '-R', 1]] as $leg) {
                [$account, $owner, $type, $suffix, $sign] = $leg;
                $general += $sign * $cents / 100;
                $movement = CustomerMovimiento::create([
                    'code' => $reference . $suffix, 'company_id' => $customer->company_id,
                    'customer_id' => $owner->id, 'customer_code' => $owner->code,
                    'customer_name' => trim($owner->nombres . ' ' . $owner->apellidos),
                    'customer_ruc' => $owner->numero_documento, 'customer_address' => $owner->direccion,
                    'customer_telefono' => $owner->telefono, 'customer_tipo_ahorro_id' => $account->id,
                    'afecta' => $type->afecta, 'type_transaction_id' => $type->id,
                    'type_transaction_name' => $type->name, 'type_transaction_action' => $type->action,
                    'valor_movimiento' => $cents / 100, 'saldo_general' => $general,
                    'observation' => $input['descripcion'], 'user_created_id' => $user->id,
                    'date_created' => date('Y-m-d'), 'hour_created' => date('H:i:s'), 'status' => true,
                ]);
                CustomerHistorial::create([
                    'company_id' => $customer->company_id, 'customer_code' => $owner->code,
                    'customer_movimiento_code' => $movement->code, 'customer_tipo_ahorro_id' => $account->id,
                    'afecta' => $type->afecta, 'type_transaction_id' => $type->id,
                    'type_transaction_name' => $type->name, 'type_transaction_action' => $type->action,
                    'valor_movimiento' => $cents / 100, 'saldo_general' => $general,
                    'date_created' => date('Y-m-d'), 'hour_created' => date('H:i:s'), 'status' => true,
                ]);
                $cartola = CartolaHeader::where('company_id', $customer->company_id)->where('customer_id', $owner->id)
                    ->where('customer_tipo_ahorro_id', $account->id)->where('status', 'ACTIVA')->lockForUpdate()->first();
                if (!$cartola) $cartola = CartolaHeader::create([
                    'code' => 'WEB-' . Str::uuid(), 'company_id' => $customer->company_id, 'customer_id' => $owner->id,
                    'customer_code' => $owner->code, 'customer_name' => $movement->customer_name,
                    'customer_tipo_ahorro_id' => $account->id, 'customer_date_create' => date('Y-m-d'), 'status' => 'ACTIVA',
                ]);
                CartolaDetail::create([
                    'cartola_header_code' => $cartola->code, 'cartola_headers_id' => $cartola->id,
                    'customer_movimientos_id' => $movement->id, 'type_transaction_id' => $type->id,
                    'type_transaction_name' => $type->name, 'type_transaction_action' => $type->action,
                    'valor_transaction' => $cents / 100, 'date_transaction' => date('Y-m-d'),
                    'saldo_transaction' => $this->balance($account) / 100,
                ]);
            }
            return $reference;
        }, 3);
    }

    private function fail($message)
    {
        throw ValidationException::withMessages(['valor' => $message]);
    }
}
