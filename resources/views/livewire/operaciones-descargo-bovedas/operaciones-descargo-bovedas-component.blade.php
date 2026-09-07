<div>
    <div class="col-3 mt-2 mb-2">
        <button wire:click="abrirModal(0);" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
            class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i><b> Nueva Operación</b></button>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><b>Operaciones</b></h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                        <thead>
                            <tr>
                                <th style="width: 1%;">id</th>
                                <th>Nombre</th>
                                <th>Descripcion</th>
                                <th>Nombre Corto</th>
                                <th>Estado</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($operaciones as $oper)
                                <tr>
                                    <td>{{ $oper->id }}</td>
                                    <td>{{ $oper->nombre }}</td>
                                    <td>{{ $oper->descripcion }}</td>
                                    <td>{{ $oper->nombre_corto }}</td>
                                    <td>
                                        @if ($oper->status)
                                            <small class="badge bg-primary"
                                                wire:click="cambioEstado({{ $oper->id }})"></i>Activo</small>
                                        @else
                                            <small class="badge bg-danger"
                                                wire:click="cambioEstado({{ $oper->id }})"></i>Desactivado</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- <button wire:click="abrirModal({{ $oper->id }})" type="button"
                                            data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button"
                                            class="btn bg-light btn-xs"><i class="fa fa-pen"></i>
                                        </button> --}}
                                        {{-- <button wire:click="borrarOperacion({{ $oper->id }})"
                                            class="btn bg-light btn-xs"><i class="fa fa-trash"></i>
                                        </button> --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $operaciones->links() }}
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
                    <h4 class="modal-title">Operaciones </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeHeader">
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="callout callout-warning">
                                <h5>Verifica estas observaciones.</h5>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </div>
                        @endif
                        <div class="row mb-6">
                            <div class="col-12">
                                <label>Nombre</label>
                                <input type="text" class="form-control" placeholder="Ingresa Boveda Original"
                                    wire:model="nombre">
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-12">
                                <label>Descripción</label>
                                <input type="text" class="form-control" placeholder="Ingresa Boveda destino"
                                    wire:model="descripcion">
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-12">
                                <label>Nombre corto</label>
                                <input type="text" class="form-control" placeholder="Ingresa Boveda destino"
                                    wire:model="nombre_corto">
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-12">
                                <label>Afecta</label>
                                <select class="form-control" wire:model="afecta">
                                    <option value="E">Empresa</option>
                                    <option value="C">Cliente</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-12">
                                <label>Accion</label>
                                <select class="form-control" wire:model="accion">
                                    <option value="S">SUMA</option>
                                    <option value="R">RESTA</option>
                                </select>
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
