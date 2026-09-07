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
                            <b style="color: red">INFORME DE INGRESOS Y EGRESOS</b><br>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <br>           

        <table style="width: 100%;" class="table">
            <tbody style="font-size: 11px;">  

                <tr>
                    <td colspan="2" style="text-align: center;"> </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;"> {!! html_entity_decode($data['company']->company_name) !!}</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;"> INFORME DE INGRESOS Y EGRESOS</td>
                </tr>
                <tr>
                    <td style="width: 50px">DESDE</td>
                    <td style="width: 100px">{{$data['inicio']}}</td>
                </tr>
                <tr>
                    <td style="width: 50px">HASTA</td>
                    <td style="width: 100px">{{$data['fin']}}</td>
                </tr>

            </tbody>           
        </table>
        <table style="width: 100%;" class="table">
            <thead>
                <tr>
                    <th style="width: 75%;text-align: left;">Ingresos</th>
                    <th style="text-align: right;">{{$data['sumaIntereses']}} $</th>
                </tr>
            </thead>
            <tbody style="font-size: 11px;">                
                @foreach ($data['detallePrestamos'] as $value)
                <tr>
                    <td style="text-align: left;">Interes Cobrados de <b>{{ $value['prestamoNombre'] }}</b></td>
                    <td style="text-align: right;">{{ $value['interesPrestamo'] }} $</td>

                </tr>
                <tr>
                    <td style="text-align: left;">Interes de Mora de <b>{{ $value['prestamoNombre'] }}</b></td>
                    <td style="text-align: right;">{{ $value['interesMOra'] }} $</td>
                </tr>
                @endforeach
            </tbody>

        </table>
        <table style="width: 100%;" class="table">
            <thead>
                <tr>
                    <th style="width: 75%;text-align: left;">Egresos</th>
                    <th style="text-align: right;">- {{$data['sumaGastos']}} $</th>
                </tr>
            </thead>
            <tbody style="font-size: 11px;">                
                @foreach ($data['gastos'] as $valueGastos)
                <tr>
                    <td style="text-align: left;"><b>{{ $valueGastos['observation'] }}</b></td>
                    <td style="text-align: right;">- {{ $valueGastos['valor_movimiento'] }} $</td>

                </tr>
                @endforeach
                @foreach ($data['gastosIntresAhorro'] as $valueIntAho)
                <tr>
                    <td style="text-align: left;"><b>{{ $valueIntAho['ahorroNombre'] }}</b></td>
                    <td style="text-align: right;">- {{ $valueIntAho['valorAhorro'] }} $</td>

                </tr>
                @endforeach
            </tbody>
        </table>
        <table style="width: 100%;" class="table">
            <thead>
                <tr>
                    <th style="width: 75%;text-align: right;">Utilidad o pérdida</th>
                    <th style="text-align: right;">
                        @if($data['utiliPerdi'] >= 0)
                        <label style="color: green;">{{$data['utiliPerdi']}}</label>
                        @else
                        <label style="color: red;">{{$data['utiliPerdi']}}</label>
                        @endif
                    </th>
                </tr>
            </thead>

        </table>

    </body>
</html>