<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\CreditFolderDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;



class ReporteNivelesExport implements FromView
{
    protected $data;
    public $totalValores;

    public function __construct(array $data, $totalValores)
    {
        $this->data = $data;
        $this->totalValores = $totalValores;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function view(): View
    {
        $totalValoresSum = $this->totalValores;
        $letras = $this->data;
        $company = Company::find(Auth::user()->company_id);
        return view('reportes.export_niveles')
            ->with('totalValoresSum', $totalValoresSum)
            ->with('letras', $letras)
            ->with('company', $company);
    }
}
