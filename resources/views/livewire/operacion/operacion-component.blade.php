<div>
    <div>
        <div class="col-3 mt-2 mb-2">
            <button wire:click="abrirModal(0);" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
                class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i><b>Operaciones</b></button>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><b>Operaciónes</b></h3>
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
                                    <th>Porcentaje</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($operacion as $ope)
                                    <tr class="text-center">
                                        <td>{{ $ope->id }}</td>
                                        <td>{{ $ope->nombre }}</td>
                                        <td><b>{{ $ope->porcentaje }}%</b></td>
                                        <td>
                                            @if ($ope->status)
                                                <small class="badge bg-primary"
                                                    wire:click="cambioEstado({{ $ope->id }})"></i>Activo</small>
                                            @else
                                                <small class="badge bg-danger"
                                                    wire:click="cambioEstado({{ $ope->id }})"></i>Desactivado</small>
                                            @endif
                                        </td>
                                        <td>
                                            <button wire:click="abrirModal({{ $ope->id }})" type="button"
                                                data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button"
                                                class="btn bg-light btn-xs"><i class="fa fa-pen"></i>
                                            </button>
                                            {{-- <button wire:click="borrarConcepto({{ $con->id }})"
                                                class="btn bg-light btn-xs"><i class="fa fa-trash"></i>
                                            </button> --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $operacion->links() }}
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
                <div class="modal-header bg-primary">
                    <h4 class="modal-title">Operacion</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeOperacion">
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
                                <label>Descripción</label>
                                <textarea type="text" class="form-control" placeholder="Ingrese un nombre" wire:model="descripcion"></textarea>
                            </div>
                            <div class="col-12"style="margin-bottom: 10px;">
                                <label>Porcentaje</label>
                                <input type="number" step="0.00" class="form-control" placeholder="0.00%"
                                    wire:model="porcentaje">
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
