<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerTransferencias extends Model
{
    protected $table = 'customer_transferencias';
    protected $fillable = [
        'id',
        'company_id',
        'fecha_creacion',
        'customer_origen_id',
        'customer_destino_id',
        'valor',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
