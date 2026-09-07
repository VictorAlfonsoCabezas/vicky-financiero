<div>
    <div class="row col col-sm-12">
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
                                <th><b>Nombre Socio</b></th>
                                <th><b>Cédula de Identidad</b></th>
                                <th><b>Fecha Pago</b></th>
                                <th><b>Valor Cuota </b></th>
                                <th><b>Capital Amortizado </b></th>
                                <th><b>Componentes </b></th>
                                <th><b>Interes </b></th>
                                <th><b>Interes Mora </b></th>
                                <th><b>Gasto Cobranza </b></th>
                                <th><b>Total Pagado </b></th>
                                <th><b>Forma de Pago </b></th>
                                <th><b>Observación </b></th>
                            </tr>
                            <tr style="font-size: 11px;">
                                <th><b></b></th>
                                <th><b> </b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$letrasVencidasPagadasSuma}} $</label></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$letrasVencidasPagadas->sum('capital_amortizado')}} $</label></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$componentesSuma}} $</label></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$letrasVencidasPagadas->sum('interes_periodo')}} $</label></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$letrasVencidasPagadas->sum('interes_mora')}} $</label></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$gastosCobranzaSumado}} $</label></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$letrasVencidasPagadas->sum('valor_pagado')}} $</label></b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            @foreach($letrasVencidas as $letra)
                            <tr>
                                <td>{{$letra->idCliente}}</td>
                                <td>{{$letra->nombreSocio}}</td>
                                <td>{{$letra->identificacionCliente}}</td>
                                <td>{{$letra->date_pay}}</td>
                                <td>{{$letra->valor_cuota}}</td>
                                <td>{{$letra->capital_amortizado}}</td>
                                <td>{{$letra->gastosComponentes}}</td>
                                <td>{{$letra->interes_periodo}}</td>
                                <td>{{$letra->interes_mora}}</td>
                                <td>{{$letra->gastosCobranza}}</td>
                                <td>{{$letra->valor_pagado}}</td>
                                <td>{{$letra->formaPago}} </td>
                                <td>{{$letra->obervation_pago}}</td>

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