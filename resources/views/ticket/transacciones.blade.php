<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ticket de Compra</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            text-align: center;
            width: 95mm;
            /* Ancho estándar de ticket */
            margin: 0;
            padding-left: 10px;
            /* Solo margen izquierdo */
            padding-top: 5;
            padding-right: 10px;
            padding-bottom: 4;
            /* Reinicia el padding completamente */
        }


        .items {
            width: 100%;
            border-top: 1px dashed black;
            border-bottom: 1px dashed black;
            padding: 5px 0;
            text-align: left;
        }

        .item {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin: 2px 0;
        }

        .total {
            font-weight: bold;
            font-size: 14px;
            margin-top: 10px;
        }

        .footer {
            font-size: 10px;
            margin-top: 10px;
        }

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
    <br>
    <br>
    <br>
    <p>
        COMPROBANTE DE {{mb_strtoupper($movimiento->type_transaction_name)}}
    </p>
    <br>


    <table style="width: 95%;">
        <tbody style="font-size: 10px;">
            <tr>
                <td style="text-align: left;"><b>CUENTA No:</b></td>
                <td style="text-align: right;">{{$cuenta->codigo}}</td>
            </tr>
            <tr>
                <td style="text-align: left;"><b>TIPO CUENTA:</b></td>
                <td style="text-align: right;">{{$tipoCuenta->name}}</td>
            </tr>
            <tr>
                <td style="text-align: left;"><b>FECHA:</b> </td>
                <td style="text-align: right;"> {{$movimiento->date_created .' '. $movimiento->hour_created}}</td>
            </tr>
            <tr>
                <td style="text-align:left;"><b>SOCIO:</b></td>
                <td style="text-align:right;"> {{$movimiento->customer_name}}</td>
            </tr>
            <tr>
                <td style="text-align: left;"><b>No DOCUMENTO:</b> </td>
                <td style="text-align: right;"> {{$movimiento->code}}</td>
            </tr>
            <tr>
                <td style="text-align: left;"><b>DETALLE DEPOSITO:</b> </td>
                <td style="text-align: right;">
                    <table style="width: 100%;">
                        <tbody style="font-size: 15px;">
                            <td style="text-align: left;"><b> {{$formaPago}}:</b> </td>
                            <td style="text-align: right;"> ${{number_format($movimiento->valor_movimiento, 2, '.', '')  }}</td>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="2"><b></b> </td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="2"><b>Observaciones: </b> </td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="2">{{$movimiento->observation}} </td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="2"><b></b> </td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="2"><b>USUARIO: </b>{{$usuario->username}} </td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="2"><b>OFICINA: </b>{{$caja}} </td>
            </tr>
        </tbody>
    </table>




</body>

</html>