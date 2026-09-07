<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoRetencion extends Model
{
    protected $table = 'tipo_retencion';
    protected $fillable = [
        'name',
        'status'
    ];
}
