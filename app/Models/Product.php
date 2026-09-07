<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $table = 'product';
    protected $fillable = [
        'id',
        'company_id',
        'name',
        'description',
        'description_larga',
        'tipo_iva',
        'costo',
        'precio_a',
        'precio_b',
        'precio_c',
        'tipo',
        'observation',
        'barcode',
        'promotion',
        'photo',
        'photoVenta',
        'stock',
        'stock_minimo',
        'lotes',
        'status',
    ];

    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
