<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>CODEV - Credito - {{$data['cabecera']->customer_name}}</title>
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

                            <b>TIPO</b><br>
                            <b style="color: red">TABLA DE AMORTIZACION</b><br>
                            <b>CARPETA: <i style="color: red;">{{$data['cabecera']->code}}</i></b><br>
                        </div>
                    </td>
                </tr>
            </table>
            <hr>
            <table style="font-size: 11px;width: 100%">
                <tr>
                    <td style="width: 60px"><b>Cliente: </b></td>
                    <td id="txtCliente" style="width: 300px;">{{$data['cabecera']->customer_name}}</td>
                    <td><b>Fecha: </b></td>
                    <td id="txtFecha">{{$data['cabecera']->date_created}}</td>
                    <td><b>Hora:</b></td>
                    <td id="txtVendor">{{$data['cabecera']->hour_created}}</td>
                </tr>
                <tr>
                    <td><b>RUC: </b></td>
                    <td><div id="txtRuc">{{$data['cabecera']->customer_ruc}}</div></td>
                    <td><b>Teléfono: </b></td>
                    <td><div id="txtTelefono">{{$data['cabecera']->customer_phone}}</div></td>
                </tr>
                <tr>
                    <td><b>Dirección: </b></td>
                    <td colspan="3"><div id="txtDireccion">{{$data['cabecera']->customer_address}}</div></td>
                </tr>
                <tr>
                    <td><b>Valor Prestamo: </b></td>
                    <td id="txtCiudad">{{$data['cabecera']->valor_solicitado}}</td>
                    <td><b>Valor Cuota: </b></td>
                    <td id="txtCiudad">{{$data['cabecera']->valor_cuota}}</td>
                    <td><b>Años: </b></td>
                    <td id="txtFechaVencimiento"> {{round($data['cabecera']->anios_pagar)}}</td>

                </tr>
                <tr>
                    <td><b>Número Cuotas: </b></td>
                    <td id="txtFechaVencimiento"> {{round($data['cabecera']->cuotas_pagar)}}</td>
                    <td><b>Total Interes:</b></td>
                    <td id="txtDiasCredito">{{$data['cabecera']->valor_interes_pago}}</td>
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
                    <th>DESGRAVAMEN</th>
                    <th>VALOR CUOTA</th>
                    <th>SALDO</th>
                </tr>
            </thead>
            <tbody style="font-size: 11px;">
                @foreach($data['detalle'] as $detalle)
                <tr>
                    <td style="text-align: center;">{{$detalle->numero_cuota}}</td>
                    <td style="text-align: center;">{{$detalle->date_vencimiento}}</td>
                    <td style="text-align: center;">{{$detalle->interes_periodo}}</td>
                    <td style="text-align: center;">{{$detalle->capital_amortizado}}</td>
                    <td style="text-align: center;">{{$detalle->fondo_desgravamen}}</td>
                    <td style="text-align: center;">{{$detalle->valor_cuota}}</td>
                    <td style="text-align: center;">{{$detalle->saldo_remanente}}</td>
                </tr>
                @endforeach
            </tbody>

        </table>
        <!--casado-->
        @if($data['estado_civil'] == 'casado'  && $data['tarifa_name'] == 'PARTICULAR')
        <div style="text-align: center;bottom: 10px;">
            <br>
            <br>
            <br>
            <br>
            <table style="width: 100%;">
                <tbody style="font-size: 12px;">
                    <tr>
                        <td style="text-align: center;"><b>_______________________________</b></td>
                        <td style="text-align: center;"><b>_______________________________</b></td>
                        @if($data['numero_garante'] != 0)
                        <td style="text-align: center;"><b>_______________________________</b></td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: center;">Sr.(a){{$data['cabecera']->customer_name}}</td>
                        <td style="text-align: center;">Sr.(a) {{$data['customer']->conyugue_nombre}}</td>
                        @if($data['numero_garante'] != 0)
                        <td style="text-align: center;">Sr.(a) {{$data['garante']->nombres}} {{$data['garante']->apellidos}}</td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>DEUDOR</b></td>
                        <td style="text-align: center;"><b>CONYUGUE</b</td>
                        @if($data['numero_garante'] != 0)
                        <td style="text-align: center;"><b>GARANTE</b</td>  
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>Nº C.I: {{$data['cabecera']->customer_ruc}}</b></td>
                        <td style="text-align: center;"><b>Nº C.I: {{$data['customer']->conyugue_identificacion}}</b</td>
                        @if($data['numero_garante'] != 0)
                        <td style="text-align: center;"><b>Nº C.I: {{$data['garante']->numero_documento}}</b</td>
                        @endif
                    </tr>
                </tbody>
            </table>
            <br>
            <br>
            <br>
            <table style="width: 100%;">
                <tbody style="font-size: 12px;">
                    <tr>
                        <td style="text-align: center;"><b>_______________________________</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">Sr.(a) &nbsp; {{$data['company']->legal_representative}} </td>                   
                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>GERENTE</b></td>                 
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
        @if($data['estado_civil'] == 'casado'   && $data['tarifa_name'] == 'SOCIO')
        <div style="text-align: center;bottom: 10px;">
            <br>
            <br>
            <br>
            <br>
            <table style="width: 100%;">
                <tbody style="font-size: 12px;">
                    <tr>
                        <td style="text-align: center;"><b>_______________________________</b></td>
                        <td style="text-align: center;"><b>_______________________________</b></td>

                    </tr>
                    <tr>
                        <td style="text-align: center;">Sr.(a){{$data['cabecera']->customer_name}}</td>
                        <td style="text-align: center;">Sr.(a) {{$data['customer']->conyugue_nombre}}</td>

                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>DEUDOR</b></td>
                        <td style="text-align: center;"><b>CONYUGUE</b</td>

                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>Nº C.I: {{$data['cabecera']->customer_ruc}}</b></td>
                        <td style="text-align: center;"><b>Nº C.I: {{$data['customer']->conyugue_identificacion}}</b</td>

                    </tr>
                </tbody>
            </table>
            <br>
            <br>
            <br>
            <table style="width: 100%;">
                <tbody style="font-size: 12px;">
                    <tr>
                        <td style="text-align: center;"><b>_______________________________</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">Sr.(a) &nbsp; {{$data['company']->legal_representative}} </td>                   
                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>GERENTE</b></td>                 
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
        <!--soltero-->
        @if($data['estado_civil'] == 'soltero'  && $data['tarifa_name'] == 'PARTICULAR')
        <div style="text-align: center;bottom: 10px;">
            <br>
            <br>
            <br>
            <br>
            <table style="width: 100%;">
                <tbody style="font-size: 12px;">
                    <tr>
                        <td style="text-align: center;"><b>_______________________________</b></td>
                        @if($data['numero_garante'] != 0)
                        <td style="text-align: center;"><b>_______________________________</b></td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: center;">Sr.(a){{$data['cabecera']->customer_name}}</td>
                        @if($data['numero_garante'] != 0)
                        <td style="text-align: center;">Sr.(a) {{ (isset($data['garante']->nombres)) ? $data['garante']->nombres : ''}} {{(isset($data['garante']->apellidos)) ? $data['garante']->apellidos: ''}}</td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>DEUDOR</b></td>
                        @if($data['numero_garante'] != 0)
                        <td style="text-align: center;"><b>GARANTE</b</td> 
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>Nº C.I: {{(isset($data['cabecera']->customer_ruc)) ? $data['cabecera']->customer_ruc :''}}</b></td>
                        @if($data['numero_garante'] != 0)
                        <td style="text-align: center;"><b>Nº C.I: {{ (isset($data['garante']->numero_documento)) ?  $data['garante']->numero_documento : ''}}</b</td>                    
                        @endif
                    </tr>
                </tbody>
            </table>
            <br>
            <br>
            <br>
            <table style="width: 100%;">
                <tbody style="font-size: 12px;">
                    <tr>
                        <td style="text-align: center;"><b>_______________________________</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">Sr.(a) &nbsp; {{$data['company']->legal_representative}} </td>                   
                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>GERENTE</b></td>                 
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
        @if($data['estado_civil'] == 'soltero'   && $data['tarifa_name'] == 'SOCIO')
        <div style="text-align: center;bottom: 10px;">
            <br>
            <br>
            <br>
            <br>
            <table style="width: 100%;">
                <tbody style="font-size: 12px;">
                    <tr>
                        <td style="text-align: center;"><b>_______________________________</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">Sr.(a){{$data['cabecera']->customer_name}}</td>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>DEUDOR</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>Nº C.I: {{$data['cabecera']->customer_ruc}}</b></td>


                    </tr>
                </tbody>
            </table>
            <br>
            <br>
            <br>
            <table style="width: 100%;">
                <tbody style="font-size: 12px;">
                    <tr>
                        <td style="text-align: center;"><b>_______________________________</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">Sr.(a) &nbsp; {{$data['company']->legal_representative}} </td>                   
                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>GERENTE</b></td>                 
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
    </body>
</html>
