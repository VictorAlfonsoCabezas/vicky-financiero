<?php

namespace App\Exports;

use App\Services\BankMovementReport;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class BankMovementExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithCustomValueBinder
{
    private $filters;

    public function __construct(array $filters) { $this->filters = $filters; }

    public function query() { return app(BankMovementReport::class)->rows($this->filters); }

    public function headings(): array
    {
        return ['Fecha', 'Hora', 'Banco', 'Cuenta bancaria', 'Código', 'Comprobante', 'Referencia', 'Cliente',
            'Identificación', 'Concepto', 'Forma de pago', 'Acción registrada', 'Valor', 'Entrada computable', 'Salida computable', 'Estado', 'Observación'];
    }

    public function map($m): array
    {
        $valid = $m->status && $m->identified_bank_id !== null;
        return [$m->date_created, $m->hour_created, $m->banco_nombre ?? 'Sin banco/cuenta identificada', $m->banco_cuenta,
            $m->code, $m->comprobante, $m->numero_deposito, $m->customer_name, $m->customer_ruc,
            $m->type_transaction_name, $m->forma_pago_name, $m->type_transaction_action, (float) $m->valor_movimiento,
            $valid && $m->type_transaction_action === 'S' ? (float) $m->valor_movimiento : 0,
            $valid && $m->type_transaction_action === 'R' ? (float) $m->valor_movimiento : 0,
            $m->status ? 'Activo' : 'Anulado', $m->observation];
    }

    public function bindValue(Cell $cell, $value)
    {
        // Preserve references and prevent formulas in user-entered text.
        if (is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }
}
