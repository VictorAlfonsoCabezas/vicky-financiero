<div>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body" style="background-color: #ededf3;">
                <div class="tab-content">
                    @if ($errors->any())
                    <div class="callout callout-warning">
                        <h5>Verifica estas observaciones.</h5>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </div>
                    @endif
                    <div class="post">
                        <h5>Calculadora de ahorro programado<i class="fa fa-edit"></i></h5>
                    </div>
                    <div class="row  mt-3">
                        <div class="col-3">
                            <label class="small">Valor</label>
                            <div class="input-group input-group-sm">
                                <input id="valoPlazo" type="number" wire:model="valoPlazo" class="form-control" placeholder="Valor Plazo">
                            </div>
                        </div>
                        <div class="col-3">
                            <label class="small">Plazo/Interés </label>
                            <select id="interesPlazo" wire:model="interesPlazo" class="form-control form-control-sm">
                                <option value=""> --SELECCIONE--</option>
                                @foreach ($plazos as $opcion)
                                <option value="{{ $opcion->id }}">{{ $opcion->interes.'% '.'DESDE '.$opcion->rango_min.' HASTA '.$opcion->rango_max .' DÍAS'}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-3">
                            <label class="small">Fecha Inicio</label>
                            <div class="input-group input-group-sm">
                                <input id="fechaInicio" type="date" wire:model="fechaInicio" class="form-control" placeholder="Fecha Prestamo">
                            </div>
                            @error('fechaInicio')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-3">
                            <label class="small">Pago </label>
                            <select id="pagoPlazo" wire:model="pagoPlazo" class="form-control form-control-sm">
                                <option value=""> --SELECCIONE-- </option>
                                <option value="C"> Al Cumplimiento </option>
                                <option value="M"> Mensualizado </option>
                            </select>
                        </div>
                    </div>
                    <div class="row  mt-3">
                        <div class="col-3"> <!-- Contenedor para el input y el checkbox -->
                            <label class="small">Plazo Manual </label>
                            <div class="input-group input-group-sm">
                                @if($this->activarManual)
                                <input type="number" id="diasManual" wire:model="diasManual" class="form-control" placeholder="Días Plazo Opcional" wire:change="onDiasManualChange">
                                @endif
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <input type="checkbox" id="usarManual" wire:model="usarManual" wire:click="toggleUsarManual">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row  mt-3">
                        <div class="col-12">
                            <button type="button" wire:click="generarPlazo" class="btn btn-block btn-primary btn-xs" wire:loading.attr="disabled">Generar</button>
                        </div>
                    </div>
                    <hr>
                    <div class="row  mt-3">
                        <table class="table table-striped table-valign-middle">
                            <thead>
                                <tr>
                                    <th>Monto</th>
                                    <th>Interés</th>
                                    <th>Plazo</th>
                                    <th>Rentabilidad</th>
                                    <th>Penalizado</th>
                                    <th>Total</th>
                                    <th>Fecha a Pagar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($this->pagoPlazo == 'C')
                                <tr>
                                    <td class="small">{{$this->montoVer}}</td>
                                    <td class="small">{{$this->interesVer}}</td>
                                    <td class="small">{{$this->plazoVer}}</td>
                                    <td class="small">{{$this->valorGanadoVer}}</td>
                                    <td class="small">{{$this->penalidadVer}}</td>
                                    <td class="small">{{$this->totalPagarVer}}</td>
                                    <td class="small">{{$this->fechaPago}}</td>
                                </tr>
                                @else
                                @foreach($this->mensualizados as $value)
                                <tr>
                                    <td class="small">{{$this->montoVer}}</td>
                                    <td class="small">{{$value['interes']}}</td>
                                    <td class="small">{{$this->plazoVer}}</td>
                                    <td class="small">{{$value['ganado']}}</td>
                                    <td class="small">{{$value['penali']}}</td>
                                    <td class="small">{{$value['totalPagarVer']}}</td>                                    
                                    <td class="small">{{$value['fechaPago']}}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
