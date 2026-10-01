<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;

class CompanyLogoController extends Controller
{
    public function show(Company $company)
    {
        $path = $company->logo_path;
        abort_unless($path, 404);
        return response()->file($path, ['Cache-Control' => 'no-cache', 'X-Content-Type-Options' => 'nosniff']);
    }
}
