<div>
    <div class="col-3 mt-2 mb-2">
        <button wire:click="modulos(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
            class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i> Nuevo</button>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Módulos</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm">
                            <input type="text" wire:model="search" class="form-control float-end"
                                placeholder="Buscar">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-head-fixed text-nowrap table-hover table-striped"
                        style="font-size: 15px;">
                        <thead>
                            <tr>
                                <th style="width: 1%;">#</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($modulos as $mod)
                                <tr>
                                    <td>{{ $mod->id }}</td>
                                    <td>{{ $mod->nombre }}</td>
                                    <td>{{ $mod->description }}</td>
                                    <td>
                                        <button wire:click="modulos({{ $mod->id }})" type="button"
                                            data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button"
                                            class="btn bg-light btn-xs"><i class="fa fa-pen"></i></button>
                                    </td>
                                    <td>
                                        @if ($mod->status)
                                            <small class="badge bg-primary"
                                                wire:click="cambioEstado({{ $mod->id }})"></i>Activo</small>
                                        @else
                                            <small class="badge bg-danger"
                                                wire:click="cambioEstado({{ $mod->id }})"></i>Desactivado</small>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $modulos->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title"> Módulos</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="callout callout-warning">
                                <h5>Verifica estas observaciones.</h5>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </div>
                        @endif
                        <div class="row mb-4">
                            <div class="col-12">
                                <label>Nombre</label>
                                <input type="text" class="form-control" placeholder="Nombre" wire:model="nombre"
                                    id="name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label>Descripción</label>
                                <textarea class="form-control" rows="3" placeholder="Descripcion del Modulo" wire:model="description"
                                    id="description"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
