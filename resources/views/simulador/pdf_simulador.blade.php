<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>CODEV - Simulador de Credito </title>
        <style>
            .table thead tr th{
                border-bottom: 1px dotted #000;
                background: #CCC
            }
            .table tbody tr td{
                border-bottom: 1px dotted #000;
                border-left: 1px dotted #000;
                border-right: 1px dotted #000;
            }
        </style>
    </head>

    <body>
        <div class="text-align-left" style="position: absolute;z-index: 0;top: 0;left: 0;width: 100%; opacity: 0.07;">
            <img id="bcTarget" class="img_bar_code" src="data:image/png;base64,{{$data['imagen']}}" alt="" width="100%"/>
        </div>
        <div id="row" style="width: 100%;border: 0.5px solid; padding: 10px;border-radius: 15px;">
            <table style="font-size: 11px;width: 100%">
                <tr>
                    <td style="text-align: center;width: 130px;">
                        <div id="txtDireccion"><img src="data:image/png;base64,{{$data['imagen']}}" alt="" style="width: 90px;"/></div></td>
                    <td colspan="4" style="text-align: center;font-size: 9px;">
                        <div id="txtDireccion">
                            {!! html_entity_decode($data['company']->company_name) !!}
                            <br>
                            <b>TIPO DE TRANSACCION</b><br>
                            <b style="color: red">SIMULADOR</b><br>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <br>
        <table style="width: 100%;" class="table">
            <thead>
                <tr>
                    <th style="width: 50px">CUOTAS</th>
                    <th style="width: 100px">FECHAS</th>
                    <th style="width: 50px">INTERES PERIODO</th>
                    <th style="width: 50px">CAPITAL AMORTIZADO</th>
                    @if($data['tipoPrestamo'] != 'A')
                    <th>DESGRAVAMEN</th>
                    @endif
                    <th>VALOR CUOTA</th>
                    <th>SALDO</th>
                </tr>
            </thead>
            <tbody style="font-size: 11px;">
                @foreach ($data['detalle'] as $value)
                <tr>
                    <td style="text-align: center;">{{ $value['cuotas'] }}</td>
                    <td style="text-align: center;">{{ $value['fechas'] }}</td>
                    <td style="text-align: center;">{{ $value['interes'] }}</td>
                    <td style="text-align: center;">{{ $value['amoritizado'] }}</td>
                    @if($data['tipoPrestamo'] != 'A')
                    <td style="text-align: center;">{{ $value['desgravamen'] }}</td>
                    @endif
                    <td style="text-align: center;">{{number_format($value['cuotaPago'] , 2) }}</td>
                    <td style="text-align: center;">{{ str_replace("-", "", $value['deuda']) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>