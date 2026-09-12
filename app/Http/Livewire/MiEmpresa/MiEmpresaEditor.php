<?php

namespace App\Http\Livewire\MiEmpresa;

use App\Models\{Company, Category};
use App\Support\MiEmpresaForm;
use App\Http\Requests\SaveMiEmpresaRequest;
use App\Services\MiEmpresaWriter;
use Illuminate\Support\Facades\{Crypt, Validator};
use Livewire\Component;
use Livewire\WithFileUploads;

class MiEmpresaEditor extends Component
{
    use WithFileUploads, MiEmpresaAccess;

    public $record = '';
    public $data = [];
    public $photo;
    public $message = '';

    public function mount($companyId = null)
    {
        $this->authorizeMiEmpresaAccess();
        abort_unless(auth()->user()->company_id, 403);
        $company = Company::findOrFail(auth()->user()->company_id);
        $this->record = Crypt::encryptString((string) ($company->id ?: 'new'));
        foreach (MiEmpresaForm::fields() as $name => $field) {
            $value = $company->getAttribute($field[5] ?? $name) ?? ($field[3] ?? '');
            if ($field[1] === 'switch') $value = (bool) $value;
            if ($field[1] === 'time' && $value) $value = substr($value, 0, 5);
            $this->data[$name] = $value;
        }
    }

    protected function company()
    {
        try { $id = Crypt::decryptString($this->record); }
        catch (\Throwable $error) { abort(403); }
        abort_unless($id !== 'new' && (int) $id === (int) auth()->user()->company_id, 403);
        return Company::findOrFail(auth()->user()->company_id);
    }

    public function updatedPhoto()
    {
        $this->validate(['photo' => (new SaveMiEmpresaRequest())->rules()['photo']]);
    }

    public function save()
    {
        $this->authorizeMiEmpresaAccess();
        $company = $this->company();
        $input = [];
        foreach (MiEmpresaForm::fields() as $name => $field) {
            $value = $this->data[$name] ?? null;
            if (is_string($value)) $value = trim($value) === '' ? null : trim($value);
            $input[$name] = $value;
        }
        $request = new SaveMiEmpresaRequest();
        $request->replace($input);
        $validator = Validator::make($input + ['photo' => $this->photo], $request->rules(), $request->messages(), $request->attributes());
        $validator->validate();
        $request->setValidator($validator);
        app(MiEmpresaWriter::class)->save($company, $request->companyData(), $this->photo);
        $this->record = Crypt::encryptString((string) $company->id);
        $this->photo = null;
        $this->message = 'Cambios guardados correctamente.';
        $this->resetErrorBag();
        $this->dispatchBrowserEvent('mi-empresa-saved');
    }

    public function render()
    {
        $this->authorizeMiEmpresaAccess();
        $company = $this->company();
        $category = Category::where('type', 2)->where('status', true)->orderBy('title')->get();
        $sections = MiEmpresaForm::sections();
        return view($this->editorView(), compact('company', 'category', 'sections'));
    }

    protected function editorView()
    {
        return 'livewire.mi-empresa.editor';
    }
}
