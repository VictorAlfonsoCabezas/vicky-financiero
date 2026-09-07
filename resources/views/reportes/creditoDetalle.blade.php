<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CODEV - Credito - {{$data['cabecera']->customer_name}}</title>
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
    </style>
</head>

<body>
    <div class="text-align-left" style="position: absolute;z-index: 0;top: 0;left: 0;width: 100%; opacity: 0.07;">
        <img id="bcTarget" class="img_bar_code" src="data:image/png;base64,{{$data['imagen']}}" alt="" width="100%" />
    </div>
    <div id="row" style="width: 100%;border: 0.5px solid; padding: 10px;border-radius: 15px;">
        <table style="font-size: 10px;width: 100%">
            <tr>
                <td style="text-align: center;width: 130px;">
                    <div id="txtDireccion"><img src="data:image/png;base64,{{$data['imagen']}}" alt=""
                            style="width: 90px;" /></div>
                </td>
                <td colspan="4" style="text-align: center;font-size: 18px;">
                    <div id="txtDireccion">
                        {!! html_entity_decode($data['company']->company_name) !!}
                        <br>
                        {{$data['company']->ruc}}
                        <br>
                        <b>TIPO</b><br>
                        <b style="color: red">TABLA DE AMORTIZACION</b><br>
                    </div>
                </td>
            </tr>
        </table>
        <hr>
        <table style="font-size: 11px;width: 100%">
            <tr>
                <td><b>CLIENTE:</b></td>
                <td>{{$data['cabecera']->customer_name}}</td>
                <td><b>CÉDULA / RUC:</b></td>
                <td>{{$data['cabecera']->customer_ruc}}</td>
                <td><b>OFICINA:</b></td>
                <td>{{$data['cabecera']->code_1 ?? '...'}}</td>
            </tr>

            <tr>
                <td><b>USUARIO:</b></td>
                <td>{{$data['usario_creador']->username ?? '...'}}</td>
                <td><b>TIPO DE CRÉDITO:</b></td>
                <td>{{$data['nombrePrestamo']}}</td>
                <td><b>CALIFICACIÓN:</b></td>
                <td>{{$data['cabecera']->calificacion ?? '...'}}</td>
            </tr>

            <tr>
                <td><b>DESTINO:</b></td>
                <td>{{$data['cabecera']->tipocredito ?? '...'}}</td>
                <td><b>FREC. DE PAGO:</b></td>
                <td>{{$data['tipo_pago'] ?? '...'}}</td>
                <td><b>SALDO ACTUAL:</b></td>
                <td>{{ $data['valorDeve'] ?? '...'}}</td>
            </tr>

            <tr>
                <td><b>DEUDA INICIAL:</b></td>
                <td>{{$data['cabecera']->valor_solicitado}} $</td>
                <td><b>Nº CUOTAS:</b></td>
                <td>{{round($data['cabecera']->cuotas_pagar)}}</td>
                <td><b>TEA:</b></td>
                <td>{{$data['tea_valor'] ?? '...'}}</td>
            </tr>

            <tr>
                <td><b>Nº CRÉDITO:</b></td>
                <td>{{$data['cabecera']->code ?? '...'}}</td>
                <td><b>Nº SOLICITUD:</b></td>
                <td>{{$data['cabecera']->numero_solicitud ?? '...'}}</td>
                <td><b>FECHA DE ADJUDICACIÓN:</b></td>
                <td>{{$data['cabecera']->date_created}}</td>
            </tr>

            <tr>
                <td><b>Total Interes:</b></td>
                <td id="txtDiasCredito">$ {{$data['detalleSumaInteres']}}</td>
                <td><b>Adicional Préstamo:</b></td>
                <td>{{$data['cabecera']->suma_valores_gastos_prestamo}} $</td>
                <td><b>FECHA DE VENC.:</b></td>
                <td>{{$data['ultimo'] ?? '...'}}</td>
            </tr>

            <tr>
                <td></td>
                <td></td>
                <td><b>Valor Primera Letra:</b></td>
                <td>{{$data['primeraLetra']->valor_gasto}} $</td></td>
                <td></td>
                <td></td>
            </tr>


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
    <div style="text-align: center;bottom: 10px;">
        <br>
        <br>
        <table style="width: 100%;">
            <tbody style="font-size: 11px;">
                <tr>
                    <td style="text-align: center;"><b>_______________________________</b></td>
                    @if($data['customer']->conyugue_nombre != "")
                        <td style="text-align: center;"><b>_______________________________</b></td>
                    @endif

                </tr>
                <tr>
                    <td style="text-align: center;">Sr.(a){{$data['cabecera']->customer_name}}</td>
                    @if($data['customer']->conyugue_nombre != "")
                        <td style="text-align: center;">Sr.(a){{strtoupper($data['customer']->conyugue_nombre)}}</td>
                    @endif

                </tr>
                <tr>
                    <td style="text-align: center;"><b>DEUDOR</b></td>
                    @if($data['customer']->conyugue_nombre != "")
                        <td style="text-align: center;"><b>CONYUGE</b></td>
                    @endif

                </tr>
                <tr>
                    <td style="text-align: center;"><b>Nº C.I:
                            {{(isset($data['cabecera']->customer_ruc)) ? $data['cabecera']->customer_ruc : ''}}</b></td>
                    @if($data['customer']->conyugue_nombre != "")
                        <td style="text-align: center;"><b>Nº C.I: {{$data['customer']->conyugue_identificacion}}</b></td>
                    @endif
                </tr>
            </tbody>
        </table>
        <br>
        <br>

        @if(isset($data['garanteMostrar']))
            <table style="width: 100%;">
                <tbody style="font-size: 11px;">
                    <tr>
                        <td style="text-align: center;"><b>_______________________________</b></td>
                        @if($data['garanteMostrar']->conyugue_nombre != "")
                            <td style="text-align: center;"><b>_______________________________</b></td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: center;">Sr.(a) &nbsp;
                            {{$data['garanteMostrar']->nombres . " " . $data['garanteMostrar']->apellidos }}
                        </td>
                        @if($data['garanteMostrar']->conyugue_nombre != "")
                            <td style="text-align: center;">Sr.(a) &nbsp;
                                {{strtoupper($data['garanteMostrar']->conyugue_nombre)}}
                            </td>
                        @endif

                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>GARANTE</b></td>
                        @if($data['garanteMostrar']->conyugue_nombre != "")
                            <td style="text-align: center;"><b>CO GARANTE</b></td>
                        @endif

                    </tr>

                    <tr>
                        <td style="text-align: center;"><b>Nº C.I: {{$data['garanteMostrar']->customer_ruc}}</b></td>
                        @if($data['garanteMostrar']->conyugue_nombre != "")
                            <td style="text-align: center;"><b>Nº C.I: {{$data['garanteMostrar']->conyugue_identificacion}}</b>
                            </td>
                        @endif
                    </tr>

                </tbody>
            </table>
        @endif
        <table style="width: 100%;">
            <tbody style="font-size: 11px;">
                @foreach($data['garantesTabla'] as $garantes)
                    <tr>
                        <td style="text-align: center;"><b>_______________________________</b></td>
                        @if($garantes->customer_conyuge_name != "")
                            <td style="text-align: center;"><b>_______________________________</b></td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: center;">Sr.(a) &nbsp; {{$garantes->customer_name }} </td>
                        @if($garantes->customer_conyuge_name != "")
                            <td style="text-align: center;">Sr.(a) &nbsp; {{$garantes->customer_conyuge_name }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: center;"><b>GARANTE</b></td>
                        @if($garantes->customer_conyuge_name != "")
                            <td style="text-align: center;"><b>CO GARANTE</b></td>
                        @endif

                    </tr>

                    <tr>
                        <td style="text-align: center;"><b>Nº C.I: {{$garantes->customer_identificacion }}</b></td>
                        @if($garantes->customer_conyuge_name != "")
                            <td style="text-align: center;"><b>Nº C.I: {{$garantes->customer_conyuge_identificacion }}</b></td>
                        @endif
                    </tr>
                    <tr>
                        <td style="text-align: center;"><b><br><br></b></td>
                        @if($garantes->customer_conyuge_name != "")
                            <td style="text-align: center;"><b></b></td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        <br>


        <br>
        <table style="width: 100%;">
            <tbody style="font-size: 11px;">
                <tr>
                    <td style="text-align: center;"><b>_______________________________</b></td>
                </tr>
                <tr>
                    <td style="text-align: center;"><b>AUTORIZADO POR:</b></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>