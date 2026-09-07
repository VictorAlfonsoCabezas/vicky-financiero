<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    protected $table = 'customer';
    protected $fillable = [
        'id',
        'code',
        'user_id',
        'date_open_account',
        'date_interes',
        'status_interes',
        'nombres',
        'apellidos',
        'documento',
        'numero_documento',
        'fecha_nacimiento',
        'direccion',
        'nivel_academico_id',
        'parentesco_customer',
        'name_parentesco',
        'numero_identificacion_parentesco',
        'telefono_parentesco',
        'estado_civil',
        'conyugue_nombre',
        'conyugue_identificacion',
        'conyugue_telefono',
        'conyuge_nivel_academico_id',
        'conyuge_fecha_nacimiento',
        'conyuge_ocupacion',
        'conyuge_empresa_nombre',
        'conyuge_empresa_direccion',
        'conyuge_empresa_telefono',
        'conyuge_tiempo_empresa',
        'conyuge_cargo_empresa',
        'ocupacion',
        'empresa_nombre',
        'empresa_direccion',
        'empresa_provincia_id',
        'empresa_canton_id',
        'empresa_parroquia_id',
        'empresa_telefono',
        'empresa_tiempo',
        'empresa_cargo',
        'negocio_nombre',
        'negocio_direccion',
        'negocio_provincia_id',
        'negocio_canton_id',
        'negocio_parroquia_id',
        'negocio_telefono',
        'negocio_tiempo',
        'negocio_actividad',
        'latitud',
        'longitud',
        'banco_id',
        'tipo_cuenta_id',
        'no_cuenta',
        'telefono',
        'telefono_2',
        'telefono_3',
        'telefono_fijo',
        'correo',
        'status',
        'company_id',
        'customer_tarifa_id',
        'customer_tarifa_name',
        'customer_tarifa_interes',
        'country_id',
        'tipo_documento_id',
        'genero_id',
        'estado_civil_id',
        'cargas_familiares',
        'seperacion_bienes',
        'tipo_vivienda',
        'tiempo_vivienda',
        'provincia_id',
        'parroquia_id',
        'ciudad_id',
        'parentezco_id',
        'fundador',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }

    public function estadoCivil()
    {
        return $this->belongsTo('App\Models\EstadoCivil', 'estado_civil_id');
    }

    public function banco()
    {
        return $this->belongsTo('App\Models\Bancos', 'banco_id');
    }

    public function tipoCuenta()
    {
        return $this->belongsTo('App\Models\TipoCuenta', 'tipo_cuenta_id');
    }

    public function nivelAcademico()
    {
        return $this->belongsTo('App\Models\NivelAcademico', 'nivel_academico_id');
    }

    public function conyugeNivelAcademico()
    {
        return $this->belongsTo('App\Models\NivelAcademico', 'conyuge_nivel_academico_id');
    }
}
