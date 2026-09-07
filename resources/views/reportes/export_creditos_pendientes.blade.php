<table>
    <thead>
        <tr role="row">
            <th colspan="11" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b>#</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>CREDITO</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>CEDULA</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>NOMBRES</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>VALOR LETRA</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>INTERES MORA</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>TOTAL A PAGAR</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>FECHA</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>DIAS ATRASO</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>PLAZO EN MESES</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b># CUOTA</b></th>
        </tr>
    </thead>
    <tbody style="font-size: 14px;">
        @php $totalSaldo = 0; @endphp
        @php $totalInteres = 0; @endphp
        @php $totalPagarSumado = 0; @endphp
        @foreach ($creditos as $key => $credit)
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
            @php $totalSaldo += $credit->valor_cuota; @endphp
            @php $totalInteres += $credit->interesCalculado; @endphp
            @php $totalPagarSumado += $credit->totalPagar; @endphp
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" style="text-align: right;"><b>TOTAL.</b></td>
            <td>{{ $totalSaldo }} $</td>
            <td>{{ $totalInteres }} $</td>
            <td>{{ $totalPagarSumado }} $</td>
            <td colspan="4" style="text-align: right;"><b></b></td>
        </tr>
    </tfoot>
</table>