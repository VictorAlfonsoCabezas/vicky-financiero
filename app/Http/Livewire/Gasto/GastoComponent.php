<?php

namespace App\Http\Livewire\Gasto;

use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Models\AsientosDetalle;
use App\Models\AsientosHeader;
use App\Models\CentroCostos;
use App\Models\Company;
use App\Models\ConfigPlanDetalle;
use App\Models\ConfigPlanHeader;
use App\Models\CustomerMovimiento;
use App\Models\FormasPago;
use App\Models\Gastos;
use App\Models\GastosCategorias;
use App\Models\GastosFormaPagos;
use App\Models\GastosImpuestos;
use App\Models\GastosListaRetencion;
use App\Models\GastosPlanCuentas;
use App\Models\Impuestos;
use App\Models\ListaRetencion;
use App\Models\PlanCuentas;
use App\Models\Proveedores;
use App\Models\SedesCentroCostos;
use App\Models\SustentoTributario;
use App\Models\TipoComprobante;
use App\Models\TypeTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use SimpleXMLElement;
use Log;

use Carbon\Carbon;

use App\Models\Bancos;



class GastoComponent extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $descuento = 0;
    public $subtotal_descuento = 0;
    public $subtotal_exento = 0;
    public $suma_subtotal = 0;
    public $suma_iva = 0;
    public $total = 0;

    public $tiene_impuestos = null;
    public $id_asiento_seleccionado = 0;
    public $id_aprobar = 0;
    public $descripcion = '';
    public $gastos_categorias_id = 0;
    public $fecha_creacion = '';
    public $valor = 0;
    public $gastosTotales = 0;
    public $gastosValor = 0;
    public $proveedor_id = 0;
    public $user_responsable_id = '';
    public $pagado_compania_empleado = true;
    public $estado = 'PENDIENTE';
    public $razon_rechaza = '';
    public $gastos_plan_cuentas_id;
    public $forma_pago_id;
    public $fp_valor;
    public $lista_retencion_id = 0;
    public $ret_valor;
    public $ret_calculo;
    public $gastos_centro_costos_id;
    public $gastos_valor = 0;
    public $categorias = [];
    public $proveedores = [];
    public $planCuentas = [];
    public $centroCostos = [];
    public $chartData;
    public $fecha_inicio;
    public $fecha_fin;
    public $formasPago = [];
    public $listaRetenciones = [];
    public $listaEstados = [];
    public $estadoFiltro = 'TODOS';
    public $sustentoTributario = [];
    public $tipoComprobante = [];
    public $sustentoTributario_id;
    public $tipoComprobante_id;
    public $numero;
    public $establecimiento;
    public $punto_emision;
    public $autorizacion;
    public $fecha_autorizacion;
    public $xmlFile; // Asegúrate de que esta propiedad exista
    public $xmlData = [];
    public $xmlDataimpuestos = [];
    public $htmlXml = '';
    public $htmlXmlTabla1 = '';
    public $htmlXmlTabla2 = '';
    public $xmlDataRuc = '';
    public $tipo_factura_proveedor;

    public $bancos = [];
    public $banco_id;
    public $numero_comprobante;

    public $fecha_emision;

    public $cuenta_valor = 0;


    public $valorTotalCuentas = 0; // suma de cuentas agregadas

    public $gastos_detalle_valor;

    public $gastosImpuestos = [];



    public $mostrarTransferencia = false;

    public function updatedFormaPagoId($value)
    {
        $this->mostrarTransferencia = ($value == 3);
        if ($this->mostrarTransferencia) {
            $this->bancos = Bancos::whereIn('tipo_cuenta_id', [1, 2])
                ->where('status', 1)
                ->orderBy('nombre')
                ->get();
        } else {
            $this->bancos = [];
        }
    }

    public function banco()
    {
        return $this->belongsTo(\App\Models\Bancos::class, 'banco_id');
    }



    protected $listeners = ['elminarGasto', 'quitarFormaPago'];

    public function __construct()
    {
        $this->fecha_inicio = date('Y-m') . '-01';
        $this->fecha_fin = date('Y-m-d');
    }

    public function abrirModal($id)
    {
        $this->limpiarFormulario();

        $this->id_seleccionado = $id;
        $this->fecha_creacion = date('Y-m-d H:i:s');

        // =========================
        // NUEVO GASTO
        // =========================
        if ((int) $id === 0) {
            $this->id_seleccionado = 0;
            $this->estado = 'BORRADOR';
            $this->tiene_impuestos = null; // ninguno seleccionado
            $this->fecha_emision = date('Y-m-d');
            return;
        }

        // =========================
        // EDITAR GASTO EXISTENTE
        // =========================
        $gastos = Gastos::find($id);

        if (!$gastos) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => 'Error',
                'color' => 'danger',
                'mensaje' => 'No se encontró el gasto.'
            ]);
            return;
        }

        // Datos generales
        $this->id_seleccionado = $gastos->id;
        $this->valor = $gastos->valor ?? 0;
        $this->tiene_impuestos = isset($gastos->tiene_impuestos)
            ? (int) $gastos->tiene_impuestos
            : null;

        $this->descuento = $gastos->descuento ?? 0;
        $this->subtotal_descuento = $gastos->subtotal_descuento ?? 0;
        $this->subtotal_exento = $gastos->subtotal_exento ?? 0;
        $this->suma_subtotal = $gastos->suma_subtotal ?? 0;
        $this->suma_iva = $gastos->suma_iva ?? 0;
        $this->total = $gastos->total ?? 0;

        $this->descripcion = strtoupper($gastos->descripcion ?? '');
        $this->gastos_categorias_id = $gastos->gastos_categorias_id;
        $this->proveedor_id = $gastos->proveedor_id;

        $this->fecha_creacion = $gastos->fecha_creacion ?? date('Y-m-d H:i:s');
        $this->fecha_emision = $gastos->fecha_emision ?? date('Y-m-d');
        $this->user_responsable_id = $gastos->user_responsable_id;
        $this->pagado_compania_empleado = $gastos->pagado_compania_empleado;
        $this->estado = $gastos->estado ?? 'BORRADOR';

        // =========================
        // DATOS TRIBUTARIOS
        // =========================
        if ((int) $this->tiene_impuestos === 1) {
            $this->sustento_tributario_id = $gastos->sustento_tributario_id;
            $this->tipo_comprobante_id = $gastos->tipo_comprobante_id;
            $this->numero = $gastos->numero;
            $this->establecimiento = $gastos->establecimiento;
            $this->punto_emision = $gastos->punto_emision;
            $this->autorizacion = $gastos->autorizacion;
            $this->fecha_autorizacion = $gastos->fecha_autorizacion;
        } else {
            $this->sustento_tributario_id = null;
            $this->tipo_comprobante_id = null;
            $this->numero = null;
            $this->establecimiento = null;
            $this->punto_emision = null;
            $this->autorizacion = null;
            $this->fecha_autorizacion = null;
        }
    }
    public function abrirModalXML($id)
    {
        $this->id_seleccionado = $id;
        $this->htmlXml = '';
        $this->htmlXmlTabla1 = '';
        $this->htmlXmlTabla2 = '';
    }

    public function abrirModalAsientos($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();
        /*$query = DB::table('gastos')
            ->select('asientos_header.id')
            ->join('customer_movimientos', 'gastos.id', '=', 'customer_movimientos.gastos_id')
            ->join('asientos_header', 'customer_movimientos.id', '=', 'asientos_header.customer_movimiento_id')
            ->where('gastos.id', $id)
            ->first();*/

        // $this->id_asiento_seleccionado = isset($query->id) ? $query->id : 0;
    }

    public function abrirModalFP($id)
    {
        $this->id_seleccionado = $id;
        $this->fp_valor = $this->pendientePago($id);

        $this->limpiarFP();
        $this->fecha_creacion = date('Y-m-d');
    }

    public function abrirModalRET($id)
    {
        $this->id_seleccionado = $id;
        // $this->limpiarFormulario();
        // if ($this->id_seleccionado !== 0) {
        //     $gastos = Gastos::find($id);
        //     $this->valor = $gastos->valor;
        //     $this->tiene_impuestos = $gastos->tiene_impuestos;
        //     $this->descuento = $gastos->descuento;
        //     $this->subtotal_descuento = $gastos->subtotal_descuento;
        //     $this->subtotal_exento = $gastos->subtotal_exento;
        //     $this->suma_subtotal = $gastos->suma_subtotal;
        //     $this->suma_iva = $gastos->suma_iva;
        //     $this->total = $gastos->total;
        //     $this->descripcion = strtoupper($gastos->descripcion);
        //     $this->gastos_categorias_id = $gastos->gastos_categorias_id;
        //     $this->proveedor_id = $gastos->proveedor_id;
        //     $this->fecha_creacion = $gastos->fecha_creacion;
        //     $this->user_responsable_id = $gastos->user_responsable_id;
        //     $this->pagado_compania_empleado = $gastos->pagado_compania_empleado;
        //     $this->estado = $gastos->estado;
        // }
    }

    public function verGasto($id)
    {
        $this->limpiarFormulario();

        $this->id_aprobar = $id;

        $gastos = Gastos::find($this->id_aprobar);

        if (!$gastos) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => 'Error',
                'color' => 'danger',
                'mensaje' => 'No se encontró el gasto.'
            ]);
            return;
        }

        // ============================================
        // DATOS GENERALES DEL GASTO
        // ============================================
        $this->id_seleccionado = $gastos->id;
        $this->tiene_impuestos = isset($gastos->tiene_impuestos) ? (int) $gastos->tiene_impuestos : null;

        $this->valor = (float) ($gastos->valor ?? 0);
        $this->descuento = (float) ($gastos->descuento ?? 0);
        $this->subtotal_descuento = (float) ($gastos->subtotal_descuento ?? 0);
        $this->subtotal_exento = (float) ($gastos->subtotal_exento ?? 0);
        $this->suma_subtotal = (float) ($gastos->suma_subtotal ?? 0);
        $this->suma_iva = (float) ($gastos->suma_iva ?? 0);
        $this->total = (float) ($gastos->total ?? 0);

        $this->descripcion = strtoupper($gastos->descripcion ?? '');
        $this->gastos_categorias_id = $gastos->gastos_categorias_id;
        $this->proveedor_id = $gastos->proveedor_id;
        $this->fecha_emision = $gastos->fecha_emision ?? date('Y-m-d');
        $this->razon_rechaza = $gastos->razon_rechaza ?? null;
        $this->estado = $gastos->estado ?? 'BORRADOR';

        // ============================================
        // VALOR FIJO DEL GASTO EN EL MODAL APROBAR
        // ESTE NO DEBE CAMBIAR AL AGREGAR/ELIMINAR CUENTAS
        // ============================================
        $this->gastos_valor = (float) ($gastos->subtotal_descuento ?? $gastos->valor ?? 0);

        // ============================================
        // DATOS DEL PROVEEDOR
        // ============================================
        $proveedor = null;
        $this->tipo_factura_proveedor = null;

        if (!empty($gastos->proveedor_id)) {
            $proveedor = Proveedores::find($gastos->proveedor_id);

            if ($proveedor) {
                $this->tipo_factura_proveedor = $proveedor->tipo_factura ?? null;
            }
        }

        // ============================================
        // DATOS TRIBUTARIOS
        // ============================================
        if ((int) $this->tiene_impuestos === 1) {
            $this->sustento_tributario_id = $gastos->sustento_tributario_id;
            $this->tipo_comprobante_id = $gastos->tipo_comprobante_id;
            $this->numero = $gastos->numero;
            $this->establecimiento = $gastos->establecimiento;
            $this->punto_emision = $gastos->punto_emision;

            if ($proveedor && $proveedor->tipo_factura === 'fisica') {
                $this->autorizacion = $proveedor->numero_autorizacion;
                $this->fecha_autorizacion = $proveedor->fecha_caducidad;
            } else {
                $this->autorizacion = $gastos->autorizacion;
                $this->fecha_autorizacion = $gastos->fecha_autorizacion;
            }
        } else {
            $this->sustento_tributario_id = null;
            $this->tipo_comprobante_id = null;
            $this->numero = null;
            $this->establecimiento = null;
            $this->punto_emision = null;
            $this->autorizacion = null;
            $this->fecha_autorizacion = null;
        }

        // ============================================
        // SI NO EXISTE CUENTA CONTABLE AUTOMÁTICA, CREARLA
        // ============================================
        if (
            $proveedor &&
            !empty($proveedor->plan_cuenta_id) &&
            !empty($proveedor->centro_costos_id)
        ) {
            if (!GastosPlanCuentas::where('gasto_id', $id)->exists()) {
                $gastoCuenta = new GastosPlanCuentas();
                $gastoCuenta->company_id = Auth::user()->company_id;
                $gastoCuenta->gasto_id = $id;
                $gastoCuenta->plan_cuentas_id = $proveedor->plan_cuenta_id;
                $gastoCuenta->centro_costos_id = $proveedor->centro_costos_id;
                $gastoCuenta->valor = (float) ($gastos->subtotal_descuento ?? $gastos->valor ?? 0);
                $gastoCuenta->save();
            }
        }

        // ============================================
        // TOTAL ACTUAL DE CUENTAS
        // ============================================
        $this->valorTotalCuentas = (float) GastosPlanCuentas::where('gasto_id', $id)->sum('valor');
        $this->gastosTotales = $this->valorTotalCuentas;

        // ============================================
        // LIMPIAR CAMPOS DE NUEVA CUENTA
        // ============================================
        $this->gastos_plan_cuentas_id = null;
        $this->gastos_centro_costos_id = null;
    }


    public function aprobar($id)
    {
        $gasto = Gastos::find($id);

        if (!$gasto) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => 'Error',
                'color' => 'danger',
                'mensaje' => 'No se encontró el gasto.'
            ]);
            return;
        }

        // =========================================================
        // VALOR EDITABLE DEL MODAL DE APROBACIÓN
        // =========================================================
        $valorAprobado = (float) $this->subtotal_descuento;

        if ($valorAprobado <= 0) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => 'Error',
                'color' => 'danger',
                'mensaje' => 'El valor del gasto debe ser mayor a 0.'
            ]);
            return;
        }

        // =========================================================
        // VALIDAR CAMPOS BASE
        // =========================================================
        $rules = [
            'subtotal_descuento' => 'required|numeric|min:0.01',
        ];

        $messages = [
            'subtotal_descuento.required' => 'El valor del gasto es obligatorio.',
            'subtotal_descuento.numeric' => 'El valor del gasto debe ser numérico.',
            'subtotal_descuento.min' => 'El valor del gasto debe ser mayor a 0.',
        ];

        // =========================================================
        // SI TIENE IMPUESTOS -> VALIDAR CAMPOS TRIBUTARIOS
        // =========================================================
        if ((int) $this->tiene_impuestos === 1) {

            if (
                $this->fecha_autorizacion &&
                Carbon::parse($this->fecha_autorizacion)->lt(Carbon::today())
            ) {
                $this->dispatchBrowserEvent('alertaGrande', [
                    'titulo' => 'Error',
                    'color' => 'danger',
                    'mensaje' => 'La fecha de autorización está vencida.'
                ]);
                return;
            }

            $rules = array_merge($rules, [
                'sustento_tributario_id' => 'required',
                'tipo_comprobante_id' => 'required',
                'numero' => 'required',
                'establecimiento' => 'required|digits:3',
                'punto_emision' => 'required|digits:3',
                'autorizacion' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $length = strlen((string) $value);

                        if ($this->tipo_factura_proveedor === 'fisica' && $length != 10) {
                            $fail('La autorización para factura física debe tener 10 dígitos.');
                        }

                        if ($this->tipo_factura_proveedor === 'electronica' && $length != 49) {
                            $fail('La autorización para factura electrónica debe tener 49 dígitos.');
                        }
                    },
                ],
                'fecha_autorizacion' => 'required',
            ]);

            $messages = array_merge($messages, [
                'sustento_tributario_id.required' => 'El campo Sustento Tributario es obligatorio.',
                'tipo_comprobante_id.required' => 'El campo Tipo de Comprobante es obligatorio.',
                'numero.required' => 'El campo # Factura es obligatorio.',
                'establecimiento.required' => 'El establecimiento es obligatorio.',
                'establecimiento.digits' => 'El establecimiento debe tener exactamente 3 dígitos.',
                'punto_emision.required' => 'El punto de emisión es obligatorio.',
                'punto_emision.digits' => 'El punto de emisión debe tener exactamente 3 dígitos.',
                'autorizacion.required' => 'La autorización es obligatoria.',
                'fecha_autorizacion.required' => 'La fecha de autorización es obligatoria.',
            ]);
        }

        $this->validate($rules, $messages);

        // =========================================================
        // OBTENER CUENTAS DEL GASTO
        // =========================================================
        $cuentas = GastosPlanCuentas::where('gasto_id', $gasto->id)->get();
        $cantidadCuentas = $cuentas->count();

        // =========================================================
        // SINCRONIZAR CUENTAS CONTABLES CON EL VALOR APROBADO
        // =========================================================
        if ($cantidadCuentas === 0) {
            // Si no hay cuentas creadas, intentar crear una automática con proveedor
            $proveedor = Proveedores::find($gasto->proveedor_id);

            if (
                $proveedor &&
                !empty($proveedor->plan_cuenta_id) &&
                !empty($proveedor->centro_costos_id)
            ) {
                $nuevaCuenta = new GastosPlanCuentas();
                $nuevaCuenta->company_id = Auth::user()->company_id;
                $nuevaCuenta->gasto_id = $gasto->id;
                $nuevaCuenta->plan_cuentas_id = $proveedor->plan_cuenta_id;
                $nuevaCuenta->centro_costos_id = $proveedor->centro_costos_id;
                $nuevaCuenta->valor = $valorAprobado;
                $nuevaCuenta->save();

                $cantidadCuentas = 1;
            } else {
                $this->dispatchBrowserEvent('alertaGrande', [
                    'titulo' => 'Error',
                    'color' => 'danger',
                    'mensaje' => 'El gasto no tiene cuentas contables asignadas.'
                ]);
                return;
            }
        }

        // Volver a consultar por si se creó una cuenta nueva
        $cuentas = GastosPlanCuentas::where('gasto_id', $gasto->id)->get();
        $cantidadCuentas = $cuentas->count();

        if ($cantidadCuentas === 1) {
            // Si solo hay una cuenta, se ajusta automáticamente al valor aprobado
            $cuenta = $cuentas->first();
            $cuenta->valor = $valorAprobado;
            $cuenta->save();
        } else {
            // Si hay varias cuentas, validar que la suma coincida
            $sumaCuentas = (float) $cuentas->sum('valor');

            if (round($sumaCuentas, 2) != round($valorAprobado, 2)) {
                $this->dispatchBrowserEvent('alertaGrande', [
                    'titulo' => 'Error',
                    'color' => 'danger',
                    'mensaje' => 'La suma de las cuentas contables ($' . number_format($sumaCuentas, 2) . ') no coincide con el valor del gasto ($' . number_format($valorAprobado, 2) . ').'
                ]);
                return;
            }
        }

        // =========================================================
        // ACTUALIZAR DATOS DEL GASTO
        // =========================================================
        $gasto->estado = 'APROBADO';
        $gasto->fecha_aprobacion = date('Y-m-d H:i:s');
        $gasto->user_aprueba_id = Auth::user()->id;
        $gasto->razon_rechaza = strtoupper($this->razon_rechaza ?? '');

        // ACTUALIZAR EL VALOR APROBADO EN EL GASTO
        $gasto->valor = $valorAprobado;
        $gasto->subtotal_descuento = $valorAprobado;

        // Si manejas total igual al valor aprobado en aprobación, déjalo así:
        $gasto->total = $valorAprobado;

        if ((int) $this->tiene_impuestos === 1) {
            $gasto->sustento_tributario_id = $this->sustento_tributario_id;
            $gasto->tipo_comprobante_id = $this->tipo_comprobante_id;
            $gasto->numero = $this->numero;
            $gasto->establecimiento = $this->establecimiento;
            $gasto->punto_emision = $this->punto_emision;
            $gasto->autorizacion = $this->autorizacion;
            $gasto->fecha_autorizacion = $this->fecha_autorizacion;
        } else {
            // Si NO tiene impuestos, limpiar campos tributarios
            $gasto->sustento_tributario_id = null;
            $gasto->tipo_comprobante_id = null;
            $gasto->numero = null;
            $gasto->establecimiento = null;
            $gasto->punto_emision = null;
            $gasto->autorizacion = null;
            $gasto->fecha_autorizacion = null;
        }

        $gasto->save();

        // =========================================================
        // SI NO TIENE IMPUESTOS -> DEJAR SOLO IMPUESTO 0%
        // =========================================================
        if ((int) $this->tiene_impuestos !== 1) {
            GastosImpuestos::where('gastos_id', $gasto->id)->delete();

            $impuestoCero = Impuestos::where('codigo', 0)->first();

            if ($impuestoCero) {
                $gastoImpuesto = new GastosImpuestos();
                $gastoImpuesto->gastos_id = $gasto->id;
                $gastoImpuesto->impuestos_id = $impuestoCero->id;
                $gastoImpuesto->subtotal = $valorAprobado;
                $gastoImpuesto->iva = 0;
                $gastoImpuesto->save();
            }
        }

        // =========================================================
        // RECALCULAR ASIENTO
        // =========================================================
        $respuestaAsiento = AsientosHeader::recalcularAsientos($gasto->id, 'gastos');

        if (($respuestaAsiento['code'] ?? null) !== '200') {
            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Contabilidad',
                'color' => 'warning',
                'mensaje' => $respuestaAsiento['msg'] ?? 'No se pudo generar el asiento del gasto.'
            ]);
        }

        $this->dispatchBrowserEvent('closeModal');
    }

    public function reversarPendiente($id)
    {
        $gasto = Gastos::find($id);
        $gasto->estado = 'PENDIENTE';
        $gasto->fecha_aprobacion = null;
        $gasto->user_aprueba_id = null;
        $gasto->razon_rechaza = null;
        $gasto->save();

        $gastosFormaPagos = GastosFormaPagos::where('gasto_id', $gasto->id)->get();
        foreach ($gastosFormaPagos as $key => $gFp) {
            $customerMovimientos = CustomerMovimiento::where('gastos_forma_pagos_id', $gFp->id)->get();
            foreach ($customerMovimientos as $key => $cm) {
                CustomerMovimiento::find($cm->id)->delete();
            }
            GastosFormaPagos::find($gFp->id)->delete();
        }

        GastosListaRetencion::where('gasto_id', $gasto->id)->delete();
        CustomerMovimiento::where('gastos_forma_pagos_id', $id)->delete();
    }

    public function reversarAprobado($id)
    {
        $gasto = Gastos::find($id);
        $gasto->estado = 'APROBADO';
        $gasto->save();
    }

    public function pagado($id)
    {
        $gasto = Gastos::find($id);
        $gasto->estado = 'PAGADO';
        $gasto->save();
    }

    public function guardarTransaccion($id, $gastos_forma_pagos_id, $forma_pago_id, $fecha)
    {
        $company = Company::find(Auth::user()->company_id);
        $gasto = Gastos::find($id);
        $gastosFormaPagos = GastosFormaPagos::find($gastos_forma_pagos_id);
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)->where('name_corto', 'PPR')->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();

        $data = [
            "code" => $code,
            "company_id" => $company->id,
            "customer_code" => $company->id,
            "afecta" => $transaction->afecta,
            "customer_name" => $company->company_name,
            "customer_ruc" => $company->ruc,
            "customer_address" => $company->address,
            "customer_telefono" => $company->phone,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $gastosFormaPagos->valor,
            "saldo_general" => $valorTotal - $gastosFormaPagos->valor,
            "forma_pago_id" => $forma_pago_id,
            "forma_pago_name" => FormasPago::find($forma_pago_id)->nombre,
            "gastos_id" => $gasto->id,
            "gastos_forma_pagos_id" => $gastos_forma_pagos_id,
            "observation" => $gasto->razon_rechaza,
            "user_created_id" => Auth::user()->id,
            "date_created" => Carbon::parse($fecha)->format('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $movimientos->valor_movimiento, $company->id, $movimientos->saldo_general, $movimientos->date_created);

        return $movimientos;
    }

    public function denegar($id)
    {
        $this->validate(
            [
                'razon_rechaza' => 'required',
            ],
            [
                'razon_rechaza.required' => 'Si deniega el gasto, debe ingresar una razón obligatoriamente.',
            ]
        );

        $gasto = Gastos::find($id);
        $gasto->estado = 'RECHAZADO';
        $gasto->user_rechaza_id = Auth::user()->id;
        $gasto->razon_rechaza = strtoupper($this->razon_rechaza);
        $gasto->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['descripcion', 'gastos_categorias_id', 'proveedor_id', 'valor', 'user_responsable_id', 'pagado_compania_empleado', 'estado', 'razon_rechaza', 'pagado_compania_empleado', 'gastos_plan_cuentas_id', 'gastos_centro_costos_id', 'gastos_valor', 'tiene_impuestos', 'descuento', 'subtotal_descuento', 'subtotal_exento', 'suma_subtotal', 'suma_iva', 'total']);
        $this->resetErrorBag();
        $this->resetValidation();

        $this->id_seleccionado = 0;
        $this->tiene_impuestos = null;
        $this->valor = 0;
        $this->descuento = 0;
        $this->subtotal_descuento = 0;
        $this->subtotal_exento = 0;
        $this->suma_subtotal = 0;
        $this->suma_iva = 0;
        $this->total = 0;
        $this->gastosImpuestos = collect();

        $this->sustento_tributario_id = null;
        $this->tipo_comprobante_id = null;
        $this->numero = null;
        $this->establecimiento = null;
        $this->punto_emision = null;
        $this->autorizacion = null;
        $this->fecha_autorizacion = null;
    }
    private function limpiarPlanes()
    {
        $this->reset(['gastos_plan_cuentas_id', 'gastos_centro_costos_id', 'gastos_valor']);
        $this->resetErrorBag();
        $this->resetValidation();

        $this->gastos_plan_cuentas_id = null;
        $this->gastos_centro_costos_id = null;
        $this->cuenta_valor = 0;
    }

    public function storeGasto($estado = 'BORRADOR')
    {
        // =========================================
        // VALIDACIÓN BASE
        // =========================================
        $rules = [
            'proveedor_id' => 'required',
            'gastos_categorias_id' => 'required',
            'valor' => 'required|numeric|min:0.01',
            'fecha_emision' => 'required|date',
            'tiene_impuestos' => 'required|in:0,1',
        ];

        $messages = [
            'proveedor_id.required' => 'El proveedor es obligatorio.',
            'gastos_categorias_id.required' => 'La categoría es obligatoria.',
            'valor.required' => 'El valor es obligatorio.',
            'valor.numeric' => 'El valor debe ser numérico.',
            'valor.min' => 'El valor debe ser mayor a 0.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'fecha_emision.date' => 'La fecha de emisión no es válida.',
            'tiene_impuestos.required' => 'Debe seleccionar si el gasto tiene impuestos o no.',
            'tiene_impuestos.in' => 'La opción de impuestos seleccionada no es válida.',
        ];

        // =========================================
        // SI ELIGE "CON IMPUESTOS", VALIDAR CAMPOS TRIBUTARIOS
        // =========================================
        if ((int) $this->tiene_impuestos === 1) {
            $rules = array_merge($rules, [
                'sustento_tributario_id' => 'required',
                'tipo_comprobante_id' => 'required',
                'numero' => 'required',
                'establecimiento' => 'required|digits:3',
                'punto_emision' => 'required|digits:3',
                'autorizacion' => 'required',
                'fecha_autorizacion' => 'required|date',
            ]);

            $messages = array_merge($messages, [
                'sustento_tributario_id.required' => 'El sustento tributario es obligatorio.',
                'tipo_comprobante_id.required' => 'El tipo de comprobante es obligatorio.',
                'numero.required' => 'El número de comprobante es obligatorio.',
                'establecimiento.required' => 'El establecimiento es obligatorio.',
                'establecimiento.digits' => 'El establecimiento debe tener 3 dígitos.',
                'punto_emision.required' => 'El punto de emisión es obligatorio.',
                'punto_emision.digits' => 'El punto de emisión debe tener 3 dígitos.',
                'autorizacion.required' => 'La autorización es obligatoria.',
                'fecha_autorizacion.required' => 'La fecha de autorización es obligatoria.',
                'fecha_autorizacion.date' => 'La fecha de autorización no es válida.',
            ]);
        }

        $this->validate($rules, $messages);

        // =========================================
        // NORMALIZAR TOTALES
        // =========================================
        $this->valor = (float) ($this->valor ?? 0);
        $this->descuento = (float) ($this->descuento ?? 0);
        $this->subtotal_descuento = (float) ($this->subtotal_descuento ?? 0);
        $this->subtotal_exento = (float) ($this->subtotal_exento ?? 0);
        $this->suma_subtotal = (float) ($this->suma_subtotal ?? 0);
        $this->suma_iva = (float) ($this->suma_iva ?? 0);
        $this->total = (float) ($this->total ?? 0);

        // Si no hay subtotal calculado, usar el valor
        if ($this->subtotal_descuento <= 0) {
            $this->subtotal_descuento = $this->valor;
        }

        if ($this->total <= 0) {
            $this->total = $this->valor;
        }

        // =========================================
        // CREAR O EDITAR GASTO
        // =========================================
        if ($this->id_seleccionado > 0) {
            $gasto = Gastos::find($this->id_seleccionado);

            if (!$gasto) {
                $this->dispatchBrowserEvent('alertaGrande', [
                    'titulo' => 'Error',
                    'color' => 'danger',
                    'mensaje' => 'No se encontró el gasto.'
                ]);
                return;
            }
        } else {
            $gasto = new Gastos();
            $gasto->company_id = Auth::user()->company_id;
            $gasto->fecha_creacion = date('Y-m-d H:i:s');
            $gasto->user_responsable_id = Auth::user()->id;
        }

        // =========================================
        // DATOS GENERALES
        // =========================================
        $gasto->estado = $estado; // BORRADOR o PENDIENTE
        $gasto->tiene_impuestos = (int) $this->tiene_impuestos;
        $gasto->descripcion = strtoupper($this->descripcion ?? '');
        $gasto->gastos_categorias_id = $this->gastos_categorias_id;
        $gasto->proveedor_id = $this->proveedor_id;
        $gasto->valor = $this->valor;
        $gasto->descuento = $this->descuento;
        $gasto->subtotal_descuento = $this->subtotal_descuento;
        $gasto->subtotal_exento = $this->subtotal_exento;
        $gasto->suma_subtotal = $this->suma_subtotal;
        $gasto->suma_iva = $this->suma_iva;
        $gasto->total = $this->total;
        $gasto->fecha_emision = $this->fecha_emision;
        $gasto->pagado_compania_empleado = $this->pagado_compania_empleado ?? null;

        // =========================================
        // DATOS TRIBUTARIOS
        // =========================================
        if ((int) $this->tiene_impuestos === 1) {
            $gasto->sustento_tributario_id = $this->sustento_tributario_id;
            $gasto->tipo_comprobante_id = $this->tipo_comprobante_id;
            $gasto->numero = $this->numero;
            $gasto->establecimiento = $this->establecimiento;
            $gasto->punto_emision = $this->punto_emision;
            $gasto->autorizacion = $this->autorizacion;
            $gasto->fecha_autorizacion = $this->fecha_autorizacion;
        } else {
            // NO impuestos => limpiar tributarios
            $gasto->sustento_tributario_id = null;
            $gasto->tipo_comprobante_id = null;
            $gasto->numero = null;
            $gasto->establecimiento = null;
            $gasto->punto_emision = null;
            $gasto->autorizacion = null;
            $gasto->fecha_autorizacion = null;
        }

        $gasto->save();

        // guardar id para seguir trabajando sobre el mismo gasto
        $this->id_seleccionado = $gasto->id;

        // =========================================
        // RECONSTRUIR IMPUESTOS SOLO SI ES NECESARIO
        // =========================================
        // Caso 1: NO tiene impuestos => dejar solo 0%
        if ((int) $this->tiene_impuestos !== 1) {
            GastosImpuestos::where('gastos_id', $gasto->id)->delete();

            $impuestoCero = Impuestos::where('codigo', 0)->first();

            if ($impuestoCero) {
                $gastoImpuesto = new GastosImpuestos();
                $gastoImpuesto->gastos_id = $gasto->id;
                $gastoImpuesto->impuestos_id = $impuestoCero->id;
                $gastoImpuesto->subtotal = $this->valor;
                $gastoImpuesto->iva = 0;
                $gastoImpuesto->save();
            }
        } else {
            // Caso 2: SÍ tiene impuestos
            // Si todavía no existen registros de impuestos para este gasto, crearlos
            $existenImpuestos = GastosImpuestos::where('gastos_id', $gasto->id)->exists();

            if (!$existenImpuestos) {
                $impuestos = Impuestos::where('status', true)->get();

                foreach ($impuestos as $imp) {
                    $gastoImpuesto = new GastosImpuestos();
                    $gastoImpuesto->gastos_id = $gasto->id;
                    $gastoImpuesto->impuestos_id = $imp->id;
                    $gastoImpuesto->subtotal = 0;
                    $gastoImpuesto->iva = 0;
                    $gastoImpuesto->save();
                }

                $impuestoDefecto = Impuestos::where('status', true)
                    ->where('por_defecto', true)
                    ->first();

                if ($impuestoDefecto) {
                    $gastoDefecto = GastosImpuestos::where('gastos_id', $gasto->id)
                        ->where('impuestos_id', $impuestoDefecto->id)
                        ->first();

                    if ($gastoDefecto) {
                        $gastoDefecto->subtotal = $this->valor;
                        $gastoDefecto->iva = $this->valor * ($impuestoDefecto->valor / 100);
                        $gastoDefecto->save();
                    }
                }
            }
        }

        // =========================================
        // RECALCULAR TOTALES VISUALES
        // =========================================
        $this->calcular();

        // =========================================
        // MENSAJE
        // =========================================
        $mensaje = $estado === 'PENDIENTE'
            ? 'El gasto se envió a aprobación correctamente.'
            : 'El gasto se guardó en borrador correctamente.';

        $this->dispatchBrowserEvent('alerta', [
            'titulo' => 'Correcto',
            'color' => 'success',
            'mensaje' => $mensaje
        ]);

        $this->dispatchBrowserEvent('closeModal');
    }



    public function aumentarFP()
    {
        $rules = [
            'forma_pago_id' => 'required',
            'fp_valor' => 'required|numeric|min:0.01',
            'fecha_creacion' => 'required|date',
            'banco_id' => $this->forma_pago_id == 3 ? 'required' : 'nullable',
            'numero_comprobante' => $this->forma_pago_id == 3 ? 'required' : 'nullable',
        ];

        $this->validate($rules);

        try {
            DB::transaction(function () {
                $fechaPago = Carbon::parse($this->fecha_creacion)
                    ->setTimeFrom(now());

                $gastoFormaPago = new GastosFormaPagos();
                $gastoFormaPago->gasto_id = $this->id_seleccionado;
                $gastoFormaPago->forma_pago_id = $this->forma_pago_id;
                $gastoFormaPago->valor = $this->fp_valor;
                $gastoFormaPago->banco_id = $this->banco_id;
                $gastoFormaPago->numero_comprobante = $this->numero_comprobante;
                $gastoFormaPago->fecha_creacion = $fechaPago;
                $gastoFormaPago->save();

                // Verificar si esta pagada y cambiar de estado.
                $this->gastoVerificarValorPendiente($gastoFormaPago->gasto_id);

                $movimiento = $this->guardarTransaccion(
                    $this->id_seleccionado,
                    $gastoFormaPago->id,
                    $gastoFormaPago->forma_pago_id,
                    $fechaPago
                );

                $resultado = AsientosHeader::recalcularAsientos($movimiento->id, 'customer_movimientos');
                if (($resultado['code'] ?? null) !== '200') {
                    throw new \RuntimeException($resultado['msg'] ?? 'No se pudo crear el asiento contable del pago.');
                }
            });
        } catch (\Throwable $e) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => 'No se pudo registrar el pago',
                'color' => 'danger',
                'mensaje' => $e->getMessage(),
            ]);

            return;
        }

        $this->limpiarFP();

        $this->fp_valor = $this->pendientePago($this->id_seleccionado);

        $this->dispatchBrowserEvent('alerta', [
            'titulo' => 'Correcto',
            'color' => 'success',
            'mensaje' => 'La forma de pago y su asiento se registraron correctamente.',
        ]);
    }

    public function gastoVerificarValorPendiente($id)
    {
        $valorPendiente = $this->pendientePago($id);
        if ($valorPendiente <= 0) {
            $gasto = Gastos::find($id);
            $gasto->estado = 'PAGADO';
            $gasto->save();
        } else {
            $gasto = Gastos::find($id);
            $gasto->estado = 'APROBADO';
            $gasto->save();
        }
    }

    public function pendientePago($id)
    {
        $gasto = Gastos::find($id);

        if (!$gasto) {
            return 0;
        }

        $valorPendiente =
            $gasto->total
            - GastosFormaPagos::where('gasto_id', $id)->sum('valor')
            - GastosListaRetencion::where('gasto_id', $id)->sum('calculo');

        return $valorPendiente;
    }


    public function verTipoRetencion()
    {
        if ($this->lista_retencion_id !== "0") {
            $retencion = ListaRetencion::find($this->lista_retencion_id);
            if ($retencion->tipoRetencion->name == "FUENTE") {
                $impuestoRenta = GastosImpuestos::where('gastos_id', $this->id_seleccionado)->sum('subtotal');
                $this->ret_valor = $impuestoRenta;

                $this->ret_calculo = $impuestoRenta * ((float) $retencion->porcentaje / 100);
            } else {
                $impuestoIva = GastosImpuestos::where('gastos_id', $this->id_seleccionado)->sum('iva');
                $this->ret_valor = $impuestoIva;
                $this->ret_calculo = $impuestoIva * ((float) $retencion->porcentaje / 100);
            }
        }
    }

    public function aumentarRET()
    {
        $this->validate([
            'lista_retencion_id' => 'required',
            'ret_valor' => 'required',
            'ret_calculo' => 'required',
        ]);

        $gasto = new GastosListaRetencion();
        $gasto->gasto_id = $this->id_seleccionado;
        $gasto->lista_retencion_id = $this->lista_retencion_id;
        $gasto->valor = $this->ret_valor;
        $gasto->calculo = $this->ret_calculo;
        // $gasto->fecha_creacion = date('Y-m-d H:i:s');
        $gasto->save();
        $this->limpiarRET();
    }

    public function confirmarQuitarFP($id)
    {
        $fp = GastosFormaPagos::find($id);

        if (!$fp) {
            return;
        }

        $this->dispatchBrowserEvent('confirmarEliminarFormaPago', [
            'id' => $fp->id,
        ]);
    }

    public function quitarFormaPago($id)
    {
        $fp = GastosFormaPagos::find($id);

        if (!$fp) {
            return;
        }

        $gastoId = $fp->gasto_id;

        try {
            DB::transaction(function () use ($id, $fp) {
                $movimientos = CustomerMovimiento::where('gastos_forma_pagos_id', $id)->get();

                foreach ($movimientos as $movimiento) {
                    $asientos = AsientosHeader::where('company_id', Auth::user()->company_id)
                        ->where('customer_movimiento_id', $movimiento->id)
                        ->get();

                    foreach ($asientos as $asiento) {
                        AsientosDetalle::where('company_id', Auth::user()->company_id)
                            ->where('asientos_header_id', $asiento->id)
                            ->delete();
                        $asiento->delete();
                    }

                    $movimiento->delete();
                }

                $fp->delete();
            });
        } catch (\Throwable $e) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => 'No se pudo eliminar',
                'color' => 'danger',
                'mensaje' => $e->getMessage(),
            ]);

            return;
        }

        $this->gastoVerificarValorPendiente($gastoId);

        $this->limpiarFP();
        $this->fp_valor = $this->pendientePago($gastoId);

        $this->dispatchBrowserEvent('alerta', [
            'titulo' => 'Correcto',
            'color' => 'success',
            'mensaje' => 'La forma de pago y su asiento se eliminaron correctamente.',
        ]);
    }

    public function aumentarCuenta($id)
    {
        $this->validate([
            'gastos_plan_cuentas_id' => 'required',
            'gastos_centro_costos_id' => 'required',
            'gastos_detalle_valor' => 'required|numeric|min:0.01',
        ], [
            'gastos_plan_cuentas_id.required' => 'Debe seleccionar una cuenta contable.',
            'gastos_centro_costos_id.required' => 'Debe seleccionar un centro de costos.',
            'gastos_detalle_valor.required' => 'Debe ingresar un valor.',
            'gastos_detalle_valor.numeric' => 'El valor debe ser numérico.',
            'gastos_detalle_valor.min' => 'El valor debe ser mayor a 0.',
        ]);

        // ============================================
        // BUSCAR EL GASTO
        // ============================================
        $gasto = Gastos::find($id);

        if (!$gasto) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => 'Error',
                'color' => 'danger',
                'mensaje' => 'No se encontró el gasto.'
            ]);
            return;
        }

        // ============================================
        // VALOR FIJO DEL GASTO
        // ============================================
        $valorMaximoGasto = (float) ($gasto->subtotal_descuento ?? $gasto->valor ?? 0);

        // VALOR QUE QUIERES AGREGAR EN LA NUEVA CUENTA
        $valorNuevaCuenta = (float) $this->gastos_detalle_valor;

        // ============================================
        // SUMA ACTUAL DE CUENTAS YA INGRESADAS
        // ============================================
        $totalActual = (float) GastosPlanCuentas::where('gasto_id', $id)->sum('valor');
        $nuevoTotal = $totalActual + $valorNuevaCuenta;

        // ============================================
        // VALIDAR QUE NO SUPERE EL VALOR DEL GASTO
        // ============================================
        if ($nuevoTotal > $valorMaximoGasto) {
            $disponible = $valorMaximoGasto - $totalActual;

            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => 'Valor excedido',
                'color' => 'warning',
                'mensaje' => 'No puede superar el valor total del gasto. Disponible: $' . number_format(max($disponible, 0), 2, '.', ',')
            ]);
            return;
        }

        // ============================================
        // GUARDAR CUENTA
        // ============================================
        $detalle = new GastosPlanCuentas();
        $detalle->company_id = Auth::user()->company_id;
        $detalle->gasto_id = $id;
        $detalle->plan_cuentas_id = $this->gastos_plan_cuentas_id;
        $detalle->centro_costos_id = $this->gastos_centro_costos_id;
        $detalle->valor = $valorNuevaCuenta;
        $detalle->save();

        // ============================================
        // RECALCULAR TOTAL DE CUENTAS
        // ============================================
        $this->valorTotalCuentas = (float) GastosPlanCuentas::where('gasto_id', $id)->sum('valor');
        $this->gastosTotales = $this->valorTotalCuentas;

        // ============================================
        // EL VALOR DEL GASTO SE MANTIENE FIJO
        // ============================================
        $this->gastos_valor = $valorMaximoGasto;

        // ============================================
        // LIMPIAR SOLO CAMPOS DE NUEVA CUENTA
        // ============================================
        $this->gastos_plan_cuentas_id = null;
        $this->gastos_centro_costos_id = null;
        $this->gastos_detalle_valor = null;
    }

    public function quitarRET($id)
    {
        $gasto = GastosListaRetencion::find($id)->delete();
        $this->limpiarRET();
    }

    private function limpiarFP()
    {
        $this->reset(['forma_pago_id', 'banco_id', 'numero_comprobante',]);

        $this->mostrarTransferencia = false;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    private function limpiarRET()
    {
        $this->reset(['lista_retencion_id', 'ret_valor', 'ret_calculo']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function eliminarCuenta($id)
    {
        $cuenta = GastosPlanCuentas::find($id);

        if (!$cuenta) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => 'Error',
                'color' => 'danger',
                'mensaje' => 'No se encontró la cuenta contable.'
            ]);
            return;
        }

        $gastoId = $cuenta->gasto_id;

        // ============================================
        // ELIMINAR LA CUENTA
        // ============================================
        $cuenta->delete();

        // ============================================
        // RECALCULAR TOTAL DE CUENTAS
        // ============================================
        $this->valorTotalCuentas = (float) GastosPlanCuentas::where('gasto_id', $gastoId)->sum('valor');
        $this->gastosTotales = $this->valorTotalCuentas;

        // ============================================
        // MANTENER EL VALOR DEL GASTO FIJO
        // ============================================
        $gasto = Gastos::find($gastoId);

        if ($gasto) {
            $this->gastos_valor = (float) ($gasto->subtotal_descuento ?? $gasto->valor ?? 0);
        }

        // ============================================
        // LIMPIAR CAMPOS DE NUEVA CUENTA
        // ============================================
        $this->gastos_plan_cuentas_id = null;
        $this->gastos_centro_costos_id = null;
        $this->gastos_detalle_valor = null;

        $this->dispatchBrowserEvent('alerta', [
            'titulo' => 'Correcto',
            'color' => 'success',
            'mensaje' => 'Cuenta eliminada correctamente.'
        ]);
    }

    public function confirmarBorrarGasto($id)
    {
        $boveda = Gastos::find($id);
        $data = [
            'id' => $id
        ];
        $this->dispatchBrowserEvent('notificarEliminar', $data);
    }

    public function elminarGasto($id)
    {
        GastosPlanCuentas::where('gasto_id', $id)->delete();
        GastosImpuestos::where('gastos_id', $id)->delete();
        Gastos::find($id)->delete();
    }



    public function configurarImpuestos($estado = null)
    {
        // ============================
        // ASIGNAR SI TIENE IMPUESTOS
        // ============================
        if ($estado !== null) {
            $this->tiene_impuestos = (int) $estado;
        } else {
            $this->tiene_impuestos = null;
        }

        // ============================
        // VALOR BASE DEL GASTO
        // ============================
        $valorBase = (float) ($this->valor ?? 0);

        // si estás creando y el valor se llena desde otro input, también lo tomamos
        if ($valorBase <= 0 && !empty($this->subtotal_descuento)) {
            $valorBase = (float) $this->subtotal_descuento;
        }

        // ============================
        // REINICIAR VALORES
        // ============================
        $this->descuento = 0;
        $this->subtotal_exento = 0;
        $this->subtotal_descuento = $valorBase;
        $this->suma_subtotal = $valorBase;
        $this->suma_iva = 0;
        $this->total = $valorBase;

        // ============================
        // SI NO TIENE IMPUESTOS
        // ============================
        if ((int) $this->tiene_impuestos !== 1) {
            $this->sustento_tributario_id = null;
            $this->tipo_comprobante_id = null;
            $this->numero = null;
            $this->establecimiento = null;
            $this->punto_emision = null;
            $this->autorizacion = null;
            $this->fecha_autorizacion = null;

            // vaciar lista visual
            $this->gastosImpuestos = collect();

            $this->suma_subtotal = 0;
            $this->suma_iva = 0;
            $this->total = $valorBase;

            // si es edición también actualiza BD
            if ($this->id_seleccionado > 0) {
                $gasto = Gastos::find($this->id_seleccionado);

                if ($gasto) {
                    $gasto->tiene_impuestos = 0;
                    $gasto->descuento = 0;
                    $gasto->subtotal_descuento = $valorBase;
                    $gasto->subtotal_exento = 0;
                    $gasto->suma_subtotal = 0;
                    $gasto->suma_iva = 0;
                    $gasto->total = $valorBase;

                    $gasto->sustento_tributario_id = null;
                    $gasto->tipo_comprobante_id = null;
                    $gasto->numero = null;
                    $gasto->establecimiento = null;
                    $gasto->punto_emision = null;
                    $gasto->autorizacion = null;
                    $gasto->fecha_autorizacion = null;
                    $gasto->save();

                    GastosImpuestos::where('gastos_id', $gasto->id)->delete();

                    $impuestoCero = Impuestos::where('codigo', 0)->first();
                    if ($impuestoCero) {
                        $detalle = new GastosImpuestos();
                        $detalle->gastos_id = $gasto->id;
                        $detalle->impuestos_id = $impuestoCero->id;
                        $detalle->subtotal = $valorBase;
                        $detalle->iva = 0;
                        $detalle->save();
                    }
                }
            }

            return;
        }

        // ======================================================
        // DESDE AQUÍ: SÍ TIENE IMPUESTOS
        // ======================================================

        $impuestos = Impuestos::where('status', true)->get();

        // ==========================================
        // CASO 1: EDICIÓN DE GASTO YA GUARDADO
        // ==========================================
        if ($this->id_seleccionado > 0) {

            $gasto = Gastos::find($this->id_seleccionado);

            if ($gasto) {
                $gasto->tiene_impuestos = 1;
                $gasto->descripcion = $this->descripcion;
                $gasto->gastos_categorias_id = $this->gastos_categorias_id ?? null;
                $gasto->proveedor_id = $this->proveedor_id ?? null;
                $gasto->valor = $valorBase;
                $gasto->fecha_emision = $this->fecha_emision ?? date('Y-m-d');
                $gasto->descuento = $this->descuento ?? 0;
                $gasto->subtotal_descuento = $this->subtotal_descuento ?? 0;
                $gasto->subtotal_exento = $this->subtotal_exento ?? 0;
                $gasto->suma_subtotal = $this->suma_subtotal ?? 0;
                $gasto->suma_iva = $this->suma_iva ?? 0;
                $gasto->total = $this->total ?? 0;
                $gasto->save();

                // borrar impuestos anteriores
                GastosImpuestos::where('gastos_id', $gasto->id)->delete();

                // crear nuevos detalles
                foreach ($impuestos as $imp) {
                    $detalle = new GastosImpuestos();
                    $detalle->gastos_id = $gasto->id;
                    $detalle->impuestos_id = $imp->id;
                    $detalle->subtotal = 0;
                    $detalle->iva = 0;
                    $detalle->save();
                }

                // impuesto por defecto
                $impuestoDefecto = $impuestos->firstWhere('por_defecto', true);

                if ($impuestoDefecto) {
                    $detalleDefecto = GastosImpuestos::where('gastos_id', $gasto->id)
                        ->where('impuestos_id', $impuestoDefecto->id)
                        ->first();

                    if ($detalleDefecto) {
                        $detalleDefecto->subtotal = $valorBase;
                        $detalleDefecto->iva = $valorBase * ((float) $impuestoDefecto->valor / 100);
                        $detalleDefecto->save();
                    }
                }

                // recargar desde BD con relación impuestos
                $this->gastosImpuestos = GastosImpuestos::with('impuestos')
                    ->where('gastos_id', $gasto->id)
                    ->get();

                $this->calcular();
                return;
            }
        }

        // ==========================================
        // CASO 2: GASTO NUEVO (AÚN NO GUARDADO)
        // ==========================================
        $coleccion = collect();

        foreach ($impuestos as $imp) {
            $subtotal = 0;
            $iva = 0;

            if ((int) $imp->por_defecto === 1) {
                $subtotal = $valorBase;
                $iva = $valorBase * ((float) $imp->valor / 100);
            }

            $coleccion->push((object) [
                'id' => $imp->id, // id temporal
                'gastos_id' => 0,
                'impuestos_id' => $imp->id,
                'subtotal' => $subtotal,
                'iva' => $iva,
                'valor' => (float) $imp->valor, // importante para la vista
                'impuestos' => (object) [
                    'id' => $imp->id,
                    'valor' => (float) $imp->valor,
                    'codigo' => $imp->codigo ?? null,
                    'por_defecto' => $imp->por_defecto ?? 0,
                ],
            ]);
        }

        $this->gastosImpuestos = $coleccion;

        // recalcular visual
        $this->calcular();
    }



    public function cambioSubtotal($id, $valor)
    {
        $valor = (float) ($valor ?: 0);

        // =========================================================
        // GASTO EXISTENTE (YA GUARDADO)
        // =========================================================
        if ($this->id_seleccionado > 0) {

            $detalle = GastosImpuestos::with('impuestos')->find($id);

            if (!$detalle) {
                return;
            }

            $detalle->subtotal = $valor;

            if ((int) $this->tiene_impuestos === 1 && $detalle->impuestos) {
                $detalle->iva = $valor * ((float) $detalle->impuestos->valor / 100);
            } else {
                $detalle->iva = 0;
            }

            $detalle->save();

            $this->calcular();
            return;
        }

        // =========================================================
        // GASTO NUEVO (SIN GUARDAR)
        // =========================================================
        $coleccion = collect($this->gastosImpuestos)->map(function ($imp) {
            return is_array($imp) ? (object) $imp : $imp;
        });

        $subtotalDisponible = (float) $this->subtotal_descuento - (float) $this->subtotal_exento;
        if ($subtotalDisponible < 0) {
            $subtotalDisponible = 0;
        }

        // calcular total de otros impuestos (sin contar el que se está editando)
        $totalOtros = 0;
        foreach ($coleccion as $imp) {
            if ((int) $imp->id !== (int) $id) {
                $totalOtros += (float) ($imp->subtotal ?? 0);
            }
        }

        // no dejar que exceda el subtotal disponible
        $maximoPermitido = $subtotalDisponible - $totalOtros;
        if ($maximoPermitido < 0) {
            $maximoPermitido = 0;
        }

        if ($valor > $maximoPermitido) {
            $valor = $maximoPermitido;
        }

        $coleccion = $coleccion->map(function ($imp) use ($id, $valor) {
            if ((int) $imp->id === (int) $id) {

                $porcentaje = 0;
                if (isset($imp->impuestos) && isset($imp->impuestos->valor)) {
                    $porcentaje = (float) $imp->impuestos->valor;
                } elseif (isset($imp->valor)) {
                    $porcentaje = (float) $imp->valor;
                }

                $imp->subtotal = $valor;
                $imp->iva = $valor * ($porcentaje / 100);
            }

            return $imp;
        });

        $this->gastosImpuestos = $coleccion;

        $this->calcular();
    }



    public function calcular()
    {
        $this->valor = (float) ($this->valor ?? 0);
        $this->descuento = (float) ($this->descuento ?? 0);
        $this->subtotal_exento = (float) ($this->subtotal_exento ?? 0);

        // ==========================================
        // BASE DEL GASTO
        // subtotal_descuento = valor - descuento
        // ==========================================
        $this->subtotal_descuento = $this->valor - $this->descuento;

        if ($this->subtotal_descuento < 0) {
            $this->subtotal_descuento = 0;
        }

        // =========================================================
        // CASO 1: GASTO EXISTENTE (YA GUARDADO EN BD)
        // =========================================================
        if ($this->id_seleccionado > 0) {

            $gasto = Gastos::find($this->id_seleccionado);

            if (!$gasto) {
                return;
            }

            // ------------------------------------------
            // SIN IMPUESTOS
            // ------------------------------------------
            if ((int) $this->tiene_impuestos !== 1) {
                $this->suma_subtotal = $this->subtotal_descuento;
                $this->suma_iva = 0;
                $this->total = $this->suma_subtotal;

                $gasto->descuento = $this->descuento;
                $gasto->subtotal_exento = 0;
                $gasto->subtotal_descuento = $this->subtotal_descuento;
                $gasto->suma_subtotal = $this->suma_subtotal;
                $gasto->suma_iva = 0;
                $gasto->total = $this->total;
                $gasto->save();

                return;
            }

            // ------------------------------------------
            // CON IMPUESTOS
            // ------------------------------------------
            $detalles = GastosImpuestos::with('impuestos')
                ->where('gastos_id', $this->id_seleccionado)
                ->get();

            $sumaSubtotalesImpuestos = (float) $detalles->sum('subtotal');
            $sumaIva = (float) $detalles->sum('iva');

            // subtotal exento + subtotales de impuestos
            $this->suma_subtotal = $this->subtotal_exento + $sumaSubtotalesImpuestos;
            $this->suma_iva = $sumaIva;
            $this->total = $this->suma_subtotal + $this->suma_iva;

            // guardar en gasto
            $gasto->descuento = $this->descuento;
            $gasto->subtotal_exento = $this->subtotal_exento;
            $gasto->subtotal_descuento = $this->subtotal_descuento;
            $gasto->suma_subtotal = $this->suma_subtotal;
            $gasto->suma_iva = $this->suma_iva;
            $gasto->total = $this->total;
            $gasto->save();

            // refrescar colección visual
            $this->gastosImpuestos = $detalles;

            return;
        }

        // =========================================================
        // CASO 2: GASTO NUEVO (SIN GUARDAR TODAVÍA)
        // =========================================================

        // ------------------------------------------
        // SIN IMPUESTOS
        // ------------------------------------------
        if ((int) $this->tiene_impuestos !== 1) {
            $this->suma_subtotal = $this->subtotal_descuento;
            $this->suma_iva = 0;
            $this->total = $this->suma_subtotal;
            return;
        }

        // ------------------------------------------
        // CON IMPUESTOS (EN MEMORIA)
        // ------------------------------------------
        $subtotalDisponible = $this->subtotal_descuento - $this->subtotal_exento;

        if ($subtotalDisponible < 0) {
            $subtotalDisponible = 0;
        }

        $sumaSubtotalesImpuestos = 0;
        $sumaIva = 0;

        $coleccion = collect($this->gastosImpuestos)->map(function ($imp) {
            return is_array($imp) ? (object) $imp : $imp;
        });

        // cuánto hay ya asignado a impuestos
        $totalAsignadoImpuestos = (float) $coleccion->sum(function ($imp) {
            return (float) ($imp->subtotal ?? 0);
        });

        // si no hay nada asignado todavía, cargar el subtotal al impuesto por defecto
        if ($totalAsignadoImpuestos <= 0) {
            $coleccion = $coleccion->map(function ($imp) use ($subtotalDisponible) {
                $porDefecto = 0;

                if (isset($imp->impuestos) && isset($imp->impuestos->por_defecto)) {
                    $porDefecto = (int) $imp->impuestos->por_defecto;
                }

                if ($porDefecto === 1) {
                    $porcentaje = isset($imp->impuestos->valor) ? (float) $imp->impuestos->valor : (float) ($imp->valor ?? 0);
                    $imp->subtotal = $subtotalDisponible;
                    $imp->iva = $subtotalDisponible * ($porcentaje / 100);
                } else {
                    $imp->subtotal = 0;
                    $imp->iva = 0;
                }

                return $imp;
            });
        } else {
            // recalcular IVA de todos los subtotales ya ingresados
            $coleccion = $coleccion->map(function ($imp) {
                $subtotal = (float) ($imp->subtotal ?? 0);
                $porcentaje = 0;

                if (isset($imp->impuestos) && isset($imp->impuestos->valor)) {
                    $porcentaje = (float) $imp->impuestos->valor;
                } elseif (isset($imp->valor)) {
                    $porcentaje = (float) $imp->valor;
                }

                $imp->iva = $subtotal * ($porcentaje / 100);
                return $imp;
            });
        }

        $sumaSubtotalesImpuestos = (float) $coleccion->sum(function ($imp) {
            return (float) ($imp->subtotal ?? 0);
        });

        $sumaIva = (float) $coleccion->sum(function ($imp) {
            return (float) ($imp->iva ?? 0);
        });

        $this->gastosImpuestos = $coleccion;
        $this->suma_subtotal = $this->subtotal_exento + $sumaSubtotalesImpuestos;
        $this->suma_iva = $sumaIva;
        $this->total = $this->suma_subtotal + $this->suma_iva;
    }

    public function solicitarAprobacion()
    {
        // ============================================
        // VALIDACIÓN SOLO DE CREACIÓN / EDICIÓN
        // NO VALIDAR CAMPOS TRIBUTARIOS DE APROBACIÓN
        // ============================================
        $this->validate([
            'proveedor_id' => 'required',
            'gastos_categorias_id' => 'required',
            'valor' => 'required|numeric|min:0.01',
            'fecha_emision' => 'required|date',
            'tiene_impuestos' => 'required',
        ], [
            'proveedor_id.required' => 'El proveedor es obligatorio.',
            'gastos_categorias_id.required' => 'La categoría es obligatoria.',
            'valor.required' => 'El valor es obligatorio.',
            'valor.numeric' => 'El valor debe ser numérico.',
            'valor.min' => 'El valor debe ser mayor a 0.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'fecha_emision.date' => 'La fecha de emisión no es válida.',
            'tiene_impuestos.required' => 'Debe seleccionar si el gasto tiene impuestos o no.',
        ]);

        // ============================================
        // BUSCAR O CREAR GASTO
        // ============================================
        if ($this->id_seleccionado > 0) {
            $gasto = Gastos::find($this->id_seleccionado);

            if (!$gasto) {
                $this->dispatchBrowserEvent('alertaGrande', [
                    'titulo' => 'Error',
                    'color' => 'danger',
                    'mensaje' => 'No se encontró el gasto.'
                ]);
                return;
            }
        } else {
            $gasto = new Gastos();
            $gasto->company_id = Auth::user()->company_id;
            $gasto->fecha_creacion = date('Y-m-d H:i:s');
            $gasto->user_responsable_id = Auth::user()->id;
        }

        // ============================================
        // RESETEAR CÁLCULOS BASE
        // ============================================
        $valor = (float) ($this->valor ?? 0);

        $this->descuento = 0;
        $this->subtotal_exento = 0;
        $this->subtotal_descuento = $valor;
        $this->suma_subtotal = $valor;
        $this->suma_iva = 0;
        $this->total = $valor;

        // ============================================
        // GUARDAR DATOS DEL GASTO EN PENDIENTE
        // ============================================
        $gasto->estado = 'PENDIENTE';
        $gasto->tiene_impuestos = (int) $this->tiene_impuestos;
        $gasto->descripcion = $this->descripcion;
        $gasto->gastos_categorias_id = $this->gastos_categorias_id;
        $gasto->proveedor_id = $this->proveedor_id;
        $gasto->valor = $valor;
        $gasto->descuento = 0;
        $gasto->subtotal_descuento = $valor;
        $gasto->subtotal_exento = 0;
        $gasto->suma_subtotal = $valor;
        $gasto->suma_iva = 0;
        $gasto->total = $valor;
        $gasto->fecha_emision = $this->fecha_emision;

        // Si no tiene impuestos, limpiar campos tributarios
        if ((int) $this->tiene_impuestos !== 1) {
            $gasto->sustento_tributario_id = null;
            $gasto->tipo_comprobante_id = null;
            $gasto->numero = null;
            $gasto->establecimiento = null;
            $gasto->punto_emision = null;
            $gasto->autorizacion = null;
            $gasto->fecha_autorizacion = null;
        }

        $gasto->save();
        $this->id_seleccionado = $gasto->id;

        // ============================================
        // RECREAR IMPUESTOS SEGÚN SELECCIÓN
        // ============================================
        GastosImpuestos::where('gastos_id', $gasto->id)->delete();

        if ((int) $this->tiene_impuestos === 1) {
            // Crear todos los impuestos activos
            $impuestos = Impuestos::where('status', true)->get();

            foreach ($impuestos as $imp) {
                $gastoImpuesto = new GastosImpuestos();
                $gastoImpuesto->gastos_id = $gasto->id;
                $gastoImpuesto->impuestos_id = $imp->id;
                $gastoImpuesto->subtotal = 0;
                $gastoImpuesto->iva = 0;
                $gastoImpuesto->save();
            }

            // Asignar el valor al impuesto por defecto
            $impuestoDefecto = Impuestos::where('status', true)
                ->where('por_defecto', true)
                ->first();

            if ($impuestoDefecto) {
                $gastoDefecto = GastosImpuestos::where('gastos_id', $gasto->id)
                    ->where('impuestos_id', $impuestoDefecto->id)
                    ->first();

                if ($gastoDefecto) {
                    $gastoDefecto->subtotal = $valor;
                    $gastoDefecto->iva = $valor * ($impuestoDefecto->valor / 100);
                    $gastoDefecto->save();
                }
            }

        } else {
            // Dejar solo impuesto 0%
            $impuestoCero = Impuestos::where('codigo', 0)->first();

            if ($impuestoCero) {
                $gastoImpuesto = new GastosImpuestos();
                $gastoImpuesto->gastos_id = $gasto->id;
                $gastoImpuesto->impuestos_id = $impuestoCero->id;
                $gastoImpuesto->subtotal = $valor;
                $gastoImpuesto->iva = 0;
                $gastoImpuesto->save();
            }
        }

        // Recalcular totales visuales
        $this->calcular();

        $this->dispatchBrowserEvent('closeModal');
        $this->dispatchBrowserEvent('alerta', [
            'titulo' => 'Correcto',
            'color' => 'success',
            'mensaje' => 'El gasto fue enviado a aprobación en estado PENDIENTE.'
        ]);
    }

    public function updatedXmlFile()
    {
        $this->procesarXML();
    }

    public function procesarXML()
    {
        if (!$this->xmlFile) {
            return;
        }

        $this->validate([
            'xmlFile' => 'required|mimes:xml|max:2048', // Valida que sea XML
        ]);

        try {


            $path = $this->xmlFile->getRealPath();
            $xmlContent = file_get_contents($path);
            $xml = new SimpleXMLElement($xmlContent);


            // Extraer datos principales
            $this->xmlData = [
                'estado' => (string) $xml->estado,
                'numeroAutorizacion' => (string) $xml->numeroAutorizacion,
                'fechaAutorizacion' => date("Y-m-d", strtotime((string) $xml->fechaAutorizacion)),
                'ambiente' => (string) $xml->ambiente,
            ];

            // Procesar contenido dentro de <comprobante> (CDDATA)
            if (!empty($xml->comprobante)) {

                $comprobanteXML = new SimpleXMLElement($xml->comprobante);

                $htmlXml = '
                
                <div class="row">
                                <div class="col-12">
                                <h4>
                                    <i class="fas fa-globe"></i>' . (string) $comprobanteXML->infoTributaria->nombreComercial
                    . '<small class="float-right">Fecha Emisión:' . (string) $comprobanteXML->infoFactura->fechaEmision . '</small>
                                </h4>
                                </div>
                            </div>
                
                ' .
                    '
                <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                De:
                                <address>
                                    <strong>' . (string) $comprobanteXML->infoTributaria->nombreComercial . '</strong><br>
                                    <i class="fa fa-map-marker" aria-hidden="true"></i>' . (string) $comprobanteXML->infoTributaria->dirMatriz . '<br>
                                    <b>Ruc:</b> <i>' . (string) $comprobanteXML->infoTributaria->ruc . '</i><br>
                                    <b>Estado:</b>' . (string) $xml->estado . '<br>
                                    <b>Ambiente:</b>' . (string) $xml->ambiente . '<br>
                                    <b>Obligado:</b>' . (string) $comprobanteXML->infoFactura->obligadoContabilidad . '<br>
                                </address>
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                Para:
                                <address>
                                    <strong>' . (string) $comprobanteXML->infoFactura->razonSocialComprador . '</strong><br>
                                    <i class="fa fa-map-marker" aria-hidden="true"></i>' . (string) $comprobanteXML->infoFactura->direccionComprador . '<br>
                                    <b>Identificación:</b> <i>' . (string) $comprobanteXML->infoFactura->identificacionComprador . '</i><br>
                                    <b>Fecha Emisión:</b>' . (string) $comprobanteXML->infoFactura->fechaEmision . '<br>
                                </address>
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                <b>Factura #' . (string) $comprobanteXML->infoTributaria->estab . '-' . (string) $comprobanteXML->infoTributaria->ptoEmi . '-' . (string) $comprobanteXML->infoTributaria->secuencial . '</b><br>
                                <br>
                                <b>Establecimiento:</b>' . (string) $comprobanteXML->infoTributaria->estab . '<br>
                                <b>Punto Emision:</b>' . (string) $comprobanteXML->infoTributaria->ptoEmi . '<br>
                                <b>Secuencia:</b>' . (string) $comprobanteXML->infoTributaria->secuencial . '<br>
                                <b>Autorización:</b> <i>' . (string) $comprobanteXML->infoTributaria->claveAcceso . '</i><br>
                                </div>
                                <!-- /.col -->
                            </div>               
                
                
                ' .
                    '
                
                <div class="row">
                                <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Cantidad</th>
                                            <th>Codigo Principal</th>
                                            <th>Código Auxiliar</th>
                                            <th>Descripción</th>
                                            <th>Precio Unitario</th>
                                            <th>Descuento</th>
                                            <th>Precio Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

                foreach ($comprobanteXML->detalles->detalle as $detalle) {
                    $htmlXml .= '
                <tr>
                    <td>' . (float) $detalle->cantidad . '</td>
                    <td>' . (string) $detalle->codigoPrincipal . '</td>
                    <td>' . (string) $detalle->codigoAuxiliar . '</td>
                    <td>' . (string) $detalle->descripcion . '</td>
                    <td>' . (float) $detalle->precioUnitario . '</td>
                    <td>' . (float) $detalle->descuento . '</td>
                    <td>' . (float) $detalle->precioTotalSinImpuesto . '</td>
                </tr>';
                }

                $htmlXml .= '
                                    </tbody>
                                </table>
                    </div>
                </div>';


                $arrayDesgloseimpuestos = array();
                foreach ($comprobanteXML->infoFactura->totalConImpuestos->totalImpuesto as $totalImpuesto) {
                    $arrayDesgloseimpuestos['impuestos'][] = [
                        'impuesto_id' => intval(Impuestos::where('codigo', (string) $totalImpuesto->codigoPorcentaje)->first()->id),
                        'porcentaje' => intval(Impuestos::where('codigo', (string) $totalImpuesto->codigoPorcentaje)->first()->valor),
                        'codigoPorcentaje' => (string) $totalImpuesto->codigoPorcentaje,
                        'baseImponible' => (float) $totalImpuesto->baseImponible,
                        'valor' => (float) $totalImpuesto->valor,
                    ];
                }

                $this->xmlDataimpuestos = $arrayDesgloseimpuestos['impuestos'];



                // Formas de pago
                $arrayDesglose = array();
                foreach ($comprobanteXML->infoFactura->pagos->pago as $pagos) {
                    $arrayDesglose['pagos'][] = [
                        'formaPago' => FormasPago::where('code', $pagos->formaPago)->first()->nombre ?? $pagos->formaPago,
                        'total' => (float) $pagos->total,
                        'plazo' => (string) $pagos->plazo,
                    ];
                }

                $this->htmlXml = $htmlXml;


                $htmlXmlTabla1 = '
                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Forma de Pago</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <thead>';
                foreach ($arrayDesglose['pagos'] as $pagos) {

                    $htmlXmlTabla1 .= '
                                            <tr>
                                                <th>' . $pagos['formaPago'] . '</th>
                                                <th>' . $pagos['total'] . '</th>
                                            </tr>';
                }
                '</thead>
                                    </tbody>
                                </table>';
                $this->htmlXmlTabla1 = $htmlXmlTabla1;
                $htmlXmlTabla2 = '
                <table class="table">
                                        <tbody>
                                            <tr>
                                                <th style="width:50%">Subtotal:</th>
                                                <td>$ ' . (string) $comprobanteXML->infoFactura->totalSinImpuestos . '</td>
                                            </tr>
                                            <tr>
                                                <th>Descuento</th>
                                                <td>$ ' . (string) $comprobanteXML->infoFactura->totalDescuento . '</td>
                                            </tr>';
                foreach ($arrayDesgloseimpuestos['impuestos'] as $totalImpuesto) {

                    $htmlXmlTabla2 .= ' <tr>
                                                <th>Subtotal ' . $totalImpuesto['porcentaje'] . '%:</th>
                                                <td>$ ' . $totalImpuesto['baseImponible'] . ' </td>
                                            </tr>
                                            <tr>
                                                <th>Iva ' . $totalImpuesto['porcentaje'] . '%:</th>
                                                <td>$' . $totalImpuesto['valor'] . ' </td>
                                            </tr>';
                }


                $htmlXmlTabla2 .= '<tr>
                                                <th>Propina:</th>
                                                <td>$ ' . (string) $comprobanteXML->infoFactura->propina . '</td>
                                            </tr>
                                            <tr>
                                                <th>Total:</th>
                                                <td>$ ' . (string) $comprobanteXML->infoFactura->importeTotal . '</td>
                                            </tr>
                                        </tbody>
                                    </table>
                ';



                $this->htmlXmlTabla2 = $htmlXmlTabla2;
                //variables para guardar en la base de datos
                // Info proveedor
                $this->xmlData['ruc'] = (string) $comprobanteXML->infoTributaria->ruc;
                $this->xmlData['razonSocial'] = (string) $comprobanteXML->infoTributaria->razonSocial;
                $this->xmlData['nombreComercial'] = (string) $comprobanteXML->infoTributaria->nombreComercial;
                $this->xmlData['direccion'] = (string) $comprobanteXML->infoTributaria->dirMatriz;
                $this->xmlData['secuencial'] = (string) $comprobanteXML->infoTributaria->secuencial;
                $this->xmlData['estab'] = (string) $comprobanteXML->infoTributaria->estab;
                $this->xmlData['ptoEmi'] = (string) $comprobanteXML->infoTributaria->ptoEmi;
                // Subtotales
                $this->xmlData['totalSinImpuestos'] = (string) $comprobanteXML->infoFactura->totalSinImpuestos;
                $this->xmlData['totalDescuento'] = (string) $comprobanteXML->infoFactura->totalDescuento;
                $this->xmlData['importeTotal'] = (string) $comprobanteXML->infoFactura->importeTotal;

                $this->xmlData['claveAcceso'] = (string) $comprobanteXML->infoTributaria->claveAcceso;
                //dd($this->xmlData);

            }
        } catch (\Exception $e) {
            $this->xmlData = ['error' => 'Error al procesar el XML'];
        }
    }


    public function guardarGastos()
    {
        if (Gastos::where('autorizacion', $this->xmlData['claveAcceso'])->exists()) {
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => 'Alerta',
                'color' => 'danger',
                'mensaje' => 'Esta factura ya esta ingresada...'
            ]);
            return;
        }

        // Guardar proveedores
        if (Proveedores::where('ruc', $this->xmlData['ruc'])->exists()) {
            $proveedores = Proveedores::where('ruc', $this->xmlData['ruc'])->first();
        } else {
            $proveedores = new Proveedores();
            $proveedores->company_id = Auth::user()->company_id;

            // Cuenta gasto configuracion 
            $typeTransaction = TypeTransaction::where('name_corto', 'GAS')->first();
            $configHeader = ConfigPlanHeader::where('type_transaction_id', $typeTransaction->id)->first();
            $configDetalle = ConfigPlanDetalle::where('config_plan_header_id', $configHeader->id)->where('debe_haber', 1)->first();
            $plan_cuenta_id = $configDetalle->plan_cuentas_id;
            $centro_costos_id = SedesCentroCostos::where('sede_id', Auth::user()->sede_id)->first()->centro_costos_id ?? null;

            $proveedores->nombre = strtoupper($this->xmlData['razonSocial']);
            $proveedores->descripcion = strtoupper($this->xmlData['nombreComercial']);
            $proveedores->ruc = $this->xmlData['ruc'];
            $proveedores->direccion = $this->xmlData['direccion'];
            $proveedores->telefono = '0999999999';
            $proveedores->email = 'sincorreo@gmail.com';
            $proveedores->plan_cuenta_id = $plan_cuenta_id;
            $proveedores->centro_costos_id = $centro_costos_id;
            $proveedores->status = true;
            $proveedores->save();
        }


        //Guardar gasto
        $gasto = new Gastos();
        $gasto->company_id = Auth::user()->company_id;
        $gasto->fecha_creacion = date('Y-m-d H:i:s');
        $gasto->user_responsable_id = Auth::user()->id;
        $gasto->estado = 'PENDIENTE';
        $gasto->sustento_tributario_id = 3; // Quemado
        $gasto->tipo_comprobante_id = 1; // Quemado
        $gasto->numero = $this->xmlData['secuencial'];
        $gasto->establecimiento = $this->xmlData['estab'];
        $gasto->punto_emision = $this->xmlData['ptoEmi'];
        $gasto->autorizacion = $this->xmlData['claveAcceso'];

        $gasto->fecha_autorizacion = $this->xmlData['fechaAutorizacion'];
        $gasto->fecha_aprobacion = date('Y-m-d H:i:s');
        $gasto->user_aprueba_id = Auth::user()->id;
        $gasto->razon_rechaza = 'CARGA POR XML';

        $gasto->tiene_impuestos = true;
        $gasto->descripcion = 'CARGA POR XML';
        $gasto->gastos_categorias_id = 1; // Quemado
        $gasto->proveedor_id = $proveedores->id;
        $gasto->valor = $this->xmlData['totalSinImpuestos'];
        $gasto->descuento = $this->xmlData['totalDescuento'];
        $gasto->subtotal_descuento = $this->xmlData['totalSinImpuestos'] - $this->xmlData['totalDescuento'];
        $gasto->subtotal_exento = 0;
        $gasto->suma_subtotal = 0;
        $gasto->suma_iva = 0;
        $gasto->total = $this->xmlData['importeTotal'];
        $gasto->save();


        // Vaciar impuestos y cargar nuevos
        foreach ($this->xmlDataimpuestos as $imp) {
            $gastoImpuesto = new GastosImpuestos();
            $gastoImpuesto->gastos_id = $gasto->id;
            $gastoImpuesto->impuestos_id = $imp['impuesto_id'];
            $gastoImpuesto->subtotal = $imp['baseImponible'];
            $gastoImpuesto->iva = $imp['valor'];
            $gastoImpuesto->save();
        }

        // Plan Cuentas y centro 
        if (!empty($gasto->proveedor) && !empty($gasto->proveedor->plan_cuenta_id) && !empty($gasto->proveedor->centro_costos_id)) {
            $gastoPlanCuentas = new GastosPlanCuentas();
            $gastoPlanCuentas->company_id = Auth::user()->company_id;
            $gastoPlanCuentas->gasto_id = $gasto->id;
            $gastoPlanCuentas->plan_cuentas_id = $gasto->proveedor->plan_cuenta_id ?? null;
            $gastoPlanCuentas->centro_costos_id = $gasto->proveedor->centro_costos_id ?? null;
            $gastoPlanCuentas->valor = $gasto->subtotal_descuento;
            $gastoPlanCuentas->save();
        }

        $this->dispatchBrowserEvent('alertaGrande', [
            'titulo' => 'Correcto',
            'color' => 'success',
            'mensaje' => 'Se creo la factura correctamente..'
        ]);
        $this->dispatchBrowserEvent('closeModal');
    }

    public function mount()
    {
        $this->htmlXml = "";
        $this->htmlXmlTabla1 = "";
        $this->htmlXmlTabla2 = "";
        $this->categorias = GastosCategorias::where('company_id', Auth::user()->company_id)->where('status', true)->get()->toArray();
        $this->proveedores = Proveedores::where('company_id', Auth::user()->company_id)->where('status', true)->get()->toArray();
        $this->planCuentas = PlanCuentas::where('company_id', Auth::user()->company_id)->where('gasto', true)->where('status', true)->get()->toArray();
        $this->centroCostos = CentroCostos::where('company_id', Auth::user()->company_id)->where('status', true)->get()->toArray();
        $this->formasPago = FormasPago::where('status', true)->get()->toArray();
        $this->sustentoTributario = SustentoTributario::where('status', true)->get()->toArray();
        $this->tipoComprobante = TipoComprobante::where('status', true)->get()->toArray();
        $this->listaRetenciones = ListaRetencion::select('lista_retencion.*', 'tipo_retencion.name')
            ->join('tipo_retencion', 'lista_retencion.tipo_retencion_id', '=', 'tipo_retencion.id')
            ->where('lista_retencion.status', true)
            ->get()
            ->toArray();
        $this->listaEstados = ['BORRADOR', 'PENDIENTE', 'APROBADO', 'RECHAZADO', 'PAGADO'];
        $this->gastosImpuestos = collect();
    }


    public function render()
    {
        $gastos = DB::table('gastos')
            ->select([
                'gastos.id',
                'gastos.fecha_creacion',
                'gastos.fecha_aprobacion',
                'gastos_categorias.nombre as categoria_nombre',
                'gastos.numero',
                'gastos.establecimiento',
                'gastos.punto_emision',
                'gastos.valor',
                'gastos.descuento',
                'gastos.subtotal_descuento',
                'gastos.suma_subtotal',
                'gastos.fecha_emision',
                'gastos.suma_iva',
                'gastos.total',
                'gastos.estado',
                'proveedores.nombre as nombreProveedor',
                'proveedores.ruc',
                DB::raw('(SELECT IFNULL(SUM(gastos_forma_pagos.valor), 0) 
              FROM gastos_forma_pagos 
              WHERE gastos_forma_pagos.gasto_id = gastos.id) as monto_pagado'),
                DB::raw('(SELECT IFNULL(SUM(gastos_lista_retencion.calculo), 0) 
              FROM gastos_lista_retencion 
              WHERE gastos_lista_retencion.gasto_id = gastos.id) as monto_retencion'),
                DB::raw('(gastos.total - 
              (SELECT IFNULL(SUM(gastos_forma_pagos.valor), 0) 
               FROM gastos_forma_pagos 
               WHERE gastos_forma_pagos.gasto_id = gastos.id) - 
              (SELECT IFNULL(SUM(gastos_lista_retencion.calculo), 0) 
               FROM gastos_lista_retencion 
               WHERE gastos_lista_retencion.gasto_id = gastos.id)) as valor_pendiente')
            ])
            ->leftJoin('gastos_categorias', 'gastos.gastos_categorias_id', '=', 'gastos_categorias.id')
            ->leftJoin('proveedores', 'gastos.proveedor_id', '=', 'proveedores.id')
            ->where('gastos.company_id', Auth::user()->company_id)
            ->whereBetween(
                DB::raw('DATE(gastos.fecha_creacion)'),
                [
                    \Carbon\Carbon::parse($this->fecha_inicio)->format('Y-m-d'),
                    \Carbon\Carbon::parse($this->fecha_fin)->format('Y-m-d'),
                ]
            )
            ->when($this->estadoFiltro !== 'TODOS', function ($query) {
                $query->where('estado', $this->estadoFiltro);
            })
            ->orderBy('gastos.id', 'desc') // <-- NUEVO: último gasto primero
            ->paginate(15);

        $gastosAprobar = Gastos::find($this->id_aprobar);
        $cuentasGastos = GastosPlanCuentas::where('company_id', Auth::user()->company_id)
            ->where('gasto_id', $this->id_aprobar)
            ->get();

        $this->gastosTotales = GastosPlanCuentas::where('company_id', Auth::user()->company_id)
            ->where('gasto_id', $this->id_aprobar)
            ->sum('valor');

        $valorTotalCuentas = $this->gastosTotales;
        $this->gastosValor = isset($gastosAprobar->subtotal_descuento) ? $gastosAprobar->subtotal_descuento : 0;
        $gastosValor = $this->gastosValor;

        $gastosImpuestos = GastosImpuestos::where('gastos_id', $this->id_seleccionado)->get();
        $gastoFormaPagos = GastosFormaPagos::where('gasto_id', $this->id_seleccionado)->get();
        $gastoRetenciones = GastosListaRetencion::where('gasto_id', $this->id_seleccionado)->get();

        $asientosHeader = AsientosHeader::with('detalles')
            ->where('gasto_id', $this->id_seleccionado)
            ->orderBy('gasto_id')
            ->get();

        return view(
            'livewire.gasto.gasto-component',
            compact(
                'gastos',
                'gastosAprobar',
                'cuentasGastos',
                'valorTotalCuentas',
                'gastosValor',
                'asientosHeader',
                'gastosImpuestos',
                'gastoFormaPagos',
                'gastoRetenciones'
            )
        );
    }
}
