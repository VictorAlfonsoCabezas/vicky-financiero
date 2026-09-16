<?php

namespace App\Http\Livewire\Conciliacion;

use App\Exports\BankMovementExport;
use App\Services\BankMovementReport;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;

class ConciliacionComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $filters = [];
    public $applied = [];

    public function mount()
    {
        app(BankMovementReport::class)->companyId();
        $this->filters = $this->applied = BankMovementReport::defaults();
    }

    public function updatedFilters($value, $key)
    {
        $this->applyFilters();
    }

    public function applyFilters()
    {
        try {
            $this->applied = app(BankMovementReport::class)->validate($this->filters);
        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $key => $messages) $errors['filters.' . $key] = $messages;
            throw ValidationException::withMessages($errors);
        }
        $this->resetErrorBag();
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->filters = BankMovementReport::defaults();
        $this->applyFilters();
    }

    public function exportExcel()
    {
        $this->applyFilters();
        return Excel::download(new BankMovementExport($this->applied), 'movimientos-bancarios.xlsx');
    }

    public function exportPdf()
    {
        $this->applyFilters();
        $report = app(BankMovementReport::class);
        if ($report->query($this->applied)->count() > 2000) {
            $this->addError('export', 'Para PDF, reduzca el período a un máximo de 2.000 movimientos. Puede exportar el reporte completo a Excel.');
            return;
        }
        $pdf = app('dompdf.wrapper')->loadView('conciliacion.pdf', [
            'movimientos' => $report->rows($this->applied)->get(),
            'totales' => $report->totals($this->applied), 'filters' => $this->applied,
            'labels' => $report->filterLabels($this->applied),
        ])->setPaper('a4', 'landscape');
        return response()->streamDownload(function () use ($pdf) { echo $pdf->output(); }, 'movimientos-bancarios.pdf');
    }

    public function render()
    {
        $report = app(BankMovementReport::class);
        return view('livewire.conciliacion.conciliacion-component', array_merge($report->catalogs(), [
            'movimientos' => $report->rows($this->applied)->paginate(25),
            'totales' => $report->totals($this->applied),
        ]));
    }
}
