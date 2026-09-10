<?php

namespace App\Http\Livewire\Company;

use App\Models\{Company, Category};
use App\Support\CompanyForm;
use App\Http\Requests\SaveCompanyRequest;
use App\Services\CompanyWriter;
use Illuminate\Support\Facades\{Crypt, Validator};
use Livewire\Component;
use Livewire\WithFileUploads;

class CompanyEditor extends Component
{
    use WithFileUploads, CompanyAccess;

    public $record = '';
    public $data = [];
    public $photo;
    public $message = '';

    public function mount($companyId = null)
    {
        $this->authorizeCompanyAccess();
        $company = $companyId ? Company::findOrFail($companyId) : new Company();
        $this->record = Crypt::encryptString((string) ($company->id ?: 'new'));
        foreach (CompanyForm::fields() as $name => $field) {
            $value = $company->getAttribute($field[5] ?? $name) ?? ($field[3] ?? '');
            if ($field[1] === 'switch') $value = (bool) $value;
            if ($field[1] === 'time' && $value) $value = substr($value, 0, 5);
            $this->data[$name] = $value;
        }
    }

    private function company()
    {
        try { $id = Crypt::decryptString($this->record); }
        catch (\Throwable $error) { abort(403); }
        return $id === 'new' ? new Company() : Company::findOrFail($id);
    }

    public function updatedPhoto()
    {
        $this->validate(['photo' => (new SaveCompanyRequest())->rules()['photo']]);
    }

    public function save()
    {
        $this->authorizeCompanyAccess();
        $company = $this->company();
        $input = [];
        foreach (CompanyForm::fields() as $name => $field) {
            $value = $this->data[$name] ?? null;
            if (is_string($value)) $value = trim($value) === '' ? null : trim($value);
            $input[$name] = $value;
        }
        $request = new SaveCompanyRequest();
        $request->replace($input);
        $validator = Validator::make($input + ['photo' => $this->photo], $request->rules(), $request->messages(), $request->attributes());
        $validator->validate();
        $request->setValidator($validator);
        if (!$company->exists) $company->status = true;
        app(CompanyWriter::class)->save($company, $request->companyData(), $this->photo);
        $this->record = Crypt::encryptString((string) $company->id);
        $this->photo = null;
        $this->message = 'Cambios guardados correctamente.';
        $this->resetErrorBag();
        $this->dispatchBrowserEvent('company-saved');
    }

    public function render()
    {
        $this->authorizeCompanyAccess();
        $company = $this->company();
        $category = Category::where('type', 2)->where('status', true)->orderBy('title')->get();
        $sections = CompanyForm::sections();
        return view('livewire.company.editor', compact('company', 'category', 'sections'));
    }
}
