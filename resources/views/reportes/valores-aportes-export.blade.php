<table>
    <thead>
        <tr role="row">
            <th colspan="6" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b>Cuenta N°</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Cliente</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Valor Aporte</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>N° Letras Pendientes</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Valor Creditos</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Total</b></th>
        </tr>
    </thead>    
    <tbody style="font-size: 14px;">
        @foreach($cuentasClientes as $key => $value)
        <tr>
            <td>{{ $value->codigo }}</td>
            <td>{{ $value->cliente }}</td>
            <td>{{ $value->valorperiodico }}</td>
            <td>{{ $value->letrasPendientes }}</td>
            <td>{{ $value->valorPendiente }}</td>
            <td>{{ $value->totalRecaudar }}</td>
        </tr>
        @endforeach
    </tbody>
</table>