<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaHeader extends Model
{
    protected $table = 'proforma_header';
    protected $fillable = [
        'id',
        'country_id',
        'customer_id',
        'codigo',
        'fecha_creacion',
        'fecha_caducidad',
        'descripcion',
        'subtotal',
        'iva',
        'iva_0',
        'total',
        'status',        
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
