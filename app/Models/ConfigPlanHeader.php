<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigPlanHeader extends Model
{
    protected $table = 'config_plan_header';
    protected $fillable = [
        'id',
        'company_id',
        'type_transaction_id',
        'operaciones_descargo_bovedas_id',
        'concepto_id',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function typeTransaction()
    {
        return $this->belongsTo('App\Models\TypeTransaction', 'type_transaction_id');
    }
    
    public function operacionesDescargoBovedas()
    {
        return $this->belongsTo('App\Models\OperacionesDescargoBovedas', 'operaciones_descargo_bovedas_id');  
    }

    public function concepto()
    {
        return $this->belongsTo('App\Models\Conceptos', 'concepto_id');
    }
}
