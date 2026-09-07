<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CODEV - Notificación- </title>
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
    <table style="width: 100%; font-size: 15px;">
        <tr>
            <td style="width: 50%;">
                <table style="width: 99%">
                    <tr>
                        <td style="text-align: center">
                            <div id="txtDireccion"><img src="data:image/png;base64,{{$data['imagen']}}" alt="" style="width: 30px;" /></div>
                        </td>
                        <td style="text-align: center;font-size: 9px;">
                            <div id="txtDireccion">
                                {!! html_entity_decode($data['company']->company_name) !!}
                                <b>COMPROBANTE DE INGRESO</b><br>

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>

                        </td>
                        <td style="text-align: right;">
                            <b>Notificación</b>
                        </td>
                    </tr>
                    <tr>
                        <td>

                        </td>
                        <td style="text-align: right;">
                            Fecha: {{date('Y-m-d H:i:s')}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>NOMBRES Y APELLIDOS</b>
                        </td>
                        <td>
                            {{$data['customer']->nombres .' '.$data['customer']->apellidos }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>SOCIO N°:</b>
                        </td>
                        <td>
                            {{$data['customer']->id}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>DIRECCIÓN DEL SOCIO:</b>
                        </td>
                        <td>
                            {{$data['customer']->direccion}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>NOMBRE DE LOS GARANTES:</b>
                        </td>
                        <td>
                            ..............................................................
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>NOMBRE DEL ASESOR DE CRÉDITO:</b>
                        </td>
                        <td>
                            ..............................................................
                        </td>
                    </tr>
                </table>
                
                <table style="width: 99%;" class="table">
                    <thead>
                        <tr>
                            <th style="width: 80%">DETALLE</th>
                            <th style="width: 20%">VALOR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>CAPITAL (VALOR LETRA)</td>
                            <td>{{$data['numeroCuota']->valor_cuota}}</td>
                        </tr>
                        <tr>
                            <td>VALOR DE NOTIFICACIÓN ({{$data['numeroCuota']->notificado }})</td>
                            <td>{{$data['numeroCuota']->notificado * $data['company']->valor_notificado}}</td>
                        </tr>
                        <tr>
                            <td>INTERÉS A LA FECHA</td>
                            <td>{{$data['valorMora'] }}</td>
                        </tr>
                        <tr style="background-color: #DBFFD6;">
                            <td><B>TOTAL A PAGAR</B></td>
                            <td><B>{{($data['numeroCuota']->notificado * $data['company']->valor_notificado) +  $data['numeroCuota']->valor_cuota + $data['valorMora'] }}</B></td>
                        </tr>
                    </tbody>
                </table>
                <P>Días Mora <b>{{$data['diasMora'] }}</b></P>
              
                <table style="width: 99%;">
                    <tr>
                        <td>
                            <center>NOTIFICACIÓN DEUDOR</center>
                        </td>
                    </tr>
                </table>
               
                <table style="width: 99%;">
                    <tr>
                        <td style="text-align: justify;">
                            Le comunicamos que su préstamo se encuentra atrasado, lo que nos está causando problemas como institución, y a usted como socio por lo que le solicitamos que en el plazo no mayor a 48 HORAS (DOS DIAS) se sirva acercarse a cancelar lo adeudado, caso contrario nos veremos en la obligación de tomar acciones legales que nos permitan la recuperación del préstamo.
                        </td>
                    </tr>
                </table>
                <BR>
                <table style="font-size: 10px;width: 99%">
                    <tr>
                        <td>
                            FIRMA DEL ASESOR
                        </td>
                        <td>
                            FIRMA RECIBI CONFORMA
                        </td>
                    </tr>
                    <tr>
                        <td>

                        </td>
                        <td>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            Cel .....................................
                        </td>
                        <td>
                            Nombres .....................................
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%;">
                <table style="width: 99%">
                    <tr>
                        <td style="text-align: center">
                            <div id="txtDireccion"><img src="data:image/png;base64,{{$data['imagen']}}" alt="" style="width: 30px;" /></div>
                        </td>
                        <td style="text-align: center;font-size: 9px;">
                            <div id="txtDireccion">
                                {!! html_entity_decode($data['company']->company_name) !!}
                                <b>COMPROBANTE DE INGRESO</b><br>

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>

                        </td>
                        <td style="text-align: right;">
                            <b>Notificación</b>
                        </td>
                    </tr>
                    <tr>
                        <td>

                        </td>
                        <td style="text-align: right;">
                            Fecha: {{date('Y-m-d H:i:s')}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>NOMBRES Y APELLIDOS</b>
                        </td>
                        <td>
                            {{$data['customer']->nombres .' '.$data['customer']->apellidos }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>SOCIO N°:</b>
                        </td>
                        <td>
                            {{$data['customer']->id}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>DIRECCIÓN DEL SOCIO:</b>
                        </td>
                        <td>
                            {{$data['customer']->direccion}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>NOMBRE DE LOS GARANTES:</b>
                        </td>
                        <td>
                            ..............................................................
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>NOMBRE DEL ASESOR DE CRÉDITO:</b>
                        </td>
                        <td>
                            ..............................................................
                        </td>
                    </tr>
                </table>
                
                <table style="width: 99%;" class="table">
                    <thead>
                        <tr>
                            <th style="width: 80%">DETALLE</th>
                            <th style="width: 20%">VALOR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>CAPITAL (VALOR LETRA)</td>
                            <td>{{$data['numeroCuota']->valor_cuota}}</td>
                        </tr>
                        <tr>
                            <td>VALOR DE NOTIFICACIÓN ({{$data['numeroCuota']->notificado }})</td>
                            <td>{{$data['numeroCuota']->notificado * $data['company']->valor_notificado}}</td>
                        </tr>
                        <tr>
                            <td>INTERÉS A LA FECHA</td>
                            <td>{{$data['valorMora'] }}</td>
                        </tr>
                        <tr style="background-color: #DBFFD6;">
                            <td><B>TOTAL A PAGAR</B></td>
                            <td><B>{{($data['numeroCuota']->notificado * $data['company']->valor_notificado) +  $data['numeroCuota']->valor_cuota + $data['valorMora'] }}</B></td>
                        </tr>
                    </tbody>
                </table>
                <P>Días Mora <b>{{$data['diasMora'] }}</b></P>
               
                <table style="width: 99%;">
                    <tr>
                        <td>
                            <center>NOTIFICACIÓN DEUDOR</center>
                        </td>
                    </tr>
                </table>
                
                <table style="width: 99%;">
                    <tr>
                        <td style="text-align: justify;">
                            Le comunicamos que su préstamo se encuentra atrasado, lo que nos está causando problemas como institución, y a usted como socio por lo que le solicitamos que en el plazo no mayor a 48 HORAS (DOS DIAS) se sirva acercarse a cancelar lo adeudado, caso contrario nos veremos en la obligación de tomar acciones legales que nos permitan la recuperación del préstamo.
                        </td>
                    </tr>
                </table>
                <BR>
                <table style="font-size: 10px;width: 99%">
                    <tr>
                        <td>
                            FIRMA DEL ASESOR
                        </td>
                        <td>
                            FIRMA RECIBI CONFORMA
                        </td>
                    </tr>
                    <tr>
                        <td>

                        </td>
                        <td>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            Cel .....................................
                        </td>
                        <td>
                            Nombres .....................................
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>