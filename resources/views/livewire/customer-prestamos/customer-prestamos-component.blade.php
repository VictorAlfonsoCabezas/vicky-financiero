<div>
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-user"></i>
                    {{Auth::user()->firstname . ' ' . Auth::user()->lastname}}
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-9 order-2 order-md-1">
                        <div class="row">
                            @foreach ($prestamos as $pres)
                            <div class="col-12 col-sm-3" wire:click="selecionaPrestamos({{ $pres->id }})">
                                <div class="info-box bg-{{ $this->cuenta_selec == $pres->id ? 'warning' : 'light' }}">
                                    @if ($this->cuenta_selec == $pres->id)
                                    <i class="fa fa-hand-pointer" aria-hidden="true"></i>
                                    @endif
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted"><b>Prestamo:</b>
                                            {{ $pres->code }}</span>
                                        <span class="info-box-text text-center text-muted"><b>Fecha:</b>
                                            {{ $pres->date_created }}</span>
                                        <span class="info-box-text text-center text-muted"><b>Estado:</b> <small
                                                class="badge bg-primary"><i class="fa fa-check"></i>
                                                {{ $pres->status }}</small></span>
                                        <span class="info-box-number text-center text-muted mb-0"><b>Valor Cuota: </b> $
                                            {{ $pres->valor_cuota }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                        <tr>
                                            <th>No. Cuota</th>
                                            <th>Fecha Vencimiento</th>
                                            <th>Interes Mora</th>
                                            <th>Valor de la Cuota</th>
                                            <th>Valor Pagado</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Acción</th>
                                            <th><b>Observación</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($detalle as $det)
                                        <tr>
                                            <td>
                                                {{ $det->numero_cuota }}
                                            </td>
                                            <td> {{ $det->date_vencimiento }}</td>

                                            <td> {{ $det->valorInteres }}</td>

                                            <td>{{$det->valor_cuota}}</td>

                                            <td>
                                                <small class="text-success me-1">
                                                    $
                                                    {{ $det->valor_pagado }}
                                                </small>
                                            </td>
                                            <td>
                                                @if ($det->status == 'PAGADA')
                                                <small class="text-success me-1">
                                                    $
                                                    {{ $det->valor_pagado }}
                                                </small>
                                                @else
                                                <small class="text-success me-1">
                                                    $
                                                    {{ $det->valor_cuota + $det->valorInteres }}
                                                </small>
                                                @endif
                                            </td>

                                            <td>

                                                @if ($det->status == 'PAGADA')

                                                <small class="badge bg-primary">
                                                    <i class="fa fa-check"></i>
                                                    {{ $det->status }}
                                                </small>

                                                @elseif ($det->status == 'STAND BY')

                                                <small class="badge bg-warning">
                                                    <i class="fa fa-clock"></i>
                                                    {{ $det->status }}
                                                </small>

                                                @else

                                                <small class="badge bg-danger">
                                                    <i class="fa fa-check"></i>
                                                    {{ $det->status }}
                                                </small>

                                                @endif

                                            </td>

                                            <td>
                                                @if ($det->status == 'PAGADA')
                                                <a class="text-muted"
                                                    href="{{ URL::to('credit/pdfCuotaVer/' . $det->id . '/' . $det->customerID) }}"
                                                    title="Entregar ticket">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                @endif

                                                @if($det->status == 'PENDIENTE')
                                                <button
                                                    class="btn btn-success btn-sm" wire:click="subirPago({{ $det->id }})"> Subir Pago
                                                </button>
                                                @endif

                                                @if($det->status == 'STAND BY')
                                                <button
                                                    class="btn btn-info btn-sm" wire:click="verPago({{ $det->id }})"> Ver Pago
                                                </button>
                                                @endif
                                            </td>
                                            <td>
                                                @if($det->ultimoPago && $det->ultimoPago->solicitado == 2)
                                                <span class="text-danger">
                                                    <b>Transferencia rechazada</b>
                                                </span>
                                                @else
                                                <span class="text-muted">Sin observación</span>
                                                @endif
                                            </td>

                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center"><i class="fa fa-hand-pointer" aria-hidden="true"></i> Seleccione un Préstamo</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                {{ $detalle->links() }}
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-2 order-1 order-md-2">
                        @if ($this->id_seleccionado > 0)
                        <h3 class="text-primary"><i class="far fa-fw fa-file-pdf"></i> Archivos Subidos</h3>
                        <br>
                        <ul class="list-unstyled">
                            @foreach ($files as $fil)
                            @if ($fil->formato == 'doc' || $fil->formato == 'docx')
                            <li>
                                <a href="{{ $fil->path }}" class="btn-link text-secondary" target="_blank"><i
                                        class="far fa-fw fa-file-word"></i>
                                    {{ $fil->descripcion }}</a>
                            </li>
                            @elseif ($fil->formato == 'pdf')
                            <li>
                                <a href="{{ $fil->path }}" class="btn-link text-secondary" target="_blank"><i
                                        class="far fa-fw fa-file-pdf"></i>
                                    {{ $fil->descripcion }}</a>
                            </li>
                            @elseif ($fil->formato == 'jpg' || $fil->formato == 'jpeg' || $fil->formato == 'png' || $fil->formato == 'gif')
                            <li>
                                <a href="{{ $fil->path }}" class="btn-link text-secondary" target="_blank"><i
                                        class="far fa-fw fa-image "></i>
                                    {{ $fil->descripcion }}</a>
                            </li>
                            @endif
                            @endforeach
                        </ul>


                        <!-- Modal Agregado-->

                        <div wire:ignore.self class="modal fade" id="modalPago" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h4 class="modal-title"> Subir Comprobante de Pago </h4>

                                        <button type="button" class="close" data-bs-dismiss="modal"> × </button>
                                    </div>

                                    <div class="card card-primary card-outline mb-0">

                                        <div class="card-body">
                                            <div class="accordion" id="accordionExample">
                                                <div class="accordion-item">

                                                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample" style="">
                                                        <div class="accordion-body">
                                                            <strong>NOTA:</strong> Pago solo en TRANSFERENCIA, subir la información de acuerdo al
                                                            <code>comprobante de pago</code>, OBLIGATORIO.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-body">

                                        @if($detalleSeleccionado)

                                        @if ($errors->any())
                                        <div class="callout callout-warning">
                                            <h5><i class="fas fa-exclamation-triangle"></i> Verifica estas observaciones.</h5>

                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif

                                        @if (session()->has('error'))
                                        <div class="alert alert-danger">
                                            {{ session('error') }}
                                        </div>
                                        @endif

                                        @if (session()->has('message'))
                                        <div class="alert alert-success">
                                            {{ session('message') }}
                                        </div>
                                        @endif

                                        <div class="row">

                                            <div class="col-md-6">
                                                <label>Cuota</label>
                                                <input type="text" class="form-control" value="{{ $detalleSeleccionado->numero_cuota }}" readonly>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Valor</label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    value="{{ $valorTotal }}"
                                                    readonly>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Forma de Pago</label>

                                                <input type="text" class="form-control" value="TRANSFERENCIA" readonly>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Banco</label>
                                                <select class="form-control" wire:model="banco_id">

                                                    <option value=""> - Seleccione - </option>

                                                    @foreach($bancos as $ban)
                                                    <option value="{{ $ban->id }}">
                                                        {{ $ban->nombre }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Fecha del Comprobante</label>

                                                <input
                                                    type="date"
                                                    class="form-control"
                                                    wire:model="fecha_comprobante">
                                            </div>

                                            <div class="col-md-6">
                                                <label>Hora del Comprobante</label>

                                                <input
                                                    type="time"
                                                    class="form-control"
                                                    wire:model="hora_comprobante">
                                            </div>

                                            <div class="col-md-6">
                                                <label># Comprobante</label>
                                                <input type="text" class="form-control" wire:model="numero_comprobante">
                                            </div>


                                            <div class="col-md-6">
                                                <label>Comprobante</label>

                                                <input
                                                    type="file"
                                                    class="form-control"
                                                    wire:model="comprobante">
                                            </div>

                                        </div>

                                        @endif

                                    </div>

                                    <div class="modal-footer">

                                        <button class="btn btn-success" wire:click="guardarPago" wire:loading.attr="disabled">
                                            Guardar
                                        </button>

                                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                                            Cerrar
                                        </button>

                                    </div>

                                </div>
                            </div>
                        </div>

                        <div wire:ignore.self class="modal fade" id="modalVerPago" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h4 class="modal-title"> Información del Pago </h4>

                                        <button type="button" class="close" data-bs-dismiss="modal"> × </button>
                                    </div>

                                    <div class="modal-body">

                                        @if($detalleSeleccionado && $registroPago)

                                        <div class="row">

                                            <div class="col-md-6">
                                                <label>Cuota</label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    value="{{ $detalleSeleccionado->numero_cuota }}"
                                                    readonly>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Valor</label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    value="$ {{ number_format($registroPago->valor,2,'.',',') }}"
                                                    readonly>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Forma de Pago</label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    value="{{ $registroPago->forma_pago }}"
                                                    readonly>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Banco</label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    value="{{ optional($registroPago->banco)->nombre }}"
                                                    readonly>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Fecha del Comprobante</label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    value="{{ $registroPago->fecha_comprobante }}"
                                                    readonly>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Hora del Comprobante</label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    value="{{ $registroPago->hora_comprobante }}"
                                                    readonly>
                                            </div>

                                            <div class="col-md-6">
                                                <label># Comprobante</label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    value="{{ $registroPago->numero_comprobante }}"
                                                    readonly>
                                            </div>

                                            <div class="col-md-6">
                                                <label>Comprobante</label>

                                                @if($detalleSeleccionado->path)

                                                <a href="{{ asset('storage/'.$detalleSeleccionado->path) }}"
                                                    target="_blank"
                                                    class="btn btn-danger btn-block">

                                                    <i class="fa fa-file-pdf"></i>
                                                    Ver Comprobante

                                                </a>

                                                @else

                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    value="No existe comprobante"
                                                    readonly>

                                                @endif

                                            </div>

                                        </div>

                                        @endif

                                    </div>

                                    <div class="modal-footer">

                                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                                            Cerrar
                                        </button>

                                    </div>

                                </div>
                            </div>
                        </div>

                        <script>
                            window.addEventListener('show-modal-pago', () => {
                                $('#modalPago').modal('show');
                            });

                            window.addEventListener('close-modal-pago', () => {
                                $('#modalPago').modal('hide');
                            });

                            window.addEventListener('show-modal-ver-pago', () => {
                                $('#modalVerPago').modal('show');
                            });

                            window.addEventListener('close-modal-ver-pago', () => {
                                $('#modalVerPago').modal('hide');
                            });
                        </script>

                        <div class="text-muted">
                            <p class="text-sm">Usuario Creado
                                <b class="d-block">{{ $header->user_created_name }}</b>
                            </p>
                            <p class="text-sm">Garante
                                <b class="d-block">{{ $header->customer_garante_name }}</b>
                            </p>
                        </div>
                        @else
                        <h3 class="text-primary"><i class="far fa-fw fa-user"></i>Información General</h3>
                        <div class="text-muted">
                            <p class="text-sm">Email
                                <b class="d-block">{{Auth::user()->email}}</b>
                            </p>
                            <p class="text-sm">Identificación
                                <b class="d-block">{{ Auth::user()->ruc }}</b>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>