<div>
    <div>
        <div>
            <div class="col-3 mt-2 mb-2">
                <button wire:click="abrirModal(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i> Nuevo Valor</button>
            </div>
            <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                <thead>
                    <tr>
                        <th style="width: 1%;">#</th>
                        <th>Nombre</th>
                        <th>Descripcion</th>
                        <th>Operacion</th>
                        <th>Modificar</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($acciones as $accion)
                    <tr>
                        <td>{{ $accion->id }}</td>
                        <td>{{ $accion->nombre }}</td>
                        <td>{{ $accion->descripcion }}</td>
                        <td>{{ $accion->operacion }}</td>
                        <td>
                            <button wire:click="editarValor({{ $accion->id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button" class="btn bg-light btn-xs"><i class="fa fa-pen"></i></button>
                            <button wire:click="borrarValor({{ $accion->id }})" class="btn bg-light btn-xs"><i class="fa fa-trash"></i></button>
                        </td>
                        <td>
                            @if ($accion->status)
                            <small class="badge bg-primary" wire:click="cambioEstado({{ $accion->id }})"></i>Activo</small>
                            @else
                            <small class="badge bg-danger" wire:click="cambioEstado({{ $accion->id }})"></i>Desactivado</small>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $acciones->links() }}
        </div>

        {{-- MODAL --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"> Acciones y Valores </h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="storeValor">
                        <div class="modal-body">
                            @if ($errors->any())
                            <div class="callout callout-warning">
                                <h5>Verifica estas observaciones.</h5>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </div>
                            @endif
                            <div class="row mb-10">
                                <div class="col-12">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" placeholder="Ingrese el Nombre" wire:model="nombre" id="nombre">
                                </div>
                                <div class="col-12">
                                    <label>Descripción</label>
                                    <div class="input-group mb-10">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                        </div>
                                        <textarea class="form-control" rows="8" placeholder="Ingrese una descripción, esto es opcional" wire:model="descripcion" id="descripcion">
                                            </textarea>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <label>Tipo</label>
                                    <select class="custom-select" wire:model="tipo">
                                        <option value="SALDO">SALDO</option>
                                        <option value="PORCENTAJE">PORCENTAJE</option>
                                        <option value="UTILIDAD">UTILIDAD</option>
                                        <option value="OTROS">OTROS</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label>Bloqueado</label>
                                    <select class="custom-select" wire:model="bloqueado">
                                        <option value="1">SI</option>
                                        <option value="0">NO</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label>Operación</label>
                                    <select class="custom-select" wire:model="operacion">
                                        <option value="N">NINGUNA</option>
                                        <option value="S">SUMA</option>
                                        <option value="R">RESTA</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label>Valor Defecto</label>
                                    <input type="text" class="form-control" wire:model="valor_defecto" id="valor_defecto">
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