<div>
    <div class="row">
        <div class="col-md-6">
            <div class="input-group input-group-sm mb-2 mt-2">
                <input type="text" wire:model="search" id="search" class="form-control" placeholder="Buscar Nombres, Apellidos, Identificación">
                <div class="input-group-append">
                    <div class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-6">
            <div class="input-group">
                <input type="date" class="form-control" wire:model="fechaInicio">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                </div>
                <input type="date" class="form-control" wire:model="fechaFin">
            </div>
        </div>
    </div>
    <div class="col-md-12 mt-3">
        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-valign-middle table-sm">
                <thead>
                    <tr>
                        <th>Valor</th>
                        <th>Cliente Solicita</th>
                        <th>Fecha Creación</th>
                        <th>Observación</th>
                        <th>Estado</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movimientos as $key => $mov)
                    <tr>
                        <td class="h4">
                            <b> $ {{ $mov->valor }}</b>
                        </td>
                        <td>
                            <i>{{ $mov->nombres }} {{ $mov->apellidos }}</i>
                            <br><b>{{ $mov->numero_documento }}</b>
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
                            <button wire:click="abrirModal({{ $mov->id }});" data-bs-toggle="modal" data-bs-target="#modalGeneral1" type="button" class="btn btn-block bg-primary btn-xs"><i class="fa fa-eye"></i>
                                Ver</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="h3 text-center" colspan="7">
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
    <div wire:ignore.self class="modal fade" id="modalGeneral1" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title"> Imagen </h4>
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
                                    <div class="card-body" style="display: block;">
                                        <img class="img-fluid pad" src="{{ $this->path }}" alt="Photo">
                                        <a href="{{ asset($this->path) }}" target="_blank" class="btn btn-default btn-sm"><i class="fas fa-file"></i> Descargar</a>
                                        <span class="float-end text-muted">Comprobante de Desposito</span>
                                    </div>
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
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
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
                                <textarea class="form-control" rows="3" placeholder="Ingrese una razón del rechazo, obligatorio" wire:model="razon_rechazo"></textarea>
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