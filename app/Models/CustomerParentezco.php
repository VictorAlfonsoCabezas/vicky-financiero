<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerParentezco extends Model
{
    protected $table = 'customer_parentezco';
    protected $fillable = [
        'id',
        'company_id',
        'customer_id',
        'parentezco_id',
        'country_id',
        'tipo_documento_id',
        'nombres_apellidos',
        'documento',
        'celular'
    ];
}
