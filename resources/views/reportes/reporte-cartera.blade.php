<table>
    <thead>
        <tr role="row">
            <th colspan="16" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b>N° Socio</b></th>
            <th scope="col" style="background-color: #7eb8da;width: 100px;"><b>Identificación</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Nombre Socio</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Codigo Crédito</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Codigo Crédito</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Estado Crédito</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Estado Letra</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Saldo</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Valor de la Cuota</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Valor de la Cuota a Pagar</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Interes Mora</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Días de Mora</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Total a Pagar </b></th>
            <th scope="col" style="background-color: #7eb8da;width: 250px;"><b>INFORMACIÓN DEUDOR <br>Dirección - Teléfonos</b></th>
            <th scope="col" style="background-color: #7eb8da;width: 250px;"><b>INFORMACIÓN GARANTE 1 <br>Dirección - Teléfonos</b></th>
            <th scope="col" style="background-color: #7eb8da;width: 250px;"><b>INFORMACIÓN GARANTE 2 <br>Dirección - Teléfonos</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$sumaSolicitado}} $ </b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$totalPagandoSuma}}$</b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$valorCuotaSuma}}$</b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$valorCuotaPagarSuma}}$</b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$interesMoraSuma}}$</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$valorCuotaPagarSuma+$interesMoraSuma }}$</b></th>
            <th scope="col" style="background-color: #7eb8da;width: 250px;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;width: 250px;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;width: 250px;"><b></b></th>
        </tr>
    </thead>
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
</table>