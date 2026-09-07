<div>
    <div>
        <div class="col-3 mt-2 mb-2">
            <button wire:click="abrirModal(0);" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
                class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i><b> Bóvedas</b></button>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><b>Nueva Bovedas</b></h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 1%;">id</th>
                                    <th>Nombre</th>
                                    <th>Descripcion</th>
                                    <th>Principal</th>
                                    <th>Boveda</th>
                                    <th>Estado</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bovedas as $bov)
                                    <tr class="text-center">
                                        <td>{{ $bov->id }}</td>
                                        <td>{{ $bov->nombre }}</td>
                                        <td>{{ $bov->descripcion }}</td>
                                        <td>
                                            @if ($bov->principal)
                                                <small class="badge bg-primary"
                                                    wire:click="cambioPrincipal({{ $bov->id }})"></i>Activo</small>
                                            @else
                                                <small class="badge bg-danger"
                                                    wire:click="cambioPrincipal({{ $bov->id }})"></i>Desactivado</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($bov->boveda)
                                                <small class="badge bg-primary"
                                                    wire:click="cambioBoveda({{ $bov->id }})"></i>Activo</small>
                                            @else
                                                <small class="badge bg-danger"
                                                    wire:click="cambioBoveda({{ $bov->id }})"></i>Desactivado</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($bov->status)
                                                <small class="badge bg-primary"
                                                    wire:click="cambioEstado({{ $bov->id }})"></i>Activo</small>
                                            @else
                                                <small class="badge bg-danger"
                                                    wire:click="cambioEstado({{ $bov->id }})"></i>Desactivado</small>
                                            @endif
                                        </td>
                                        <td>
                                            <button wire:click="editarBancos({{ $bov->id }})" type="button"
                                                data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button"
                                                class="btn bg-light btn-xs"><i class="fa fa-pen"></i>
                                            </button>
                                            <button wire:click="borrarBancosNotificar({{ $bov->id }})"
                                                class="btn bg-light btn-xs"><i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $bovedas->links() }}
                    </div>
                </div>
            </div>
        </div>
        {{-- MODAL --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"> Bóvedas</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="storeBovedas">
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
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" placeholder="Ingrese un nombre"
                                        wire:model="nombre" id="nombre">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-12 mt-3">
                                    <label>Descripcion</label>
                                    <textarea class="form-control" wire:model="descripcion" placeholder="descripcion"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-success">Guardar </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener('notificarAccion', function (event) {
        var id = event.detail.id;
        var boveda = event.detail.boveda;
        console.log(boveda, event);
        Swal.fire({
            title: 'Confirmar Eliminación',
            text: '¿Estás seguro de que deseas eliminar la boveda '+boveda.nombre+'?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                Livewire.emit('borrarBancos', id);
            }
        });
    });
</script>