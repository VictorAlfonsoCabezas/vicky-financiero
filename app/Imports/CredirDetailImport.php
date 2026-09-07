<?php

namespace App\Imports;

use App\Models\CreditFolderDetail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CredirDetailImport implements ToModel, WithHeadingRow {

    public function model(array $row) {
        return new CreditFolderDetail([
            'numero_cuota' => $row['numero_cuota'],
            'company_id' => 1,
            'code_folder_header' => $row['carpeta'],
            'user_pay_id' => 0,
            'date_vencimiento' => $row['fecha_pago'],
            'interes_periodo' => $row['interes_periodo'],
            'interes_mora' => 0,
            'capital_amortizado' => $row['capital_amortizado'],
            'fondo_desgravamen' => $row['fondo_desgravamen'],
            'valor_cuota' => $row['valor_cuota'],
            'saldo_remanente' => $row['saldo_remanente'],
            'tipo_pago' => $row['tipo_pago'],
            'status' => $row['status'],
        ]);
    }

}
