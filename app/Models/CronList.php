<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CronList extends Model
{
    protected $table = 'cron_lists';
    protected $fillable = [
        'id',
        'company_id',
        'name',
        'detalle',
        'cron',
        'status', 
    ];
}
