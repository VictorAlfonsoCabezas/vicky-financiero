<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CODEV - Gasto de - {{$data['datos']->date_created}}</title>
    <style>
        * {
            margin: 4px;
            padding: 0;

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
    <div id="row" style="width: 100%;border: 0.5px solid; padding: 10px;border-radius: 15px;">
        <table style="font-size: 11px;width: 100%">
            <tr>
                <td style="text-align: center">
                    <div id="txtDireccion"><img src="data:image/png;base64,{{$data['imagen']}}" alt="" style="width: 30px;" /></div>
                </td>
                <td colspan="4" style="text-align: center;font-size: 9px;">
                    <div id="txtDireccion">
                        {!! html_entity_decode($data['company']->company_name) !!}
                        <br><br>
                        <b>COMPROBANTE DE EGRESOS</b><br>
                        <b style="color: red">GASTOS DE CAJA</b><br>
                        <b style="color: red">{{$data['datos']->date_created}}</b><br>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <table style="width: 100%;" class="table">
        <thead>
            <tr>
                <th style="width: 80%">DETALLE</th>
                <th style="width: 20%">VALOR</th>
            </tr>
        </thead>
        <tbody style="font-size: 10px;">
            <tr>
                <td style="text-align: left;">Aporte para Gastos de Administración</td>
                <td style="text-align: center;">$ 0.00</td>
            </tr>
            <tr>
                <td style="text-align: left;">Encajes</td>
                <td style="text-align: center;">$ 0.00</td>
            </tr>
            <tr>
                <td style="text-align: left;">Cuota Préstamo</td>
                <td style="text-align: center;">$ 0.00</td>
            </tr>
            <tr>
                <td style="text-align: left;">Interes Préstamo</td>
                <td style="text-align: center;">$ 0.00</td>
            </tr>
            <tr>
                <td style="text-align: left;">Interes Mora</td>
                <td style="text-align: center;">$ 0.00</td>
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
                <td style="text-align: left;">Aporte Capital</td>
                <td style="text-align: center;">$ 0.00</td>
            </tr>
            <tr>
                <td style="text-align: left;">Aporte de Capital Socio Estratégico</td>
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
                <td style="text-align: left;">Liquidación Total del Crédito</td>
                <td style="text-align: center;">$ 0.00</td>
            </tr>
            <tr>
                <td style="text-align: left;">Gastos de Caja</td>
                <td style="text-align: center;">$ -{{$data['datos']->valor_movimiento}}</td>
            </tr>
            <tr>
                <td style="text-align: right;">TOTAL</td>
                <td style="text-align: center;color: red">$ {{$data['datos']->valor_movimiento}}</td>
            </tr>
        </tbody>
    </table>
    <div style="text-align: center;bottom: 9px;position: static;">
        <table style="font-size: 10px;width: 100%;">
            <tr>
                <td style="text-align: left;">La cantidad de <b>{{$data['datos']->valor_movimiento}}</b><br></td>
            </tr>
            <tr>
                <td style="text-align: center;">__________________<br>Beneficiario</td>

            </tr>
        </table>
    </div>

</body>

</html>