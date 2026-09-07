<?php

namespace App\Http\Livewire\ValoresAportes;

use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\Customer;
use App\Models\CustomerTipoAhorros;
use App\Models\TipoAhorros;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Exports\ValoresAportes\ValoresAportesExport;
use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Models\Bovedas;
use App\Models\Company;
use App\Models\CustomerMovimiento;
use App\Models\DescargoBovedasHeader;
use App\Models\OperacionesDescargoBovedas;
use App\Models\TypeTransaction;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ValoresAportesComponent extends Component
{

    use WithPagination;
    use WithFileUploads;


    protected $paginationTheme = 'bootstrap';
    public $mes_fin = '';
    public $mes_fin_dias = '';
    public $exportar = [];
    public $search = '';
    public $uploadedFile;

    public function export()
    {

        return Excel::download(new ValoresAportesExport($this->exportar), 'Reporte Aportar.xlsx');
    }

    public function generateTxt()
    {

        $variableConsulta = $this->exportar;
        $fin = $variableConsulta['mes_fin_dias'];

        $company = Company::find(Auth::user()->company_id);
        $cuentasDescargoIds = TipoAhorros::where('cuenta_certificado', true)->pluck('id')->toArray();
        $cuentasClientes = CustomerTipoAhorros::whereIn('tipo_ahorros_id', $cuentasDescargoIds)->get();
        $data = [];
        foreach ($cuentasClientes as $val) {

            $customer =  Customer::find($val->customer_id);
            $cuenta = TipoAhorros::find($val->tipo_ahorros_id);

            $val->cuenta = $cuenta->name;
            $val->cliente = $customer->nombres . ' ' . $customer->apellidos;
            $val->valorperiodico = $cuenta->valor_periodico;
            $val->letrasPendientes = 0;
            $val->valorPendiente = 0;
            $val->letrasVencidas = array();



            if ($fin  != '') {
                $creditos = CreditFolderHeader::where('customer_id', $val->customer_id)->pluck('code')->toArray();
                $valoresLetras = CreditFolderDetail::whereIn('code_folder_header', $creditos)->where('date_vencimiento', '<=', $fin);
                $val->letrasPendientes = $valoresLetras->count();
                $val->valorPendiente = $valoresLetras->sum('valor_cuota');
                $val->letrasVencidas = $valoresLetras->get();
            }
            $val->totalRecaudar =  $val->valorperiodico + $val->valorPendiente;

            $cuentaDescargo = CustomerTipoAhorros::join('tipo_ahorros', 'customer_tipo_ahorros.tipo_ahorros_id', '=', 'tipo_ahorros.id')
                ->where('customer_tipo_ahorros.customer_id', $val->customer_id)
                ->where('tipo_ahorros.descargo_creditos', true)
                ->select('customer_tipo_ahorros.*')
                ->first();
            $cuentaDescargo = (isset($cuentaDescargo->codigo))  ? $cuentaDescargo->codigo :   $val->codigo;

            $data[] = [
                $val->cliente,             // Nombre del cliente
                $customer->numero_documento,  // Número de cédula
                $val->codigo,        // cuenta aportar
                $val->valorperiodico,       // valor aportar
                $cuentaDescargo,       // cuenta descargo
                $val->valorPendiente,       // valor creditos
                $val->totalRecaudar,       // total
            ];
        }

        $filename =   $fin . time() . '.txt';

        $content = "Nombre\tNúmero de Cédula\tCuenta Aporte\tValor Aporte\tCuenta Créditos\tValor Créditos\tTotal\n";
        foreach ($data as $row) {
            $content .= implode("\t", $row) . "\n";
        }

        // Crear un archivo en memoria para que se descargue sin guardarlo
        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename);
    }

    public function obtenerDatos()
    {
        $this->mes_fin = $this->mes_fin ?? '';
        if ($this->mes_fin != '') {
            $fecha = $this->mes_fin;

            // Crear una instancia de Carbon a partir de la cadena de fecha
            $date = Carbon::createFromFormat('Y-m', $fecha);

            // Obtener el último día del mes
            $ultimo_dia = $date->endOfMonth()->format('Y-m-d');
            $this->mes_fin_dias  = $ultimo_dia;
        }
    }

    public function processFile()
    {
        $this->validate([
            'uploadedFile' => 'required|mimes:txt', // Eliminamos la restricción de tamaño
        ]);
        // Leer el contenido del archivo
        $content = file_get_contents($this->uploadedFile->getRealPath());
        // Procesar el contenido del archivo
        $lines = explode("\n", trim($content));

        $data = [];
        foreach ($lines as $index => $line) {
            if ($index === 0) {
                // Omitir la primera línea si es la cabecera
                continue;
            }

            // Dividir cada línea por tabulaciones (o espacios si es el caso)
            $columns = preg_split('/\t+/', $line);

            // Agregar los datos a un array para su posterior uso
            $data[] = [
                'nombre' => $columns[0],
                'cedula' => $columns[1],
                'cuenta_aporte' => $columns[2],
                'valor_aporte' => $columns[3],
                'cuenta_creditos' => $columns[4],
                'valor_creditos' => $columns[5],
                'total' => number_format($columns[6], 2, '.', ''),
            ];
        }
        $valorCompleto = 0;
        foreach ($data as $row) {
            $valorAporte = (float) str_replace(',', '', $row['valor_aporte']);
            $valorPrestamos = (float) str_replace(',', '', $row['valor_creditos']);
            $valorCompleto +=   $valorAporte+ $valorPrestamos; 
            if ($valorAporte > 0) {
                $customer = Customer::where('numero_documento', $row['cedula'])->first();
                $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                    ->where('name_corto', 'DCD')
                    ->first();
                $tabla = 'customer_movimientos';
                $code = BaseController::generarCodigo($tabla, 9);
                $valorTotal = BaseController::valorTotal();
                $cuentaAporte = CustomerTipoAhorros::where('codigo', $row['cuenta_aporte'])->first();
                $dataCustomer = [
                    "code" => $code,
                    "comprobante" => "CARGA" . date('Ymd Hmi'),
                    "company_id" => Auth::user()->company_id,
                    "customer_id" => $customer->id,
                    "customer_code" => $customer->code,
                    "afecta" => $transaction->afecta,
                    "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                    "customer_ruc" => $customer->numero_documento,
                    "customer_address" => $customer->direccion,
                    "customer_telefono" => $customer->telefono,
                    "customer_tipo_ahorro_id" => $cuentaAporte->id,
                    "type_transaction_id" => $transaction->id,
                    "type_transaction_name" => $transaction->name,
                    "type_transaction_action" => $transaction->action,
                    "valor_movimiento" => $valorAporte,
                    "saldo_general" => $valorTotal + $valorAporte,
                    "observation" => "Se acredita el valor de " . $row['valor_aporte'] . " mediante carga de documentos",
                    "user_created_id" => Auth::user()->id,
                    "date_created" => date('Y-m-d'),
                    "hour_created" => date("H:i:s"),

                ];
                $movimientos = CustomerMovimiento::create($dataCustomer);
                CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $valorAporte, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
                BaseController::guardarValoresCartola($cuentaAporte->id, $customer->id, $movimientos, $transaction);
                BaseController::enviarMail($movimientos);
            }
            
            if ($valorPrestamos > 0) {
                $customer = Customer::where('numero_documento', $row['cedula'])->first();
                $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                    ->where('name_corto', 'DCD')
                    ->first();
                $tabla = 'customer_movimientos';
                $code = BaseController::generarCodigo($tabla, 9);
                $valorTotal = BaseController::valorTotal();
                $cuentaAporte = CustomerTipoAhorros::where('codigo', $row['cuenta_creditos'])->first();

                $data = [
                    "code" => $code,
                    "comprobante" => "CARGA" . date('Ymd Hmi'),
                    "company_id" => Auth::user()->company_id,
                    "customer_id" => $customer->id,
                    "customer_code" => $customer->code,
                    "afecta" => $transaction->afecta,
                    "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                    "customer_ruc" => $customer->numero_documento,
                    "customer_address" => $customer->direccion,
                    "customer_telefono" => $customer->telefono,
                    "customer_tipo_ahorro_id" => $cuentaAporte->id,
                    "type_transaction_id" => $transaction->id,
                    "type_transaction_name" => $transaction->name,
                    "type_transaction_action" => $transaction->action,
                    "valor_movimiento" => $valorPrestamos,
                    "saldo_general" => $valorTotal + $valorPrestamos,
                    "observation" => "Se acredita el valor de " . $row['valor_aporte'] . " mediante carga de documentos",
                    "user_created_id" => Auth::user()->id,
                    "date_created" => date('Y-m-d'),
                    "hour_created" => date("H:i:s"),

                ];
                $movimientos = CustomerMovimiento::create($data);
                CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $valorPrestamos, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
                BaseController::guardarValoresCartola($cuentaAporte->id, $customer->id, $movimientos, $transaction);
                BaseController::enviarMail($movimientos);
            }
        }
        $this->descargarValorBoveda($valorCompleto);
        $this->uploadedFile = null;
        $color = 'info';
        $mensaje = 'Cargado correctamente';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
        return;
    }

    public function descargarValorBoveda($valorCompleto)
    {
        $bovedas = Bovedas::where('status', 1)->where('boveda', 1)->first();
        if ($bovedas) {
            $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'AVCMC')->first();
            $solicitud = new DescargoBovedasHeader();
            $solicitud->company_id = Auth::user()->company_id;
            $solicitud->operaciones_descargo_bovedas_id = $operacion->id;
            $solicitud->boveda_origen_id = $bovedas->id;
            $solicitud->boveda_destino_id = 0;
            $solicitud->valor = $valorCompleto;
            $solicitud->observacion = $operacion->descripcion . date('Ymd Hmi');
            $solicitud->fecha_creacion = date('Y-m-d');
            $solicitud->estado = 'FINALIZADO';
            $solicitud->save();
        } else {
            $color = 'danger';
            $mensaje = 'NO tiene una bóveda para descargar el credito';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            $this->dispatchBrowserEvent('closeModal');
            $this->generarListadeCreditos();

            return;
        }
    }

    public function render()
    {
        $cuentasDescargoIds = TipoAhorros::where('cuenta_certificado', true)->pluck('id')->toArray();
        $cuentasClientes = CustomerTipoAhorros::select(
            'customer_tipo_ahorros.*',
            DB::raw("CONCAT(customer.nombres, ' ', customer.apellidos) as nombre_completo"),
            'customer.numero_documento',

        )
            ->leftjoin('customer', 'customer_tipo_ahorros.customer_id', '=', 'customer.id')
            ->where(function ($query) {
                $query->where('customer.nombres', 'like', '%' . $this->search . '%')
                    ->orWhere('customer.apellidos', 'like', '%' . $this->search . '%')
                    ->orWhere('customer.numero_documento', 'like', '%' . $this->search . '%');
            })
            ->whereIn('tipo_ahorros_id', $cuentasDescargoIds)->paginate(10);
        //$cuentasClientes = CustomerTipoAhorros::whereIn('tipo_ahorros_id', $cuentasDescargoIds)->paginate(10);
        $cuentasClientesEx = CustomerTipoAhorros::whereIn('tipo_ahorros_id', $cuentasDescargoIds)->get();
        foreach ($cuentasClientes as $val) {
            $customer =  Customer::find($val->customer_id);
            $cuenta = TipoAhorros::find($val->tipo_ahorros_id);

            $val->cuenta = $cuenta->name;
            $val->cliente = $customer->nombres . ' ' . $customer->apellidos;
            $val->valorperiodico = $cuenta->valor_periodico;
            $val->letrasPendientes = 0;
            $val->valorPendiente = 0;
            $val->letrasVencidas = array();



            if ($this->mes_fin_dias != '') {
                $creditos = CreditFolderHeader::where('customer_id', $val->customer_id)->pluck('code')->toArray();
                $valoresLetras = CreditFolderDetail::whereIn('code_folder_header', $creditos)->where('date_vencimiento', '<=', $this->mes_fin_dias);
                $val->letrasPendientes = $valoresLetras->count();
                $val->valorPendiente = $valoresLetras->sum('valor_cuota');
                $val->letrasVencidas = $valoresLetras->get();
            }
            $val->totalRecaudar =  $val->valorperiodico + $val->valorPendiente;
        }
        if ($this->mes_fin_dias != '') {

            $dataExport = [
                'mes_fin_dias' => $this->mes_fin_dias
            ];
            $this->exportar =  $dataExport;
        }
        return view('livewire.valores-aportes.valores-aportes-component', compact('cuentasClientes'));
    }
}
