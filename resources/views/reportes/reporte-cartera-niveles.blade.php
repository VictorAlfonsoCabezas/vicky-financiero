<table>
    <thead>
        <tr role="row">
            <th colspan="14" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b>N° Socio</b></th>
            <th scope="col" style="background-color: #7eb8da;width: 100px;"><b>Identificación</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Nombre Socio</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Correo Electrónico</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Celular</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Dirección</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Código Crédito</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Días / Cuota</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Fecha Pago</b></th>
            <th scope="col" style="background-color: #7eb8da;" colspan="2"><b>Recargo: 1 - 30 Días</b></th>
            <th scope="col" style="background-color: #7eb8da;" colspan="2"><b>Recargo: 31 - 60 Días</b></th>
            <th scope="col" style="background-color: #7eb8da;" colspan="2"><b>Recargo: 61 - 90 Días</b></th>
            <th scope="col" style="background-color: #7eb8da;" colspan="2"><b>Recargo: 91 - 999 Días</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Total </b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;width: 100px;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>

            <th scope="col" style="background-color: #7eb8da;"><b>CUOTA</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>INTERE</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>CUOTA</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>INTERE</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>CUOTA</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>INTERE</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>CUOTA</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>INTERE</b></th>
            

            <th scope="col" style="background-color: #7eb8da;"><b> </b></th>
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