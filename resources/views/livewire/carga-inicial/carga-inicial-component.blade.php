<div>
    <div class="row">
        <div class="col-md-12 mt-2">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Carga Incial</h3>
                        <a href="javascript:void(0);">Ver Asientos</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-sm" style="border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th style="width: 1%;">Asiento</th>
                                <th>Fecha Contable</th>
                                <th>Usuario</th>
                                <th>Cuenta Debe</th>
                                <th>Cuenta Haber</th>
                                <th>Debe Monto</th>
                                <th>Haber Monto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $header = 0 @endphp
                            @foreach ($detalle as $key => $det)
                            @if ($header !== $det->header_id)
                            <tr class="bg-{{ $det->suma_debe == $det->suma_haber ? 'primary' : 'danger' }} text-white">
                                <td colspan="3">
                                    <b>
                                        Asiento: <b> {{ $det->header_id }} - </b>
                                        {{ $det->concepto }}
                                        @if ($det->manual)
                                        &nbsp;&nbsp;&nbsp;<i>(Asiento Manual)</i>
                                        @endif
                                    </b>
                                </td>
                                <td colspan="2"><b>Total:</b></td>
                                <td style="border: 1px solid #dee2e6;">{{ $det->suma_debe }}</td>
                                <td style="border: 1px solid #dee2e6;">{{ $det->suma_haber }}</td>
                                <td style="border: 1px solid #dee2e6; text-align: center;">
                                    @if ($det->manual)
                                    <a class="text-white" wire:click="abrirModal({{ $det->header_id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"> <i class="fa fa-edit"></i></a>
                                    @endif
                                    <a class="text-white" href="/asientos/comprobante/{{ $det->header_id }}"> <i class="fas fa-print"></i></a>
                                    <!-- <a class="ms-2 text-white" wire:click="elminarAsiento({{ $det->header_id }})"><i
                                                class="fa fa-trash"></i></a> -->
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td style="border: 1px solid #dee2e6;" class="align-middle text-center">
                                    {{ $det->detalle_id }}
                                </td>
                                <td style="border: 1px solid #dee2e6;">
                                    {{ $det->fecha_contable }}
                                </td>
                                <td style="border: 1px solid #dee2e6;" class="align-middle text-center">
                                    {{ $det->nombres }}
                                </td>
                                @if ($det->debe_haber)
                                <td style="border: 1px solid #dee2e6;">
                                    <b>{{ $det->codigo }}</b>- {{ $det->nombre }}
                                </td>
                                <td style="border: 1px solid #dee2e6;">

                                </td>
                                @else
                                <td style="border: 1px solid #dee2e6;">

                                </td>
                                <td style="border: 1px solid #dee2e6;">
                                    <b>{{ $det->codigo }}</b>- {{ $det->nombre }}
                                </td>
                                @endif
                                @if ($det->debe_haber)
                                <td style="border: 1px solid #dee2e6;">
                                    <div class="input-group">
                                        <input type="number" id="inputValue{{ $det->detalle_id }}" class="form-control" value="{{ $det->valor }}">
                                        <span class="input-group-append">
                                            <a class="btn btn-primary text-white" onclick="javascript:modificarValor({{ $det->detalle_id }})">
                                                <i class="fa fa-pen"></i>
                                            </a>
                                        </span>
                                    </div>
                                </td>
                                <td style="border: 1px solid #dee2e6;">
                                    0.00
                                </td>
                                @else
                                <td style="border: 1px solid #dee2e6;">
                                    0.00
                                </td>
                                <td style="border: 1px solid #dee2e6;">
                                <div class="input-group">
                                        <input type="number" id="inputValue{{ $det->detalle_id }}" class="form-control" value="{{ $det->valor }}">
                                        <span class="input-group-append">
                                            <a class="btn btn-success text-white" onclick="javascript:modificarValor({{ $det->detalle_id }})">
                                                <i class="fa fa-pen"></i>
                                            </a>
                                        </span>
                                    </div>
                                </td>
                                @endif
                                <td style="border: 1px solid #dee2e6;">

                                </td>
                            </tr>
                            @php $header = $det->header_id @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function modificarValor(detalleId) {
        var input = document.getElementById('inputValue' + detalleId);
        var inputValue = input.value;

        @this.call('modificartValor', detalleId, inputValue);
    }
</script>
