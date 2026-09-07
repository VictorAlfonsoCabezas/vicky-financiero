<div>
    <div class="card mt-3" style="position: relative; left: 0px; top: 0px;">
        <div class="card-header ui-sortable-handle" style="cursor: move;">
            <h3 class="card-title">
                <i class="fas fa-users me-1"></i>
                {{ $header->nombre }} <b>${{ $header->capital }}</b>
                <br><a href="/acciones-valores" target="_blank" style="font-size: 12px;">Crear Valores</a>
            </h3>
            <div class="card-tools">
                @if ($header->estado == 'ACTIVO')
                    @if ($entregados == 0)
                        <a wire:click="crear({{ $header->id }})" class="btn btn-primary" style="color: white;"
                            type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"><i
                                class="fa fa-calculator"></i> Calcular</a>
                    @endif
                    <a wire:click="vaciar({{ $header->id }})" class="btn btn-danger" style="color: white;"><i
                            class="fa fa-trash"></i> Vaciar</a>
                    <a wire:click="finalizar({{ $header->id }})" class="btn btn-warning" style="color: white;"><i
                            class="fa fa-bell-o"></i> Finalizar</a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content p-0">
                <div class="col-md-12 mt-3">
                    <div class="row">
                    </div>
                    <div class="card">
                        <input type="text" wire:model="search" id="search" class="form-control float-end"
                            placeholder="Buscar">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped table-valign-middle table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nombres</th>
                                        <th>Cédula</th>
                                        @foreach ($historial as $his)
                                            <th title="{{ $his->descripcion }}" style="font-size: 12px;">
                                                {{ $his->nombre }}</th>
                                        @endforeach
                                        <th>Total</th>
                                        @if ($header->estado == 'FINALIZADO')
                                            <th>Acciones</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($detalle as $key => $det)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td style="font-size: 12px;">
                                                {{ $det->nombre_completo }}
                                            </td>
                                            <td>
                                                <b>{{ $det->numero_documento }}</b>
                                            </td>
                                            @foreach ($historial as $his)
                                                @if ($header->estado == 'ACTIVO')
                                                    <td style="width: 40ox;">
                                                        <input
                                                            {{ \App\Http\Controllers\AccionesDetalle\AccionesDetalleController::verBloqueado($his->id) ? 'readonly' : '' }}
                                                            class="form-control form-control-sm" type="number"
                                                            placeholder="0.00"
                                                            value="{{ \App\Http\Controllers\AccionesDetalle\AccionesDetalleController::buscarValor($his->id, $det->id) }}"
                                                            wire:change="actualizarValores({{ $his->id }}, {{ $det->id }}, $event.target.value)"
                                                            style="width: 80px;font-size: 12px;text-align: center; width: 92px; height: 23px;">
                                                    </td>
                                                @else
                                                    <td style="width: 40ox;">
                                                        <input readonly="" class="form-control form-control-sm"
                                                            type="number" placeholder="0.00"
                                                            value="{{ \App\Http\Controllers\AccionesDetalle\AccionesDetalleController::buscarValor($his->id, $det->id) }}"
                                                            wire:change="actualizarValores({{ $his->id }}, {{ $det->id }}, $event.target.value)"
                                                            style="width: 80px;font-size: 12px;text-align: center; width: 92px; height: 23px;">
                                                    </td>
                                                @endif
                                            @endforeach
                                            <td>
                                                <input class="form-control form-control-sm" type="number"
                                                    placeholder="0.00"
                                                    value="{{ \App\Http\Controllers\AccionesDetalle\AccionesDetalleController::calcularTotal($det->id) }}"
                                                    style="width: 80px;font-size: 12px;text-align: center; width: 92px; height: 23px;"
                                                    readonly="">
                                            </td>
                                            @if($header->certificado == 0)
                                            @if ($header->estado == 'FINALIZADO')
                                                <td>
                                                    @if ($det->status == 1)
                                                        <a onclick="mostrarAlerta({{ $det->id }});"
                                                            class="btn btn-success btn-xs" style="color: white;"><i
                                                                class="fas fa-money-bill-alt"></i> Entregar</a>
                                                        <div style="display: none;">

                                                            <a wire:click="entregarSocio({{ $det->id }})"
                                                                class="btn btn-success btn-xs" style="color: white;"
                                                                id="boton_entregar_{{$det->id }}"><i
                                                                    class="fas fa-money-bill-alt"></i> Entregar</a>
                                                        </div>
                                                    @else
                                                        <a href="{{ URL::to('acciones-detalle/verEntrega/' . $det->id) }}"
                                                            class="btn btn-info btn-xs" style="color: white;"><i
                                                                class="fas fa-eye"></i></a>
                                                    @endif
                                                </td>
                                            @endif
                                            @else
                                            <td>
                                            <a href="{{ URL::to('acciones-detalle/verCertificado/' . $det->id) }}"
                                                            class="btn btn-info btn-xs" style="color: white;"><i
                                                                class="fas fa-eye"></i></a>
                                            </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr class="text-center">
                                            <td colspan="{{ 4 + count($historial) }}"><i class="fa fa-thumbs-down"></i>
                                                Sin Información</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2">Total:</td>
                                        @foreach ($historial as $his)
                                            <td>{{ \App\Http\Controllers\AccionesDetalle\AccionesDetalleController::verTotalFila($his->id, $header->id) }}
                                            </td>
                                        @endforeach
                                        <td><strong>{{ \App\Http\Controllers\AccionesDetalle\AccionesDetalleController::verTotal($header->id) }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            {{ $detalle->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function mostrarAlerta(id) {
        Swal.fire({
            title: 'Confirmar Entrega',
            text: '¿Estás seguro de que deseas realizar la entrega?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            console.log(result.value);
            if (result.value) {
                console.log('entr', "boton_entregar_"+id);
                var boton = document.getElementById("boton_entregar_"+id);               
                boton.click();
            }
        });
    }
    //function mostrarAlerta(id) {
    //    Swal.fire({
    //        title: 'Confirmar Entrega',
    //        text: '¿Estás seguro de que deseas realizar la entrega?',
    //        icon: 'question',
    //        showCancelButton: true,
    //        confirmButtonText: 'Aceptar',
    //        cancelButtonText: 'Cancelar'
    //    }).then((result) => {
    //        if (result.isConfirmed) {
    //            // Si el usuario hace clic en "Aceptar", llama a la función confirmEntregarSocio
    //            confirmEntregarSocio(id);
    //        }
    //    });
    //}
    function confirmEntregarSocio(id) {
        // Llama a la función de Livewire utilizando Livewire.emit
        Livewire.emit('entregarSocio', id);
    }
</script>
