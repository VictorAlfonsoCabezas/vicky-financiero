<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>CODEV - Comprobante N° - {{$data['customerMovimiento']->code}}</title>
        <style>
            *{
                margin: 4px;
                padding: 0;

            }
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
                    <td style="text-align: center"><div id="txtDireccion"><img src="data:image/png;base64,{{$data['imagen']}}" alt="" style="width: 30px;"/></div></td>
                    <td colspan="4" style="text-align: center;font-size: 9px;">
                        <div id="txtDireccion">
                            {!! html_entity_decode($data['company']->company_name) !!}
                            <b>COMPROBANTE DE INGRESO</b><br>
                            <b style="color: red">{{$data['customerMovimiento']->code}}</b><br>
                        </div>
                    </td>
                </tr>
            </table>
            <hr>
            <table style="font-size: 9px;width: 100%">
                <tr>
                    <td><b>RECIBÍ DE: </b></td>
                    <td colspan="3"><div id="txtDireccion">{{$data['customerMovimiento']->customer_name}}</div></td>
                </tr>
                <tr>
                    <td><b>RUC/C.I: </b></td>
                    <td><div id="txtRuc">{{$data['customerMovimiento']->customer_ruc}}</div></td>
                    <td><b>FECHA: </b></td>
                    <td><div id="txtTelefono">{{$data['customerMovimiento']->date_created}}</div></td>
                </tr>
                <tr>
                    <td><b>N° CUOTA </b></td>
                    <td><div id="txtRuc">{{$data['numeroCuota']->numero_cuota}}</div></td>
                    <td><b>HORA: </b></td>
                    <td><div id="txtTelefono">{{$data['customerMovimiento']->hour_created}}</div></td>
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
                    <td style="text-align: center;">$ {{$data['numeroCuota']->valor_cuota}}</td>
                </tr>

                <tr>
                    <td style="text-align: left;">Interes Mora</td>
                    <td style="text-align: center;">$ {{$data['numeroCuota']->interes_mora}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Saldo a favor</td>


                    <td style="text-align: center;">$ {{$data['numeroCuota']->saldo_anterior_cuota}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Saldo Pendiente</td>
                    <td style="text-align: center;">$ {{$data['numeroCuota']->faltante_anterior_cuota}}</td>
                </tr>y
                <tr>
                    <td style="text-align: left;">Liquidación Total del Crédito</td>
                    <td style="text-align: center;">$ 0.00</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Otros</td>
                    <td style="text-align: center;">$ 0.00</td>
                </tr>

                <tr>
                    <td style="text-align: right;">Valor a Pagar</td>
                    <td style="text-align: center;">$ {{$data['numeroCuota']->valor_cuota + $data['numeroCuota']->interes_mora + $data['numeroCuota']->faltante_anterior_cuota - $data['numeroCuota']->saldo_anterior_cuota}}</td>
                </tr>
                <tr>
                    <td style="text-align: right;">Valor Pagado</td>
                    <td style="text-align: center;">$ {{$data['customerMovimiento']->valor_movimiento}}</td>
                </tr>
                @if($data['valorComparar'] > 0)
                <tr>
                    <td style="text-align: right;">Faltante</td>
                    <td style="text-align: center;">$ {{abs($data['valorComparar'])}}</td>
                </tr>
                @endif
                @if($data['valorComparar'] < 0)
                <tr>
                    <td style="text-align: right;">A favor</td>
                    <td style="text-align: center;">$ {{abs($data['valorComparar'])}}</td>
                </tr>
                @endif
                
                
            </tbody>
        </table>
        <div style="text-align: center;bottom: 9px;position: static;">
            <table style="font-size: 10px;width: 100%;">
                <tr>
                    <td style="text-align: left;">La cantidad de <b>{{$data['cantidadLetras']}}</b><br></td>
                </tr>
                <tr>
                    <td style="text-align: center;">__________________<br>Beneficiario</td>

                </tr>
            </table>
        </div>

    </body>
</html>
