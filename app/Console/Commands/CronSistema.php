<?php

namespace App\Console\Commands;

use App\Models\CronList;
use App\Models\Company;
use App\Http\Controllers\Creditos\CreditosController;
use App\Http\Controllers\Base\BaseController;
use App\Http\Livewire\Utilidades\UtilidadesComponent;
use App\Models\Utilidad;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Throwable;

class CronSistema extends Command
{

    protected $signature = 'command:cronSistema';
    protected $description = 'Este cron correra todos los procesos que se tengan pendientes';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $copany = Company::find(1);
        if ($copany->usuario_cron) {
            Auth::loginUsingId($copany->usuario_cron);
            $companyId = $copany->id;
            echo "**********************************************************\n";
            echo "********* Inicia a ecuchar la ejecucion del cron ********* \n";
            echo "**********************************************************\n";
            if ($copany->active_cron) {
                echo "**********************************************************\n";
                echo "********* El company tiene activos los cron HH:MM  ******* \n";
                echo "********* " . $copany->time_cron . " *************************************** \n";
                echo "**********************************************************\n";
                echo "**********************************************************\n";
                $horaActual = date('H:i') . ':00';
                $fechaActual = date('Y-m-d');
                echo "********* La hora actual es ****************************** \n";
                echo "********* " . $horaActual . " *************************************** \n";
                echo "********* La hora actual es ****************************** \n";
                echo "\n\n";

                echo "==========================================================\n";

                if ($horaActual  == $copany->time_cron) {
                    echo "**********************************************************\n";
                    echo "*********              Inician Procesos          ********* \n";
                    echo "**********************************************************\n";
                    echo "==========================================================\n";
                    echo "\n\n";

                    $procesos = CronList::where('status', 1)->get();
                    foreach ($procesos as $cron) {
                        $opcion = $cron->cron;
                        switch ($opcion) {
                            case 'pagos_automaticos':
                                echo "==========================================================\n";
                                echo "**********************************************************\n";
                                echo "*********        Entra a pagos automaticos       ********* \n";
                                echo "**********************************************************\n";
                                echo "==========================================================\n";
                                CreditosController::generarDebitos($cron);
                                echo "\n";
                                echo "==========================================================\n";
                                echo "**********************************************************\n";
                                echo "*********         Sale a pagos automaticos       ********* \n";
                                echo "**********************************************************\n";
                                echo "==========================================================\n";

                                break;
                            case 'envio_cierre_caja':
                                echo "==========================================================\n";
                                echo "**********************************************************\n";
                                echo "*********         Entra a envios de caja         ********* \n";
                                echo "**********************************************************\n";
                                echo "==========================================================\n";

                                BaseController::envioCierresCaja();

                                echo "==========================================================\n";
                                echo "**********************************************************\n";
                                echo "*********          Sale a envios de caja         ********* \n";
                                echo "**********************************************************\n";
                                echo "==========================================================\n";
                                break;
                            case 'descargo_cuentas_transaccionales':
                                echo "==========================================================\n";
                                echo "**********************************************************\n";
                                echo "****     Entra descargo de cuentas transaccionales    **** \n";
                                echo "**********************************************************\n";
                                echo "==========================================================\n";

                                BaseController::generarDescargosCuentasTransaccionales($cron);

                                echo "==========================================================\n";
                                echo "**********************************************************\n";
                                echo "****      Sale descargo de cuentas transaccionales    **** \n";
                                echo "**********************************************************\n";
                                echo "==========================================================\n";
                                break;
                            case 'pago_utilidades':
                                $utilidad = Utilidad::where('company_id', $companyId)->first();
                                $ejecutar = false;
                                if ($utilidad) {
                                    $fechaPago = Carbon::parse($utilidad->fecha_pago_automatico)->format('Y-m-d');
                                    $pagoAutomaticoActivo = $utilidad->pago_automatico;
                                    if ($pagoAutomaticoActivo && $fechaPago === $fechaActual) {
                                        $ejecutar = true;
                                    } else {
                                        echo "*** Pago de Utilidades NO se ejecuta. Configuración: Automatico: " . ($pagoAutomaticoActivo ? 'SI' : 'NO') . ", Fecha Config: $fechaPago, Fecha Actual: $fechaActual ***\n";
                                    }
                                } else {
                                    echo "*** No se encontró configuración de Utilidad para la Compañía.***\n";
                                }
                                if ($ejecutar) {
                                    echo "**********************************************************\n";
                                    echo "*********       Entra a pago de utilidades       ********* \n";
                                    echo "**********************************************************\n";
                                    $utilidades = new UtilidadesComponent();
                                    try {
                                        $utilidades->ejecutarPagoAutomatico(true);
                                        // Es clave desactivar el pago automático para que no se ejecute más de una vez hoy
                                        $utilidad->update(['pago_automatico' => false]); 
                                    } catch (Throwable $e) {
                                        echo "********* Error en pago de Utilidades: " . $e->getMessage() . " ********* \n";
                                    }
                                    echo "\n";
                                    echo "==========================================================\n";
                                    echo "**********************************************************\n";
                                    echo "*********       Sale a pago de utilidades       ********* \n";
                                    echo "**********************************************************\n";
                                    echo "==========================================================\n";
                                }
                        }
                    }
                }
            }
            Auth::logout();
        }
    }
}
