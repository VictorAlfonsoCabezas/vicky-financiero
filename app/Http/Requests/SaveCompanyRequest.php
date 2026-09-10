<?php

namespace App\Http\Requests;

use App\Support\CompanyForm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCompanyRequest extends FormRequest
{
    public function authorize() { return $this->user() !== null; }

    public function rules()
    {
        $rules = [];
        foreach (CompanyForm::fields() as $name => $field) $rules[$name] = $field[2];
        $rules['photo'] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048|dimensions:max_width=5000,max_height=5000';
        $rules['category_type'] = ['nullable', Rule::exists('category', 'id')->where('type', 2)->where('status', true)];
        if ($this->boolean('contabilidad')) $rules['fecha_inicio_contable'] = 'required|date_format:Y-m-d';
        if ($this->boolean('active_cron')) $rules['time_cron'] = 'required|date_format:H:i';
        return $rules;
    }

    public function attributes()
    {
        $labels = [];
        foreach (CompanyForm::fields() as $name => $field) $labels[$name] = mb_strtolower($field[0]);
        return $labels + ['photo' => 'logotipo'];
    }

    public function messages()
    {
        return ['required' => 'Completa el campo :attribute.', 'digits' => 'El :attribute debe contener :digits dígitos.',
            'email' => 'Introduce un correo electrónico válido.', 'photo.image' => 'Selecciona una imagen válida.',
            'photo.max' => 'El logotipo no debe superar los 2 MB.', 'fecha_inicio_contable.required' => 'Indica la fecha de inicio para activar la contabilidad.',
            'time_cron.required' => 'Indica la hora de ejecución de las tareas programadas.'];
    }

    public function companyData()
    {
        $validated = $this->validated();
        $data = [];
        foreach (CompanyForm::fields() as $name => $field) {
            if (!array_key_exists($name, $validated)) continue;
            $value = $validated[$name];
            if ($field[1] === 'switch') $value = $this->boolean($name);
            elseif ($value === null && isset($field[3])) $value = $field[3];
            $data[$field[5] ?? $name] = $value;
        }
        foreach (['company_name', 'comercial_name', 'legal_representative', 'address'] as $field) {
            if (isset($data[$field])) $data[$field] = mb_strtoupper(trim($data[$field]), 'UTF-8');
        }
        if ((string) ($data['company_type'] ?? '') === '1') { $data['category_type'] = null; $data['domicilio'] = 0; }
        if (isset($data['contabilidad']) && !$data['contabilidad']) $data['fecha_inicio_contable'] = null;
        if (isset($data['active_cron']) && !$data['active_cron']) $data['time_cron'] = null;
        if (isset($data['genera_gastos_cobranza']) && !$data['genera_gastos_cobranza']) $data['valor_notificado'] = 0;
        return $data;
    }
}
