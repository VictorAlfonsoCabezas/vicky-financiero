<div>
    <div class="row">
        <div class="col-md-12">

            <div class="row mt-2">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <input
                            type="text"
                            wire:model="buscarCedula"
                            class="form-control"
                            placeholder="Buscar por cédula o nombre">

                        <div class="input-group-append">
                            <div class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="tab-content">
        <div class="card-body table-responsive">

            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th><b>Cliente</b></th>
                        <th><b>Crédito</b></th>
                        <th><b>Fecha / Hora</b></th>
                        <th><b>Creado por</b></th>
                        <th><b>Valor Transferencia</b></th>
                        <th><b>Valor Otros</b></th>
                        <th><b>Valor Total</b></th>
                        <th><b>Aprobado por</b></th>
                        <th><b>Estado</b></th>
                        <th><b>Acciones</b></th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($liquidaciones as $liquidacion)

                    <tr>

                        <td>
                            {{ $liquidacion->apellidos }}
                            {{ $liquidacion->nombres }}
                        </td>

                        <td>
                            #{{ $liquidacion->codeFolderHeader }}
                        </td>

                        <td>
                            {{ $liquidacion->fecha_solicitud ?? '----' }}
                            <b>/</b>
                            {{ $liquidacion->hora_solicitud ?? '----' }}
                        </td>

                        <td>
                            {{ $liquidacion->user_name ?? '----' }}
                        </td>

                        <td>
                            ${{ number_format($liquidacion->valor_transferencia ?? 0, 2) }}
                        </td>

                        <td>
                            ${{ number_format($liquidacion->valor_otros ?? 0, 2) }}
                        </td>

                        <td>
                            <strong>
                                ${{ number_format($liquidacion->valor_total ?? 0, 2) }}
                            </strong>
                        </td>

                        <td>
                            {{ $liquidacion->usuario_solicitud ?? '—' }}
                        </td>

                        <td>

                            @if($liquidacion->estado_solicitud == 3)

                            <span class="badge bg-warning"
                                style="padding:8px;font-size:11px;">
                                <i class="fas fa-clock"></i>
                                PENDIENTE
                            </span>

                            @elseif($liquidacion->estado_solicitud == 2)

                            <span class="badge bg-danger"
                                style="padding:8px;font-size:11px;">
                                <i class="fas fa-times-circle"></i>
                                RECHAZADA
                            </span>

                            @else

                            <span class="badge bg-success"
                                style="padding:8px;font-size:11px;">
                                <i class="fas fa-check-circle"></i>
                                APROBADA
                            </span>

                            @endif

                        </td>

                        <td>

                            <a
                                class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalLiquidacion" title="Ver liquidación"
                                wire:click="cargarDatosLiquidacion({{ $liquidacion->liquidacion_id }},'{{ $liquidacion->fecha_solicitud }}','{{ $liquidacion->hora_solicitud }}')"
                                style="color: white;">

                                <i class="fas fa-file-invoice-dollar"></i>
                                Ver

                            </a>


                            @if($liquidacion->estado_solicitud == 3)

                            <button
                                wire:click="aprobarSolicitud({{ $liquidacion->liquidacion_id }},'{{ $liquidacion->fecha_solicitud }}','{{ $liquidacion->hora_solicitud }}')"
                                class="btn btn-success btn-sm">

                                <i class="fas fa-check"></i>
                                Aceptar

                            </button>

                            <button
                                type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalLiquidacion"
                                wire:click="cargarDatosLiquidacion({{ $liquidacion->liquidacion_id }},'{{ $liquidacion->fecha_solicitud }}','{{ $liquidacion->hora_solicitud }}')">

                                <i class="fas fa-times"></i>
                                Rechazar

                            </button>

                            @endif

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="10" class="text-center">
                            No existen registros
                        </td>
                    </tr>

                    @endforelse

                </tbody>
            </table>


            {{-- MODAL DETALLE DE LIQUIDACIÓN --}}

            <div
                wire:ignore.self
                class="modal fade"
                id="modalLiquidacion"
                tabindex="-1">

                <div class="modal-dialog modal-xl">

                    <div class="modal-content">

                        <div class="modal-header bg-info">

                            <h3 class="modal-title text-white">

                                <i class="fas fa-file-invoice-dollar me-2"></i>

                                Detalle de Liquidación

                            </h3>

                            <button
                                type="button"
                                class="close text-white"
                                data-bs-dismiss="modal">

                                &times;

                            </button>

                        </div>


                        <div class="modal-body">

                            @if($registro)

                            {{-- INFORMACIÓN DEL CLIENTE Y LIQUIDACIÓN --}}

                            <div class="row">

                                <div class="col-md-6 border-right pe-4">

                                    <h5>

                                        <i class="fas fa-user text-primary me-2"></i>

                                        <b class="text-primary">
                                            Información del Cliente
                                        </b>

                                    </h5>

                                    <hr>

                                    <div class="row">

                                        <div class="col-md-6 mb-3">

                                            <label>Apellidos</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="{{ $registro->apellidos ?? '----' }}"
                                                readonly>

                                        </div>


                                        <div class="col-md-6 mb-3">

                                            <label>Nombres</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="{{ $registro->nombres ?? '----' }}"
                                                readonly>

                                        </div>


                                        <div class="col-md-6 mb-3">

                                            <label>Cédula</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="{{ $registro->numero_documento ?? '----' }}"
                                                readonly>

                                        </div>


                                        <div class="col-md-6 mb-3">

                                            <label>Crédito</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="#{{ $registro->codeFolderHeader ?? '----' }}"
                                                readonly>

                                        </div>

                                    </div>

                                </div>


                                {{-- INFORMACIÓN DE LA SOLICITUD --}}

                                <div class="col-md-6 ps-4">

                                    <h5>
                                        <i class="fas fa-file-signature me-2"></i>
                                        <b>
                                            Información de la Solicitud
                                        </b>
                                    </h5>

                                    <hr>

                                    <div class="row">

                                        <div class="col-md-6 mb-3">

                                            <label>Creado por:</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="{{ $registro->user_name ?? '----' }}"
                                                readonly>

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label>Fecha de solicitud:</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="{{ $registro->fecha_solicitud ?? '----' }} / {{ $registro->hora_solicitud ?? '----' }}"
                                                readonly>

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label>Aprobado por:</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="{{ $registro->usuario_solicitud ?? '----' }}"
                                                readonly>

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label>Fecha de aprobación:</label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="{{ $registro->fecha_aprobacion ?? '----' }} / {{ $registro->hora_aprobacion ?? '----' }}"
                                                readonly>

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label>Valor Liquidación</label>

                                            <input
                                                type="text"
                                                class="form-control font-weight-bold"
                                                value="${{ number_format($detallePagos->sum('valor'), 2) }}"
                                                readonly>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <hr>


                            {{-- FORMAS DE PAGO --}}

                            <h5>

                                <i class="fas fa-wallet me-2"></i>

                                <b>
                                    Formas de pago
                                </b>

                            </h5>


                            @foreach($detallePagos as $pago)

                            <div class="border rounded p-3 mb-3">

                                <div class="row">

                                    <div class="col-md-4 mb-3">

                                        <label>
                                            <i class="fas fa-money-check-alt"></i>
                                            Forma de Pago
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            value="{{ $pago->forma_pago ?? '----' }}"
                                            readonly>

                                    </div>

                                    <div class="col-md-4 mb-3">

                                        <label>
                                            <i class="fas fa-dollar-sign"></i>
                                            Valor
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control font-weight-bold"
                                            value="$ {{ number_format($pago->valor ?? 0, 2) }}"
                                            readonly>

                                    </div>

                                    @if(strtoupper(trim($pago->forma_pago ?? '')) == 'TRANSFERENCIA')

                                    <div class="col-md-4 mb-3">

                                        <label>
                                            <i class="fas fa-university"></i>
                                            Banco
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            value="{{ $pago->banco->nombre ?? '----' }}"
                                            readonly>

                                    </div>

                                    <div class="col-md-4 mb-3">

                                        <label>
                                            <i class="fas fa-receipt"></i>
                                            Número de comprobante
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            value="{{ $pago->numero_comprobante ?? '----' }}"
                                            readonly>

                                    </div>

                                    @endif

                                </div>

                            </div>

                            @endforeach


                            {{-- OBSERVACIÓN --}}

                            <div class="form-group mt-3">

                                <label>

                                    <i class="fas fa-comment-alt me-2"></i>

                                    <b>
                                        Observación
                                    </b>

                                </label>

                                <textarea
                                    class="form-control"
                                    rows="3"
                                    readonly>{{ $registro->observacion ?? 'Sin observación' }}</textarea>

                            </div>


                            @else

                            <div class="text-center text-muted">

                                Sin información

                            </div>

                            @endif


                            {{-- BOTONES DEL MODAL --}}

                            <div class="modal-footer">

                                @if($registro && $registro->solicitado == 3)

                                @if(!$mostrarFormularioRechazo)

                                <button
                                    type="button"
                                    class="btn btn-success"
                                    wire:click="aprobarSolicitud({{ $liquidacionSeleccionada }},'{{ $registro->fecha_solicitud }}','{{ $registro->hora_solicitud }}')">

                                    <i class="fas fa-check"></i>
                                    Aprobar Liquidación

                                </button>

                                <button
                                    type="button" class="btn btn-danger" wire:click="abrirFormularioRechazo">

                                    <i class="fas fa-times"></i>
                                    Rechazar Solicitud

                                </button>

                                @else

                                <div class="w-100 mb-3">

                                    <label>

                                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>

                                        <strong>
                                            Motivo del rechazo de la transferencia
                                        </strong>

                                    </label>

                                    <textarea
                                        wire:model.defer="observacionRechazo"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Ingrese el motivo por el cual se rechaza la transferencia..."></textarea>

                                </div>

                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    wire:click="$set('abrirFormularioRechazo', false)">

                                    <i class="fas fa-arrow-left"></i>
                                    Cancelar

                                </button>

                                <button
                                    type="button"
                                    class="btn btn-danger"
                                    wire:click="rechazarSolicitud( {{ $liquidacionSeleccionada }}, '{{ $registro->fecha_solicitud }}', '{{ $registro->hora_solicitud }}' )">

                                    <i class="fas fa-times"></i>
                                    Confirmar Rechazo

                                </button>

                                @endif

                                @endif

                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">

                                    <i class="fas fa-times"></i>
                                    Cerrar

                                </button>

                            </div>
                        </div>

                    </div>

                </div>

            </div>


            {{ $liquidaciones->links() }}


            <script>
                window.addEventListener('close-modal-liquidacion', () => {
                    $('#modalLiquidacion').modal('hide');
                });
            </script>

        </div>
    </div>

</div>