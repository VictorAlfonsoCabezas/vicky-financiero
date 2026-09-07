<div>
    <div>
        <div>
            <div class="row">
                <div class="col-md-4">
                    <label>Busqueda</label>
                    <div class="input-group input-group-sm mb-2 mt-2">
                        <input type="text" wire:model="search" id="search" class="form-control" placeholder="Buscar Nombres, Apellidos, Identificación">
                        <div class="input-group-append">
                            <div class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Asignados</label>
                        <select class="form-control form-control-sm mt-2" wire:model="opcion">
                            <option value="0">TODOS</option>
                            <option value="1">SI</option>
                            <option value="2">NO</option>
                        </select>
                    </div>
                </div>
            </div>
            <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                <thead>
                    <thead>
                        <tr>
                            <th style="width: 1%;">id</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Número documento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                <tbody>
                    @foreach ($customer as $cli)
                    <tr>
                        <td>{{ $cli->id }}</td>
                        <td><i class="fa fa-male"></i> {{ $cli->nombres }}</td>
                        <td>{{ $cli->apellidos }}</td>
                        <td>{{ $cli->numero_documento }}</td>
                        <td>
                            @if ($cli->user_id == null)
                            <a wire:click="cargarDatosModal({{ $cli->id }})" class="btn btn-app" data-bs-toggle="modal" data-bs-target="#modalGeneral">
                                <i class="fas fa-plus"></i> Crear
                            </a>
                            @else
                            <a wire:click="enviarClaveWhatsapp({{ $cli->id }})" class="btn btn-app bg-secondary">
                                <i class="fas fa-paper-plane"></i> Enviar Password
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $customer->links() }}
        </div>

        {{-- MODAL --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-xs">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"> Usuario Nuevo </h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="guardarUsuario">
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
                                    <label>Usuario</label>
                                    <input type="text" class="form-control" placeholder="Ingrese nombre de usuario" wire:model="usuario" id="usuario">
                                </div>
                                <div class="col-6">
                                    <label>Clave</label>
                                    <input type="text" class="form-control" placeholder="Nueva Clave" wire:model="clave" id="clave">
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
<script>
    //Livewire evento whatsapp
    window.addEventListener('whatsapp', function(event) {
        var data = event.detail;
        var celular = data.telefono;
        var mensaje = data.mensaje;
        var baseUrl = 'https://web.whatsapp.com/send?phone=';
        var formattedPhoneNumber = celular.replace(/\s/g, '');
        var finalUrl = baseUrl + formattedPhoneNumber + '&text=' + encodeURIComponent(mensaje);
        var newWindow = window.open(finalUrl, '_blank', 'width=600,height=600');
        if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
            alert('Por favor, permita que se abra la ventana emergente para continuar.');
        }
    });
</script>