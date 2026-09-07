<table>
    <thead>
        <tr role="row">
            <th colspan="13" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b>#</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Usuario</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Número Crédito  </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Cedula</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Apellidos y Nombres</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>TIPO PRESTAMO</b></th>

            <th scope="col" style="background-color: #7eb8da;"><b>TIEMPO (meses)</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>CAPITAL</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>INTERES</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>TOTAL PRESTAMO </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>FECHA DE CREACION</b></th>

            <th scope="col" style="background-color: #f7f48d;"><b>VALOR DE LA CUOTA </b></th>
            <th scope="col" style="background-color: #f7f48d;"><b>INTERES </b></th>
            <th scope="col" style="background-color: #f7f48d;"><b>RECAUDACION </b></th>
            <th scope="col" style="background-color: #f7f48d;"><b>SALDO </b></th>
            <th scope="col" style="background-color: #FFBFB0;"><b>LETRAS PENDIENTES </b></th>
        </tr>
    </thead>
    <tbody style="font-size: 14px;">
        @php $totalCreditos = 0; @endphp
        @php $sumavalores = 0; @endphp
        @php $sumaValorCuota = 0; @endphp
        @php $sumaValorSaldo = 0; @endphp
        @foreach($creditos as $key => $credit)
        @php $sumavalores = 0; @endphp
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $credit->user_created_name }}</td>
            <td>{{ $credit->code }}</td>
            <td>{{ $credit->customerIdentificacion }}</td>
            <td>{{ $credit->customerNombre }}</td>
            <td>{{ $credit->prestamoNombre }}</td>

            <td>{{ $credit->cuotas_pagar }}</td>
            <td>{{ $credit->valor_solicitado }}</td>
            <td>{{ $credit->valorintereses }}</td>
            <td>{{ number_format($credit->valor_solicitado + $credit->valorintereses, 5, '.', '') }}</td>
            @php $sumavalores = $credit->valor_solicitado + $credit->valorintereses; @endphp
            <td>{{ $credit->date_created }}</td>
            <td>{{ $credit->valor_cuota }}</td>
            <td>{{ $credit->prestamoInteres }} %</td>
            <td>{{ $credit->valorPagado }} </td>
            <td>{{ $credit->valorPendiente }} </td>
            <td>{{ $credit->pendientesFecha }} </td>
        </tr>
        @php $totalCreditos += $sumavalores ; @endphp
        @php $sumaValorCuota += $credit->valor_cuota; @endphp
        @php $sumaValorSaldo += $credit->valorPendiente; @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="9" style="text-align: right;"><b>TOTAL</b></td>
            <td>{{ $totalCreditos }} $</td>
            <td style="text-align: right;"></td>
            <td>{{ $totalCreditos }} $</td>
            <td colspan="2" style="text-align: right;"></td>
            <td>{{ $sumaValorSaldo }} $</td>
            <td style="text-align: right;"></td>
        </tr>
    </tfoot>
</table>