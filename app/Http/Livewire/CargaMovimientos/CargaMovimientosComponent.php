<?php

namespace App\Http\Livewire\CargaMovimientos;

use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Models\Customer;
use App\Models\CustomerMovimiento;
use App\Models\CustomerTipoAhorros;
use App\Models\TypeTransaction;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class CargaMovimientosComponent extends Component
{

    use WithFileUploads;

    public $excelFile;
    public $rows = [];
    public $expectedColumnCount = 5;

    public function updatedExcelFile()
    {
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,xls',
        ]);
        $this->rows = [];

        $path = $this->excelFile->getRealPath();
        $data = Excel::toArray([], $path);

        // Verifica que al menos exista una hoja y filas
        if (empty($data) || empty($data[0])) {
            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'El archivo está vacío o no contiene datos.',
            ]);
            return;
        }

        // Valida que el encabezado tenga el número correcto de columnas
        $header = $data[0][0]; // Primera fila como encabezado
        if (count($header) !== $this->expectedColumnCount) {
            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'El archivo debe tener exactamente ' . $this->expectedColumnCount . ' columnas.',
            ]);
            return;
        }

        // Procesar las filas, transformando la columna 4 (fecha)
        $rows = $data[0]; // Obtiene todas las filas
        foreach ($rows as $index => $row) {
            if ($index === 0) {
                // Saltar el encabezado
                continue;
            }

            // Convertir la columna 4 (índice 3 en el array)
            if (isset($row[3]) && is_numeric($row[3])) {
                $row[3] = $this->convertExcelDateToYmd($row[3]);
            }

            $this->rows[] = $row;
        }
    }

    // Función para convertir fecha en formato Excel a Y-m-d
    private function convertExcelDateToYmd($excelDate)
    {
        $baseTimestamp = 25569; // Días desde 1900-01-01 al inicio de UNIX
        $timestamp = ($excelDate - $baseTimestamp) * 86400; // Convertir días a segundos

        // Ajuste de un día por el error de Excel
        $timestamp += 86400;

        return date('Y-m-d', $timestamp);
    }

    public function deleteRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows); // Reindexar
    }



    public function saveData()
    {
        if (empty($this->rows)) {
            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'No hay datos para guardar.',
            ]);
            return;
        }

        // Excluye el encabezado (primera fila)
        //$dataToSave = array_slice($this->rows, 1);
        $dataToSave = array_slice($this->rows, 0);
        foreach ($dataToSave as $row) {
            $cuenta = CustomerTipoAhorros::where('codigo', $row[0])->first();
            $customer = Customer::find($cuenta->customer_id);
            if ($row[2] == 'D') {
                $tipoTransaccion = 'IN';
            } else {
                $tipoTransaccion = 'EG';
            }
            $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', $tipoTransaccion)
                ->first();
            $tabla = 'customer_movimientos';
            $code = BaseController::generarCodigo($tabla, 9);
            $valorTotal = BaseController::valorTotal();
            $data = [
                "code" => $code,
                "comprobante" => $code,
                "company_id" => Auth::user()->company_id,
                "customer_id" => $customer->id,
                "customer_code" => $customer->code,
                "afecta" => $transaction->afecta,
                "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                "customer_ruc" => $customer->numero_documento,
                "customer_address" => $customer->direccion,
                "customer_telefono" => $customer->telefono,
                "customer_tipo_ahorro_id" => $cuenta->id,
                "type_transaction_id" => $transaction->id,
                "type_transaction_name" => $transaction->name,
                "type_transaction_action" => $transaction->action,
                "valor_movimiento" => $row[1],
                "saldo_general" => $valorTotal + $row[1],
                "observation" => $row[4],
                "user_created_id" => Auth::user()->id,
                "date_created" => $row[3],
                "hour_created" => date("H:i:s"),
                "carga_masiva" => true
            ];
            $movimientos = CustomerMovimiento::create($data);
            CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $row[1], $customer->code, $movimientos->saldo_general, $row[3], $movimientos->customer_tipo_ahorro_id, 1);
            BaseController::guardarValoresCartola($cuenta->id, $customer->id, $movimientos, $transaction);
        }


        $this->rows = []; // Limpia los datos después de guardar
        $this->dispatchBrowserEvent('alerta', [
            'titulo' => 'Notificación',
            'color' => 'info',
            'mensaje' => 'No hay datos para guardar.',
        ]);
        return;
    }
    public function render()
    {
        return view('livewire.carga-movimientos.carga-movimientos-component');
    }
}
