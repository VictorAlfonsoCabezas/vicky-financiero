<table>
    <thead>
        <tr role="row">                                                              
            <th colspan="7" style="text-align: center;"><b>CAJA DE AHORROS SEMBRANDO EL FUTURO</b></th>            
        </tr>
        <tr role="row">                                                              
            <th scope="col"><b>#</b></th>
            <th scope="col"><b>FECHA</b></th>
            <th scope="col"><b>CODE</b></th>
            <th scope="col"><b>DETALLE</b></th>
            <th scope="col"><b>INGRESOS</b></th>
            <th scope="col"><b>EGRESOS</b></th>
            <th scope="col"><b>SALDO</b></th>
        </tr>
    </thead>
    <tbody style="font-size: 14px;">
        @foreach($movimientos as  $historia)
        <tr class="odd">
            <td>{{$historia->id}}</td>
            <td>{{$historia->date_created}}</td>
            <td>{{$historia->code}}</td>
            <td>{{$historia->observation}}</td>
            @if($historia->type_transaction_action == 'S')
            <td style="background-color: greenyellow;">{{$historia->valor_movimiento}}</td>
            <td>0.00</td>
            @else
            <td>0.00</td>
            <td style="background-color: #ffc9d9;">{{$historia->valor_movimiento}}</td>
            @endif
            <td style="background-color: #a9e5e3;">{{$historia->saldo_general}}</td>
        </tr>
        @endforeach
    </tbody>
</table>