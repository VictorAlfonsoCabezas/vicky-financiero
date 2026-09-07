<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DenominacionBilletes extends Model
{
    protected $table = 'denominacion_billetes';
    protected $fillable = [
    'id',
    'company_id',
    'nombre',
    'valor',
    ];

   
    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }



}
