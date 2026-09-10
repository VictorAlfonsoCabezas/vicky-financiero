<?php

namespace App\Support;

class CompanyForm
{
    public static function sections()
    {
        return [
            'identidad' => ['title' => 'Identidad y contacto', 'icon' => 'fa-building', 'description' => 'Datos de la empresa que aparecen en documentos y comunicaciones.', 'fields' => [
                'company_name' => ['Razón social', 'text', 'required|string|max:255'],
                'comercial_name' => ['Nombre comercial', 'text', 'required|string|max:255'],
                'ruc' => ['RUC', 'text', 'required|digits:13'],
                'legal_representative' => ['Representante legal', 'text', 'nullable|string|max:255'],
                'phone' => ['Teléfono', 'tel', 'nullable|string|max:15'],
                'email' => ['Correo electrónico', 'email', 'nullable|email|max:255'],
                'ciudad' => ['Ciudad', 'text', 'nullable|string|max:255'],
                'pais' => ['País', 'text', 'nullable|string|max:255'],
                'address' => ['Dirección', 'textarea', 'nullable|string|max:255'],
                'company_type' => ['Tipo de empresa', 'select', 'required|in:1,2', '1', ['1' => 'Caja de ahorros', '2' => 'Layapa']],
                'category_type' => ['Categoría Layapa', 'category', 'nullable|integer'],
                'domicilio' => ['Valor de domicilio', 'number', 'nullable|numeric|min:0|max:999999', 0],
                'conexion' => ['Conexión a Internet', 'switch', 'boolean', 1],
                'electronica' => ['Facturación electrónica', 'switch', 'boolean', 0],
            ]],
            'creditos' => ['title' => 'Créditos y cobranza', 'icon' => 'fa-coins', 'description' => 'Condiciones generales para la gestión de préstamos y cobros.', 'fields' => [
                'obligar_garante' => ['Exigir garante aunque no sea socio', 'switch', 'boolean', 0],
                'porcentaje_retener_credito' => ['Retención del crédito (%)', 'number', 'nullable|numeric|min:0|max:100', 0],
                'mora' => ['Interés por mora (%)', 'number', 'nullable|numeric|min:0|max:100', 0, [], 'porcentaje_mora'],
                'desgravament' => ['Fondo de desgravamen (%)', 'number', 'nullable|numeric|min:0|max:100', 0, [], 'porcentaje_desgravament'],
                'penalidad_plazo_fijo' => ['Penalidad de plazo fijo (%)', 'number', 'nullable|numeric|min:0|max:100', 0],
                'numero_decimales' => ['Decimales de cálculo', 'number', 'nullable|integer|min:2|max:8', 2],
                'select_tipo_interes' => ['Límite de cálculo de interés', 'select', 'required|in:M,D,S', 'M', ['M' => 'Fin de mes', 'D' => 'Días', 'S' => 'Siguiente fecha']],
                'numero_dias_interes' => ['Días de interés', 'number', 'nullable|integer|min:0|max:3650', 0],
                'dias_inicio_cobro' => ['Días para iniciar el cobro', 'number', 'nullable|integer|min:0|max:3650', 0],
                'dias_gracia' => ['Días de gracia', 'number', 'nullable|integer|min:0|max:3650', 0],
                'interes_fijo_parametrizado' => ['Interés fijo parametrizado', 'switch', 'boolean', 0],
                'genera_gastos_cobranza' => ['Generar gastos de cobranza', 'switch', 'boolean', 0],
                'valor_notificado' => ['Valor por notificación', 'number', 'nullable|numeric|min:0|max:999999', 0],
                'nombre_primer_gasto_credito' => ['Nombre del primer gasto', 'text', 'nullable|string|max:255', 'Gasto 1'],
                'nombre_segundo_gasto_credito' => ['Nombre del segundo gasto', 'text', 'nullable|string|max:255', 'Gasto 2'],
                'nombre_tercer_gasto_credito' => ['Nombre del tercer gasto', 'text', 'nullable|string|max:255', 'Gasto 3'],
            ]],
            'operacion' => ['title' => 'Contabilidad y operación', 'icon' => 'fa-sliders-h', 'description' => 'Control contable, comunicaciones y tareas programadas.', 'fields' => [
                'contabilidad' => ['Activar contabilidad', 'switch', 'boolean', 0],
                'fecha_inicio_contable' => ['Fecha de inicio contable', 'date', 'nullable|date_format:Y-m-d'],
                'enviar_mails' => ['Enviar correos electrónicos', 'switch', 'boolean', 0],
                'caja_boveda' => ['Permitir transferencias de caja', 'switch', 'boolean', 0],
                'active_cron' => ['Activar tareas programadas', 'switch', 'boolean', 0],
                'time_cron' => ['Hora de ejecución', 'time', 'nullable|date_format:H:i'],
            ]],
            'documentos' => ['title' => 'Documentos y presentación', 'icon' => 'fa-file-alt', 'description' => 'Formatos de crédito, cartolas y apariencia de los reportes.', 'fields' => [
                'letra_cambio' => ['Emitir letra de cambio', 'switch', 'boolean', 0],
                'pagare' => ['Emitir pagaré', 'switch', 'boolean', 0],
                'reporte_cartera_unido' => ['Reporte de cartera unido', 'switch', 'boolean', 0],
                'cartola_a' => ['Cartola · cara A', 'number', 'nullable|integer|min:0|max:999', 0],
                'cartola_b' => ['Cartola · cara B', 'number', 'nullable|integer|min:0|max:999', 0],
                'margen_top' => ['Margen superior', 'number', 'nullable|integer|min:0|max:999', 0],
                'margen_dow' => ['Margen inferior', 'number', 'nullable|integer|min:0|max:999', 0],
                'margen_left' => ['Margen izquierdo', 'number', 'nullable|integer|min:0|max:999', 0],
                'margen_right' => ['Margen derecho', 'number', 'nullable|integer|min:0|max:999', 0],
                'color_texto' => ['Color del texto', 'color', 'nullable|regex:/^#[0-9a-fA-F]{6}$/', '#000000'],
                'color_tabla' => ['Color de la tabla', 'color', 'nullable|regex:/^#[0-9a-fA-F]{6}$/', '#000000'],
            ]],
        ];
    }

    public static function fields()
    {
        $fields = [];
        foreach (self::sections() as $section) $fields += $section['fields'];
        return $fields;
    }
}
