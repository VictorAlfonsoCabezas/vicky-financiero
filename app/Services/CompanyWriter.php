<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CompanyWriter
{
    public function save(Company $company, array $data, $upload = null)
    {
        $storage = app(CompanyLogoStorage::class);
        $storage->resolve($company->getRawOriginal('photo'));
        unset($data['photo']);
        $photo = $upload ? $storage->store($upload) : null;
        DB::transaction(function () use ($company, $data, $photo) {
            foreach ($data as $field => $value) $company->$field = $value;
            if ($photo) $company->photo = $photo;
            if (!$company->save()) throw new \RuntimeException('No se pudieron guardar los cambios de la empresa.');
        });
        // Never delete logos automatically, even on rollback.
    }
}
