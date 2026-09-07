<div>
    <div class="row">
        <div class="col-md-12">
            <div class="row mt-2">
                <div class="col-3">
                    <div class="input-group input-group-sm">
                        <input type="text" wire:model="buscarCedula" class="form-control" placeholder="Buscar por cédula o nombre">
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
                        <th><b>Letra</b></th>
                        <th><b>Fecha / Hora</b></th>
                        <th><b>Creado por</b></th>
                        <th><b>Valor Transferencia</b></th>
                        <!-- <th><b>Comprobante</b></th> -->
                        <th><b>Valor Otros</b></th>
                        <th><b>Valor Total</b></th>
                        <th><b>Aprobado por</b></th>
                        <th><b>Acciones</b></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($letras as $letra)

                    <tr>
                        <td>{{ $letra->apellidos }} {{ $letra->nombres }}</td>
                        <td>#{{ $letra->codeFolderHeader }}</td>
                        <td>#{{ $letra->numeroCuota }}</td>
                        <td>{{ $letra->fecha }} <b>/</b> {{ $letra->hora }}</td>
                        <td>{{ $letra->usuario }}</td>

                        <td>$ {{ number_format($letra->valor_transferencia,2) }}</td>

                        <!-- <td>{{$letra -> numero_comprobante}}</td> -->

                        <td>
                            $ {{ number_format($letra->valor_otros ?? 0,2) }}
                        </td>

                        <td>
                            <strong>$ {{ number_format($letra->valor_total,2) }}</strong>
                        </td>

                        <td>
                            {{ $letra->aprobado_por ?? '—' }}
                        </td>

                        <td>
                            <a class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalPagos" title="Pago Crédito" wire:click="cargarDatosPago({{ $letra->letra_id }})" style="color: white;">
                                <i class="far fa-money-bill-alt"></i> Ver
                            </a>
                            <!--<button class="btn btn-sm btn-success" wire:click="aprobarSolicitud(1, {{ $letra->letra_id }})">
                                Aprobar
                            </button>
                            <button class="btn btn-sm btn-danger" wire:click="aprobarSolicitud(0, {{ $letra->letra_id }})">
                                Rechazar
                            </button>-->

                            @if($letra->estado_solicitud == 3)
                            <button wire:click="aprobarSolicitud({{ $letra->letra_id }})" class="btn btn-success btn-sm">
                                Aceptar
                            </button>
                            <button data-bs-toggle="modal" data-bs-target="#modalPagos" title="Pago Crédito" wire:click="cargarDatosPago({{ $letra->letra_id }})" class="btn btn-danger btn-sm">
                                Rechazar
                            </button>
                            @else
                            @if($letra->estado_solicitud == 1)
                            <span class="badge bg-success" style="padding: 8px; font-size: 11px;">
                                <i class="fas fa-check-circle"></i> APROBADO
                            </span>
                            @elseif ($letra->estado_solicitud == 2)
                            <span class="badge bg-danger" style="padding: 8px; font-size: 11px;">
                                <i class="fas fa-ban"></i> RECHAZADO
                            </span>
                            @endif
                            @endif
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="11" class="text-center">
                            No existen registros
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div wire:ignore.self class="modal fade" id="modalPagos" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">

                        <div class="modal-header bg-info">
                            <h3 class="modal-title text-white"><i class="fas fa-file-invoice-dollar me-2"></i>Detalle del Pago</h3>
                            <button type="button" class="close text-white" data-bs-dismiss="modal">
                                &times;
                            </button>
                        </div>

                        <div class="modal-body">

                            @if($registro)

                            <div class="row">
                                <div class="col-md-6 border-right pe-4">
                                    <h5>
                                        <i class="fas fa-user text-primary me-2"></i>
                                        <b class="text-primary">Información del Cliente:</b>

                                    </h5>
                                    <hr>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Apellidos</label>
                                            <input type="text" class="form-control"
                                                value="{{ $registro->apellidos ?? '----' }}" readonly>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Nombres</label>
                                            <input type="text" class="form-control"
                                                value="{{ $registro->nombres ?? '----' }}" readonly>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Cédula</label>
                                            <input type="text" class="form-control"
                                                value="{{ $registro->numero_documento ?? '----' }}" readonly>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Crédito</label>
                                            <input type="text" class="form-control"
                                                value="#{{ $registro->codeFolderHeader }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Letra</label>
                                            <input type="text" class="form-control"
                                                value="#{{ $registro->numeroCuota }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 ps-4">
                                    <h5>
                                        <i class="fas fa-money-check-alt me-2"></i>
                                        <b>Información del Pago:</b>
                                        <hr>
                                    </h5>
                                    <div class="row">

                                        <div class="col-md-6 mb-3">
                                            <label>Creado por:</label>
                                            <input type="text" class="form-control"
                                                value="{{ $registro->user_name ?? '----' }}" readonly>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Fecha creación:</label>
                                            <input type="text" class="form-control"
                                                value="{{ $registro->date_create ?? '----' }}" readonly>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label>Aprobado por:</label>
                                            <input type="text" class="form-control"
                                                value="{{ $registro->usuario_solicitud ?? '----' }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Fecha aprobación:</label>
                                            <input type="text" class="form-control"
                                                value="{{ $registro->fecha_solicitud ?? '----' }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="custom-control custom-checkbox mb-2" wire:ignore>
                                                <input type="checkbox"
                                                    id="chkInteresMora"
                                                    class="custom-control-input"
                                                    wire:model="aplicarInteresMora">

                                                <label class="custom-control-label" for="chkInteresMora">
                                                    Aplicar interés mora:
                                                </label>
                                            </div>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="$ {{ number_format($interesMoraOriginal, 2) }}"
                                                readonly>
                                        </div>



                                        <div class="col-md-6 mb-3">
                                            <label>Total a aprobar:</label>

                                            <input
                                                type="text"
                                                class="form-control font-weight-bold"
                                                value="$ {{ number_format($totalAprobar,2) }}"
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <hr>

                            <h5><i class="fas fa-wallet me-2"></i><b>Formas de pago:</b></h5>

                            @foreach($detallePagos as $pago)
                            <div class="border rounded p-2 mb-2">
                                <div class="row">

                                    <div class="col-md-3 mb-3 pe-1 ps-2">
                                        <label>Forma de Pago:</label>
                                        <input type="text" class="form-control" value="{{ $pago->forma_pago ?? '----'}} " readonly>
                                    </div>
                                    <div class="col-md-3 mb-3 pe-1">
                                        <label>Valor:</label>
                                        <input type="text" class="form-control" value="{{ number_format($pago->valor,2) ?? '----'}} " readonly>
                                    </div>

                                    @if(strtoupper(trim($pago->forma_pago)) == 'TRANSFERENCIA')

                                    <div class="col-md-3 mb-3 pe-1 ps-2">
                                        <label>Comprobante:</label>
                                        <input type="text" class="form-control" value="{{ $pago->numero_comprobante ?? '----'}} " readonly>
                                    </div>
                                    <div class="col-md-3 mb-3  ps-2">
                                        <label>Banco:</label>
                                        <input type="text" class="form-control" value="{{ $pago->banco->nombre ?? '----'}}" readonly>
                                    </div>

                                    <div class="col-md-3 mb-3 pe-1 ps-2">
                                        <label>Fecha del Comprobante:</label>
                                        <input type="text" class="form-control" value="{{ $pago->fecha_comprobante ?? '----'}} " readonly>
                                    </div>
                                    <div class="col-md-3 mb-3 pe-1 ps-2">
                                        <label>Hora del Comprobante:</label>
                                        <input type="text" class="form-control" value="{{ $pago->hora_comprobante ?? '----'}} " readonly>
                                    </div>
                                    <div class="col-md-6 mb-3 ps-2">
                                        <label>Comprobante:</label>

                                        <div class="input-group">

                                            <div class="input-group-append">
                                                @if(!empty($pago->path))
                                                <button type="button"
                                                    class="btn btn-info"
                                                    title="Ver comprobante"
                                                    data-comprobante-url="{{ route('solicitud-pagos-creditos.comprobante', $pago->letra_id) }}"
                                                    onclick="abrirComprobantePago(this)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @endif
                                            </div>

                                            <input type="text" class="form-control" value="{{ !empty($pago->path) ? basename($pago->path) : 'Sin archivo' }}" readonly>
                                        </div>

                                    </div>

                                    @endif

                                </div>
                            </div>

                            <!--<div class="border rounded p-2 mb-2">
                                <b>Forma:</b> {{ $pago->forma_pago }} |
                                <b>Comprobante:</b> {{ $pago->numero_comprobante ?? '—'}} |
                                <b>Valor:</b> ${{ number_format($pago->valor,2) }} |
                                <b>Banco:</b> {{ $pago->banco_id ?? '—' }}
                            </div>-->
                            @endforeach

                            @else
                            <div class="text-center text-muted">
                                Sin información
                            </div>
                            @endif

                            <div class="modal-footer">

                                @if($registro && $registro->solicitado == 3)

                                <!--<div class="col-md-12 mb-3">
                                    <label><b>Observación de rechazo:</b></label>
                                    <textarea wire:model.defer="observacionRechazo" class="form-control" rows="3" placeholder="Ingrese el motivo por el cual se rechaza la solicitud..."></textarea>
                                </div>-->

                                <button type="button" class="btn btn-success" data-bs-dismiss="modal"
                                    wire:click="aprobarSolicitud({{ $letraSeleccionada }})">
                                    Aceptar
                                </button>

                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                                    wire:click="rechazarSolicitud({{ $letraSeleccionada }})">
                                    Rechazar
                                </button>

                                @endif

                                <button class="btn btn-secondary" data-bs-dismiss="modal">
                                    Cerrar
                                </button>

                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <div wire:ignore.self class="modal fade" id="modalComprobantePago" tabindex="-1"
                role="dialog" aria-labelledby="tituloModalComprobantePago" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info">
                            <h4 class="modal-title text-white" id="tituloModalComprobantePago">
                                <i class="fas fa-receipt me-2"></i>Comprobante del Pago
                            </h4>
                            <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body p-2">
                            <iframe id="visorComprobantePago"
                                title="Comprobante del pago"
                                style="width: 100%; height: 70vh; border: 0;"
                                src="about:blank">
                            </iframe>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{ $letras->links() }}
            <style>
                #modalComprobantePago {
                    z-index: 1060;
                }

                .modal-backdrop.modal-comprobante-backdrop {
                    z-index: 1055;
                }
            </style>
            <script>
                function abrirComprobantePago(boton) {
                    var url = boton.getAttribute('data-comprobante-url');

                    if (!url) {
                        return;
                    }

                    document.getElementById('visorComprobantePago').src = url;
                    $('#modalComprobantePago').modal('show');
                }

                $('#modalComprobantePago')
                    .off('shown.bs.modal.comprobantePago hidden.bs.modal.comprobantePago')
                    .on('shown.bs.modal.comprobantePago', function() {
                        $('.modal-backdrop').last().addClass('modal-comprobante-backdrop');
                    })
                    .on('hidden.bs.modal.comprobantePago', function() {
                        document.getElementById('visorComprobantePago').src = 'about:blank';

                        if ($('#modalPagos').hasClass('show')) {
                            $('body').addClass('modal-open');
                        }
                    });

                window.addEventListener('close-modal-pagos', function () {
                    // Esperar a que Livewire termine de actualizar el DOM antes de
                    // pedirle a Bootstrap que cierre completamente los modales.
                    setTimeout(function () {
                        $('#modalComprobantePago').modal('hide');
                        $('#modalPagos').modal('hide');

                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css('padding-right', '');
                    }, 0);
                });
            </script>

        </div>
    </div>


</div>
