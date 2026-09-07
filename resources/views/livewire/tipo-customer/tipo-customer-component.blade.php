<div>
    <div>
        <div>
            <div class="col-3 mt-2 mb-2">
                <button wire:click="abrirModal(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
                    class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i> Tipo Cliente </button>
            </div>
            <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                <thead>
                    <tr>
                        <th style="width: 1%;">#</th>
                        <th>Nombre</th>
                        <th>Socio</th>
                        <th>Particular</th>
                        <th>Acciones</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customer as $cus)
                        <tr>
                            <td>{{ $cus->id }}</td>
                            <td>{{ $cus->nombre }}</td>
                            <td>
                            @if ($cus->socio)
                                    <small class="badge bg-primary"
                                        ></i>si</small>
                                @else
                                    <small class="badge bg-danger"
                                        ></i>no</small>
                                @endif
                            </td>
                            <td>
                            @if ($cus->particular)
                                    <small class="badge bg-primary"
                                        ></i>si</small>
                                @else
                                    <small class="badge bg-danger"
                                        ></i>no</small>
                                @endif
                            </td>
                            <td>
                                <button wire:click="editarCustomer({{ $cus->id }})" type="button"
                                    data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button"
                                    class="btn bg-light btn-xs"><i class="fa fa-pen"></i></button>
                                <button wire:click="borrarCustomer({{ $cus->id }})"
                                    class="btn bg-light btn-xs"><i class="fa fa-trash"></i></button>
                            </td>
                            <td>
                                @if ($cus->status)
                                    <small class="badge bg-primary"
                                        wire:click="cambioEstado({{ $cus->id }})"></i>Activo</small>
                                @else
                                    <small class="badge bg-danger"
                                        wire:click="cambioEstado({{ $cus->id }})"></i>Desactivado</small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $customer->links() }}
        </div>
        {{-- MODAL --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"> Tipo Cliente </h4>
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
                                <div class="col-11">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" placeholder="Ingrese un nombre"
                                        wire:model="nombre" id="nombre">
                                </div>
                            </div>
                             <div class="form-check">
                                    <input  wire:model="socio" id="socio" class="form-check-input" type="checkbox" value=""
                                        id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Socio
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input  wire:model="particular" id="particular" class="form-check-input" type="checkbox" value="" id="flexCheckChecked"
                                        checked>
                                    <label class="form-check-label" for="flexCheckChecked">
                                        Particular
                                    </label>
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
