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
            width: 100%;
            /* Ancho estándar de ticket */
            margin: 0px;
            padding-left: 10px;
            /* Solo margen izquierdo */
            padding-top: 5;
            padding-right: 15px;
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
            border-bottom: 0.6px dotted #000;
            background: #CCC
        }

        .table tbody tr td {
            padding-left: 5px;
            border-bottom: 0.6px dotted #000;
            border-left: 0.6px dotted #000;
            border-right: 0.6px dotted #000;
        }
    </style>
</head>

<body>
    <p>
        COMPROBANTE DE PAGO DE CRÉDITO # 0000175
    </p>
    <br>

    <table style="width: 95%;">
        <tbody style="font-size: 10px;">
            <tr>
                <td style="text-align: left;"><b>Cliente:</b> {{ $cabecera->customer_name }}</td>

                <td style="text-align: left;"><b>C.I.:</b> {{ $cabecera->customer_ruc }}</td>
            </tr>
            <tr>


                <td style="text-align: left;" colspan="2"><b>Cajero:</b> {{$usuario}}</td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="3"><b>Producto:</b> {{$prestamo->name}}</td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="3"><b>No. Crédito:</b> {{ str_pad($letra->id, 6, '0', STR_PAD_LEFT)   }}</td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="3"><b>Fecha de Vencimiento:</b> {{$letra->date_vencimiento}}</td>
            </tr>
            <tr>
                <td style="text-align: left;"><b>Fecha de Pago:</b>{{$letra->date_pay}}</td>

                <td style="text-align: left;"><b>Cuota(s):</b> {{$letra->numero_cuota}}(P)/{{$cabecera->cuotas_pagar}}</td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="2"><b>Capital Prestado:</b> {{$cabecera->valor_solicitado}}</td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="2"><b>Valor total adeudado:</b> {{$letrasPendientes}}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <table style="width: 95%;" class="table">
        <thead>
            <tr>
                <th colspan="2"><b>DETALLES DE VALORES PAGADOS</b></th>

            </tr>
            <tr>
                <th style="width: 80%">DETALLE</th>
                <th style="width: 20%">VALOR</th>
            </tr>
        </thead>
        <tbody style="font-size: 10px;">
            <tr>
                <td style="text-align: left;">Cuota Préstamo </td>
                <td style="text-align: right;">$ {{$letra->valor_cuota}}</td>
            </tr>
            <tr>
                <td style="text-align: left;">Interés Mora</td>
                <td style="text-align: right;">$ {{$letra->interes_mora}}</td>
            </tr>
            <tr>
                <td style="text-align: left;">Saldo a favor </td>
                <td style="text-align: right;">${{$letra->adelanto_prox_cuota}}</td>
            </tr>
            <tr>
                <td style="text-align: left;">Liquidación Total del Crédito</td>
                <td style="text-align: right;">$ 0.00</td>
            </tr>
            <tr>
                <td style="text-align: left;">Gasto Cobranza</td>
                <td style="text-align: right;">$ {{ number_format($letra->notificado * $company->valor_notificado, 2, '.', '') }}</td>
            </tr>
            <tr>
                <td style="text-align: left;">Otros</td>
                <td style="text-align: right;">$ 0.00</td>
            </tr>
            <tr>
                <td style="text-align: left;">Valor a Pagar</td>
                <td style="text-align: right;">$ {{$letra->valor_cuota + $letra->interes_mora +($letra->notificado * $company->valor_notificado)}}</td>
            </tr>
            <tr>
                <td style="text-align: left;">Valor Pagado </td>
                <td style="text-align: right;">$ {{$letra->valor_pagado }}</td>
            </tr>
            <tr>
                <td style="text-align: left;">Faltante</td>
                <td style="text-align: right;">$ {{$letra->faltante_prox_cuota}}</td>
            </tr>

        </tbody>
    </table>
    <table style="width: 95%;">
        <tbody style="font-size: 10px;">
            <tr>
                <td style="text-align: left;"><b>La cantidad de </b> {{$cantidadLetras}}</td>
            </tr>
            <tr>
                <td style="text-align: left;"><b>Forma de Pago: </b> {{$formasPago}}</td>
            </tr>
            <tr>
                <td style="text-align: left;"><b>Saldo Cuota Actual: </b>{{$letrasPagadas}}</td>
            </tr>
            <tr>
                <td style="text-align: left;"><b>Fecha de próximo vencimiento: </b> {{$letraSiguienteFecha}} </td>
            </tr>
        </tbody>
    </table>
    <br>
    <br>
    <br>
    <br>
    <table style="width: 95%;">
        <tbody style="font-size: 10px;">
            <tr>
                <td style="text-align: left;">_____________________________</td>
                <td style="text-align: left;">_____________________________</td>
            </tr>
            <tr>
                <td style="text-align: center;">Firma del Socio</td>
                <td style="text-align: center;">Firma Autorizada</td>
            </tr>
        </tbody>
    </table>
</body>

</html>