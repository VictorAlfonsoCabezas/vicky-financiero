<?php

return [
    // Point this at a persistent volume shared by all production instances.
    'root' => env('COMPANY_LOGOS_PATH', storage_path('app/company-logos')),
    'legacy_root' => storage_path('app/public/uploads/companies'),
];
