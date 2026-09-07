<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentosParametrizables extends Model
{
    protected $table = 'documentos_parametrizables';
    protected $fillable = [
        'company_id',
        'formato',
        'content',
        'date_created',
        'hour_created',
        'user_created_id',
        'user_created_name',
        'status',
        
    ];

}
