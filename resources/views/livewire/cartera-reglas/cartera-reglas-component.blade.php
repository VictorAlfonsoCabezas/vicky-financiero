<div>
    <div class="col-3 mt-2 mb-2">
        <button wire:click="editarRegla(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i> Nuevo</button>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Reglas Cartera</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" wire:model="search" id="search" class="form-control float-end" placeholder="Buscar">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                        <thead>
                            <tr>
                                <th style="width: 1%;">#</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Comparación</th>
                                <th>Dias Mora</th>
                                <th>Acciones</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($carteraReglas as $car)
                            <tr>
                                <td>{{ $car->id }}</td>
                                <td>{{ $car->nombre }}</td>
                                <td style="font-size: 10px;">{{ $car->mensaje }}</td>
                                <td>{{ $car->comparacion }}</td>
                                <td>{{ $car->dias_mora }}</td>
                                <td>
                                    <button wire:click="editarRegla({{ $car->id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button" class="btn bg-light btn-xs"><i class="fa fa-pen"></i></button>
                                    <button wire:click="borrarRegla({{ $car->id }})" class="btn bg-light btn-xs"><i class="fa fa-trash"></i></button>
                                </td>
                                <td>
                                    @if ($car->status)
                                    <small class="badge bg-primary" wire:click="cambioEstado({{ $car->id }})"></i>Activo</small>
                                    @else
                                    <small class="badge bg-danger" wire:click="cambioEstado({{ $car->id }})"></i>Desactivado</small>
                                    @endif
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $carteraReglas->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Tipos de Ahorros</h4>
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
                            <div class="col-6">
                                <label>Nombre</label>
                                <input type="text" class="form-control" placeholder="Nombre" wire:model="nombre" id="nombre">
                            </div>
                            <div class="col-3">
                                <label>Comparación</label>
                                <select class="form-control" wire:model="comparacion" id="comparacion">
                                    <option disabled>-Seleccione-</option>
                                    <option value="=">IGUAL</option>
                                    <option value=">">MAYOR</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label>Dias Mora</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" wire:model="dias_mora" id="dias_mora">
                                    <div class="input-group-append">
                                        <span class="input-group-text" placeholder="Edad Min">Dias Mora</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label>Mensaje</label>
                                <textarea class="form-control" rows="3" placeholder="Descripcion del Tipo de ahorro" wire:model="mensaje" id="mensaje"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>