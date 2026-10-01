<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CompanyLogoStorage
{
    public function store($upload)
    {
        $name = Str::uuid() . '.' . $upload->extension();
        $archive = $this->archivePath($name);
        $this->copyVerified($upload->getRealPath(), $archive);
        $this->copyVerified($archive, public_path('uploads/companies/' . $name));
        return $name;
    }

    public function archivePath($name)
    {
        return rtrim(config('company_logos.root'), '/\\') . '/' . $name;
    }

    public function resolve($name)
    {
        if (!$name || basename($name) !== $name || strpos($name, '\\') !== false || $name === '.' || $name === '..') return null;
        $public = public_path('uploads/companies/' . $name);
        $archive = $this->archivePath($name);
        $legacy = rtrim(config('company_logos.legacy_root'), '/\\') . '/' . $name;
        $source = $this->usable($archive) ? $archive : ($this->usable($public) ? $public : ($this->usable($legacy) ? $legacy : null));
        if (!$source) return null;
        try {
            if (!$this->usable($archive)) $this->copyVerified($source, $archive);
            if (!$this->usable($public)) $this->copyVerified($archive, $public);
        } catch (\Throwable $error) {
            // A failed backup must not hide the remaining readable original.
            report($error);
        }
        return $this->usable($public) ? $public : $source;
    }

    private function usable($path)
    {
        clearstatcache(true, $path);
        return is_file($path) && is_readable($path) && filesize($path) > 0;
    }

    private function copyVerified($source, $destination)
    {
        File::ensureDirectoryExists(dirname($destination));
        $temporary = $destination . '.' . Str::uuid() . '.tmp';
        try {
            if (!File::copy($source, $temporary) || !$this->usable($temporary)
                || hash_file('sha256', $source) !== hash_file('sha256', $temporary)
                || !rename($temporary, $destination)) {
                throw new \RuntimeException('No se pudo conservar el logotipo. Los cambios no se han guardado.');
            }
        } finally {
            if (is_file($temporary)) File::delete($temporary);
        }
    }
}
