<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveCompanyRequest;
use App\Models\Company;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        return view('company.index');
    }

    public function create()
    {
        return $this->form(new Company());
    }

    public function edit(Company $company)
    {
        return $this->form($company);
    }

    private function form(Company $company)
    {
        return view($company->exists ? 'company.edit' : 'company.create', compact('company'));
    }

    public function store(SaveCompanyRequest $request)
    {
        $company = new Company();
        $company->status = true;
        $this->saveCompany($request, $company);
        return redirect()->route('company.index')->with('mensaje', 'Empresa creada correctamente.');
    }

    public function update(SaveCompanyRequest $request, Company $company)
    {
        $this->saveCompany($request, $company);
        return redirect()->route('company.edit', $company)->with('mensaje', 'Cambios guardados correctamente.');
    }

    private function saveCompany(SaveCompanyRequest $request, Company $company)
    {
        app(\App\Services\CompanyWriter::class)->save($company, $request->companyData(), $request->file('photo'));
    }

    public function status(Request $request, Company $company)
    {
        $request->validate(['status' => 'required|boolean']);
        $this->setStatus($company, $request->boolean('status'));
        return redirect()->route('company.index', ['estado' => 'todas'])->with('mensaje', $company->status ? 'Empresa reactivada.' : 'Empresa desactivada. Sus datos se conservan.');
    }

    private function setStatus(Company $company, $active)
    {
        if (!$active && (int) Auth::user()->company_id === (int) $company->id) {
            throw \Illuminate\Validation\ValidationException::withMessages(['status' => 'No puedes desactivar la empresa con la que estás trabajando. Cambia de empresa primero.']);
        }
        $company->status = $active;
        $company->save();
    }

    public function destroy($id)
    {
        $this->setStatus(Company::findOrFail($id), false);
        return redirect()->route('company.index')->with('mensaje', 'Empresa desactivada. Sus datos se conservan.');
    }

    public function changeCompany($id)
    {
        $user = User::find(Auth::user()->id);
        $allowed = array_filter(explode(',', (string) $user->company_varias));
        $allowed[] = (string) $user->company_id;
        abort_unless(in_array((string) $id, array_map('trim', $allowed), true), 403);
        Company::where('status', true)->findOrFail($id);
        $user->company_id = $id;
        $user->save();
        return Response::json(true);
    }

    public function conexionCompanies()
    {
        return Response::json(Company::select('id', 'conexion')->where('status', true)->get());
    }

    public function desactivarCompany($id)
    {
        $company = Company::findOrFail($id);
        $this->setStatus($company, !$company->status);
        return Response::json($company);
    }
}
