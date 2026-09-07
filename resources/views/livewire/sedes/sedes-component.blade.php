<div>
    <div class="col-3 mt-2 mb-2">
        <button wire:click="abrirModal(0);" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
            class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i><b> Sedes</b></button>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><b>Sedes</b></h3>
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
                            <tr class="text-center">
                                <th style="width: 1%;">id</th>
                                <th>Nombre</th>
                                <th>Descripcion</th>
                                <th>Establecimiento</th>
                                <th>Matriz</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sedes as $sed)
                                <tr class="text-center">
                                    <td>{{ $sed->id }}</td>
                                    <td>{{ $sed->name }}</td>
                                    <td>{{ $sed->descripcion }}</td>
                                    <td><b>{{ $sed->establecimiento }}</b></td>
                                    <td>
                                        @if ($sed->matriz)
                                            <b>SI</b>
                                        @else
                                            <b>NO</b>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($sed->status)
                                            <small class="badge bg-primary"
                                                wire:click="cambioEstado({{ $sed->id }})"></i>Activo</small>
                                        @else
                                            <small class="badge bg-danger"
                                                wire:click="cambioEstado({{ $sed->id }})"></i>Desactivado</small>
                                        @endif
                                    </td>
                                    <td>
                                        <button wire:click="abrirModal({{ $sed->id }})" type="button"
                                            data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button"
                                            class="btn bg-light btn-xs"><i class="fa fa-pen"></i>
                                        </button>
                                        {{-- <button wire:click="borrarConceptos({{ $sed->id }})"
                                            class="btn bg-light btn-xs"><i class="fa fa-trash"></i>
                                        </button> --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $sedes->links() }}
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
                    <h4 class="modal-title"> Sedes </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeConceptos">
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="callout callout-warning">
                                <h5>Verifica estas observaciones.</h5>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </div>
                        @endif
                        <div class="row mb-2">
                            <div class="col-12"style="margin-bottom: 10px;">
                                <label>Nombre</label>
                                <input type="text" class="form-control" placeholder="Ingrese un nombre"
                                    wire:model="nombre">
                            </div>
                            <div class="col-12"style="margin-bottom: 10px;">
                                <label>Descripcion</label>
                                <textarea type="text" class="form-control" placeholder="Descripcion" wire:model="descripcion"></textarea>
                            </div>
                            <div class="col-6"style="margin-bottom: 10px;">
                                <label>Establecimiento</label>
                                <input type="text" class="form-control" placeholder="001"
                                    wire:model="establecimiento">
                            </div>
                            <div class="col-6"style="margin-bottom: 10px;">
                                <label>Matriz</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="matriz">
                                    <label class="form-check-label">Matriz</label>
                                </div>
                            </div>
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
