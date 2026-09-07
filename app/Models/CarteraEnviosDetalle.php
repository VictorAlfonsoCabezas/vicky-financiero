<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarteraEnviosDetalle extends Model
{
    protected $table = 'cartera_envios_detalle';
    protected $fillable = [
        'id',
        'company_id',
        'cartera_envios_header_id',
        'credit_folder_detalle_id',
        'mensaje',
        'estado'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function certeraReglas()
    {
        return $this->belongsTo('App\Models\CreditFolderDetail', 'credit_folder_detalle_id');
    }
}
