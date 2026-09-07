<div>
    <div class="col-3 mt-2 mb-2">
        <button wire:click="abrirModal(0);" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" class="btn btn-primary btn-flat"><i class="fa fa-plus"></i><b></b></button>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><b>Nivel Académico</b></h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm">
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                        <thead>
                            <tr class="text-center">
                                <th style="width: 1%;">id</th>
                                <th>Nombre</th>
                                <th>Defecto</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($niveles as $niv)
                            <tr class="text-center">
                                <td>{{ $niv->id }}</td>
                                <td>{{ $niv->nombre }}</td>
                                <td>
                                    @if ($niv->defecto)
                                    <small class="badge bg-primary" wire:click="cambioDefecto({{ $niv->id }})"></i>Activo</small>
                                    @else
                                    <small class="badge bg-danger" wire:click="cambioDefecto({{ $niv->id }})"></i>Desactivado</small>
                                    @endif
                                </td>
                                <td>
                                    @if ($niv->status)
                                    <small class="badge bg-primary" wire:click="cambioEstado({{ $niv->id }})"></i>Activo</small>
                                    @else
                                    <small class="badge bg-danger" wire:click="cambioEstado({{ $niv->id }})"></i>Desactivado</small>
                                    @endif
                                </td>
                                <td>
                                    <button wire:click="editar({{ $niv->id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button" class="btn bg-light btn-xs"><i class="fa fa-pen"></i>
                                    </button>
                                    <!-- <button wire:click="borrar({{ $niv->id }})" class="btn bg-light btn-xs"><i class="fa fa-trash"></i>
                                    </button> -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 mb-2">
                    {{ $niveles->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Nivel Académico</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        @if ($errors->any())
                        <div class="col-12">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-danger"><b>Revisa la siguiente información</b></span>
                                    @foreach ($errors->all() as $error)
                                    <small class="text-danger">{{ $error }}</small>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row mb-2">
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label>Nombre</label>
                                <input type="text" class="form-control" placeholder="Ingrese un nombre" wire:model="nombre">
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>