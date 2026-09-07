<table>
    <thead>
        <tr role="row">
            <th colspan="9" style="text-align: center;"><b>{{$company->comercial_name}}</b></th>
        </tr>
    </thead>
    @foreach($tipotransacciones as $val)
    <thead>
        <tr role="row">
            <th colspan="8" style="text-align: center;"><b>{{$val->name .'- ' .$val->name_corto}}</b></th>
        </tr>
        <tr role="row">
            <th colspan="9" style="text-align: center;"><b>{{$val->description}}</b></th>
        </tr>
        <tr role="row">
            <th scope="col" style="background-color: #7eb8da;"><b>CODIGO MOVMIENTO</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b> NOMBRE SOCIO</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>IDENTIFICACION</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>DIRECCION</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>FECHA CREACION</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>HORA CREACION</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>USUARIO</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>F. PAGO</b></th>
            <th scope="col" style="background-color: #7eb8da;"><b>VALOR</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach($val->movimientos->get() as $movimiento)
        <tr>
            <td>{{$movimiento->code}}</td>
            <td>{{$movimiento->customer_name}}</td>
            <td>{{$movimiento->customer_ruc}}</td>
            <td>{{$movimiento->customer_address}}</td>
            <td>{{$movimiento->date_created}}</td>
            <td>{{$movimiento->hour_created}}</td>
            <td>{{$movimiento->usuario_nombre}}</td>
            <td>{{$movimiento->formaPagoNombre}}</td>
            <td style="text-align: right;">$ {{$movimiento->valor_movimiento}}</td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="8" style="text-align: right;">TOTAL</td>
            <td style="text-align: right;">$ {{$val->movimientos->sum('valor_movimiento')}}</td>
            
        </tr>
        <tr><td colspan="9" style="text-align: right;"></td></tr>
        <tr><td colspan="9" style="text-align: right;"></td></tr>
        
    </tfoot>

    @endforeach

</table>