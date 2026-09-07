<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CODEV - {{$data['typo']}} - {{ $data['movimiento']->customer_name}}</title>
    <style>
        * {
            margin: 4px;
            padding: 0.2px;

        }

        body {
            position: relative;
            margin: 0;
            padding: 0;
        }

        .fondo {
            position: fixed;
            top: 0;
            left: 0px;
            width: 100%;
            height: 100%;
            background-image: url("data:image/png;base64,{{$data['imagen']}}");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            opacity: 0.2;
            z-index: 0;
        }

        .table thead tr th {
            border-bottom: 0.5px dotted #000;
            background: #CCC;
            padding: 1.6px;
        }

        .table tbody tr td {
            border-bottom: 0.5px dotted #000;
            border-left: 0.5px dotted #000;
            border-right: 0.5px dotted #000;
            padding: 1.6px;
        }

        .table_head tr td {
            border-left: 0.1px dotted #000;
            border-bottom: 0.1px dotted #000;
            padding: 1px;
        }
    </style>
</head>

<body>
    <div class="fondo"></div>
    <div id="row" style="border: 0.5px solid; padding: 10px;border-radius: 15px;">
        <table style="font-size: 15px;width: 100%">
            <tr>
                <td style="text-align: center; border-right: 0.1px dotted #000" ;>
                    <div id="txtDireccion"><img src="data:image/png;base64,{{$data['imagen']}}" alt="" style="width: 65px;" /></div>
                </td>
                <td colspan="6" style="text-align: center;font-size: 15px;">
                    <div id="txtDireccion">
                        {!! html_entity_decode($data['company']->company_name) !!}
                        <br>
                        <b style="font-size: 13px;">TIPO DE TRANSACCION</b><br>
                        <b style="color: red;font-size: 12px">{{$data['typo']}}</b><br>
                    </div>
                </td>
            </tr>
        </table>
        <hr>
        <table style="font-size: 9px;width: 100%" class="table_head">
            <tr>
                <td><b>RECIBÍ DE: </b></td>
                <td colspan="3">
                    <div id="txtDireccion">{{$data['movimiento']->customer_name}}</div>
                </td>
            </tr>
            <tr>
                <td><b>RUC/C.I: </b></td>
                <td>
                    <div id="txtRuc">{{$data['movimiento']->customer_ruc}}</div>
                </td>
                <td><b>FECHA: </b></td>
                <td style="border-right: 0.1px dotted #000;">
                    <div id="txtTelefono">{{$data['movimiento']->hour_created}}</div>
                </td>
            </tr>
            <tr>
                <td><b>N° </b></td>
                <td>
                    <div id="txtRuc">{{$data['movimiento']->code}}</div>
                </td>
                <td><b>HORA: </b></td>
                <td style="border-right: 0.1px dotted #000;">
                    <div id="txtTelefono">{{$data['movimiento']->hour_created}}</div>
                </td>
            </tr>
        </table>
    </div>
    <table style="width: 100%; font-size: 13px" class="table">
        <thead>
            <tr>
                <th style="width: 80%">DETALLE</th>
                <th style="width: 20%">VALOR</th>
            </tr>
        </thead>
        <tbody style="font-size: 10px;">
            <tr>
                <td style="text-align: left;">Aporte para Gastos de Administración</td>

                @if($data['typo'] == 'GASTOADMINISTRATIVO' )
                <td style="text-align: center;"> ${{$data['movimiento']->valor_movimiento}}</td>
                @else
                <td style="text-align: center;">$ 0.00</td>
                @endif
            
            </tr>
            <tr>
                <td style="text-align: left;">Multas Atrasos</td>
                <td style="text-align: center;">$ 0.00</td>
            </tr>
            <tr>
                <td style="text-align: left;">Multas Inasistencias</td>
                <td style="text-align: center;">$ 0.00</td>
            </tr>
            <tr>
                <td style="text-align: left;">Ahorro Programado</td>
                <td style="text-align: center;">$ 0.00</td>
            </tr>
            <tr>
                <td style="text-align: left;">Ahorro a la Vista</td>
                <td style="text-align: center;">$ 0.00</td>
            </tr>
            <tr>
                <td style="text-align: left;">Ahorro Infantil</td>
                <td style="text-align: center;">$ 0.00</td>
            </tr>
            <tr>
                <td style="text-align: left;">Otros</td>
                <td style="text-align: center;">$ 0.00</td>
            </tr>

            <tr>
                <td style="text-align: right;">TOTAL</td>
                @if($data['typo'] == 'DEPOSITO' )
                <td style="text-align: center;">+ ${{$data['movimiento']->valor_movimiento}}</td>
                @elseif($data['typo'] == 'GASTOADMINISTRATIVO')
                <td style="text-align: center;"> ${{$data['movimiento']->valor_movimiento}}</td>
                @else
                <td style="text-align: center;">- ${{$data['movimiento']->valor_movimiento}}</td>
                @endif
            </tr>
        </tbody>
    </table>
    <div style="text-align: center;bottom: 9px;position: static;">
        <table style="font-size: 10px;width: 100%;">
            <tr>
                <td style="text-align: left;">La cantidad de <b>$ {{$data['movimiento']->valor_movimiento}}</b><br></td>
            </tr>
        </table>
    </div>
    <div style="position: fixed; bottom: 30px; width: 100%; text-align: center; font-size: 10px;">
        __________________<br>Beneficiario
    </div>

</body>

</html>