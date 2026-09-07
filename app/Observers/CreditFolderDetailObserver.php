<?php

namespace App\Observers;

use App\Models\CreditFolderDetail;
use App\Services\CreditFolderAuditService;

class CreditFolderDetailObserver
{
    private $auditService;

    public function __construct(CreditFolderAuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function created(CreditFolderDetail $detail)
    {
        $this->auditService->recordCreated($detail);
    }

    public function updated(CreditFolderDetail $detail)
    {
        $this->auditService->recordUpdated($detail);
    }

    public function deleted(CreditFolderDetail $detail)
    {
        $this->auditService->recordDeleted($detail);
    }
}
