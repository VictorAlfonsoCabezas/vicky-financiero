<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerminosUsoClientes extends Model
{
    protected $table = 'terminos_uso_clientes';
    protected $fillable = [
        'id',
        'company_id',
        'user_id',
        'customer_id',
        'terminos_usos_id',
        'periodo',
        'date_create',
        'hour_create',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
