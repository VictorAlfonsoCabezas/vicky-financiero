<?php

namespace App\Http\Livewire\Company;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyIndex extends Component
{
    use WithPagination, CompanyAccess;

    protected $paginationTheme = 'bootstrap';
    public $q = '';
    public $estado = 'activas';
    public $pendingCompany = null;
    public $pendingName = '';
    public $pendingActive = false;
    public $message = '';
    protected $queryString = ['q' => ['except' => ''], 'estado' => ['except' => 'activas']];

    public function mount()
    {
        $this->authorizeCompanyAccess();
    }

    public function updatingQ() { $this->resetPage(); }
    public function updatingEstado() { $this->resetPage(); }
    public function clearFilters() { $this->q = ''; $this->estado = 'activas'; $this->resetPage(); }
    public function showAll() { $this->q = ''; $this->estado = 'todas'; $this->resetPage(); }

    public function confirmStatus($id)
    {
        $this->authorizeCompanyAccess();
        $company = Company::findOrFail($id);
        $this->resetErrorBag();
        $this->pendingCompany = $company->id;
        $this->pendingName = $company->comercial_name ?: $company->company_name;
        $this->pendingActive = !$company->status;
        $this->dispatchBrowserEvent('company-status-open');
    }

    public function cancelStatus() { $this->pendingCompany = null; $this->resetErrorBag(); $this->dispatchBrowserEvent('company-status-close'); }

    public function saveStatus()
    {
        $this->authorizeCompanyAccess();
        $this->validate(['pendingCompany' => 'required|integer', 'pendingActive' => 'boolean']);
        $company = Company::findOrFail($this->pendingCompany);
        if (!$this->pendingActive && (int) auth()->user()->company_id === (int) $company->id) {
            $this->addError('status', 'No puedes desactivar la empresa con la que estás trabajando.');
            return;
        }
        $company->status = (bool) $this->pendingActive;
        $company->save();
        $this->message = $company->status ? 'Empresa reactivada.' : 'Empresa desactivada. Sus datos se conservan.';
        $this->pendingCompany = null;
        $this->dispatchBrowserEvent('company-status-close');
        $this->resetPage();
    }

    public function render()
    {
        $this->authorizeCompanyAccess();
        \Illuminate\Support\Facades\Validator::make(['q' => $this->q, 'estado' => $this->estado],
            ['q' => 'nullable|string|max:120', 'estado' => 'required|in:activas,inactivas,todas'])->validate();
        $query = Company::query();
        if ($this->estado !== 'todas') $query->where('status', $this->estado === 'activas');
        if (trim($this->q) !== '') {
            $term = trim($this->q);
            $query->where(function ($q) use ($term) {
                foreach (['company_name', 'comercial_name', 'ruc', 'ciudad'] as $field) $q->orWhere($field, 'like', '%' . $term . '%');
            });
        }
        $companies = $query->orderBy('comercial_name')->orderBy('id')->paginate(12);
        $stats = ['total' => Company::count(), 'active' => Company::where('status', true)->count(),
            'accounting' => Company::where('status', true)->where('contabilidad', true)->count()];
        return view('livewire.company.index', compact('companies', 'stats'));
    }
}
