<table>
    <thead>
        <tr role="row">
            <th colspan="9" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b>#</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>CREDITO</b></th>
            <th scope="col" style="background-color: #7eb8da;width: 100px;"><b>CEDULA</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>NOMBRES</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>VALOR</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>FECHA</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>DIAS ATRASO</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>PLAZO EN MESES</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b># CUOTA</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;text-align: right;"><b>{{$totalValoresSum}} $</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
            <th scope="col" style="background-color: #7eb8da;"><b></b></th>
        </tr>
    </thead>
    <tbody style="font-size: 14px;">
        @php $totalSaldo = 0; @endphp
        @foreach ($letras as $key => $letras)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{$letras['code_folder_header']}}</td>
            <td>{{$letras['cedulaCliente']}}</td>
            <td>{{$letras['nombresCliente']}}</td>
            <td>{{$letras['valor_cuota']}}</td>
            <td>{{$letras['date_vencimiento']}}</td>
            <td>{{$letras['diasDiferencia']}}</td>
            <td>{{$letras['totalLetras']}}</td>
            <td>{{$letras['numero_cuota']}}</td>
            @php $totalSaldo += $letras['valor_cuota']; @endphp
        </tr>
        @endforeach
    </tbody>
</table>