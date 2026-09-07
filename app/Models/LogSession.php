<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogSession extends Model
{
    protected $table = 'log_session';
    protected $fillable = [
        'id',
        'user_id',
        'ip_address',
        'description',
        'fecha_creacion',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
