<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\{DB, File};
use Illuminate\Support\Str;

class CompanyWriter
{
    public function save(Company $company, array $data, $upload = null)
    {
        $photo = null;
        if ($upload) {
            $directory = public_path('uploads/companies');
            File::ensureDirectoryExists($directory);
            $photo = Str::uuid() . '.' . $upload->extension();
            if (!File::copy($upload->getRealPath(), $directory . '/' . $photo)) {
                throw new \RuntimeException('No se pudo guardar el logotipo.');
            }
        }
        try {
            DB::transaction(function () use ($company, $data, $photo) {
                foreach ($data as $field => $value) $company->$field = $value;
                if ($photo) $company->photo = $photo;
                $company->save();
            });
        } catch (\Throwable $error) {
            if ($photo) File::delete(public_path('uploads/companies/' . $photo));
            throw $error;
        }
    }
}
