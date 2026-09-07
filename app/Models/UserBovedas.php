<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBovedas extends Model
{
    protected $table = 'user_bovedas';
    protected $fillable = [
        'company_id',
        'user_id',
        'bovedas_id',
    ];

    public function userAsignado()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function bovedas()
    {
        return $this->belongsTo('App\Models\Bovedas', 'bovedas_id');
    }
}
