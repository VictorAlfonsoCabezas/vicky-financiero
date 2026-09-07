<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CODEV - Recíbo de Pago</title>
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
    <div class="text-align-left" style="position: absolute;z-index: 0;top: 0;left: 0;width: 100%; opacity: 0.07;">
        <img id="bcTarget" class="img_bar_code" src="data:image/png;base64,{{$data['imagen']}}" alt="" width="100%" />
    </div>
    <div id="row" style="border: 0.5px solid; padding: 10px;border-radius: 15px;">
        <table style="font-size: 11px;width: 100%">
            <tr>
                <td style="text-align: center">
                    <div id="txtDireccion">
                        <img src="data:image/png;base64,{{$data['imagen']}}" alt="" style="width: 30px;" />
                    </div>
                </td>
                <td colspan="4" style="text-align: center;font-size: 9px;">
                    <div id="txtDireccion">
                        {!! html_entity_decode($data['company']->company_name) !!}
                        <br>
                        <b>COMPROBANTE DEL CRÉDITO</b><br>
                        <b style="color: red">{{$data['credito']->code}}</b><br>
                    </div>
                </td>
            </tr>
        </table>
        <hr>
        <table style="font-size: 9px;width: 100%">
            <tr>
                <td><b>Cliente: </b></td>
                <td colspan="3">
                    <div id="txtDireccion">{{$data['customer']->apellidos .' '.$data['customer']->nombres }}</div>
                </td>
            </tr>
            <tr>
                <td><b>RUC/C.I: </b></td>
                <td>
                    <div id="txtRuc">{{$data['customer']->numero_documento}}</div>
                </td>
                <td><b>N° CUOTAs </b></td>
                <td>
                    <div id="txtRuc">{{$data['credito']->cuotas_pagar}}</div>
                </td>
            </tr>
        </table>
    </div>
    <div style="text-align: center;bottom: 9px;position: static;">
        <table style="font-size: 10px;width: 100%;">
            <tr>
                <td style="text-align: center;">
                    <b>DETALLE DE LETRAS PAGADAS</b>
                    <br>
                </td>
            </tr>
        </table>
    </div>
    <table style="width: 100%;font-size: 10px;" class="table">
        <thead>
            <tr>
                <th style="width: 20%">LETRA</th>
                <th style="width: 20%">FECHA PAGO</th>
                <th style="width: 20%">VALOR</th>
                <th style="width: 20%">MORA</th>
                <th style="width: 20%">TOTAL</th>
            </tr>
        </thead>
        <tbody style="font-size: 10px;text-align: center;">
            @php $totalPagado = 0; @endphp
            @foreach($data['letras'] as $letra)
            <tr>
                <td>{{$letra->numero_cuota}}</td>
                <td>{{$letra->date_pay .' ' . $letra->hour_pay }}</td>
                <td>{{$letra->valor_pagado }}</td>
                <td>{{$letra->interes_mora }}</td>
                <td>{{$letra->valor_pagado + $letra->interes_mora }}</td>
                @php $totalPagado += $letra->valor_pagado; @endphp
            </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align: right;">TOTAL</td>
                <td>{{$totalPagado}}</td>
            </tr>
        </tbody>
    </table>
    <div style="text-align: center;bottom: 9px;position: static;">
        <table style="font-size: 10px;width: 100%;">
            <tr>
                <td style="text-align: left;">La cantidad de <b>{{$data['cantidadLetras']}}</b><br></td>
            </tr>
            <tr>
                <td style="text-align: left;"><br><br></td>
            </tr>
            <tr>
                <td style="text-align: center;">___________________________<br>{{$data['customer']->apellidos .' '.$data['customer']->nombres }}</td>

            </tr>
        </table>
    </div>
</body>

</html>