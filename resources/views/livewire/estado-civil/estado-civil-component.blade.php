<div>
    <div>
        <div>
            <div class="col-3 mt-2 mb-2">
                <button wire:click="abrirModal(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
                    class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i><b> Nuevo Estado Civil</b></button>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><b>Estado Civil</b></h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0" style="height: 300px;">
                            <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 1%;">#</th>
                                        <th>Nombre</th>
                                        <th>Defecto</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($estado as $est)
                                        <tr class="text-center">
                                            <td>{{ $est->id }}</td>
                                            <td>{{ $est->nombre }}</td>
                                            <td>
                                                @if ($est->defecto)
                                                    <small class="badge bg-primary"
                                                        wire:click="cambioDefecto({{ $est->id }})"></i>Activo</small>
                                                @else
                                                    <small class="badge bg-danger"
                                                        wire:click="cambioDefecto({{ $est->id }})"></i>Desactivado</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($est->status)
                                                    <small class="badge bg-primary"
                                                        wire:click="cambioEstado({{ $est->id }})"></i>Activo</small>
                                                @else
                                                    <small class="badge bg-danger"
                                                        wire:click="cambioEstado({{ $est->id }})"></i>Desactivado</small>
                                                @endif
                                            </td>
                                            <td>
                                                <button wire:click="editarEstadoCivil({{ $est->id }})"
                                                    type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
                                                    type="button" class="btn bg-light btn-xs"><i
                                                        class="fa fa-pen"></i></button>
                                                <button wire:click="borrarEstadoCivil({{ $est->id }})"
                                                    class="btn bg-light btn-xs"><i
                                                        class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $estado->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- MODAL --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Nuevo Estado Civil </h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="storeEstadoCivil">
                        <div class="modal-body">
                            @if ($errors->any())
                                <div class="callout callout-warning">
                                    <h5>Verifica estas observaciones.</h5>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </div>
                            @endif
                            <div class="col-12"style="margin-bottom: 10px;">
                                <label>Nombre</label>
                                <input type="text" class="form-control" placeholder="Ingrese un nombre"
                                    wire:model="nombre" id="nombre">
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
