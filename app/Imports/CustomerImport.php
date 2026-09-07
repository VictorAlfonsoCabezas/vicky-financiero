<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\CustomerTarifa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToModel, WithHeadingRow {

    public function model(array $row) {
        return new Customer([
            'code' => $row['code'],
            'nombres' => $row['nombres'],
            'apellidos' => $row['apellidos'],
            'documento' => 4,
            'numero_documento' => $row['numero_cedula'],
            'direccion' => $row['direccion'],
            'parentesco_customer' => $row['parentesco'],
            'name_parentesco' => $row['nombre_parentesco'],
            'numero_identificacion_parentesco' => $row['cedula_parentesco'],
            'telefono_parentesco' => $row['telefono_parentesco'],
            'estado_civil' => $row['estado_civil'],
            'conyugue_nombre' => $row['nombre_conyugue'],
            'conyugue_identificacion' => $row['cedula_conyugue'],
            'conyugue_telefono' => $row['telefono_conyugue'],
            'telefono' => $row['telefono'],
            'status' => 1,
            'company_id' => 1,
            'customer_tarifa_id' => ($row['tarifa'] == 'SOCIO') ? 2 : 1,
            'customer_tarifa_name' => ($row['tarifa'] == 'SOCIO') ? 'SOCIO' : 'PARTICULAR',
            'customer_tarifa_interes' => ($row['tarifa'] == 'SOCIO') ? 1.50 : 2.50,
        ]);
    }

}
