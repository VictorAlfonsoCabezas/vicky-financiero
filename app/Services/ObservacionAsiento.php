<?php

namespace App\Services;

use App\Models\{AsientosHeader, CustomerMovimiento, Customer, Gastos, DescargoBovedasHeader, ConfigPlanHeader, TypeTransaction, RegistroFormasPago, OperacionesDescargoBovedas};
use App\User;
use Illuminate\Support\Facades\Auth;

class ObservacionAsiento
{
    public static function corregirTexto($texto)
    {
        return $texto === null ? null : str_replace(
            ['REESTRUCCTURACION', 'Reestruccturacion', 'reestruccturacion'],
            ['REESTRUCTURACION', 'Reestructuracion', 'reestructuracion'],
            $texto
        );
    }

    public static function generar(AsientosHeader $asiento)
    {
        $contexto = $asiento->contextoObservacion;
        $movimiento = self::origen(CustomerMovimiento::class, $asiento->customer_movimiento_id, $asiento);
        $gasto = self::origen(Gastos::class, $asiento->gasto_id, $asiento);
        $boveda = !$movimiento && !$gasto
            ? self::origen(DescargoBovedasHeader::class, $asiento->descargo_bovedas_header_id, $asiento) : null;
        if (!$movimiento && $boveda) {
            $movimiento = self::origen(CustomerMovimiento::class, $boveda->customer_movimiento_id, $asiento);
        }
        $config = self::origen(ConfigPlanHeader::class, $asiento->config_plan_header_id, $asiento);
        $tipo = self::origen(TypeTransaction::class, $movimiento ? $movimiento->type_transaction_id : optional($config)->type_transaction_id, $asiento);
        $codigo = optional($tipo)->name_corto;
        $modulo = $asiento->manual ? 'Asientos manuales' : 'Contabilidad general';
        if ($movimiento) {
            $modulo = optional($tipo)->name ?: $movimiento->type_transaction_name ?: 'Movimientos de clientes';
            if ($movimiento->credit_folder_header_id || in_array($codigo, ['ENC', 'PC', 'PCA', 'PCI'])) $modulo = 'Créditos';
            if ($movimiento->customer_tipo_ahorro_id) $modulo = 'Ahorros';
            if ($codigo === 'PPR' || $movimiento->gastos_id) $modulo = 'Gastos / pago de proveedores';
            if ($codigo === 'EUS') $modulo = 'Utilidades';
        }
        if ($gasto) $modulo = 'Gastos';
        if ($boveda) $modulo = 'Bóvedas';
        $clienteId = $contexto['customer_id'] ?? optional($movimiento)->customer_id;
        $cliente = self::origen(Customer::class, $clienteId, $asiento);
        $nombreCliente = $contexto['customer_name'] ?? (optional($movimiento)->customer_name ?: trim(optional($cliente)->nombres . ' ' . optional($cliente)->apellidos));
        $usuarioId = optional($movimiento)->user_created_id ?: $asiento->user_created ?: Auth::id();
        $usuario = self::origen(User::class, $usuarioId, $asiento);
        $nombreUsuario = trim(optional($usuario)->firstname . ' ' . optional($usuario)->lastname);
        $persona = $clienteId || $nombreCliente
            ? 'Cliente: ' . ($nombreCliente ?: 'ID ' . $clienteId) . ($clienteId ? ' (ID ' . $clienteId . ')' : '')
            : 'Usuario: ' . (optional($movimiento)->user_created_name ?: $nombreUsuario ?: ($usuarioId ? 'ID ' . $usuarioId : 'No registrado'));
        $operacion = optional($tipo)->name ?: optional($movimiento)->type_transaction_name
            ?: ($gasto ? 'Registro de gasto' : ($boveda ? 'Registro de operación de bóveda' : 'Registro de asiento contable'));
        if ($boveda) {
            $operacionBoveda = self::origen(OperacionesDescargoBovedas::class, $boveda->operaciones_descargo_bovedas_id, $asiento);
            $operacion = optional($operacionBoveda)->nombre ?: $operacion;
        }
        $detalle = $gasto ? $gasto->descripcion : ($boveda ? $boveda->observacion : optional($movimiento)->observation);
        if (!$detalle && !in_array($asiento->descripcion, ['ASIENTO AUTOMATICO', null, ''], true)) $detalle = $asiento->descripcion;
        $valor = $movimiento ? $movimiento->valor_movimiento : ($gasto ? $gasto->valor : optional($boveda)->valor);
        $accion = $contexto['operacion'] ?? ('Se contabilizó ' . $operacion . ($valor !== null ? ' por ' . number_format((float) $valor, 2, '.', ',') : '') . ($detalle ? '. Detalle: ' . $detalle : ''));
        $referencia = $movimiento
            ? ($movimiento->code ?: 'Sin código') . ' (ID ' . $movimiento->id . ')'
            : 'Sin movimiento asociado';
        // Los pagos de una cuota pueden contabilizar varios movimientos en un asiento.
        if ($movimiento && $movimiento->credit_folder_details_id && in_array($codigo, ['PC', 'PCA', 'PCI'])) {
            $consultaAgrupados = CustomerMovimiento::where('company_id', $asiento->company_id)
                ->where('credit_folder_details_id', $movimiento->credit_folder_details_id)
                ->where('type_transaction_id', $movimiento->type_transaction_id)
                ->where(function ($query) { $query->whereNull('status')->orWhereNotIn('status', [0, '0']); });
            $idsRegistro = RegistroFormasPago::where('company_id', $asiento->company_id)
                ->where('letra_id', $movimiento->credit_folder_details_id)->whereIn('solicitado', [1, 3])
                ->where(function ($query) { $query->whereNull('status')->orWhereNotIn('status', [2, 0, '0']); })
                ->pluck('customer_movimientos_id')->filter()->unique();
            if ($idsRegistro->isNotEmpty()) {
                $consultaAgrupados = CustomerMovimiento::where('company_id', $asiento->company_id)->whereIn('id', $idsRegistro);
            }
            $agrupados = $consultaAgrupados->orderBy('id')->get();
            if ($agrupados->count() > 1) {
                $referencia = $agrupados->map(function ($item) { return ($item->code ?: 'Sin código') . ' (ID ' . $item->id . ')'; })->implode(', ');
                $accion .= '. Total de movimientos de la cuota: ' . number_format((float) $agrupados->sum('valor_movimiento'), 2, '.', ',');
            }
        }
        if ($gasto) $accion .= '. Gasto ID ' . $gasto->id . ($gasto->numero ? ', comprobante ' . $gasto->numero : '');
        if ($boveda) $accion .= '. Bóveda: operación ID ' . $boveda->operaciones_descargo_bovedas_id . ', registro ID ' . $boveda->id;
        return ($contexto['modulo'] ?? $modulo) . ' - ' . $persona . ' - ' . $accion . ' - Movimiento: ' . ($contexto['movimiento'] ?? $referencia);
    }

    private static function origen($clase, $id, AsientosHeader $asiento)
    {
        return $id ? $clase::where('company_id', $asiento->company_id)->find($id) : null;
    }
}
