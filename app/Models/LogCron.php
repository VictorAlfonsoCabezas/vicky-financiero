<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogCron extends Model
{
    protected $table = 'log_crons';
    protected $fillable = [
        'id',
        'company_id', 
        'cron_lists_id',
        'credit_header_id',
        'credit_detail_id',
        'detalle',
        'date_create',
        'hour_create',
    ];
}
