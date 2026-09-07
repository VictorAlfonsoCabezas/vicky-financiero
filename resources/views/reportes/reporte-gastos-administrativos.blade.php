@if($cuenta_prestamo == 1)


<table>
    <thead>
        <tr role="row">
            <th colspan="6" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b>Origen</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Socio</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Identificacion</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Fecha</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Observacion</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Valor</b></th>

        </tr>
    </thead>
    <tbody style="font-size: 14px;">
        @foreach ($cuentas as $cuenta)
        <tr>
            <td>{{ $cuenta->nombreHorro }}<br>{{$cuenta->numeroCuenta }}</td>
            <td>{{ $cuenta->customer_name }}</td>
            <td>{{ $cuenta->customer_ruc }}</td>
            <td>{{ $cuenta->date_created }}</td>
            <td>{{ $cuenta->observation }}</td>
            <td>{{ $cuenta->total_valor_movimiento }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="5" style="text-align: right;">Total</td>
            <td>
                {{$sumnaCuentas }}
            </td>
        </tr>
    </tbody>
</table>



@else




<table>
    <thead>
        <tr role="row">
            <th colspan="9" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b>Número Crédito </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Cédula</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Apellidos y Nombres</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>TIPO PRESTAMO</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Fecha Creación </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Gasto Administrativo</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Primer Gasto</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Segundo Gasto</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Tercer Gasto</b></th>


        </tr>
    </thead>
    <tbody style="font-size: 14px;">
        @foreach($creditos as $key => $credit)
        <tr>
            <td>{{ $credit->code }}</td>
            <td>{{ $credit->customerIdentificacion }}</td>
            <td>{{ $credit->customerNombre }}</td>
            <td>{{ $credit->prestamoNombre }}</td>
            <td>{{ $credit->date_created }}</td>
            <td>{{ $credit->gasto_administrativo }}</td>
            <td>{{ $credit->primer_gasto }}</td>
            <td>{{ $credit->segundo_gasto }}</td>
            <td>{{ $credit->tercer_gasto }}</td>

        </tr>
        @endforeach
        <tr>
            <td colspan="5" style="text-align: right;">Total</td>
            <td>{{$sumnaCreditosGastos}} </td>
            <td>{{$sumnaCreditosGastosPrimer}} </td>
            <td>{{$sumnaCreditosGastosSegundo}} </td>
            <td>{{$sumnaCreditosGastosTercero}} </td>
        </tr>

    </tbody>
</table>



@endif