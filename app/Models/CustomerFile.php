<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerFile extends Model
{
    protected $table = 'customer_file';
    protected $fillable = [
        'id',
        'company_id',
        'customer_id',
        'customer_tipo_ahorros_id',
        'credit_header_id',
        'descripcion',
        'archivo',
        'path',
        'formato',
    ];
    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }
}
