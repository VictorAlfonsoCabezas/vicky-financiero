<?php

namespace App\Exports;

use App\Services\TransactionMovementReport;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class TransactionMovementExport extends DefaultValueBinder implements FromView, WithCustomValueBinder
{
    private $filters;

    public function __construct(array $filters) { $this->filters = $filters; }

    public function view(): \Illuminate\Contracts\View\View
    {
        $report = app(TransactionMovementReport::class);
        return view('reportes.movimientos-transacciones-export', [
            'filters' => $this->filters, 'summary' => $report->summary($this->filters),
            'movimientos' => $report->rows($this->filters)->get(),
        ]);
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
