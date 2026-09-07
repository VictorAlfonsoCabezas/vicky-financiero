<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanCuentas extends Model
{
    protected $table = "plan_cuentas";
    protected $fillable = [
        'id',
        'company_id',
        'nombre',
        'codigo',
        'nivel1',
        'nivel2',
        'nivel3',
        'nivel4',
        'nivel5',
        'nivel6',
        'nivel7',
        'saldo',
        'prestamo',
        'banco_id',
        'tiempo_inicio',
        'tiempo_fin',
        'gasto',
        'utilidad',
        'creado_usuario',
        'estado_resultados',
        'status',
        'accion_cuenta',
    ];
    public function creditos()
    {
        return $this->hasMany('\App\Models\CreditFolderHeader', 'cuenta_contable_id');
    }
}
