<div>
    <div class="row">
        <div class="col-md-12">
            <div class="row mt-2">
                <div class="col-4">
                    <label>Busqueda</label>
                    <div class="input-group input-group-sm">
                        <input type="text" wire:model="search" id="search" class="form-control"
                               placeholder="Buscar Socio">
                        <div class="input-group-append">
                            <div class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <label>Fecha Inicio</label>
                    <input type="date" class="form-control form-control-sm" placeholder="Fecha Inicio"
                           wire:model="fechaInicio">
                </div>
                <div class="col-4">
                    <label>Fecha Fin</label>
                    <input type="date" class="form-control form-control-sm" placeholder="Fecha Fin"
                           wire:model="fechaFin">
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-valign-middle table-sm">
                    <thead>
                        <tr>
                            <th style="width: 1%;">#</th>
                            <th style="width: 1%;">id</th>
                            <th>Nombres</th>
                            <th>Identificación</th>
                            <th>Crédito</th>
                            <th>Dias Mora</th>
                            <th>Fecha Vencimiento</th>
                            <th># Cuota</th>
                            <th>Valor Cuota</th>
                            <th>Cuenta Débito Automático</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalle as $key => $det)
                        <tr>
                            <td>
                                {{ $key + 1 }}
                            </td>
                            <td>
                                {{ $det->id }}
                            </td>
                            <td>
                                {{ $det->nombre_completo }}
                            </td>
                            <td>
                                <b>{{ $det->numero_documento }}</b>
                            </td>
                            <td>
                                <b>({{ $det->credit_folder_headers_id }})</b>
                            </td>
                            <td>
                                <small class="text-success me-1">
                                    {{ $det->dias_mora }}
                                </small>
                            </td>
                            <td>
                                <small class="text-success me-1">

                                    <i class="fas fa-arrow-down"></i>
                                    {{ $det->date_vencimiento }}
                                </small>
                            </td>
                            <td>
                                <b>#{{ $det->numero_cuota }}</b>
                            </td>
                            <td>
                                <i class="fas fa-arrow-down"></i>
                                ${{ $det->valor_cuota }}
                            </td>
                            <td>
                                <small
                                    class="badge badge-{{ $det->tipo_ahorro_class }}">{{ $det->tipo_ahorro_name }}</small>
                            </td>
                            <td>
                                <small class="badge bg-primary"><i class="far fa-clock"></i>
                                    {{ $det->status }}</small>
                            </td>
                            <td>
                                @if ($det->tipo_ahorro_name)
                                <a wire:click="debitoAutomaticonotificacion({{ $det->id }}, {{ $det->customer_tipo_ahorros_id}}, true)" class="btn btn-sm btn-info" style="color: white;">
                                    <i class="fa fa-arrow-right"></i>Pagar con Interés
                                </a>
                                <a wire:click="debitoAutomaticonotificacion({{ $det->id }}, {{ $det->customer_tipo_ahorros_id}}, false)" class="btn btn-sm btn-danger" style="color: white;">
                                    <i class="fa fa-arrow-right"></i>Pagar sin Interés
                                </a>
                                @endif
                                <a class="btn btn-default btn-sm"
                                   wire:click="envioWhatsapp({{ $det->id }})"
                                   title="enviar Whatsapp Web">
                                    <i class="fa fa-paper-plane"></i>
                                </a>
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
    //Livewire evento whatsapp
    window.addEventListener('whatsapp', function (event) {
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
    window.addEventListener('notificarAccion', function (event) {
        var id = event.detail.id;
        var customer_tipo_id = event.detail.customer_tipo_id;
        var mora = event.detail.mora;
        var letra = event.detail.letra;
        var cabecera = event.detail.cabecera;
        console.log(cabecera, event);
        Swal.fire({
            title: 'Confirmar Pago',
            text: '¿Estás seguro de que deseas pagar la letra #' + letra.numero_cuota + ' dle cliente ' + cabecera.customer_name + ' ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                Livewire.emit('debitoAutomatico', id, customer_tipo_id, mora);
            }
        });
    });
</script>