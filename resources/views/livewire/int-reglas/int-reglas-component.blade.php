<div>
    <div>
        <div class="col-3 mt-2 mb-2">
            <button wire:click="abrirModal(0);" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
                class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i> Nueva Regla</button>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Reglas Interés</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm">
                                <input type="text" wire:model="search" id="search"
                                    class="form-control float-end" placeholder="Buscar">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                            <thead>
                                <tr>
                                    <th style="width: 1%;">#</th>
                                    <th>Nombre</th>
                                    <th>Valor</th>
                                    <th class="text-center">Operación</th>
                                    <th class="text-center">Fecha Creación</th>
                                    <th>Acciones</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reglas as $tip)
                                    <tr>
                                        <td>{{ $tip->id }}</td>
                                        <td>{{ $tip->nombre }}</td>
                                        <td><b>$ {{ $tip->valor }}</b></td>
                                        <td class="text-center">{{ $tip->operacion }}</td>
                                        <td class="text-center">{{ $tip->created_at }}</td>
                                        <td>
                                            <button wire:click="editarRegla({{ $tip->id }})" type="button"
                                                data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button"
                                                class="btn bg-light btn-xs"><i class="fa fa-pen"></i></button>
                                            <button wire:click="borrarRegla({{ $tip->id }})"
                                                class="btn bg-light btn-xs"><i
                                                    class="fa fa-trash"></i></button>
                                        </td>
                                        <td>
                                            @if ($tip->status)
                                                <small class="badge bg-primary"
                                                    wire:click="cambioEstado({{ $tip->id }})"></i>Activo</small>
                                            @else
                                                <small class="badge bg-danger"
                                                    wire:click="cambioEstado({{ $tip->id }})"></i>Desactivado</small>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No existen Registros</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $reglas->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL REGLAS --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"> Regla Intereses</h4>
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
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label>Tipo Ahorros</label>
                                    <select class="form-control" wire:model="tipo_ahorro_id">
                                        <option value="0">Ninguno</option>
                                        @foreach ($tipoAhorros as $tip)
                                            <option value="{{ $tip->id }}">{{ $tip->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" placeholder="Ingrese un nombre"
                                        wire:model="nombre" id="nombre">
                                </div>
                                <div class="col-3">
                                    <label>Texto</label>
                                    <input type="text" class="form-control" placeholder="Ingrese un nombre"
                                        wire:model="texto" id="texto">
                                </div>
                                <div class="col-3">
                                    <label>Valor</label>
                                    <input type="number" step="any" class="form-control"
                                        placeholder="Ingrese un nombre" wire:model="valor" id="valor">
                                </div>
                                <div class="col-3">
                                    <label>Operación</label>
                                    <select class="form-control" wire:model="operacion">
                                        <option value="">-Seleccione-</option>
                                        <option value="(+)">(+)</option>
                                        <option value="(-)">(-)</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label>Descripción</label>
                                    <textarea class="form-control" rows="3" placeholder="Ingrese una descripción, esto es opcional"
                                        wire:model="comentario"></textarea>
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
