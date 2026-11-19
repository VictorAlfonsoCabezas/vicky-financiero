<?php

namespace App\Http\Livewire\CalificacionSocios;

use App\Models\Company;
use App\Models\Customer;
use App\Services\CalificacionCrediticia;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class CalificacionSociosComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $buscar = '';
    public $socioId;

    public function updatedBuscar(): void
    {
        $this->resetPage();
    }

    public function seleccionarSocio(int $socioId): void
    {
        $this->socioId = $this->socios()->findOrFail($socioId)->id;
        $this->resetPage('cuotasPage');
    }

    private function socios()
    {
        return Customer::where('company_id', app(CalificacionCrediticia::class)->empresaId());
    }

    public function imprimirPdf()
    {
        $socio = $this->socios()->findOrFail($this->socioId);
        $resultado = app(CalificacionCrediticia::class)->calcular($socio->id);
        $empresa = Company::find($socio->company_id, ['comercial_name', 'company_name']);
        $pdf = app('dompdf.wrapper')->loadView('calificacion-socios.pdf', [
            'socio' => $socio,
            'resultado' => $resultado,
            'empresa' => optional($empresa)->comercial_name ?: optional($empresa)->company_name,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'calificacion-socio-' . $socio->id . '-' . $resultado['corte'] . '.pdf');
    }

    public function render()
    {
        $consulta = $this->socios();
        $buscar = trim(mb_substr((string) $this->buscar, 0, 120));
        if ($buscar !== '') {
            foreach (preg_split('/\s+/', $buscar) as $parte) {
                $consulta->where(function ($query) use ($parte) {
                    $patron = '%' . $parte . '%';
                    $query->where('nombres', 'like', $patron)->orWhere('apellidos', 'like', $patron)
                        ->orWhere('numero_documento', 'like', $patron)->orWhere('code', 'like', $patron);
                });
            }
        }

        $columnas = ['id', 'code', 'nombres', 'apellidos', 'numero_documento'];
        $socios = $consulta->orderBy('apellidos')->orderBy('nombres')->orderBy('id')->paginate(10, $columnas);
        $socio = $this->socioId ? $this->socios()->findOrFail($this->socioId, $columnas) : null;
        $resultado = $socio ? app(CalificacionCrediticia::class)->calcular($socio->id) : null;
        $cuotas = null;
        if ($resultado) {
            $pagina = LengthAwarePaginator::resolveCurrentPage('cuotasPage');
            $cuotas = new LengthAwarePaginator(
                $resultado['cuotas']->forPage($pagina, 15)->values(),
                $resultado['cuotas']->count(),
                15,
                $pagina,
                ['path' => LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'cuotasPage']
            );
        }

        return view('livewire.calificacion-socios.calificacion-socios-component', compact('socios', 'socio', 'resultado', 'cuotas'));
    }
}
