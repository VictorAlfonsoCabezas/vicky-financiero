<div>
    <div class="row col col-sm-12">
        <section class="col col-sm-4">
            <div class="form-group">
                <label for="fecha_inicio">Desde:</label>
                <input type="date" class="form-control text-uppercase" id="fecha_inicio" name="fecha_inicio" wire:model="fecha_inicio">
            </div>
        </section>
        <section class="col col-sm-4">
            <div class="form-group">
                <label for="fecha_fin">Hasta:</label>
                <input type="date" class="form-control text-uppercase" id="fecha_fin" name="fecha_fin" wire:model="fecha_fin">
            </div>
        </section>
        <section class="col col-sm-4">
            <div class="col-6">
                <label class="small">Nivel </label>
                <select id="tipo_reporte" wire:model="tipo_reporte" class="form-control text-uppercase">
                    <option value="0"> --SELECCIONE--</option>
                    <option value="1">CREDITOS VIGENTES</option>
                    <option value="2">CREDITOS TOTALIZADO</option>
                </select>
            </div>
        </section>

    </div>
    <div class="row col col-sm-12">
        <div class="row">
            <a class="btn btn-primary btn-xs" style="color: white;" wire:click="export">
                <i class="fas fa-plus"></i> Exportar
            </a>
        </div>
    </div>

    <div class="row col col-sm-12">
        <div class="card-body" style="background-color: #ededf3;">
            <div class="tab-content">
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th><b>N° Socio</b></th>
                                <th><b>Identificación</b></th>
                                <th><b>Nombre Socio</b></th>
                                <th><b>Codigo Crédito</b></th>
                                <th><b>Estado Crédito</b></th>
                                <th><b>Estado Letra</b></th>
                                <th><b>Deuda Inicial </b></th>
                                <th><b>Saldo </b></th>
                                <th><b>Valor de la Cuota </b></th>
                                <th><b>Valor de la Cuota a Pagar </b></th>
                                <th><b>Interes Mora </b></th>
                                <th><b>Días de Mora</b></th>
                                <th><b>Total a Pagar </b></th>
                                <th><b>INFORMACIÓN DEUDOR <br>Dirección - Teléfonos</b></th>
                                <th><b>INFORMACIÓN GARANTE 1 <br>Dirección - Teléfonos</b></th>
                                <th><b>INFORMACIÓN GARANTE 2 <br>Dirección - Teléfonos</b></th>
                            </tr>
                            <tr style="font-size: 11px;">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$sumaSolicitado}} $</label></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$totalPagandoSuma}}$</label> </b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$valorCuotaSuma}}$</label> </b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$valorCuotaPagarSuma}}$</label> </b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$interesMoraSuma}}$</label> </b></th>
                                <th></th>
                                <th><label style="color:rgb(52, 129, 44);"> {{$valorCuotaPagarSuma+$interesMoraSuma }}$</label> </b></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        @if($this->tipo_reporte == 1)
                        <tbody style="font-size: 14px;">
                            @foreach($letrasVencidas as $letra)
                            @if($letra->entregado == 'ENTREGADO')
                            <tr>
                                <td>{{$letra->identificacionClienteID}}</td>
                                <td>{{$letra->identificacionCliente}}</td>
                                <td>{{$letra->nombreSocio}}</td>
                                <td>{{$letra->code_folder_header}}</td>
                                <td>{{$letra->entregado}}</td>
                                <td>{{$letra->status}}</td>
                                <td>{{$letra->valor_solicitado}}</td>
                                <td>{{$letra->total_pagando}}</td>
                                <td>{{$letra->valor_cuota}}</td>
                                <td>{{$letra->valor_cuotaPagar}}</td>
                                <td>{{$letra->interesMora}}</td>
                                <td>{{$letra->diferenciaDias}}</td>
                                <td>{{$letra->valor_cuotaPagar + $letra->interesMora }}</td>
                                <td> {!! html_entity_decode($letra->datosDeudor) !!} </td>
                                <td> {!! html_entity_decode($letra->datosGarante1) !!} </td>
                                <td> {!! html_entity_decode($letra->datosGarante2) !!} </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                        @else
                        <tbody style="font-size: 14px;">
                            @foreach($letrasVencidas as $letra)
                            <tr>
                                <td>{{$letra->identificacionClienteID}}</td>
                                <td>{{$letra->identificacionCliente}}</td>
                                <td>{{$letra->nombreSocio}}</td>
                                <td>{{$letra->code_folder_header}}</td>
                                <td>{{$letra->entregado}}</td>
                                <td>{{$letra->status}}</td>
                                <td>{{$letra->valor_solicitado}}</td>
                                <td>{{$letra->total_pagando}}</td>
                                <td>{{$letra->valor_cuota}}</td>
                                <td>{{$letra->valor_cuotaPagar}}</td>
                                <td>{{$letra->interesMora}}</td>
                                <td>{{$letra->diferenciaDias}}</td>
                                <td>{{$letra->valor_cuotaPagar + $letra->interesMora }}</td>
                                <td> {!! html_entity_decode($letra->datosDeudor) !!} </td>
                                <td> {!! html_entity_decode($letra->datosGarante1) !!} </td>
                                <td> {!! html_entity_decode($letra->datosGarante2) !!} </td>
                            </tr>
                            @endforeach
                        </tbody>
                        @endif
                    </table>
                    {{ $letrasVencidas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>