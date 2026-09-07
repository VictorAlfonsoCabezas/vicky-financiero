<div>
    <div class="row justify-content-between">
        <div class="col-3 mt-2 mb-2">
            <button type="button" class="btn btn-primary btn-block" wire:click="abrirModal(0);" data-bs-toggle="modal" data-bs-target="#modalGeneral"><i class="fa fa-plus"></i> Agregar Categoria</button>
        </div>
        <div class="col-3 mt-2 mb-2">
            <div class="input-group input-group-sm">
                <input type="text" wire:model="search" class="form-control float-end" placeholder="Buscar">
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
            <div class="card card-default">
                <div class="card-header" style="padding: 8px;">
                    <h5 class="card-title"><i class="fa fa-comment-dollar"></i> <b>Categorias Gastos</b></h5>
                </div>
                <div class="card-body table-responsive p-2">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr style="padding: 10px;">
                                <th style="width: 1%;">#</th>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categorias as $cat)
                            <tr>
                                <td>{{ $cat->id }}</td>
                                <td><b>{{ $cat->nombre }}</b></td>
                                <td>
                                    @if ($cat->status)
                                    <small class="badge bg-primary" wire:click="cambioEstado({{ $cat->id }})"></i>Activo</small>
                                    @else
                                    <small class="badge bg-danger" wire:click="cambioEstado({{ $cat->id }})"></i>Desactivado</small>
                                    @endif
                                </td>
                                <td>
                                    <button wire:click="editarCategorias({{ $cat->id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button" class="btn bg-light btn-xs"><i class="fa fa-pen"></i>
                                    </button>
                                    {{-- <button wire:click="borrarCategorias({{ $cat->id }})"
                                    class="btn bg-light btn-xs"><i class="fa fa-trash"></i>
                                    </button> --}}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center" colspan="4">No Existen productos</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $categorias->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title"><i class="fa fa-comment-dollar"></i> Categorias Gastos </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeCategorias">
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
                                <input type="text" class="form-control" placeholder="Ingrese un nombre" wire:model="nombre" id="nombre">
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