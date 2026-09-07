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
                    <h3 class="card-title"><b>Lista de Retenciones</b></h3>
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
                                <th>Código</th>
                                <th>Porcentaje</th>
                                <th>Tipo de Retención</th>
                                <th>Estado</th>
                                <th>Acciónes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($listaRetencion as $lista)
                            <tr>
                                <td class="text-center">{{ $lista->id }}</td>
                                <td class="text-center">{{ $lista->nombre }}</td>
                                <td class="text-center"><b>{{ $lista->codigo }}</b></td>
                                <td class="text-center">{{ $lista->porcentaje }}</td>
                                <td class="text-center">{{ $lista->tipoRetencion->name }}</td>
                                <td class="text-center">
                                    @if ($lista->status)
                                    <small class="badge bg-primary"
                                        wire:click="cambioEstado({{ $lista->id }})"></i>Activo</small>
                                    @else
                                    <small class="badge bg-danger"
                                        wire:click="cambioEstado({{ $lista->id }})"></i>Desactivado</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button wire:click="editarListaRetencion({{ $lista->id }})" type="button"
                                        data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button"
                                        class="btn btn-outline-primary btn-xs" title="Editar"><i
                                            class="fa fa-pen"></i>
                                    </button>
                                    <!-- <button wire:click="borrarImpuestos({{ $lista->id }})"
                                        class="btn bg-light btn-xs"><i
                                            class="fa fa-trash"></i>
                                    </button> -->
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-2 mb-2 ms-3">
                        {{ $listaRetencion->links() }}
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
                    <h4 class="modal-title">Lista de Retenciones</h4>
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
                            <div class="col-6">
                                <label>Nombre</label>
                                <input type="text" class="form-control" placeholder="Ingrese un nombre"
                                    wire:model="nombre">
                            </div>
                            <div class="col-6">
                                <label>Código</label>
                                <input type="text" class="form-control" placeholder="código"
                                    wire:model="codigo">
                            </div>
                            <div class="col-6">
                                <label>Porcentaje</label>
                                <input type="text" class="form-control" placeholder="0.00"
                                    wire:model="porcentaje">
                            </div>
                            <div class="col-6">
                                <label>Tipo de Retención</label>
                                <select class="form-control" wire:model="tipo_retencion_id">
                                    <option value="">Seleccione</option>
                                    @foreach ($tiposRetencion as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->name }}</option>
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