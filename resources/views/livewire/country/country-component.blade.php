<div>
    <div class="col-3 mt-2 mb-2">
        <button wire:click="abrirModal(0);" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i><b> Nuevo Pais y Codigo</b></button>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><b>Lista de Países</b></h3>
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
                                <th>Codigo País</th>
                                <th>Codigo Llamada</th>
                                <th>Accion</th>
                                <th>Defecto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($country as $pais)
                            <tr class="text-center">
                                <td>{{ $pais->id }}</td>
                                <td>{{ $pais->nombre }}</td>
                                <td>{{ $pais->codigo_pais }}</td>
                                <td>{{ $pais->codigo_llamada }}</td>
                                <td>
                                    <button wire:click="editarPais({{ $pais->id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button" class="btn bg-light btn-xs"><i class="fa fa-pen"></i>
                                    </button>
                                    <button wire:click="borrarPais({{ $pais->id }})" class="btn bg-light btn-xs"><i class="fa fa-trash"></i>
                                    </button>
                                </td>
                                <td>
                                    @if ($pais->defecto)
                                    <small class="badge bg-primary" wire:click="cambioDefecto({{ $pais->id }})"></i>Activo</small>
                                    @else
                                    <small class="badge bg-danger" wire:click="cambioDefecto({{ $pais->id }})"></i>Desactivado</small>
                                    @endif
                                </td>
                                <td>
                                    @if ($pais->status)
                                    <small class="badge bg-primary" wire:click="cambioEstado({{ $pais->id }})"></i>Activo</small>
                                    @else
                                    <small class="badge bg-danger" wire:click="cambioEstado({{ $pais->id }})"></i>Desactivado</small>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 mb-2">
                    {{ $country->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Nuevo País y Codigo </h4>
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
                        <div class="row mb-2">
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label>Nombre</label>
                                <input type="text" class="form-control" placeholder="Ingrese un nombre" wire:model="nombre" id="nombre">
                            </div>
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label>Codigo Pais</label>
                                <input type="number" class="form-control" placeholder="Ingrese el codigo del pais" wire:model="codigo_pais" id="codigo_pais">
                            </div>
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label>Codigo Llamada</label>
                                <input type="text" class="form-control" placeholder="Ingrese el codigo de llamada" wire:model="codigo_llamada" id="codigo_llamada">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Guardar </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>