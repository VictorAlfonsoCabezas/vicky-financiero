<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model {

    protected $table = 'company';
    protected $fillable = [
        'id', 'ruc', 'company_name', 'company_type', 'category_type', 'comercial_name', 'company_description', 'legal_representative',
        'address', 'phone', 'email', 'photo', 'conexion', 'imprimir_comprobantes',
        'ip', 'latitude', 'longitude', 'electronica', 'domicilio', 'contribuyente_especial',
        'obligado_contabilidad', 'hora_inicio', 'hora_fin', 'dias_mora', 'porcentaje_mora', 'porcentaje_desgravament', ' status', 'ciudad',
        'pais', 'obligar_garante', 'porcentaje_retener_credito', 'numero_decimales', 'dias_inicio_cobro',
        'letra_cambio',
        'pagare','time_cron','color_texto','color_tabla','active_cron',
        'contabilidad',
        'cartola_a',
        'cartola_b',
        'usuario_cron',
        'enviar_mails',
        'genera_gastos_cobranza',
        'valor_notificado',
        'penalidad_plazo_fijo',
        'fecha_inicio_contable',
        'nombre_primer_gasto_credito',
        'nombre_segundo_gasto_credito',
        'nombre_tercer_gasto_credito',
        'interes_fijo_parametrizado',
        'reporte_cartera_unido',
        'caja_boveda',
    ];

    public function puedeContabilizar($fecha = null)
    {
        if (!$this->contabilidad || $this->fecha_inicio_contable === null) {
            return false;
        }

        if ($fecha === null) {
            return true;
        }

        return date('Y-m-d', strtotime($fecha)) >= $this->fecha_inicio_contable;
    }
}
