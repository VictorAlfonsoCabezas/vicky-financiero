<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCustomer extends Model
{
    protected $table = 'tipo_customer';
    protected $fillable = [
        'id',
        'company_id',
        'nombre',
        'socio',
        'particular',
        'status'
        
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

}
