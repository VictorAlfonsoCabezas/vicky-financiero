@if($tipo == 1)
<table>
    <thead>
        <tr role="row">
            <th colspan="15" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b>Usuario</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Número Crédito </b></th>
            <th scope="col" style="background-color: #7eb8da; width: 100px;"><b>Cedula</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Apellidos y Nombres</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>TIPO PRESTAMO</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>TIEMPO (meses)</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>CAPITAL</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>INTERES</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>TOTAL PRESTAMO </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>FECHA DE CREACION</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>VALOR DE LA CUOTA </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>INTERES </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>RECAUDACION </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>SALDO </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>LETRAS PENDIENTES </b></th>
        </tr>
        <tr style="font-size: 11px;">
            <th><b></b></th>
            <th><b> </b></th>
            <th><b></b></th>
            <th><b></b></th>
            <th><b></b></th>
            <th><b></b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$sumaValorSolicitado}} $</b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$valorInteresesCreditos}} $ </b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$valorInteresesCreditos + $sumaValorSolicitado }} $ </b></th>
            <th><b></b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$valorvalorCuotaTotal}} $</b></th>
            <th><b></b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$valorPagadoCreditos}} $ </b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$valorPendienteCreditos}} $ </b></th>
            <th><b> </b></th>
        </tr>
    </thead>
    <tbody style="font-size: 14px;">
        @foreach($creditos as $key => $credit)
        @if($credit->pendientesFecha != 0)
        <tr>
            <td>{{ $credit->user_created_name }}</td>
            <td>{{ $credit->code }}</td>
            <td>{{ $credit->customerIdentificacion }}</td>
            <td>{{ $credit->customerNombre }}</td>
            <td>{{ $credit->prestamoNombre }}</td>
            <td>{{ $credit->cuotas_pagar }}</td>
            <td>{{ $credit->valor_solicitado }}</td>
            <td>{{ $credit->valorintereses }}</td>
            <td>{{ number_format($credit->valor_solicitado + $credit->valorintereses, 5, '.', '') }}</td>
            <td>{{ $credit->date_created }}</td>
            <td>{{ $credit->valor_cuota }}</td>
            <td>{{ $credit->prestamoInteres }} %</td>
            <td>{{ $credit->valorPagado }} </td>
            <td>{{ $credit->valorPendiente }} </td>
            <td>{{ $credit->pendientesFecha }} </td>
        </tr>
        @endif
        @endforeach
    </tbody>
</table>
@elseif($tipo == 2)
<table>
    <thead>
        <tr role="row">
            <th colspan="11" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b>#</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>CREDITO</b></th>
            <th scope="col" style="background-color: #7eb8da;width: 100px;"><b>CEDULA</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>NOMBRES</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>VALOR LETRA</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>INTERES MORA</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>TOTAL A PAGAR</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>FECHA</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>DIAS ATRASO</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>PLAZO EN MESES</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b># CUOTA</b></th>
        </tr>
        <tr style="font-size: 11px;">
            <th><b></b></th>
            <th><b></b></th>
            <th><b></b></th>
            <th><b></b></th>
            <th scope="col" style="background-color: #7eb8da; text-align: right;"><b>{{$sumaTotalValorLetra}} $</b></th>
            <th scope="col" style="background-color: #7eb8da; text-align: right;"><b>{{$interesCalculadoLetras}} $</b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$sumaTotalValorLetra + $interesCalculadoLetras}} $</b></th>
            <th><b></b></th>
            <th><b></b></th>
            <th><b></b></th>
            <th><b></b></th>
        </tr>
    </thead>
    <tbody style="font-size: 14px;">
        @foreach ($letrasImpagas as $key => $credit)
        <tr>
            <td style="background-color: {{ $credit->colorLetra }};">{{ $key + 1 }}</td>
            <td>{{$credit->code_folder_header}}</td>
            <td>{{$credit->numeroDocumento}}</td>
            <td>{{$credit->customerName}}</td>
            <td>{{$credit->valor_cuota}}</td>
            <td>{{$credit->interesCalculado}}</td>
            <td>{{$credit->totalPagar}}</td>
            <td>{{$credit->date_vencimiento}}</td>
            <td>{{$credit->diasVencido}}</td>
            <td>{{$credit->totalLetras}}</td>
            <td>{{$credit->numero_cuota}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@elseif($tipo == 3)
<table>
    <thead>
        <tr role="row">
            <th colspan="16" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b>Usuario</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Número Crédito </b></th>
            <th scope="col" style="background-color: #7eb8da; width: 100px;"><b>Cedula</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Apellidos y Nombres</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>TIPO PRESTAMO</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>TIEMPO (meses)</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>CAPITAL</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>INTERES</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>TOTAL PRESTAMO </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>FECHA DE CREACION</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>VALOR DE LA CUOTA </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>INTERES </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>RECAUDACION </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>SALDO </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>LETRAS PENDIENTES </b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>ESTADO </b></th>
        </tr>
        <tr style="font-size: 11px;">
            <th><b></b></th>
            <th><b> </b></th>
            <th><b></b></th>
            <th><b></b></th>
            <th><b></b></th>
            <th><b></b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$sumaValorSolicitadoTotalizado}} $</b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$valorInteresesCreditosTotalizado}} $ </b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$valorInteresesCreditosTotalizado + $sumaValorSolicitadoTotalizado }} $ </b></th>
            <th><b></b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$valorvalorCuotaTotalTotalizado}} $</b></th>
            <th><b></b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$valorPagadoCreditosTotalizado}} $ </b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$valorPendienteCreditosTotalizado}} $ </b></th>
            <th><b> </b></th>
            <th><b> </b></th>
        </tr>
    </thead>
    <tbody style="font-size: 14px;">
        @foreach($creditosTotalTotalizado as $key => $credit)
        <tr>
            <td>{{ $credit->user_created_name }}</td>
            <td>{{ $credit->code }}</td>
            <td>{{ $credit->customerIdentificacion }}</td>
            <td>{{ $credit->customerNombre }}</td>
            <td>{{ $credit->prestamoNombre }}</td>
            <td>{{ $credit->cuotas_pagar }}</td>
            <td>{{ $credit->valor_solicitado }}</td>
            <td>{{ $credit->valorintereses }}</td>
            <td>{{ number_format($credit->valor_solicitado + $credit->valorintereses, 5, '.', '') }}</td>
            <td>{{ $credit->date_created }}</td>
            <td>{{ $credit->valor_cuota }}</td>
            <td>{{ $credit->prestamoInteres }} %</td>
            <td>{{ $credit->valorPagado }} </td>
            <td>{{ $credit->valorPendiente }} </td>
            <td>{{ $credit->pendientesFecha }} </td>
            <td>
                @if($credit->pendientesFecha != 0 || $credit->status == 'NEGADO')
                {{ $credit->status }}
                @else
                Pagado
                @endif
                .:.  {{ $credit->status }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif