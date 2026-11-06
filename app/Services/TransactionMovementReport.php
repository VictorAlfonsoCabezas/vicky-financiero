<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TransactionMovementReport
{
    public function companyId()
    {
        abort_unless(Auth::user() && Auth::user()->company_id, 403);
        return Auth::user()->company_id;
    }

    public function filters(array $input)
    {
        $company = $this->companyId();
        return Validator::make(array_merge([
            'desde' => date('Y-m-01'),
            'hasta' => date('Y-m-d'),
            'tipo' => '',
            'estado' => 'activos',
            'buscar' => '',
        ], $input), [
            'desde' => 'required|date_format:Y-m-d',
            'hasta' => 'required|date_format:Y-m-d|after_or_equal:desde',
            'tipo' => ['nullable', 'integer', Rule::exists('type_transactions', 'id')->where('company_id', $company)],
            'estado' => 'required|in:activos,anulados,todos',
            'buscar' => 'nullable|string|max:120',
        ], ['hasta.after_or_equal' => 'La fecha final debe ser igual o posterior a la inicial.'])->validate();
    }

    public function types()
    {
        return DB::table('type_transactions')->where('company_id', $this->companyId())->orderBy('name')->get();
    }

    public function query(array $filters)
    {
        $f = $this->filters($filters);
        $company = $this->companyId();
        $q = DB::table('customer_movimientos as m')->leftJoin('type_transactions as t', function ($join) use ($company) {
            $join->on('t.id', '=', 'm.type_transaction_id')->where('t.company_id', '=', $company);
        })->where('m.company_id', $company)->whereBetween('m.date_created', [$f['desde'], $f['hasta']]);
        if (!empty($f['tipo'])) $q->where('m.type_transaction_id', $f['tipo']);
        if ($f['estado'] !== 'todos') $q->where('m.status', $f['estado'] === 'activos' ? 1 : 0);
        if (trim($f['buscar'] ?? '') !== '') {
            $term = '%' . trim($f['buscar']) . '%';
            $q->where(function ($q) use ($term) {
                foreach (['m.code', 'm.comprobante', 'm.customer_code', 'm.customer_name', 'm.customer_ruc'] as $column) {
                    $q->orWhere($column, 'like', $term);
                }
            });
        }
        return $q;
    }

    public function rows(array $filters)
    {
        return $this->query($filters)->select('m.*')
            ->selectRaw("COALESCE(t.name, m.type_transaction_name, 'Sin tipo') as tipo_nombre")
            ->orderByDesc('m.date_created')->orderByDesc('m.hour_created')->orderByDesc('m.id');
    }

    public function summary(array $filters)
    {
        return $this->query($filters)->select('m.type_transaction_id')
            ->selectRaw("COALESCE(t.name, m.type_transaction_name, 'Sin tipo') as tipo_nombre, COUNT(*) as cantidad, SUM(m.valor_movimiento) as valor, SUM(CASE WHEN m.status = 1 AND m.type_transaction_action = 'S' THEN m.valor_movimiento ELSE 0 END) as entradas, SUM(CASE WHEN m.status = 1 AND m.type_transaction_action = 'R' THEN m.valor_movimiento ELSE 0 END) as salidas")
            ->groupBy('m.type_transaction_id', DB::raw("COALESCE(t.name, m.type_transaction_name, 'Sin tipo')"))->orderBy('tipo_nombre')->get();
    }
}
