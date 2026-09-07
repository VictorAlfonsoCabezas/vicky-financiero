<div>
    <div>
        <div class="col-3 mt-2 mb-2">
            <button wire:click="abrirModal(0);" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
                class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i><b> Nuevo Conceptos</b></button>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><b>Conceptos</b></h3>
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
                                    <th>
                                        Tipo Conceptos
                                        <a href="/tipo-concepto" target="_blank" style="font-size: 12px;"> Ver</a>
                                    </th>
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($conceptos as $con)
                                    <tr class="text-center">
                                        <td>{{ $con->id }}</td>
                                        <td>{{ $con->concepto_nombre }}</td>
                                        <td>{{ $con->nombre }}</td>
                                        <td>
                                            @if ($con->status)
                                                <small class="badge bg-primary"
                                                    wire:click="cambioEstado({{ $con->id }})"></i>Activo</small>
                                            @else
                                                <small class="badge bg-danger"
                                                    wire:click="cambioEstado({{ $con->id }})"></i>Desactivado</small>
                                            @endif
                                        </td>
                                        <td>
                                            <button wire:click="abrirModal({{ $con->id }})" type="button"
                                                data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button"
                                                class="btn bg-light btn-xs"><i class="fa fa-pen"></i>
                                            </button>
                                            {{-- <button wire:click="borrarConceptos({{ $con->id }})"
                                                class="btn bg-light btn-xs"><i class="fa fa-trash"></i>
                                            </button> --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $conceptos->links() }}
                    </div>
                </div>

                {{-- MODAL --}}
                <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
                    data-bs-backdrop="static">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header bg-primary">
                                <h4 class="modal-title"> Nueva Conceptos </h4>
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
                                                wire:model="nombre" id="nombre">
                                        </div>
                                        <div class="col-12" style="margin-bottom: 10px;">
                                            <label for="country_id">Tipo</label>
                                            <select title="Seleccionar" class="form-control"
                                                wire:model="tipo_concepto_id">
                                                <option> - Seleccione uno - </option>
                                                @foreach ($tipos as $tip)
                                                    <option value="{{ $tip->id }}">{{ $tip->nombre }}</option>
                                                @endforeach
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
        </div>
    </div>
</div>
