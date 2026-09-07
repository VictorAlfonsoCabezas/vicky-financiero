<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Onix .:. Cierres de Caja</title>
    <style>
        html {
            margin: 0;
        }

        body {
            font-family: sans-serif;
            margin: 10mm 10mm 10mm 10mm;
            font-size: 11px;
        }

        .div_cabecera {
            display: inline-block;
            height: 3cm;
            position: absolute;
            vertical-align: middle;
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
    <div style="width: 100%;">
        <table style="font-size: 8px;width: 100%">
            <tr>
                <td colspan="2">
                    <div style="width: 100%;height: 3cm;">
                        <div style="text-align: left;width: 220px;text-align: center;" class="div_cabecera">
                            <!--<img src="data:image/png;base64,{{$data['imagen']}}" style="width: 150px;margin-left: 10px;"/> -->
                        </div>
                        <div style="width: 350px;margin-left: 220px;" class="div_cabecera">
                            <div style="text-align: center;">
                                <h2>Reporte de Cierre de Caja</h2>
                                <p style="font-size: 10px;margin: 0;"><b>Desde:</b>{{$data['caja']->date_inicial}} {{$data['caja']->date_inicial}} </p>
                                <p style="font-size: 10px;margin: 0;"><b>Hasta:</b>{{$data['caja']->date_finish}} {{$data['caja']->hour_finish}}</p>
                                <p style="font-size: 10px;margin: 0;"><b>Caja: </b>{{$data['caja']->id}} </p>
                                <p style="font-size: 10px;margin: 0;"><b>Código: </b>{{$data['caja']->code}}</p>
                                <p style="font-size: 10px;margin: 0;"><b>Usuario Abre: </b>{{$data['caja']->user_name_inicial}} <b>Usuario Cierra:</b> {{$data['caja']->user_finish_name}}</p>
                            </div>
                        </div>
                        <div style="text-align: left;width: 220px;margin-left: 570px;text-align: center;" class="div_cabecera">
                            <img src="data:image/png;base64,{{$data['imagen']}}" style="width: 70px;margin-top: 20px;">
                            <p><b>{{$data['caja']->code}}</b></p>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="vertical-align: middle; padding-left: 8px;font-size: 10px;height:37px;text-align: center;background: #CCC;"><b>RESUMEN GENERAL DE LA CAJA</b></td>
            </tr>
            <tr>
                <td style="border-right: 1px solid #000;">
                    <table style="width: 100%;">
                        <thead style="border-bottom: #000 solid 1px;">
                            <tr>
                                <th style="text-align: left;">Descipción</th>
                                <th style="text-align: right;">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: #CCC solid 1px;">
                                <td style="text-align: left;">Valor Apertura</td>
                                <td style="text-align: right;">${{$data['caja']->valor_inicial}}</td>
                            </tr>
                            <tr style="border-bottom: #CCC solid 1px;">
                                <td style="text-align: left;">Valor Cierra</td>
                                <td style="text-align: right;">${{$data['caja']->total_final}}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>

                <td>
                    <table style="width: 100%;">
                        <thead style="border-bottom: #000 solid 1px;">
                            <tr>
                                <td colspan="4" style="vertical-align: middle; padding-left: 8px;font-size: 10px;height:37px;text-align: center;background: #CCC;"><b>ARQUEO DE CAJA</b></td>
                            </tr>
                            <tr>
                                <th style="text-align: left;">Lista</th>
                                <th style="text-align: right;">Valor</th>
                                <th style="text-align: right;">Cantidad</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['denom'] as $deno)
                            <tr>
                                <td class="small">{{$deno['nombre']}}</td>
                                <td class="small" style="text-align: right;">$ {{$deno['valor']}}</td>
                                <td class="small" style="text-align: center;">{{$deno['cantidad']}}</td>
                                <td class="small" style="text-align: right;">$ {{number_format($deno['totalValor'],2)}}</td>

                            </tr>
                            @endforeach

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right;">TOTAL</td>
                                <td style="text-align: right;">${{$data['caja']['total']}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </td>

            </tr>
        </table>

        <table style="font-size: 8px;width: 100%">
            <tr>
                <td colspan="2" style="vertical-align: middle; padding-left: 8px;font-size: 10px;height:37px;text-align: center;background: #CCC;"><b>DETALLE OTROS VALORES</b></td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid black;">
                    <table style="width: 100%;" class="table">
                        <thead style="border-bottom: #000 solid 1px;">
                            <tr>
                                <th style="text-align: left;">CONCEPTO</th>
                                <th style="text-align: left;">DESCRIPCION</th>
                                <th style="text-align: right;">VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['otrosValores']->get() as $valores)
                            <tr style="border-bottom: #CCC solid 1px;">
                                <td style="text-align: left;">{{$valores->name}}</td>
                                <td style="text-align: left;">{{$valores->descripcion}}</td>
                                <td style="text-align: right;">$ {{$valores->valor}}</td>
                            </tr>
                            @endforeach
                            <tr style="border-bottom: #CCC dotted 1px;">
                                <td colspan="2" style="text-align: right;"><b>Total: </b></td>
                                <td style="text-align: right;"><b>$ {{$data['otrosValores']->sum('valor')}}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <table style="font-size: 8px;width: 100%">
            <tr>
                <td colspan="2" style="vertical-align: middle; padding-left: 8px;font-size: 10px;height:37px;text-align: center;background: #CCC;"><b>ENTREGA DE CREDITOS</b></td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid black;">
                    <table style="width: 100%;" class="table">
                        <thead style="border-bottom: #000 solid 1px;">
                            <tr>
                                <th style="text-align: left;">DOCUMENTO</th>
                                <th style="text-align: left;">NOMBRE</th>
                                <th style="text-align: right;">OBSERVACION</th>
                                <th style="text-align: right;">VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['detalleEntregaCreditos']->get() as $valores)
                            <tr style="border-bottom: #CCC solid 1px;">
                                <td style="text-align: left;">{{$valores->comprobante}}</td>
                                <td style="text-align: left;">
                                    {{ $valores->customer_name}}<br>
                                    <b>{{$valores->customer_ruc}}</b>
                                </td>
                                <td style="text-align: left;">{{$valores->observation}}</td>
                                <td style="text-align: right;">$ {{$valores->valor_movimiento}}</td>
                            </tr>
                            @endforeach
                            <tr style="border-bottom: #CCC dotted 1px;">
                                <td colspan="3" style="text-align: right;"><b>Total: </b></td>
                                <td style="text-align: right;"><b>$ {{$data['detalleEntregaCreditos']->sum('valor_movimiento')}}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <table style="font-size: 8px;width: 100%">
            <tr>
                <td colspan="2" style="vertical-align: middle; padding-left: 8px;font-size: 10px;height:37px;text-align: center;background: #CCC;"><b>DEPOSITOS A LAS CUENTAS</b></td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid black;">
                    <table style="width: 100%;" class="table">
                        <thead style="border-bottom: #000 solid 1px;">
                            <tr>
                                <th style="text-align: left;">DOCUMENTO</th>
                                <th style="text-align: left;">NOMBRE</th>
                                <th style="text-align: right;">OBSERVACION</th>
                                <th style="text-align: right;">VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['detalleDepositos']->get() as $valores)
                            <tr style="border-bottom: #CCC solid 1px;">
                                <td style="text-align: left;">{{$valores->comprobante}}</td>
                                <td style="text-align: left;">
                                    {{ $valores->customer_name}}<br>
                                    <b>{{$valores->customer_ruc}}</b>
                                </td>
                                <td style="text-align: left;">{{$valores->observation}}</td>
                                <td style="text-align: right;">$ {{$valores->valor_movimiento}}</td>
                            </tr>
                            @endforeach
                            <tr style="border-bottom: #CCC dotted 1px;">
                                <td colspan="3" style="text-align: right;"><b>Total: </b></td>
                                <td style="text-align: right;"><b>$ {{$data['detalleDepositos']->sum('valor_movimiento')}}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <table style="font-size: 8px;width: 100%">
            <tr>
                <td colspan="2" style="vertical-align: middle; padding-left: 8px;font-size: 10px;height:37px;text-align: center;background: #CCC;"><b>INGRESO A CLIENTES (SUMA CLIENTES)</b></td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid black;">
                    <table style="width: 100%;" class="table">
                        <thead style="border-bottom: #000 solid 1px;">
                            <tr>
                                <th style="text-align: left;">DOCUMENTO</th>
                                <th style="text-align: left;">NOMBRE</th>
                                <th style="text-align: right;">OBSERVACION</th>
                                <th style="text-align: right;">VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['detalleSumaClientes']->get() as $valores)
                            <tr style="border-bottom: #CCC solid 1px;">
                                <td style="text-align: left;">{{$valores->comprobante}}</td>
                                <td style="text-align: left;">
                                    {{ $valores->customer_name}}<br>
                                    <b>{{$valores->customer_ruc}}</b>
                                </td>
                                <td style="text-align: left;">{{$valores->observation}}</td>
                                <td style="text-align: right;">$ {{$valores->valor_movimiento}}</td>
                            </tr>
                            @endforeach
                            <tr style="border-bottom: #CCC dotted 1px;">
                                <td colspan="3" style="text-align: right;"><b>Total: </b></td>
                                <td style="text-align: right;"><b>$ {{$data['detalleSumaClientes']->sum('valor_movimiento')}}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <table style="font-size: 8px;width: 100%">
            <tr>
                <td colspan="2" style="vertical-align: middle; padding-left: 8px;font-size: 10px;height:37px;text-align: center;background: #CCC;"><b>SUMA EMPRESA</b></td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid black;">
                    <table style="width: 100%;" class="table">
                        <thead style="border-bottom: #000 solid 1px;">
                            <tr>
                                <th style="text-align: left;">DOCUMENTO</th>
                                <th style="text-align: left;">NOMBRE</th>
                                <th style="text-align: right;">OBSERVACION</th>
                                <th style="text-align: right;">VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['detalleSumaEmpresa']->get() as $valores)
                            <tr style="border-bottom: #CCC solid 1px;">
                                <td style="text-align: left;">{{$valores->comprobante}}</td>
                                <td style="text-align: left;">
                                    {{ $valores->customer_name}}<br>
                                    <b>{{$valores->customer_ruc}}</b>
                                </td>
                                <td style="text-align: left;">{{$valores->observation}}</td>
                                <td style="text-align: right;">$ {{$valores->valor_movimiento}}</td>
                            </tr>
                            @endforeach
                            <tr style="border-bottom: #CCC dotted 1px;">
                                <td colspan="3" style="text-align: right;"><b>Total: </b></td>
                                <td style="text-align: right;"><b>$ {{$data['detalleSumaEmpresa']->sum('valor_movimiento')}}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <table style="font-size: 8px;width: 100%">
            <tr>
                <td colspan="2" style="vertical-align: middle; padding-left: 8px;font-size: 10px;height:37px;text-align: center;background: #CCC;"><b>RECAUDACION</b></td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid black;">
                    <table style="width: 100%;" class="table">
                        <thead style="border-bottom: #000 solid 1px;">
                            <tr>
                                <th style="text-align: left;">DOCUMENTO</th>
                                <th style="text-align: left;">NOMBRE</th>
                                <th style="text-align: right;">OBSERVACION</th>
                                <th style="text-align: right;">VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['detalleRecaudaciones']->get() as $valores)
                            <tr style="border-bottom: #CCC solid 1px;">
                                <td style="text-align: left;">{{$valores->comprobante}}</td>
                                <td style="text-align: left;">
                                    {{ $valores->customer_name}}<br>
                                    <b>{{$valores->customer_ruc}}</b>
                                </td>
                                <td style="text-align: left;">{{$valores->observation}}</td>
                                <td style="text-align: right;">$ {{$valores->valor_movimiento}}</td>
                            </tr>
                            @endforeach
                            <tr style="border-bottom: #CCC dotted 1px;">
                                <td colspan="3" style="text-align: right;"><b>Total: </b></td>
                                <td style="text-align: right;"><b>$ {{$data['detalleRecaudaciones']->sum('valor_movimiento')}}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>


        <table style="font-size: 8px;width: 100%">
            <tr>
                <td colspan="2" style="vertical-align: middle; padding-left: 8px;font-size: 10px;height:37px;text-align: center;background: #CCC;"><b>RETIROS A LAS CUENTAS</b></td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid black;">
                    <table style="width: 100%;" class="table">
                        <thead style="border-bottom: #000 solid 1px;">
                            <tr>
                                <th style="text-align: left;">DOCUMENTO</th>
                                <th style="text-align: left;">NOMBRE</th>
                                <th style="text-align: right;">OBSERVACION</th>
                                <th style="text-align: right;">VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['detalleRetiros']->get() as $valores)
                            <tr style="border-bottom: #CCC solid 1px;">
                                <td style="text-align: left;">{{$valores->comprobante}}</td>
                                <td style="text-align: left;">
                                    {{ $valores->customer_name}}<br>
                                    <b>{{$valores->customer_ruc}}</b>
                                </td>
                                <td style="text-align: left;">{{$valores->observation}}</td>
                                <td style="text-align: right;">$ {{$valores->valor_movimiento}}</td>
                            </tr>
                            @endforeach
                            <tr style="border-bottom: #CCC dotted 1px;">
                                <td colspan="3" style="text-align: right;"><b>Total: </b></td>
                                <td style="text-align: right;"><b>$ {{$data['detalleRetiros']->sum('valor_movimiento')}}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <table style="font-size: 8px;width: 100%">
            <tr>
                <td colspan="2" style="vertical-align: middle; padding-left: 8px;font-size: 10px;height:37px;text-align: center;background: #CCC;"><b>DEPOSITOS BOVEDA MATRIZ</b></td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid black;">
                    <table style="width: 100%;" class="table">
                        <thead style="border-bottom: #000 solid 1px;">
                            <tr>
                                <th style="text-align: left;">DOCUMENTO</th>
                                <th style="text-align: left;">BANCO</th>
                                <th style="text-align: right;">OBSERVACION</th>
                                <th style="text-align: right;">VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['depositoBoveda']->get() as $valores)
                            <tr style="border-bottom: #CCC solid 1px;">
                                <td style="text-align: left;">{{str_pad($valores->id, 12, "0", STR_PAD_LEFT) }}</td>
                                <td style="text-align: left;">
                                    {{ $valores->nombreBanco}}<br>
                                    <b>{{$valores->cuentaBanco}}</b>
                                </td>
                                <td style="text-align: left;">{{$valores->observacion}}</td>
                                <td style="text-align: right;">$ {{$valores->valor}}</td>
                            </tr>
                            @endforeach
                            <tr style="border-bottom: #CCC dotted 1px;">
                                <td colspan="3" style="text-align: right;"><b>Total: </b></td>
                                <td style="text-align: right;"><b>$ {{$data['depositoBoveda']->sum('valor')}}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <table style="font-size: 8px;width: 100%">
            <tr>
                <td colspan="2" style="vertical-align: middle; padding-left: 8px;font-size: 10px;height:37px;text-align: center;background: #CCC;"><b>GASTOS</b></td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid black;">
                    <table style="width: 100%;" class="table">
                        <thead style="border-bottom: #000 solid 1px;">
                            <tr>
                                <th style="text-align: left;">DOCUMENTO</th>
                                <th style="text-align: left;">NOMBRE</th>
                                <th style="text-align: right;">OBSERVACION</th>
                                <th style="text-align: right;">VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['detalleGastosCaja']->get() as $valores)
                            <tr style="border-bottom: #CCC solid 1px;">
                                <td style="text-align: left;">{{$valores->code}}</td>
                                <td style="text-align: left;">
                                    {!! html_entity_decode($valores->customer_name) !!}
                                    <br>
                                    <b>{{$valores->customer_ruc}}</b>
                                </td>
                                <td style="text-align: left;">{{$valores->observation}}</td>
                                <td style="text-align: right;">$ {{$valores->valor_movimiento}}</td>
                            </tr>
                            @endforeach
                            <tr style="border-bottom: #CCC dotted 1px;">
                                <td colspan="3" style="text-align: right;"><b>Total: </b></td>
                                <td style="text-align: right;"><b>$ {{$data['detalleGastosCaja']->sum('valor_movimiento')}}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <br>
        <br>
        <br>

        <div class="px-14 py-10 text-sm text-neutral-700">
            <center>
                <table style="width: 100%;">
                    <tr>
                        <td>_______________________________________</td>
                        <td></td>
                        <td>_______________________________________</td>
                    </tr>
                    <tr>
                        <td><b>ENTREGADO POR:</b></td>
                        <td></td>
                        <td><b>RECIBIDO POR</b></td>
                    </tr>
                    <tr>
                        <td><b>CI:</b></td>
                        <td></td>
                        <td><b>CI:</b></td>
                    </tr>
                </table>
            </center>
        </div>



    </div>
</body>

</html>