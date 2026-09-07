<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class MenuComando extends Model
{
    protected $table = 'menu_comando';
    protected $fillable = [
        'id',
        'menu_id', 
        'comando_id',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Menu', 'menu_id');
    }

    public function intReglas()
    {
        return $this->belongsTo('App\Models\Comando', 'comando_id');
    }
}
