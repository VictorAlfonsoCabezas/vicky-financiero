<div>
    <div class="row col col-sm-12">
        <section class="col col-sm-4">
            <div class="form-group">
                <label for="fecha_inicio">Desde:</label>
                <input type="date" class="form-control text-uppercase" id="fecha_inicio" name="fecha_inicio"
                    wire:model="fecha_inicio">
            </div>
        </section>
        <section class="col col-sm-4">
            <div class="form-group">
                <label for="fecha_fin">Hasta:</label>
                <input type="date" class="form-control text-uppercase" id="fecha_fin" name="fecha_fin"
                    wire:model="fecha_fin">
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
                                <th><b>Correo Electrónico</b></th>
                                <th><b>Celular</b></th>
                                <th><b>Dirección</b></th>
                                <th><b>Código Crédito</b></th>
                                <th><b>Días / Cuota</b></th>
                                <th><b>Fecha Pago</b></th>                              
                                <th colspan="2"><b>Recargo: 1 - 30 Días</b></th>
                                <th colspan="2"><b>Recargo: 31 - 60 Días </b></th>
                                <th colspan="2"><b>Recargo: 61 - 90 Días </b></th>
                                <th colspan="2"><b>Recargo: 91 -999 Días </b></th>
                                <th><b>Total</b></th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                              
                                <th><b>CUOTA</b></th>
                                <th><b>INTERES</b></th>
                                <th><b>CUOTA</b></th>
                                <th><b>INTERES</b></th>
                                <th><b>CUOTA</b></th>
                                <th><b>INTERES</b></th>
                                <th><b>CUOTA</b></th>
                                <th><b>INTERES</b></th>
                               
                                <th></th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            @foreach($letrasVencidas as $valu)

                                <tr>
                                    <td>{{$valu->customer_id}}</td>
                                    <td>{{$valu->cedulaCliente}}</td>
                                    <td>{{$valu->nombreCliente}}</td>
                                    <td>{{$valu->correoCliente}}</td>
                                    <td>{{$valu->celularCliente}}</td>
                                    <td>{{$valu->direccionCliente}}</td>
                                    <td>{{$valu->code_folder_header}}</td>
                                    <td>{{$valu->numero_cuota}} / {{$valu->diasInteres}}</td>
                                    <td>{{$valu->date_vencimiento}}</td>
                                    
                                    @if($valu->diasInteres >= 1 && $valu->diasInteres <= 30)
                                        <td>{{$valu->valor_cuota}}</td>
                                        <td>{{$valu->valorInteres}}</td>
                                    @else
                                        <td></td>
                                        <td></td>
                                    @endif
                                    @if($valu->diasInteres >= 31 && $valu->diasInteres <= 60)
                                        <td>{{$valu->valor_cuota}}</td>
                                        <td>{{$valu->valorInteres}}</td>
                                    @else
                                        <td></td>
                                        <td></td>
                                    @endif
                                    @if($valu->diasInteres >= 61 && $valu->diasInteres <= 90)
                                        <td>{{$valu->valor_cuota}}</td>
                                        <td>{{$valu->valorInteres}}</td>
                                    @else
                                        <td></td>
                                        <td></td>
                                    @endif
                                    @if($valu->diasInteres >= 91 )
                                        <td>{{$valu->valor_cuota}}</td>
                                        <td>{{$valu->valorInteres}}</td>
                                    @else
                                        <td></td>
                                        <td></td>
                                    @endif

                                  
                                    <td>{{$valu->valor_cuota + $valu->valorInteres }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $letrasVencidas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>