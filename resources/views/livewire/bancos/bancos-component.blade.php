<div>
    <div>
        <div class="col-3 mt-2 mb-2">
            <button wire:click="abrirModal(0);" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i><b>Banco</b></button>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><b>Nueva Banco</b></h3>
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
                                    <th>Descripcion</th>
                                    <th>Numero de Cuenta</th>
                                    <th>Estado </th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bancos as $ban)
                                <tr class="text-center">
                                    <td>{{ $ban->id }}</td>
                                    <td><b>{{ $ban->nombre }}</b></td>
                                    <td>{{ $ban->descripcion }}</td>
                                    <td><b>{{ $ban->numero_cuenta }}</b></td>
                                    <td>
                                        @if ($ban->status)
                                        <small class="badge bg-primary" wire:click="cambioEstado({{ $ban->id }})"></i>Activo</small>
                                        @else
                                        <small class="badge bg-danger" wire:click="cambioEstado({{ $ban->id }})"></i>Desactivado</small>
                                        @endif
                                    </td>
                                    <td>
                                        <button wire:click="editarBancos({{ $ban->id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button" class="btn bg-light btn-xs"><i class="fa fa-pen"></i>
                                        </button>
                                        <button wire:click="borrarBancos({{ $ban->id }})" class="btn bg-light btn-xs"><i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2 mb-2">
                        {{ $bancos->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"> Nueva Banco</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="storeBancos">
                        <div class="modal-body">
                            @if ($errors->any())
                            <div class="callout callout-warning">
                                <h5>Verifica estas observaciones.</h5>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </div>
                            @endif
                            <div class="alert alert-warning alert-dismissible">
                                <button type="button" class="close" data-bs-dismiss="alert" aria-hidden="true">×</button>
                                <h5><i class="icon fas fa-exclamation-triangle"></i> Pancho!</h5>
                                Para crear bancos para <b>Clientes</b>, solo basta con el nombre.
                                <br>
                                Si es un banco para la <b>Caja</b> debe tener todos los datos.
                            </div>
                            <br>
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" placeholder="Ingrese un nombre" wire:model="nombre" id="nombre">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label>Tipo Cuenta</label>
                                    <select class="form-control" wire:model="tipo_cuenta_id">
                                        <option value="0">Seleccione</option>
                                        @foreach ($this->tipoCuentas as $tipo)
                                        <option value="{{ $tipo['id'] }}">{{ $tipo['nombre'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-12 mt-3">
                                    <label>Descripcion</label>
                                    <textarea class="form-control" wire:model="descripcion" placeholder="descripcion"></textarea>
                                </div>
                            </div>
                            <div>
                                <div class="col-12 mt-3">
                                    <label>Numero de Cuenta </label>
                                    <input type="text" class="form-control" placeholder="Numero de Cuenta" wire:model="numero_cuenta" id="numero_cuenta">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>