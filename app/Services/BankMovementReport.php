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
            'tipo' => ($filters['tipo'] ?? '') === 'carga_inicial'
                ? ['in:carga_inicial']
                : ['nullable', 'integer', Rule::exists('type_transactions', 'id')->where('company_id', $company)],
            'estado' => 'required|in:activos,anulados,todos',
            'alcance' => 'required|in:bancarios,sin_banco,todos',
            'buscar' => 'nullable|string|max:120',
        ], ['hasta.after_or_equal' => 'La fecha final debe ser igual o posterior a la inicial.'])->validate();
    }

    public function query(array $filters)
    {
        $f = $this->validate($filters);
        $company = Auth::user()->company_id;
        $q = DB::query()->fromSub($this->movementSources($company), 'm')->leftJoin('bancos as b', function ($join) use ($company) {
            $join->on('b.id', '=', 'm.banco_id')->where('b.company_id', '=', $company)
                ->whereNotNull('b.numero_cuenta')->where('b.numero_cuenta', '<>', '')->where('b.tipo_cuenta_id', '>', 0);
        })->where('m.company_id', $company)->whereBetween('m.date_created', [$f['desde'], $f['hasta']]);
        if ($f['alcance'] === 'bancarios') $q->whereNotNull('b.id');
        if ($f['alcance'] === 'sin_banco') $q->whereNull('b.id');
        if ($f['estado'] !== 'todos') $q->where('m.status', $f['estado'] === 'activos' ? 1 : 0);
        if (($f['tipo'] ?? '') === 'carga_inicial') $q->where('m.origen', 'boveda');
        elseif (!empty($f['tipo'])) $q->where('m.type_transaction_id', $f['tipo']);
        foreach (['banco' => 'b.id', 'forma' => 'm.forma_pago_id'] as $key => $column) {
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

    private function movementSources($company)
    {
        $columns = ['id', 'company_id', 'banco_id', 'forma_pago_id', 'type_transaction_id',
            'status', 'valor_movimiento', 'date_created', 'hour_created', 'code', 'comprobante',
            'numero_deposito', 'customer_name', 'customer_ruc', 'customer_code',
            'type_transaction_action', 'type_transaction_name', 'forma_pago_name',
            'observation', 'date_cancel', 'razon_cancel'];
        $customers = DB::table('customer_movimientos')->where('company_id', $company)
            ->select($columns)->selectRaw("'cliente' as origen");

        // Read vault openings directly: do not create customer movements or accounting entries.
        $openings = DB::table('descargo_bovedas_header as d')
            ->join('operaciones_descargo_bovedas as o', function ($join) use ($company) {
                $join->on('o.id', '=', 'd.operaciones_descargo_bovedas_id')->where('o.company_id', $company);
            })
            ->where('d.company_id', $company)->where('d.estado', 'FINALIZADO')
            ->whereIn('o.nombre_corto', ['CAREM', 'CARCLI'])
            // A linked customer movement is already represented by the first branch.
            ->whereNotExists(function ($query) use ($company) {
                $query->selectRaw('1')->from('customer_movimientos as linked')
                    ->whereColumn('linked.id', 'd.customer_movimiento_id')->where('linked.company_id', $company);
            })
            ->selectRaw("d.id, d.company_id, d.bancos_id as banco_id, NULL as forma_pago_id,
                NULL as type_transaction_id, d.status, d.valor as valor_movimiento,
                DATE(d.fecha_creacion) as date_created, TIME(d.fecha_creacion) as hour_created,
                'CARGA-BOVEDA' as code, NULL as comprobante, d.id as numero_deposito,
                NULL as customer_name, NULL as customer_ruc, NULL as customer_code,
                'S' as type_transaction_action, 'Carga inicial de bóveda' as type_transaction_name,
                NULL as forma_pago_name, d.observacion as observation,
                NULL as date_cancel, NULL as razon_cancel, 'boveda' as origen");

        return $customers->unionAll($openings);
    }

    public function rows(array $filters)
    {
        return $this->query($filters)->select('m.*', 'b.id as identified_bank_id', 'b.nombre as banco_nombre', 'b.numero_cuenta as banco_cuenta')
            ->orderBy('m.date_created')->orderBy('m.hour_created')->orderBy('m.origen')->orderBy('m.id');
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
            'tipos' => DB::table('type_transactions')->where('company_id', $company)->orderBy('name')->get()
                ->push((object) ['id' => 'carga_inicial', 'name' => 'Carga inicial de bóveda']),
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
