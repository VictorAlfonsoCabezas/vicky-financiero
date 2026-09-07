<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CODEV - Credito - {{$customer->nombres . ' ' . $customer->apellidos}}</title>
    <style>
        .table thead tr th {
            border-bottom: 1px dotted #000;
            background: #CCC
        }

        .table tbody tr td {
            border-bottom: 1px dotted #000;
            border-left: 1px dotted #000;
            border-right: 1px dotted #000;
        }
        

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0, 0, 0, 0.3);
            /* Ajusta la opacidad */
            white-space: nowrap;
            z-index: -1;
            /* Coloca la marca de agua detrás del contenido */
            pointer-events: none;
            /* Evita que interfiera con la interacción */
        }
    </style>
</head>

<body>
    @if(!$generar)
        <div class="watermark">SIMULADOR</div>
    @endif
    <div class="text-align-left" style="position: absolute;z-index: 0;top: 0;left: 0;width: 100%; opacity: 0.07;">
        <img id="bcTarget" class="img_bar_code" src="data:image/png;base64,{{$imagen}}" alt="" width="100%" />
    </div>
    <div id="row" style="width: 100%;border: 0.5px solid; padding: 10px;border-radius: 15px;">
        <table style="font-size: 100px;width: 100%">
            <tr>
                <td style="text-align: center;width: 130px;">
                    <div id="txtDireccion"><img src="data:image/png;base64,{{$imagen}}" alt="" style="width: 90px;" />
                    </div>
                </td>
                <td colspan="4" style="text-align: center;font-size: 9px;">
                    <div id="txtDireccion" >
                        {!! html_entity_decode($company->company_name) !!}
                        <br>
                        {{$company->ruc}}
                        <br>
                        <b>TIPO</b><br>
                        <b style="color: red">TABLA DE AMORTIZACION</b><br>
                        <b>CARPETA: <i style="color: red;">{{$codigoCredito}}</i></b><br>
                    </div>
                </td>
            </tr>
        </table>
        <hr>
        <table style="font-size: 11px;width: 100%">
            <tr>
                <td style="width: 60px"><b>Cliente: </b></td>
                <td id="txtCliente" style="width: 300px;">{{$customer->nombres . ' ' . $customer->apellidos}}</td>
                <td><b>Fecha: </b></td>
                <td id="txtFecha">{{$fecha_prestamo}}</td>
                <td><b>Hora:</b></td>
                <td id="txtVendor">{{date('H:i:s')}}</td>
            </tr>
            <tr>
                <td><b>RUC: </b></td>
                <td>
                    <div id="txtRuc">{{$customer->numero_documento}}</div>
                </td>
                <td><b>Teléfono: </b></td>
                <td>
                    <div id="txtTelefono">{{$customer->telefono}}</div>
                </td>
            </tr>
            <tr>
                <td><b>Dirección: </b></td>
                <td colspan="3">
                    <div id="txtDireccion">{{$customer->direccion}}</div>
                </td>
            </tr>
            <tr>
                <td><b>Valor Prestamo: </b></td>
                <td id="txtCiudad">{{$solicitadoPrestamo}}</td>
                <td><b>Valor adicional al prestamo por concepto de gastos: </b></td>
                <td id="txtCiudad">{{$valorCreditoSumado}} $</td>
                <td><b>Años: </b></td>
                <td id="txtFechaVencimiento">{{$anios_pagar}}</td>
            </tr>
            <tr>
                <td><b>Valor Cuota: </b></td>
                <td id="txtCiudad">{{$valor_cuota}}$ </td>
                <td><b>Número Cuotas: </b></td>
                <td id="txtFechaVencimiento">{{$cuotas_pagar}} </td>
                <td><b>Total Interes:</b></td>
                <td id="txtDiasCredito">{{$interes_periodo}}$</td>
            </tr>
            @if ($valor_ahorrar_credito > 0)
                <tr>
                    <td colspan="6">
                        <b>
                            El credito tiene un ahorro mensual de : {{$valor_ahorrar_credito}} $ , este valor se debe sumar
                            a la letra mensual de : {{$valor_cuota}} $, siendo un total de: <label
                                style="color:red;">{{$valor_ahorrar_credito + $valor_cuota}} $ </label>
                        </b>
                    </td>
                </tr>
            @endif

        </table>
    </div>
    <br>

    <table style="width: 100%;font-size: 10px;" class="table">
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
        <tbody style="font-size: 10px;">
            @foreach($listaLetras as $detalle)
                <tr>
                    <td style="text-align: center;">{{$detalle['cuotas']}}</td>
                    <td style="text-align: center;">{{$detalle['fechas']}}</td>
                    <td style="text-align: center;">{{ number_format($detalle['interes'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($detalle['amoritizado'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($detalle['desgravamen'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($detalle['valMes'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($detalle['deuda'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <div style="text-align: center;bottom: 10px;">
        <br>
        <br>
        <table style="width: 100%;">
            <tbody style="font-size: 11px;">
                <tr>
                    <td style="text-align: center;"><b>_______________________________</b></td>
                    @if($customer->conyugue_nombre != "")
                        <td style="text-align: center;"><b>_______________________________</b></td>
                    @endif

                </tr>
                <tr>
                    <td style="text-align: center;">Sr.(a) {{$customer->nombres . ' ' . $customer->apellidos}}</td>
                    @if($customer->conyugue_nombre != "")
                        <td style="text-align: center;">Sr.(a){{strtoupper($customer->conyugue_nombre)}}</td>
                    @endif

                </tr>
                <tr>
                    <td style="text-align: center;"><b>DEUDOR</b></td>
                    @if($customer->conyugue_nombre != "")
                        <td style="text-align: center;"><b>CONYUGE</b></td>
                    @endif

                </tr>
                <tr>
                    <td style="text-align: center;"><b>Nº C.I: {{$customer->numero_documento}}</b></td>
                    @if($customer->conyugue_nombre != "")
                        <td style="text-align: center;"><b>Nº C.I: {{$customer->conyugue_identificacion}}</b></td>
                    @endif
                </tr>
            </tbody>
        </table>
        <br>
        <br>
        @if(isset($garanteDato))
            <table style="width: 100%;">
                <tbody style="font-size: 11px;">
                    <tr>
                        <td style="text-align: center;"><b>_______________________________</b></td>
                        @if($garanteDato->conyugue_nombre != "")
                            <td style="text-align: center;"><b>_______________________________</b></td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: center;">Sr.(a) &nbsp;
                            {{$garanteDato->nombres . " " . $garanteDato->apellidos }}
                        </td>
                        @if($garanteDato->conyugue_nombre != "")
                            <td style="text-align: center;">Sr.(a) &nbsp; {{strtoupper($garanteDato->conyugue_nombre)}}</td>
                        @endif

                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>GARANTE</b></td>
                        @if($garanteDato->conyugue_nombre != "")
                            <td style="text-align: center;"><b>CO GARANTE</b></td>
                        @endif

                    </tr>

                    <tr>
                        <td style="text-align: center;"><b>Nº C.I: {{$garanteDato->customer_ruc}}</b></td>
                        @if($garanteDato->conyugue_nombre != "")
                            <td style="text-align: center;"><b>Nº C.I: {{$garanteDato->conyugue_identificacion}}</b></td>
                        @endif
                    </tr>

                </tbody>
            </table>

        @endif

        <table style="width: 100%;">
            <tbody style="font-size: 11px;">
                @foreach($garantesTabla as $detalle)
                    <tr>
                        <td style="text-align: center;"><b>_______________________________</b></td>
                        @if($detalle['customerConyugeNombre'] != "")
                            <td style="text-align: center;"><b>_______________________________</b></td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: center;">Sr.(a) &nbsp; {{$detalle['customerName'] }} </td>
                        @if($detalle['customerConyugeNombre'] != "")
                            <td style="text-align: center;">Sr.(a) &nbsp; {{$detalle['customerConyugeNombre']}}</td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>GARANTE</b></td>
                        @if($detalle['customerConyugeNombre'] != "")
                            <td style="text-align: center;"><b>CO GARANTE</b></td>
                        @endif

                    </tr>

                    <tr>
                        <td style="text-align: center;"><b>Nº C.I: {{$detalle['customerIdentificacion'] }}</b></td>
                        @if($detalle['customerConyugeNombre'] != "")
                            <td style="text-align: center;"><b>Nº C.I: {{$detalle['customerConyugeIdentificacion'] }}</b></td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: center;"><b><br><br></b></td>
                        @if($detalle['customerConyugeNombre'] != "")
                            <td style="text-align: center;"><b></b></td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        <br>
        <table style="width: 100%;">
            <tbody style="font-size: 11px;">
                <tr>
                    <td style="text-align: center;"><b>_______________________________</b></td>
                </tr>
                <tr>
                    <td style="text-align: center;">Sr.(a) &nbsp; {{$company->legal_representative}} </td>
                </tr>
                <tr>
                    <td style="text-align: center;"><b>GERENTE</b></td>
                </tr>
                <tr>
                    <td style="text-align: center;"><b>Nº C.I: {{$company->ruc}}</b></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>