<table>
    <thead>
        <tr role="row">
            <th colspan="6" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b>Número Cuenta</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Nombre Cuenta</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Fecha Creación</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Documento</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Titular</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>Balance</b></th>
        </tr>
    </thead>
    <tbody style="font-size: 14px;">
        @php $totalSaldo = 0; @endphp
        @foreach ($cuentas as $cuenta)
        <tr>
            <td>{{ $cuenta->codigo }}</td>
            <td>{{ $cuenta->tipoAhorro }}</td>
            <td>{{ $cuenta->created_at }}</td>
            <td>{{ $cuenta->numero_documento }}</td>
            <td>{{ $cuenta->cliente }}</td>
            <td>{{ $cuenta->saldo }}</td>
        </tr>
        @php $totalSaldo += $cuenta->saldo; @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" style="text-align: right;"><b>TOTAL</b></td>
            <td>{{ $totalSaldo }} $</td>
        </tr>
    </tfoot>
</table>