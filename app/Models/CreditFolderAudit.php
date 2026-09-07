<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditFolderAudit extends Model
{
    protected $table = 'credit_folder_audits';

    protected $fillable = [
        'credit_folder_header_id',
        'credit_folder_detail_id',
        'company_id',
        'code_folder_header',
        'source',
        'event',
        'action',
        'description',
        'old_values',
        'new_values',
        'user_id',
        'user_name',
        'ip_address',
        'user_agent',
        'url',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function header()
    {
        return $this->belongsTo(CreditFolderHeader::class, 'credit_folder_header_id');
    }

    public function detail()
    {
        return $this->belongsTo(CreditFolderDetail::class, 'credit_folder_detail_id');
    }
}
