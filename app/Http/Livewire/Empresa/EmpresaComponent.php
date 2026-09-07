<?php

namespace App\Http\Livewire\Empresa;

use App\Models\Company;
use Livewire\Component;

class EmpresaComponent extends Component
{

    public $id_seleccionado = 0;
    public $company_name = '';
    public $comercial_name = '';
    public $ruc = '';
    public $legal_representative = '';
    public $ciudad = '';
    public $pais = '';
    public $obligar_garante = '';
    public $porcentaje_retener_credito = '';
    public $address = '';
    public $phone = '';
    public $email = '';
    public $company_id = '';
    public $company_type = '';
    public $desgravament = '';
    public $mora = '';
    public $photo = '';
    public $request = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['company_name','comercial_name','ruc','legal_representative','ciudad','pais','porcentaje_retener_credito','address','phone','email','company_type','desgravament','mora']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeCompany()
    {
        $this->validate([
            "company_name" => "required",
            "comercial_name" => "required",
            "ruc" => "required",
            "legal_representative" => "required",
            "ciudad" => "required",
            "pais" => "required",
            "porcentaje_retener_credito" => "required",
            "address" => "required",
            "phone" => "required",
            "email" => "required",
            "company_type" => "required",
            "desgravament" => "required",
            "mora" => "required",
        ]);

        if ($this->id_seleccionado > 0) {
            $company = Company::find($this->id_seleccionado);
        } else {
            $company = new Company();
        }
        $company->company_name = $this->company_name;
        $company->comercial_name = $this->comercial_name;
        $company->ruc = $this->ruc;
        $company->legal_representative = $this->legal_representative;
        $company->ciudad = $this->ciudad;
        $company->pais = $this->pais;
        $company->porcentaje_retener_credit = $this->porcentaje_retener_credit;
        $company->address = $this->address;
        $company->phone = $this->phone;
        $company->email = $this->email;
        $company->company_type = $this->company_type;
        $company->desgravamen = $this->desgravamen;
        $company->mora = $this->mora;

        if ($this->photo !== null) {
            $path_archivo_destino = 'public/uploads/companies';
            if (!Company::exists($path_archivo_destino)) {
                Company::makeDirectory($path_archivo_destino, 0777, true);
            }
            $nombre = time() . '.' . $this->photo->getClientOriginalExtension();
            $this->photo->storeAs('uploads/companies', $nombre, 'public');
            $company->photo = $nombre;
        }
        $company->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrarCompany($id)
    {
        Company::find($id)->delete();
    }

    public function editarCompany($id)
    {
        $this->limpiarFormulario();
        $this->id_seleccionado = $id;
        $category = Company::find($id);
        $this->company_name = $category->company_name;
        $this->comercial_name = $category->comercial_name;
        $this->ruc = $category->ruc;
        $this->legal_representative = $category->legal_representative;
        $this->ciudad = $category->ciudad;
        $this->pais = $category->pais;
        $this->porcentaje_retener_credito = $category->porcentaje_retener_credit;
        $this->address = $category->address;
        $this->phone = $category->phone;
        $this->email = $category->email;
        $this->company_type = $category->company_type;
        $this->mora = $category->mora;

        // if ($request->file('photo') !== null) {
        //     $imagen = $request->file('photo');
        //     if ($category->photo !== null) {
        //         $nombre = $category->photo;
        //         $borrar = public_path() . '/uploads/categories/' . $category->photo;
        //         unlink($borrar);
        //     } else {
        //         $nombre = time() . '.' . $imagen->getClientOriginalExtension();
        //     }
        //     $destino = public_path('uploads/categories');
        //     $request->photo->move($destino, $nombre);
        //     $category->photo = $nombre;
        //     $category->save();
        // }
        $this->dispatchBrowserEvent('openModal');
    }

    public function render()
    {
        $company = Company::paginate(10);
        return view('livewire.empresa.empresa-component', compact('company'));
    }
}
