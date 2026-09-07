<div>
    <div>
        <div>
            <div class="col-3 mt-2 mb-2">
                <button wire:click="abrirModal(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i> Tipo Transaciones </button>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><b>Tipo Transaciones</b></h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm">
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                                <thead>
                                    <tr class="text-center">
                                        <th>Id</th>
                                        <th>Nombre</th>
                                        <th>Abrev.</th>
                                        <th>Cartola.</th>
                                        <th>Descripcion</th>
                                        <th>Accion</th>
                                        <th>Afecta</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($typetransaction as $type)
                                    <tr>
                                        <td>{{ $type->id }}</td>
                                        <td>{{ $type->name }}</td>
                                        <td><b>{{ $type->name_corto }}</b></td>
                                        <td>{{ $type->nombre_cartola }}</td>
                                        <td>{{ $type->description }}</td>
                                        <td>{{ $type->action }}</td>
                                        <td>{{ $type->afecta }}</td>
                                        <td>{{ $type->estatus }}
                                            <small class="badge bg-primary"></i>Activo</small>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs btn-success" title="Editar {{$type->name}}" wire:click="consultarDatosEdit({{ $type->id }})" data-bs-toggle="modal" data-bs-target="#modalGeneral">
                                                <i class="fas fa-user-check" style="color: white;">Editar</i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $typetransaction->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- MODAL --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h4 class="modal-title"> Tipo de Transaciones </h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="storeTransaciones">
                        <div class="modal-body">
                            @if ($errors->any())
                            <div class="callout callout-warning">
                                <h5>Verifica estas observaciones.</h5>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </div>
                            @endif
                            <div class="row mb-8">
                                <div class="col-6">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" placeholder="Ingrese el nombre" wire:model="name" id="name">
                                </div>
                                <div class="col-6">
                                    <label>Nombre Corto (Abrev)</label>
                                    <input type="text" class="form-control" placeholder="Ingrese el nombre" wire:model="name_corto" id="name_corto">
                                </div>
                                <div class="col-6">
                                    <label>Nombre Cartola</label>
                                    <input type="text" class="form-control" placeholder="Ingrese el nombre" wire:model="nombre_cartola" id="nombre_cartola">
                                </div>
                            </div>
                            <div class="row mb-6">
                                <div class="col-12">
                                    <label>Descripción</label>
                                    <textarea class="form-control" rows="3" placeholder="Ingrese una descripción" wire:model="description" id="description"></textarea>
                                </div>
                            </div>
                            <div class="row mb-8">
                                <div class="col-6">
                                    <label>Accion</label>
                                    <select class="form-control" wire:model="action" id="action ">
                                        <option disabled>-Seleccione-</option>
                                        <option value="S">S</option>
                                        <option value="R">R</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label>Afecta</label>
                                    <select class="form-control" wire:model="afecta" id="afecta">
                                        <option disabled>-Seleccione-</option>
                                        <option value="C">C</option>
                                        <option value="E">E</option>
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