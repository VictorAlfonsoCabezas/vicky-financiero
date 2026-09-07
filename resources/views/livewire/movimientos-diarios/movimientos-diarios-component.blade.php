<div>
    <div class="row">
        <div class="col-md-12">
            <div class="row mt-2">
                <div class="col-4">
                    <label>Busqueda</label>
                    <div class="input-group input-group-sm">
                        <input type="text" wire:model="search" id="search" class="form-control" placeholder="Buscar Socio">
                        <div class="input-group-append">
                            <div class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <label>Fecha Inicio</label>
                    <input type="date" class="form-control form-control-sm" placeholder="Fecha Inicio" wire:model="fechaInicio">
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-valign-middle table-sm">
                    <thead>
                        <tr>

                            <th style="width: 1%;">id</th>
                            <th>Fecha/hora</th>
                            <th>Transaccion</th>
                            <th>Valor</th>
                            <th>Cliente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalle as $key => $det)
                        <tr>

                            <td>
                                {{ $det->id }}
                            </td>
                            <td>
                                {{ $det->date_created }} / {{ $det->hour_created }}
                            </td>
                            <td>
                                {{ $det->type_transaction_name }}
                            </td>
                            <td>
                                {{ $det->valor_movimiento }}
                            </td>
                            <td>
                                {{ $det->customer_name }}
                            </td>
                            <td>
                                @if(Auth::user()->reversar_movimientos)
                                <a wire:click="notificarRecerso({{ $det->id }})" class="btn btn-sm btn-info" style="color: white;">
                                    <i class="fa fa-arrow-right"></i>Reversar Transacción
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $detalle->links() }}
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener('notificarAccion', function(event) {
        var id = event.detail.id;
        var cliente = event.detail.cliente;
        var valor = event.detail.valor;

        Swal.fire({
            title: 'Confirmar Reverso',
            text: '¿Estás seguro de que deseas reversar el movimiento del cliente '+cliente+' por el valor de '+valor+'?',
            icon: 'question',
            input: 'text',
            inputPlaceholder: 'Ingresa algún comentario (obligatorio)',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            preConfirm: (comment) => {
                if (!comment) {
                    Swal.showValidationMessage('Debes ingresar un comentario');
                }
            }
        }).then((result) => {
            if (result.value) {
                console.log(result.value);
                // result.value contendrá el valor de la caja de texto
                Livewire.emit('generarReverso', id, result.value);
            }
        });
    });
</script>