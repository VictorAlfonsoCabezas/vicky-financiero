<div>
    <div>
        <div>
            <div class="col-3 mt-2 mb-2">
                <button wire:click="abrirModal(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
                    class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i> Nuevo Meses</button>
            </div>
            <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                <thead>
                    <tr>
                        <th style="width: 1%;">#</th>
                        <th>Codigo</th>
                        <th>Mes</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($meses as $mes)
                        <tr>
                            <td>{{ $mes->id }}</td>
                            <td>{{ $mes->codigo }}</td>
                            <td>{{ $mes->mes }}</td>
                            <td>
                                <button wire:click="editarMeses({{ $mes->id }})" type="button" data-bs-toggle="modal"
                                    data-bs-target="#modalGeneral" type="button" class="btn bg-light btn-xs"><i
                                        class="fa fa-pen"></i></button>
                                <button wire:click="borrarMeses({{ $mes->id }})"
                                    class="btn bg-light btn-xs"><i class="fa fa-trash"></i></button>
                            </td>
                            <td>
                                @if ($mes->status)
                                    <small class="badge bg-primary"
                                        wire:click="cambioEstado({{ $mes->id }})"></i>Activo</small>
                                @else
                                    <small class="badge bg-danger"
                                        wire:click="cambioEstado({{ $mes->id }})"></i>Desactivado</small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $meses->links() }}
        </div>
        {{-- MODAL --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"> Meses </h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="storeMeses">
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
                                <div class="col-4">
                                    <label>codigo</label>
                                    <input type="text" class="form-control" placeholder="Ingrese numero de mes"
                                        wire:model="codigo" id="codigo">
                                </div>
                                <div class="col-4">
                                    <label>Mes</label>
                                    <input type="text" class="form-control" placeholder="Ingrese el mes"
                                        wire:model="mes" id="mes">
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
