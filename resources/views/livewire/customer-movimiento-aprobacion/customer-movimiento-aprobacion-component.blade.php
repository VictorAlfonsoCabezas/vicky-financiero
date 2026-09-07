<div>
    <div class="row">
        <div class="col-md-6">
            <div class="input-group input-group-sm mb-2 mt-2">
                <input type="text" wire:model="search" id="search" class="form-control"
                    placeholder="Buscar Nombres, Apellidos, Identificación">
                <div class="input-group-append">
                    <div class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 mt-3">
        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-valign-middle table-sm">
                <thead>
                    <tr>
                        <th>Valor</th>
                        <th>Entidad</th>
                        <th>Cliente Solicita</th>
                        <th>Cuenta</th>
                        <th>Fecha Creación</th>
                        <th>Observación</th>
                        <th>Estado</th>
                        <th>Ver</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movimientos as $key => $mov)
                    <tr>
                        <td class="h4">
                            <b> $ {{ $mov->valor }}</b>
                        </td>
                        <td>
                            <span class="text-primary">{{ $mov->banco }}</span>
                            <br><b>#{{ $mov->numero_cuenta }}</b>
                        </td>
                        <td>
                            <i>{{ $mov->nombres }} {{ $mov->apellidos }}</i>
                            <br><b>{{ $mov->numero_documento }}</b>
                        </td>
                        <td>
                            <i>{{ $mov->customerTipoAhorro->tipoAhorros->name ?? 'N/A' }}</i>
                            <br>
                            <b>{{ $mov->customerTipoAhorro->codigo ?? 'N/A' }}</b>
                        </td>
                        <td>
                            {{ $mov->fecha_creacion }}
                        </td>
                        <td>
                            {{ $mov->observacion }}
                        </td>
                        <td>
                            @if ($mov->estado == 'PENDIENTE')
                            <span class="badge bg-primary"><i class="fa fa-clock"></i>
                                {{ $mov->estado }}</span>
                            @elseif($mov->estado == 'APROBADO')
                            <span class="badge bg-success"><i class="fa fa-check"></i>
                                {{ $mov->estado }}</span>
                            @elseif($mov->estado == 'RECHAZADO')
                            <span class="badge bg-danger"><i class="fa fa-times"></i>
                                {{ $mov->estado }}</span>
                            @endif
                        </td>
                        <td>
                            <button wire:click="abrirModal({{ $mov->id }});" data-bs-toggle="modal"
                                data-bs-target="#modalGeneral1" type="button"
                                class="btn btn-block bg-primary btn-xs"><i class="fa fa-eye"></i>
                                Ver</button>
                        </td>
                        <td>
                            @if ($mov->estado == 'PENDIENTE')
                            <button wire:click="aceptar({{ $mov->id }});" type="button"
                                class="btn btn-block bg-success btn-xs"><i class="fa fa-check"></i>
                                Aprobar</button>
                            <button wire:click="seleccionar({{ $mov->id }});" type="button" data-bs-toggle="modal"
                                data-bs-target="#modalGeneral" class="btn btn-block bg-danger btn-xs"><i
                                    class="fa fa-times"></i>
                                Rechazar</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="h3 text-center" colspan="8">
                            No existe Solitudes
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $movimientos->links() }}
        </div>
    </div>

    {{-- MODAL IMAGEN --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral1" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Detalles de la Transferencia </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-widget">
                                    <div class="card-header">
                                        <div class="user-block">
                                            <img class="img-circle" src="/img/sinusuario.jpg" alt="User Image">
                                            <span class="username"><a href="#">{{ $this->user_nombres }}</a></span>
                                            <span class="description">Fecha creación:
                                                {{ $this->fecha_creacion }}</span>
                                        </div>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div
                                            class="d-flex justify-content-between align-items-center border-bottom mb-3">
                                            <p class="d-flex flex-column text-end">
                                                <span class="font-weight-bold">
                                                    <i class="fas fa-money-bill-wave"></i> Valor</strong>
                                                </span>
                                                <span class="text-muted">$ {{ $valor }}</span>
                                            </p>
                                            <p class="d-flex flex-column text-end">
                                                <span class="font-weight-bold">
                                                    <i class="fa fa-university"></i> {{$banco}}
                                                </span>
                                                <span class="text-muted"># {{$numero_cuenta}}</span>
                                            </p>
                                        </div>
                                        <div>
                                            <b># Comprobante:</b> <i>{{ $comprobante }}</i>
                                        </div>
                                        <div>
                                            <b># Documento:</b> <i>{{ $numero_deposito }}</i>
                                        </div>
                                        <div>
                                            <b>Observacion:</b> <i>{{ $observacionTransferencia }}</i>
                                        </div>
                                    </div>
                                    @if(!empty($path))
                                    <div class="card-body" style="display: block;">
                                        <img class="img-fluid pad" src="{{ $this->path }}" alt="Photo">
                                        <a href="{{ asset($this->path) }}" target="_blank"
                                            class="btn btn-default btn-sm"><i class="fas fa-file"></i> Descargar</a>
                                        <span class="float-end text-muted">Comprobante de Desposito</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL RECHAZO --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title"> Rechazar </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeRechazar">
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
                                <label>Descripción</label>
                                <textarea class="form-control" rows="3"
                                    placeholder="Ingrese una razón del rechazo, obligatorio"
                                    wire:model="razon_rechazo"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-danger">Guardar</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>