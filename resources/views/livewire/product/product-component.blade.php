<div>
    <div class="row justify-content-between">
        <div class="col-3 mt-2 mb-2">
            <button type="button" wire:click="abrirModal(0);" data-bs-toggle="modal" data-bs-target="#modalGeneral"
                class="btn btn-primary btn-sm"><b> Crear</b></button>
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
                    <h5 class="card-title"><i class="fa fa-barcode"></i> <b>Productos</b></h5>
                </div>
                <div class="card-body table-responsive p-2">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr style="padding: 10px;">
                                <th style="width: 10px">#</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Precio</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th style="width: 40px">Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productos as $prod)
                                <tr>
                                    <td>{{ $prod->id }}</td>
                                    <td>{{ $prod->name }}</td>
                                    <td>
                                        @if ($prod->tipo == 'P')
                                            <span class="description-percentage text-success">Producto</span>
                                        @else
                                            <span class="description-percentage text-primary">Servicio</span>
                                        @endif
                                    </td>
                                    <td>
                                        <b>${{ $prod->precio_a }}</b>
                                    </td>
                                    <td><small>{{ $prod->description }}</small></td>
                                    <td>
                                        @if ($prod->status)
                                            <small wire:click="cambioEstado({{ $prod->id }})"
                                                class="badge bg-primary">Activo</small>
                                        @else
                                            <small wire:click="cambioEstado({{ $prod->id }})"
                                                class="badge bg-danger">Inactivo</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btn-xs text-white"wire:click="abrirModal({{ $prod->id }});"
                                            data-bs-toggle="modal" data-bs-target="#modalGeneral"> <i
                                                class="fa fa-pen"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="6">No Existen productos</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $productos->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title"> Productos / Servicios </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white;">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeProduct">
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
                            <div class="col-4"style="margin-bottom: 10px;">
                                <label>Nombre</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Nombre Proveedor"
                                        wire:model="nombre">
                                </div>
                            </div>
                            <div class="col-4"style="margin-bottom: 10px;">
                                <label>Tipo</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-hashtag"></i></span>
                                    </div>
                                    <select class="form-control" wire:model="tipo">
                                        <option disablerd>Seleccione</option>
                                        <option value="P">Producto</option>
                                        <option value="S">Servicio</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4"style="margin-bottom: 10px;">
                                <label>Precio</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-dollar"></i></span>
                                    </div>
                                    <input type="number" step="any" class="form-control" placeholder="0.00"
                                        wire:model="precio_a">
                                </div>
                            </div>
                            <div class="col-12"style="margin-bottom: 10px;">
                                <label>Descripcion</label>
                                <textarea type="text" class="form-control" placeholder="Descripcion" wire:model="description"></textarea>
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
