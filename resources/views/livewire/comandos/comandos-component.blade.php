<div>
    <div>
        <div>
            <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                <thead>
                    <tr style="text-align: center;">
                        <th style="width: 1%;">#</th>
                        <th>Nombre</th>
                        <th>Url</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody style="text-align: center;">
                    @foreach ($comandos as $com)
                        <tr>
                            <td>{{ $com->id }}</td>
                            <td>{{ $com->nombre }}</td>
                            <td>{{ $com->url }}</td>
                            <td>
                                <button wire:click="agregaraccion({{ $com->acciones }})" type="button" data-bs-toggle="modal"
                                    data-bs-target="#modalGeneral" type="button" class="btn bg-success btn-xs"><i
                                        class="fa fa-plus"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $comandos->links() }}
        </div>
        {{-- MODAL --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"> Agregar Comandos </h4>
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
                            <div class="row mb-6">
                                <div class="col-15">
                                    <input type="text"
                                        class="form-control"placeholder="Ingrese Comandos"
                                        wire:model="codigo"
                                        id="codigo">
                                </div>
                                <div class="col-2">
                                    <button wire:click="guardasComandos({{ $com->acciones }})" type="button"
                                        data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button"
                                        class="btn bg-success btn-lg"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
