<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>CODEV - Cartola N° - {{$data['cartolaHeader']->code}}</title>
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
                        <div id="txtDireccion"><img src="data:image/png;base64,{{$data['imagen']}}" alt="" style="width: 90px;"/></div>
                    </td>
                    <td colspan="4" style="text-align: center;font-size: 9px;">
                        <div id="txtDireccion">
                            {!! html_entity_decode($data['company']->company_name) !!}
                        </div>
                    </td>
                </tr>
            </table>
            <table style="font-size: 11px;width: 100%">
               
                <tr><td><br></td></tr>
                <tr>
                    <td><b>CUENTA N°: </b> <b style="color: red">{{$data['cartolaHeader']->customer_code}}</b></td>
                    <td><b>CÉDULA: </b> <b>{{$data['customer']->numero_documento}}</b></td>
                    <td><b>TITULAR: </b> <b>{{$data['cartolaHeader']->customer_name}}</b></td>
                    <td><b>CARTOLA: </b> <b>{{$data['cartolaHeader']->code}}</b></td>
                </tr>
            </table>
        </div>
        <br>
        <table style="width: 100%;" class="table">
            <thead>
                <tr>
                    <th style="width: 100px">FECHA</th>
                    <th style="width: 50px">DEPOSITO</th>
                    <th style="width: 50px">INTERES</th>
                    <th>RETIRO</th>
                    <th>SALDO</th>

                </tr>
            </thead>
            <tbody style="font-size: 11px;">
                @foreach($data['detalleCartola'] as $detalle)
                <tr>
                    <td style="text-align: center;">{{$detalle->date_transaction}}</td>
                    @if($detalle->type_transaction_action == 'S')
                    <td style="text-align: center;">{{$detalle->valor_transaction}}</td>
                    @else
                    <td style="text-align: center;"></td>
                    @endif
                    <td style="text-align: center;">{{$detalle->interes_valor}}</td>
                    @if($detalle->type_transaction_action == 'R')
                    <td style="text-align: center;">{{$detalle->valor_transaction}}</td>
                    @else
                    <td style="text-align: center;"></td>
                    @endif
                    <td style="text-align: center;">{{$detalle->saldo_transaction}}</td>
                </tr>
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
