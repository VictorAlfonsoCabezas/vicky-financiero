<div>
    <div>
        <div>
            <div class="col-3 mt-2 mb-2">
                <button wire:click="abrirModal(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i> Parentezco</button>
            </div>

            <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                <thead>
                    <tr>
                        <th style="width: 1%;">#</th>
                        <th>Nombre</th>
                        <th>Descripcion</th>
                        <th>Defecto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($parentezco as $par)
                    <tr>
                        <td>{{ $par->id }}</td>
                        <td>{{ $par->nombre }}</td>
                        <td>{{ $par->description }}</td>
                        <td>
                            @if ($par->defecto)
                            <small class="badge bg-primary" wire:click="cambioDefecto({{ $par->id }})"></i>Activo</small>
                            @else
                            <small class="badge bg-danger" wire:click="cambioDefecto({{ $par->id }})"></i>Desactivado</small>
                            @endif
                        </td>
                        <td>
                            @if ($par->status)
                            <small class="badge bg-primary" wire:click="cambioEstado({{ $par->id }})"></i>Activo</small>
                            @else
                            <small class="badge bg-danger" wire:click="cambioEstado({{ $par->id }})"></i>Desactivado</small>
                            @endif
                        </td>
                        <td>
                            <button wire:click="editarParentezco({{ $par->id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button" class="btn bg-light btn-xs"><i class="fa fa-pen"></i></button>
                            <button wire:click="borrarParentezco({{ $par->id }})" class="btn bg-light btn-xs"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $parentezco->links() }}
        </div>

        {{-- MODAL --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-xs">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"> Parentezco</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="storeParentezco">
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
                                    <input type="text" class="form-control" placeholder="Ingrese un nombre" wire:model="nombre" id="nombre">
                                </div>
                                <div class="col-12">
                                    <label>Descripción</label>
                                    <textarea class="form-control" rows="3" placeholder="Ingrese una descripción, esto es opcional" wire:model="description"></textarea>
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

</div>