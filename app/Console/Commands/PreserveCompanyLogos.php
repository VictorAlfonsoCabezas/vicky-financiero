<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\CompanyLogoStorage;
use Illuminate\Console\Command;

class PreserveCompanyLogos extends Command
{
    protected $signature = 'company:preserve-logos';
    protected $description = 'Conserva y recupera los logotipos existentes sin borrar archivos ni modificar empresas';

    public function handle(CompanyLogoStorage $storage)
    {
        $failed = 0;
        Company::whereNotNull('photo')->where('photo', '<>', '')->chunkById(100, function ($companies) use ($storage, &$failed) {
            foreach ($companies as $company) {
                $path = $storage->resolve($company->photo);
                $archive = $storage->archivePath(basename($company->photo));
                if (!$path || !is_file($archive) || filesize($archive) === 0) {
                    $this->error('No se pudo respaldar el logo de la empresa ' . $company->id);
                    $failed++;
                }
            }
        });
        if ($failed) return 1;
        $this->info('Logotipos conservados. No se eliminaron archivos ni se modificaron registros.');
        return 0;
    }
}
