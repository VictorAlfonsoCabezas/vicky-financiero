<table>
    <thead>
        <tr role="row">
            <th colspan="11" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b>N° Socio</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Nombre Socio</b></th>
            <th scope="col" style="background-color: #7eb8da; width: 100px;"><b>Cédula de Identidad</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Fecha Pago</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Valor Cuota</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Capital Amortizado</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Componentes</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Interes</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Interes Mora</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Gasto Cobranza</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Total Pagado </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Forma de Pago </b></th>
            <th scope="col" style="background-color: #7eb8da;width: 250px;"><b>Observación </b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$letrasVencidasPagadasSuma}} $</b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$letrasVencidasPagadas->sum('capital_amortizado')}} $</b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$componentesSuma}} $</b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$letrasVencidasPagadas->sum('interes_periodo')}} $</b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$letrasVencidasPagadas->sum('interes_mora')}} $</b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$gastosCobranzaSumado}} $</b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$letrasVencidasPagadas->sum('valor_pagado')}} $</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;width: 250px;"><b> </b></th>
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