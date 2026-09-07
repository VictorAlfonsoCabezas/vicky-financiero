<div>
    <div class="row">


        <div class="col-md-12">
            <div class="card card-primary card-outline mt-2">
                <div class="card-header">
                    <h3 class="card-title">Información</h3>
                </div>
                <div class="card-body p-0">

                    @if ($cliente)

                    <div class="row ms-2 me-2 mt-2 mb-2">
                        @foreach ($cuentas as $cue)
                        <div class="col-md-3" wire:click="cuentaSeleccionada({{ $cue->id }})">
                            <div class="info-box bg-{{ $cue->tipoAhorros->class }}" style="height: 105px;">
                                @if ($this->cuenta_selec == $cue->id)
                                <i class="fas fa-check"></i>
                                @endif
                                <span class="info-box-icon">
                                    @if ($cue->tipoAhorros->cuenta_certificado)
                                    <i class="fa fa-lock"></i>
                                    @elseif($cue->tipoAhorros->programado)
                                    <i class="fa fa-calculator"></i>
                                    @else
                                    <i class="fa fa-sim-card"></i>
                                    @endif
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ $cue->tipoAhorros->name }}</span>
                                    <span class="info-box-number">{{ $cue->codigo }}</span>
                                    <span class="progress-description">
                                        {{ $cue->saldo }}$
                                    </span>
                                    @if ($cue->dias_plazo)
                                    <span class="progress-description" style="font-size: 12px;">
                                        {{ $cue->dias_plazo . ' días al ' . $cue->porcentaje . ' %' }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if ($this->cuenta_selec > 0)
                    <div class="row ms-2 me-2 mt-2 mb-2">
                        <div class="col-4">
                            <label class="small">Fecha Inicio</label>
                            <div class="input-group input-group-sm">
                                <input id="fechaInicio" type="date" wire:model="fechaInicio" class="form-control" placeholder="Fecha Inicio" wire:change="obtenerDatos">
                            </div>
                        </div>
                        <div class="col-4">
                            <label class="small">Fecha Fin</label>
                            <div class="input-group input-group-sm">
                                <input id="fechaFin" type="date" wire:model="fechaFin" class="form-control" placeholder="Fecha Fin" wire:change="obtenerDatos">
                            </div>
                        </div>
                    </div>
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th style="text-align: center;">Fecha</th>
                                <th style="text-align: center;">Comprobante</th>
                                <th style="text-align: center;">Detalle</th>
                                <th style="text-align: center;">Ahorros</th>
                                <th style="text-align: center;">Retiros</th>
                                <th style="text-align: center;">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($detalle->reverse() as $key => $det)
                            
                                <tr>
                                <td style="text-align: center;width: 280px;">
                                    {{ $this->convertirFecha($det->date_created) }}
                                    <small>{{ $det->hour_created }}</small><br>
                                </td>
                                <td>
                                    <small>{{ $det->comprobante }}</small>
                                </td>
                                <td style="text-align: center;width: 280px;">
                                    <small>{{ $det->observation }}</small>
                                </td>
                                @if($det->type_transaction_action == 'S')
                                <td style="text-align: center;">
                                    <b style="color: green;">{{ number_format($det->valor_movimiento, 2) }} $</b>
                                </td>
                                <td>
                                    <b></b>
                                </td>
                                @else
                                <td>
                                    <b></b>
                                </td>
                                <td style="text-align: center;color: red;">
                                    <b>{{ number_format($det->valor_movimiento, 2) }} $</b>
                                </td>
                                @endif
                                <td style="text-align: center;">
                                    <b>{{ number_format($det->saldoValor,2) }} $</b>
                                </td>
                                </tr>
                                
                                @empty
                                <tr>
                                    <td class="text-center" colspan="4">No hay movimientos</td>
                                </tr>

                                @endforelse
                        </tbody>
                    </table>
                    @endif
                    @else
                    <h5 class="text-center"><i class="fa fa-sack-dollar"></i> Cuentas</h5>
                    @endif
                </div>
                <div class="card-footer p-0">
                </div>
            </div>
        </div>
    </div>




</div>
<script>

</script>