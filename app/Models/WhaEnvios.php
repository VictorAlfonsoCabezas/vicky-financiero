<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhaEnvios extends Model
{
    protected $table = 'wha_envios';
    protected $fillable = [
        'id',
        'company_id',
        'envio_ahora',
        'es_transacion',
        'inmediato',
        'description',
        'type_transacction_id',
        'type_transacction_name',
        'proviene_id',
        'customer_id',
        'customer_name',
        'customer_celular',
        'whatsapp',
        'estado',
        'fecha_creacion',
        'fecha_envio',
        'prestamo_id',
        'letra_prestamo_id'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
    public function typeTransaction()
    {
        return $this->belongsTo('App\Models\TypeTransaction', 'type_transacction_id');
    }
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }
    public function letraPrestamo()
    {
        return $this->belongsTo('App\Models\CreditFolderDetail', 'letra_prestamo_id');
    }
}
