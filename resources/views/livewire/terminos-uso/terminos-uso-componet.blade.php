<div>
    <div>
        <div>
            <div class="col-3 mt-2 mb-2">
                <button wire:click="abrirModal(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" class="btn btn-block btn-default btn-flat">
                    <i class="fa fa-plus"></i>
                    <b>
                        Nuevo Término de uso
                    </b>
                </button>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><b>Terminos para uso</b></h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0" style="height: 300px;">
                            <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 1%;">#</th>
                                        <th>Nombre</th>
                                        <th>Fecha/Hora Creación</th>
                                        <th>Estado</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($terminos as $ter)
                                    <tr class="text-center">
                                        <td>{{ $ter->id }}</td>
                                        <td>{{ $ter->name }}</td>
                                        <td>{{ $ter->date_create }} / {{ $ter->hour_create }}</td>
                                        <td>
                                            @if ($ter->status)
                                            <small class="badge bg-primary" wire:click="cambioEstado({{ $ter->id }})"></i>Activo</small>
                                            @else
                                            <small class="badge bg-danger" wire:click="cambioEstado({{ $ter->id }})"></i>Desactivado</small>
                                            @endif
                                        </td>
                                        <td>
                                            <button wire:click="editarTermino({{ $ter->id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button" class="btn bg-light btn-xs"><i class="fa fa-pen"></i></button>
                                            <button wire:click="borrarTermino({{ $ter->id }})" class="btn bg-light btn-xs"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $terminos->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- MODAL --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Nueva Formas de Pago </h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="storeTermino">
                        <div class="modal-body">
                            @if ($errors->any())
                            <div class="callout callout-warning">
                                <h5>Verifica estas observaciones.</h5>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </div>
                            @endif
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label>Nombre</label>
                                <input type="text" class="form-control" placeholder="Ingrese un nombre" wire:model="name" id="name">
                            </div>
                            

                            <div class="col-12" style="margin-bottom: 10px;">
                                <label for="name">Descripción</label>
                                <textarea class="form-control" placeholder="Ingrese una descripción" wire:model="description" id="description"></textarea>
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