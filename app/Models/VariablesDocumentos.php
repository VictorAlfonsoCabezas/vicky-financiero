<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariablesDocumentos extends Model
{
    protected $table = 'variables_documentos';
    protected $fillable = [
        'id',
        'company_id',
        'variable',

    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
