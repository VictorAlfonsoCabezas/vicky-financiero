<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiConection extends Model
{
    protected $table = 'api_conections';
    protected $fillable = [
        'request_data',
        
    ];

}
