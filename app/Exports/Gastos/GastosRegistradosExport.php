<?php

namespace App\Exports\Gastos;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class GastosRegistradosExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithCustomValueBinder
{
    private $gastos;

    public function __construct(Collection $gastos)
    {
        $this->gastos = $gastos;
    }

    public function collection()
    {
        return $this->gastos;
    }

    public function headings(): array
    {
        return ['#', 'Categorías', 'Proveedor', '# Factura', 'Fecha emisión',
            'Subtotal', 'Descuento', 'Subtotal con descuento', 'Base imponible', 'IVA',
            'TOTAL', 'Monto pagado', 'Monto retenciones', 'Monto pendiente', 'Estado'];
    }

    public function map($gasto): array
    {
        return [
            (int) $gasto->id,
            $gasto->categoria_nombre,
            $gasto->nombreProveedor . ' - ' . $gasto->ruc,
            $gasto->establecimiento . '-' . $gasto->punto_emision . '-' . $gasto->numero,
            $gasto->fecha_emision,
            (float) $gasto->valor,
            (float) $gasto->descuento,
            (float) $gasto->subtotal_descuento,
            (float) $gasto->suma_subtotal,
            (float) $gasto->suma_iva,
            (float) $gasto->total,
            (float) $gasto->monto_pagado,
            (float) $gasto->monto_retencion,
            (float) $gasto->valor_pendiente,
            $gasto->estado,
        ];
    }

    public function columnFormats(): array
    {
        return array_fill_keys(range('F', 'N'), '#,##0.00');
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }
}
