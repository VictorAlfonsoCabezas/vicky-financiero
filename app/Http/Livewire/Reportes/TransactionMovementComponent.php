<?php

namespace App\Http\Livewire\Reportes;

use App\Exports\TransactionMovementExport;
use App\Services\TransactionMovementReport;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class TransactionMovementComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $filters = [];
    public $applied = [];

    public function mount()
    {
        $this->filters = $this->applied = app(TransactionMovementReport::class)->filters([]);
    }

    public function updatedFilters()
    {
        $this->applyFilters();
    }

    public function applyFilters()
    {
        try {
            $this->applied = app(TransactionMovementReport::class)->filters($this->filters);
        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $key => $messages) $errors['filters.' . $key] = $messages;
            throw ValidationException::withMessages($errors);
        }
        $this->resetValidation();
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->filters = app(TransactionMovementReport::class)->filters([]);
        $this->applyFilters();
    }

    public function exportExcel()
    {
        $this->applyFilters();
        return Excel::download(new TransactionMovementExport($this->applied), 'movimientos-por-transaccion.xlsx');
    }

    public function exportPdf()
    {
        $this->applyFilters();
        $report = app(TransactionMovementReport::class);
        $pdf = app('dompdf.wrapper')->loadView('reportes.movimientos-transacciones-export', [
            'filters' => $this->applied,
            'summary' => $report->summary($this->applied),
            'movimientos' => $report->rows($this->applied)->get(),
        ])->setPaper('a4', 'landscape');
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'movimientos-por-transaccion.pdf');
    }

    public function render()
    {
        $report = app(TransactionMovementReport::class);
        return view('livewire.reportes.transaction-movement-component', [
            'summary' => $report->summary($this->applied),
            'types' => $report->types(),
            'movimientos' => $report->rows($this->applied)->paginate(50),
        ]);
    }
}
