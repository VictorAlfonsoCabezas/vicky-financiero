<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CODEV - Cartola</title>
    <style>
        .table thead tr th {
            border-bottom: 1px dotted #000;
            background: #CCC
        }

        .table tbody tr td {
            border-bottom: 1px dotted #000;
            border-left: 1px dotted #000;
            border-right: 1px dotted #000;
        }
    </style>
</head>

<body>
    <div class="text-align-left" style="position: absolute;z-index: 0;top: 0;left: 0;width: 100%; opacity: 0.07;">
        <img id="bcTarget" class="img_bar_code" src="data:image/png;base64,{{$data['imagen']}}" alt="" width="100%" />
    </div>
    <div id="row" style="border: 0.5px solid; padding: 10px;border-radius: 15px;">
        <table style="font-size: 11px;width: 100%">
            <tr>
                <td style="text-align: center;width: 130px;">
                    <div id="txtDireccion"><img src="data:image/png;base64,{{$data['imagen']}}" alt="" style="width: 90px;" /></div>
                </td>
                <td colspan="4" style="text-align: center;font-size: 9px;">
                    <div id="txtDireccion">
                        {!! html_entity_decode($data['company']->company_name) !!}
                    </div>
                </td>
            </tr>
        </table>
        <table >

            <tr>
                <td><br></td>
            </tr>
            <tr>
                <!-- <td style="font-size: 9px; width: 15%;"><b>CUENTA N°: </b> <b style="color: red">{{$data['customer']->id}}</b></td> -->
                <td style="font-size: 9px;"><b>CÉDULA: </b> <b>{{$data['customer']->numero_documento}}</b></td>
                <td style="font-size: 9px;;"><b>TITULAR: </b> <b>{{$data['customer']->nombres}} {{$data['customer']->apellidos}}</b></td>
                <td style="font-size: 9px;"><b>CARTOLA: </b> <b>{{$data['customer']->id}}</b></td>
            </tr>
        </table>
    </div>
    <br>
    <table style="width: 100%;" class="table">
        <thead>
            <tr>
                <th>FECHA</th>
                <th>CONCEPTO</th>
                <th>DEPOSITO</th>
                <th>RETIRO</th>
                <th>SALDO</th>

            </tr>
        </thead>
        <tbody style="font-size: 11px;">
            @php
            $totalSaldo = 0; // Inicializa la variable para acumular ingresos
            
            @endphp
            @foreach($data['detalleCartola'] as $detalle)
            @if($detalle->imprimir)
            <tr>
                <td style="text-align: center;">{{$detalle->date_created}}</td>
                <td style="text-align: center;">{{$detalle->observation}}</td>
                @if($detalle->type_transaction_action == 'S')
                <td style="text-align: center;">{{$detalle->valor_movimiento}}</td>
                    @php
                        $totalSaldo += $detalle->valor_movimiento; // Suma ingresos
                    @endphp
                @else
                    <td style="text-align: center;"></td>
                @endif

                @if($detalle->type_transaction_action == 'R')
                    <td style="text-align: center;">{{$detalle->valor_movimiento}}</td>
                    @php
                        $totalSaldo -= $detalle->valor_movimiento; // Suma ingresos
                    @endphp
                @else
                    <td style="text-align: center;"></td>
                @endif
                <td style="text-align: center;">{{$totalSaldo }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>

    </table>


    <div style="text-align: center;bottom: 10px;">
        <br>
        <br>
        <br>
        <br>

        <br>
        <br>
        <br>

    </div>
</body>

</html>