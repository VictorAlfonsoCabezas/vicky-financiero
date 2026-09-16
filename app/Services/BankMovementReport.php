<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BankMovementReport
{
    public function companyId()
    {
        $user = Auth::user();
        abort_unless($user && $user->company_id, 403);
        $permissions = DB::table('usuario_rol as ur')->join('rol as r', 'r.id', '=', 'ur.rol_id')
            ->join('menu_rol as mr', 'mr.rol_id', '=', 'r.id')->join('menu as m', 'm.id', '=', 'mr.menu_id')
            // Match LoginController: legacy role assignments may have an empty pivot status.
            ->where('ur.user_id', $user->id)->where('r.status', true)
            ->whereIn('m.url', ['conciliacion', '/conciliacion', 'conciliacion.index']);
        if (session('rol_id')) $permissions->where('r.id', session('rol_id'));
        abort_unless($permissions->exists(), 403, 'No tiene acceso al reporte de conciliación.');
        return $user->company_id;
    }

    public static function defaults()
    {
        return [
            'desde' => date('Y-m-01'),
            'hasta' => date('Y-m-t'),
            'banco' => '',
            'forma' => '',
            'tipo' => '',
            'estado' => 'activos',
            'alcance' => 'todos',
            'buscar' => ''
        ];
    }

    public function validate(array $filters)
    {
        $company = $this->companyId();
        if (($filters['banco'] ?? '') === 'sin_banco') {
            $filters['banco'] = '';
            $filters['alcance'] = 'sin_banco';
        } elseif (($filters['banco'] ?? '') === 'bancarios') {
            $filters['banco'] = '';
            $filters['alcance'] = 'bancarios';
        } elseif (!empty($filters['banco'])) {
            $filters['alcance'] = 'bancarios';
        }
        return Validator::make($filters, [
            'desde' => 'required|date_format:Y-m-d',
            'hasta' => 'required|date_format:Y-m-d|after_or_equal:desde',
            'banco' => ['nullable', 'integer', Rule::exists('bancos', 'id')->where('company_id', $company)],
            'forma' => ['nullable', 'integer', Rule::exists('formas_pago', 'id')->where('company_id', $company)],
            'tipo' => ['nullable', 'integer', Rule::exists('type_transactions', 'id')->where('company_id', $company)],
            'estado' => 'required|in:activos,anulados,todos',
            'alcance' => 'required|in:bancarios,sin_banco,todos',
            'buscar' => 'nullable|string|max:120',
        ], ['hasta.after_or_equal' => 'La fecha final debe ser igual o posterior a la inicial.'])->validate();
    }

    public function query(array $filters)
    {
        $f = $this->validate($filters);
        $company = Auth::user()->company_id;
        $q = DB::table('customer_movimientos as m')->leftJoin('bancos as b', function ($join) use ($company) {
            $join->on('b.id', '=', 'm.banco_id')->where('b.company_id', '=', $company)
                ->whereNotNull('b.numero_cuenta')->where('b.numero_cuenta', '<>', '')->where('b.tipo_cuenta_id', '>', 0);
        })->where('m.company_id', $company)->whereBetween('m.date_created', [$f['desde'], $f['hasta']]);
        if ($f['alcance'] === 'bancarios') $q->whereNotNull('b.id');
        if ($f['alcance'] === 'sin_banco') $q->whereNull('b.id');
        if ($f['estado'] !== 'todos') $q->where('m.status', $f['estado'] === 'activos' ? 1 : 0);
        foreach (['banco' => 'b.id', 'forma' => 'm.forma_pago_id', 'tipo' => 'm.type_transaction_id'] as $key => $column) {
            if (!empty($f[$key])) $q->where($column, $f[$key]);
        }
        if (trim($f['buscar'] ?? '') !== '') {
            $term = '%' . trim($f['buscar']) . '%';
            $q->where(function ($query) use ($term) {
                foreach (['m.code', 'm.comprobante', 'm.numero_deposito', 'm.customer_name', 'm.customer_ruc', 'm.customer_code'] as $column) {
                    $query->orWhere($column, 'like', $term);
                }
            });
        }
        return $q;
    }

    public function rows(array $filters)
    {
        return $this->query($filters)->select('m.*', 'b.id as identified_bank_id', 'b.nombre as banco_nombre', 'b.numero_cuenta as banco_cuenta')
            ->orderBy('m.date_created')->orderBy('m.hour_created')->orderBy('m.id');
    }

    public function totals(array $filters)
    {
        return $this->query($filters)->selectRaw("COUNT(*) as cantidad,
            COALESCE(SUM(CASE WHEN m.status = 1 AND b.id IS NOT NULL AND m.type_transaction_action = 'S' THEN m.valor_movimiento ELSE 0 END), 0) as entradas,
            COALESCE(SUM(CASE WHEN m.status = 1 AND b.id IS NOT NULL AND m.type_transaction_action = 'R' THEN m.valor_movimiento ELSE 0 END), 0) as salidas,
            COALESCE(SUM(CASE WHEN b.id IS NULL OR m.type_transaction_action IS NULL OR m.type_transaction_action NOT IN ('S', 'R') THEN 1 ELSE 0 END), 0) as revisar")->first();
    }

    public function catalogs()
    {
        $company = $this->companyId();
        return [
            'bancos' => DB::table('bancos')->where('company_id', $company)->where('tipo_cuenta_id', '>', 0)->whereNotNull('numero_cuenta')->where('numero_cuenta', '<>', '')->orderBy('nombre')->get(),
            'formas' => DB::table('formas_pago')->where('company_id', $company)->orderBy('nombre')->get(),
            'tipos' => DB::table('type_transactions')->where('company_id', $company)->orderBy('name')->get(),
        ];
    }

    public function filterLabels(array $filters)
    {
        $catalogs = $this->catalogs();
        $bank = $catalogs['bancos']->firstWhere('id', $filters['banco']);
        $form = $catalogs['formas']->firstWhere('id', $filters['forma']);
        $type = $catalogs['tipos']->firstWhere('id', $filters['tipo']);
        return [
            'banco' => $bank ? $bank->nombre . ' · ' . $bank->numero_cuenta : 'Todos',
            'forma' => $form ? $form->nombre : 'Todas',
            'tipo' => $type ? $type->name : 'Todos',
            'alcance' => ['bancarios' => 'Con banco/cuenta identificada', 'sin_banco' => 'Sin banco/cuenta identificada', 'todos' => 'Todos los movimientos'][$filters['alcance']],
        ];
    }
}
