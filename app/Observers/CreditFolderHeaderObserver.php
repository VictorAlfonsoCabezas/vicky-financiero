<?php

namespace App\Observers;

use App\Models\CreditFolderHeader;
use App\Services\CreditFolderAuditService;

class CreditFolderHeaderObserver
{
    private $auditService;

    public function __construct(CreditFolderAuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function created(CreditFolderHeader $header)
    {
        $this->auditService->recordCreated($header);
    }

    public function updated(CreditFolderHeader $header)
    {
        $this->auditService->recordUpdated($header);
    }

    public function deleted(CreditFolderHeader $header)
    {
        $this->auditService->recordDeleted($header);
    }
}
