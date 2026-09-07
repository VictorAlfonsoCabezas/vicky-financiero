<div>
    <div class="row mt-3">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Utilidadades</h3>
                </div>
                <div class="card-body">
                    {{-- Mostrar mensaje de éxito --}}
                    @if (session()->has('message'))
                        <div class="alert alert-success">{{ session('message') }}</div>
                    @endif
                    @if ($numero_socios <= 0)
                    <div class="alert alert-warning shadow-sm mb-4">
                        <strong>¡Advertencia!</strong> No hay socios fundadores registrados (0 Socios). No es posible ejecutar el pago de utilidades.
                    </div>
                    @endif
                    <form wire:submit.prevent="guardarConfiguracion">
                        <div class="form-group row">
                            <div class="col d-flex">
                                <p class="col-sm-5 col-form-label font-weight-bold">N° Socios</p>
                                <div class="col-sm-7">
                                    <p class="col-form-label" id="socios">
                                        {{ $numero_socios }}
                                    </p>
                                </div>
                            </div>
                            <div class="col d-flex">
                                <label for="fecha" class="col-sm-4 col-form-label">Año</label>
                                <div class="col-sm-8">
                                    <p id="anio" class="col-form-label">
                                        {{ $anio_calculo }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col d-flex">
                                <label for="porcentaje" class="col-sm-5 col-form-label">Porcentaje</label>
                                <div class="col-sm-7">
                                    <input type="number" class="form-control @error('porcentaje') is-invalid @enderror"
                                    id="porcentaje"
                                    step="any"
                                    placeholder="Porcentaje"
                                    min="0"
                                    max="100"
                                    wire:model.live="porcentaje">
                                </div>
                                @error('porcentaje') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col d-flex">
                                <p class="col-sm-4 col-form-label font-weight-bold">Valor Total</p>
                                <div class="col-sm-8">
                                    <p id="valorTotal" class="col-form-label">
                                        ${{ number_format($valor_total_utilidad, 2, '.', ',') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col d-flex">
                                <label for="tipo_ahorros_select" class="col-sm-5 col-form-label fw-semibold">Tipos de Ahorros</label>
                                <div class="col-sm-7">
                                    <select class="custom-select @error('tipo_ahorros_id') is-invalid @enderror"
                                        id="tipo_ahorros_select"
                                        wire:model="tipo_ahorros_id">
                                        <option value="">-- Seleccione --</option>
                                        @foreach ($tiposAhorrosOpciones as $tipoAhorro)
                                            <option value="{{ $tipoAhorro->id }}">{{ $tipoAhorro->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_ahorros_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col d-flex">
                                <p class="col-sm-4 col-form-label font-weight-bold">Calculo 100%</p>
                                <div class="col-sm-8">
                                    <p id="valorSocio" class="col-form-label">
                                        $ {{ number_format($valor_por_socio, 2, '.', ',') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col col-6 d-flex">
                                <label for="tipo_ahorros_select" class="col-sm-5 col-form-label fw-semibold">Cuenta Pasivo</label>
                                <div class="col-sm-7">
                                    <select class="custom-select @error('cuenta_pasivo_id') is-invalid @enderror"
                                        id="tipo_ahorros_select"
                                        wire:model="cuenta_pasivo_id">
                                        <option value="">-- Seleccione --</option>
                                        @foreach ($cuentasPasivoOpciones as $detalle)
                                            <option value="{{ $detalle->planCuentas->id }}">
                                                {{ $detalle->planCuentas->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cuenta_pasivo_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mt-4">
                            <div class="col d-flex">
                                <label for="fecha_pago_automatico" class="col-sm-5 col-form-label fw-semibold">Fecha de Ejecución</label>
                                <div class="col-sm-7">
                                    <input type="date" class="form-control @error('fecha_pago_automatico') is-invalid @enderror" 
                                        id="fecha_pago_automatico"
                                        wire:model="fecha_pago_automatico"
                                       >
                                    @error('fecha_pago_automatico') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col d-flex align-items-center">
                                <div class="col-sm-8">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="gridCheck1" wire:model="pago_automatico">
                                        <label class="form-check-label font-weight-bold" for="gridCheck1">
                                            Aprobar Pago Aútomatico
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 d-flex">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mx-2"></i>Guardar Configuración
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-lg shadow mx-2" 
                                wire:click="ejecutarPagoAutomatico" 
                                wire:loading.attr="disabled"
                                @if ($numero_socios <= 0) disabled @endif
                                wire:confirm="¿Está seguro de que desea ejecutar el pago de utilidades Monto por Socio: ${{ number_format($valor_por_socio, 2) }}? Esta acción no se puede deshacer y creará asientos contables.">
                                <span wire:loading.remove wire:target="ejecutarPagoAutomatico">
                                    <i class="fas fa-file-invoice-dollar me-2"></i> Ejecutar Pago Ahora
                                </span>
                                <span wire:loading wire:target="ejecutarPagoAutomatico">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Procesando Pago...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @if (count($movimientosAgrupadosPorLote) > 0)
                @foreach ($movimientosAgrupadosPorLote as $loteId => $movimientosLote)
                    <div class="card card-primary @if(!$loop->first) mt-3 @endif">
                        <div class="card-header">
                            <h3 class="card-title">Aprobación Masiva - Lote #{{ $loteId }}</h3>
                        </div>
                        <div class="card-body p-0">
                            <!-- Se itera el cuerpo o las utilidades pendientes de este lote -->
                            @if ($movimientosLote->count() > 0)
                                @php
                                    // Se asume que todas tienen el mismo valor y fecha dentro del LOTE
                                    $primerMovimiento = $movimientosLote->first();
                                    $valorComun = number_format($primerMovimiento->valor, 2, '.', ',');
                                    $fechaComun = \Carbon\Carbon::parse($primerMovimiento->fecha_creacion)->format('d-m-Y H:i');
                                    $idsMovimientos = $movimientosLote->pluck('id')->toArray();
                                    $loteMasivo = implode(',', $idsMovimientos);
                                    $totalSolicitudes = $movimientosLote->count();
                                @endphp
                                <div class="p-3 border-bottom">
                                    <p class="mb-1">
                                        <i class="fas fa-money-bill-wave text-success"></i> <strong>Valor:</strong> <span class="float-end">${{ $valorComun }}</span>
                                    </p>
                                    <p class="mb-1">
                                        <i class="fa fa-calendar-alt text-info"></i> <strong>Fecha de Creación:</strong> <span class="float-end">{{ $fechaComun }}</span>
                                    </p>
                                    <p class="mb-0">
                                        <i class="fa fa-users text-primary"></i> <strong>Solicitudes en Lote:</strong> <span class="float-end">{{ $totalSolicitudes }}</span>
                                    </p>
                                </div>
                                <div class="table-responsive p-0 overflow-auto" style="max-height: 48vh;">
                                    <table class="table table-sm table-striped table-valign-middle">
                                        <thead>
                                            <tr>
                                                <th class="border-top-0">Cliente Solicita</th>
                                                <th class="border-top-0">Cuenta</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($movimientosLote as $mov)
                                                <tr>
                                                    <td>
                                                        <small>
                                                            <i>{{ $mov->nombres }} {{ $mov->apellidos }}</i><br>
                                                            <b>{{ $mov->numero_documento }}</b>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <small>
                                                            {{-- Se usa el null-safe operator ?? 'N/A' para evitar errores si las relaciones no están cargadas --}}
                                                            <i>{{ $mov->customerTipoAhorro->tipoAhorros->name ?? 'N/A' }}</i><br>
                                                            <b>{{ $mov->customerTipoAhorro->codigo ?? 'N/A' }} (  $ {{ $mov->valor ?? 'N/A' }} )</b>
                                                        </small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3 border-top d-flex">
                                    <div class="col-md-6">
                                        <button wire:click="seleccionarRechazoMasivo('{{ $loteMasivo }}');" type="button" data-bs-toggle="modal"
                                        data-bs-target="#modalRechazoMasivo" class="btn btn-block bg-danger btn-sm">
                                        <i class="fa fa-times"></i> Rechazar Todos ({{ $totalSolicitudes }})
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button wire:click="aceptarMasivo('{{ $loteMasivo }}')" type="button"
                                            class="btn btn-block bg-success btn-sm mb-1">
                                            <i class="fa fa-check"></i> Aprobar Todos ({{ $totalSolicitudes }})
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info mt-3">
                    No hay solicitudes de Utilidades Pendientes de Aprobación.
                </div>
            @endif
            <!-- Fin de la iteración por lotes -->
        </div>
    </div>

    <!-- MODAL RECHAZO MASIVO -->
    <div wire:ignore.self class="modal fade" id="modalRechazoMasivo" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title"> Rechazar Solicitudes Masivas </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="RechazarMasivo">
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="callout callout-warning">
                                <h5>Verifica estas observaciones.</h5>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-12">
                                <label>Razón de Rechazo para {{ $totalSolicitudesRechazo }} Solicitudes</label>
                                <textarea class="form-control" rows="3"
                                    placeholder="Ingrese una razón del rechazo, obligatorio"
                                    wire:model="razon_rechazo_masivo"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-danger">Rechazar y Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="modalAdvertenciaConfig" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title">
                        <i class="fa fa-exclamation-triangle text-white" aria-hidden="true"></i> 
                        Advertencia de Configuración
                    </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Detectamos **cambios no guardados** en la configuración de utilidades (Porcentaje, Cuenta de Pasivo, Fecha, etc.).
                        Si continúa, el pago se ejecutará usando los **valores de configuración actuales** (visibles en pantalla), no los últimos valores guardados en la base de datos.
                    </p>
                    <p class="font-weight-bold text-danger">
                        Por favor, **Guarde la Configuración** antes de ejecutar el pago si desea usar los valores persistentes.
                    </p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar y Revisar</button>
                    
                    {{-- ESTE ES EL BOTÓN CRUCIAL que llama al método forzado en Livewire --}}
                    <button type="button" 
                            wire:click="ejecutarPagoAutomaticoForzado" 
                            data-bs-dismiss="modal" 
                            class="btn btn-warning">
                        Continuar Sin Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>