<div>
    <div class="d-flex justify-content-between align-items-center">
        <div class="col-3 mt-2 mb-2">
            <button wire:click="abrirModal(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
                class="btn btn-primary"><i class="fa fa-plus"></i>
            </button>
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
                    <h3 class="card-title"><b>Impuestos</b></h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                        <thead>
                            <tr class="text-center">
                                <th style="width: 1%;">#</th>
                                <th>Nombre</th>
                                <th>Descripcion</th>
                                <th>Porcentaje</th>
                                <th>Defecto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($impuestos as $imp)
                            <tr>
                                <td class="text-center">{{ $imp->id }}</td>
                                <td class="text-center">{{ $imp->nombre }}</td>
                                <td class="text-center">{{ $imp->descripcion }}</td>
                                <td class="text-center">{{ $imp->valor }}<b> %</b></td>
                                <td class="text-center">
                                    @if ($imp->por_defecto)
                                    <i class="fa fa-check-circle text-success fa-lg"></i>
                                    @else
                                    <i class="fa fa-times-circle text-danger fa-lg"></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($imp->status)
                                    <small class="badge bg-primary"
                                        wire:click="cambioEstado({{ $imp->id }})"></i>Activo</small>
                                    @else
                                    <small class="badge bg-danger"
                                        wire:click="cambioEstado({{ $imp->id }})"></i>Desactivado</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button wire:click="editarImpuestos({{ $imp->id }})" type="button"
                                        data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button"
                                        class="btn btn-outline-primary btn-xs" title="Editar"><i
                                            class="fa fa-pen"></i>
                                    </button>
                                    <!-- <button wire:click="borrarImpuestos({{ $imp->id }})"
                                        class="btn bg-light btn-xs"><i
                                            class="fa fa-trash"></i>
                                    </button> -->
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-2 mb-2 ms-3">
                        {{ $impuestos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Impuesto</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeImpuestos">
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
                        <div class="row mb-6">
                            <div class="col-12">
                                <label>Nombre</label>
                                <input type="text" class="form-control" placeholder="Ingrese un nombre"
                                    wire:model="nombre">
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-12">
                                <label>Descripción</label>
                                <textarea class="form-control" rows="3" placeholder="Ingrese una descripción, esto es opcional"
                                    wire:model="descripcion"></textarea>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-4">
                                <label>Procentaje</label>
                                <input type="number" step="any" class="form-control" placeholder="Valor"
                                    wire:model="valor">
                            </div>
                            <div class="col-4">
                                <label>Código SRI</label>
                                <input type="number" class="form-control" placeholder="Código"
                                    wire:model="codigo">
                            </div>
                            <div class="col-2 mt-4 ms-4">
                                <input class="form-check-input" type="checkbox" wire:model="por_defecto">
                                <label class="form-check-label">Por Defecto</label>
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