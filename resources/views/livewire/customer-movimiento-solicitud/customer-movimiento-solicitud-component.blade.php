<div>
    <div class="row">
        <div class="mt-2">
            <a wire:click="abrirModal(0);" class="btn btn-app bg-primary" data-bs-toggle="modal" data-bs-target="#modalGeneral">
                <i class="fa fa-plus"></i> Nueva
            </a>
        </div>
        <div class="col-md-12 mt-3">

            {{-- ESTO SE VA A RETIRAR LUEGO DE FINALIZAR LA MODIFICACIÓN--}}



            @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            {{-- HASTA AQUI SE BORRA --}}



            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-valign-middle table-sm">

                    <thead>
                        <tr>
                            <th>Valor</th>
                            <th>Fecha Creación</th>
                            <th>Observación</th>
                            <th>Estado</th>
                            <th>Ver</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movimientos as $key => $mov)
                        <tr>
                            <td class="h1">
                                <b> $ {{ $mov->valor }}</b>
                            </td>
                            <td>
                                {{ $mov->fecha_creacion }}
                            </td>
                            <td>
                                {{ $mov->observacion }}
                            </td>
                            <td>
                                @if ($mov->estado == 'PENDIENTE')
                                <span class="badge bg-primary"><i class="fa fa-clock"></i>
                                    {{ $mov->estado }}</span>
                                @elseif($mov->estado == 'APROBADO')
                                <span class="badge bg-success"><i class="fa fa-check"></i>
                                    {{ $mov->estado }}</span>
                                @elseif($mov->estado == 'RECHAZADO')
                                <span class="badge bg-danger"><i class="fa fa-times"></i>
                                    {{ $mov->estado }}
                                </span>
                                <br>
                                <small><b>Razón: </b>{{ $mov->razon_rechazado }}</small>
                                @endif
                            </td>
                            <td>
                                <button wire:click="abrirModal({{ $mov->id }});"
                                    class="btn btn-block bg-warning btn-xs" data-bs-toggle="modal"
                                    data-bs-target="#modalGeneral1">
                                    <i class="fa fa-file-image" aria-hidden="true"></i> Imagen
                                </button>
                            </td>
                            <td>
                                @if ($mov->estado == 'PENDIENTE')
                                <div class="d-flex gap-1">
                                    <button wire:click="abrirModal({{ $mov->id }});" data-bs-toggle="modal"
                                        data-bs-target="#modalGeneral" type="button"
                                        class="btn bg-primary btn-xs me-1"><i class="fa fa-edit"></i> Modificar</button>

                                    <button wire:click="eliminarSolicitud({{ $mov->id }})" type="button"
                                        class="btn bg-danger btn-xs"><i class="fa fa-trash"></i> Eliminar</button>
                                </div>
                                @endif

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="h3 text-center" colspan="6">
                                No existe Solitudes
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $movimientos->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL SOLICITUD --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title"> Solicitar Ingreso </h4>
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
                        <label><i class="fa fa-bars"></i> Seleccione una de sus cuentas:</label>
                        <br>
                        <div class="row">
                            @foreach ($cuentas as $cue)
                            <div class="col-12 col-sm-4">
                                <div
                                    class="info-box bg-{{ $cue->id == $this->cuenta_selec ? 'warning' : 'light' }}">
                                    @if ($cue->id == $this->cuenta_selec)
                                    <i class="fa fa-check"></i>
                                    @endif
                                    <div class="info-box-content"
                                        wire:click="selecionaCuenta({{ $cue->id }})">
                                        <span
                                            class="info-box-text text-center text-muted">{{ $cue->tipoAhorros->name }}</span>
                                        <span
                                            class="info-box-number text-center text-muted mb-0">{{ $cue->codigo }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="row mb-4">
                            <div class="col-12">
                                <label>Valor</label>
                                <input type="number" step="0.01" class="form-control"
                                    placeholder="Ingrese un valor" wire:model="valor">
                            </div>


                            <!-- Para FP tipo Banco -->

                            <div class="col-6">
                                <label>Banco</label>
                                <select class="form-control" wire:model="banco_id">
                                    <option value="">- Seleccione -</option>
                                    @foreach($bancos as $banco)
                                        @if($banco->tipo_cuenta_id > 0)
                                        <option value="{{ $banco->id }}">
                                            {{ $banco->nombre }} {{ $banco->numero_cuenta ? '#'.$banco->numero_cuenta : '' }}
                                        </option>
                                        @endif
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-6">
                                <label># Depósito</label>
                                <input type="text" class="form-control" placeholder="# Deposito"
                                    wire:model="numero_deposito" id="numero_deposito">
                            </div>


                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label for="exampleInputFile">Archivo Comprobante</label>
                                    <div class="input-group">
                                        <div class="custom-file" id="inputFile">
                                            <input type="file" onchange="updateImagePreview()"
                                                class="custom-file-input" id="exampleInputFile"
                                                wire:model.debounce="archivo" debounce="10000">
                                            <label class="custom-file-label" for="exampleInputFile">Buscar
                                                Archivo</label>
                                        </div>
                                        <img id="image-preview" class="img-thumbnail" alt="Image preview" wire:ignore>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label>Observación</label>
                                    <textarea class="form-control" rows="3" placeholder="Ingrese una observación" wire:model="observacion"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Solicitar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL IMAGEN --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral1" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title"> Imagen </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-widget">
                                    <div class="card-header">
                                        <div class="user-block">
                                            <img class="img-circle" src="/img/sinusuario.jpg" alt="User Image">
                                            <span class="username"><a href="#">{{ Auth::user()->firstname }}
                                                    {{ Auth::user()->lastname }}</a></span>
                                            <span class="description">Fecha creación:
                                                {{ $this->fecha_creacion }}</span>
                                        </div>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body" style="display: block;">
                                        <img class="img-fluid pad" src="{{ $this->path }}" alt="Photo">
                                        <p>Comprobante que de la Solicitud de deposito</p>
                                        <a href="{{ asset($this->path) }}" target="_blank"
                                            class="btn btn-default btn-sm"><i class="fas fa-file"></i> Descargar</a>
                                        <span class="float-end text-muted">Comprobante de Desposito</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function updateImagePreview() {
        const fileInput = document.getElementById('exampleInputFile');
        const preview = document.getElementById('image-preview');

        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            const reader = new FileReader();

            // Define la acción a tomar cuando el archivo se ha leído
            reader.onload = function(e) {
                preview.src = e.target.result; // Establece la imagen de previsualización
            };

            // Lee el archivo como una URL de datos (base64)
            reader.readAsDataURL(file);
        } else {
            // Opcional: Manejar el caso en que no hay archivo seleccionado
            preview.src = ''; // O cualquier otra lógica que desees
        }
    }
</script>