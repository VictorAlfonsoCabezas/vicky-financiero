<div>
    <div class="d-flex justify-content-between align-items-center">
        <div class="col-3 mt-2 mb-2">
            <button wire:click="abrirModal(0);" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
                class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i><b> Nueva Parroquia</b></button>
        </div>
        <div class="card-tools d-flex justify-content-end">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" wire:model="search" id="search" class="form-control float-end"
                    placeholder="Buscar">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-default">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><b>Parroquia</b></h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
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
                            @foreach ($parroquia as $par)
                                <tr class="text-center">
                                    <td>{{ $par->id }}</td>
                                    <td>{{ $par->nombre }}</td>
                                    <td>
                                        @if ($par->defecto)
                                            <small class="badge bg-primary"
                                                wire:click="cambioDefecto({{ $par->id }})"></i>Activo</small>
                                        @else
                                            <small class="badge bg-danger"
                                                wire:click="cambioDefecto({{ $par->id }})"></i>Desactivado</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($par->status)
                                            <small class="badge bg-primary"
                                                wire:click="cambioEstado({{ $par->id }})"></i>Activo</small>
                                        @else
                                            <small class="badge bg-danger"
                                                wire:click="cambioEstado({{ $par->id }})"></i>Desactivado</small>
                                        @endif
                                    </td>
                                    <td>
                                        <button wire:click="editarParroquia({{ $par->id }})" type="button"
                                            data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button"
                                            class="btn bg-light btn-xs"><i class="fa fa-pen"></i>
                                        </button>
                                        <button wire:click="borrarParroquia({{ $par->id }})"
                                            class="btn bg-light btn-xs"><i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 mb-2">
                    {{ $parroquia->links() }}
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
                    <h4 class="modal-title"> Nueva Parroquia </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeParroquia">
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
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label>Nombre</label>
                                <input type="text" class="form-control" placeholder="Ingrese un nombre"
                                    wire:model="nombre" id="nombre">
                            </div>
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label for="provincia_id">Provincia</label>
                                <select title="Seleccionar" class="form-control" wire:model="provincia_id">
                                    <option> - Seleccione uno - </option>
                                    @foreach ($provincia as $pro)
                                        <option value="{{ $pro->id }}">{{ $pro->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-success">Guardar </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div
