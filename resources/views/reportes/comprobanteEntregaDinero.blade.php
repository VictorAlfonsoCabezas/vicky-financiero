<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CODEV - Comprobante N° - {{$data['cabecera']->code}}</title>
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
                <td style="text-align: center;font-size: 12px;">
                    <b> LIQUIDACIÓN DE CREDITO</b><br>
                </td>
            </tr>
        </table>
        <table style="font-size: 11px;width: 100%">
            <tr>
                <td style="text-align: left;width: 10%;">
                    <div id="txtDireccion"><img src="data:image/png;base64,{{$data['imagen']}}" alt="" style="width: 30px;" /></div>
                </td>
                <td style="text-align: center;font-size: 9px;">
                    <div id="txtDireccion">
                        {!! html_entity_decode($data['company']->company_name) !!}
                        <b style="color: red"> <br> {{$data['cabecera']->code}}</b>
                    </div>
                </td>
            </tr>
        </table>
        <hr>
        <table style="font-size: 9px;width: 100%">
            <tr>
                <td><b>RECIBE: </b></td>
                <td colspan="3">
                    <div id="txtDireccion">{{$data['cabecera']->customer_name}}</div>
                </td>
            </tr>
            <tr>
                <td><b>RUC/C.I: </b></td>
                <td>
                    <div id="txtRuc">{{$data['cabecera']->customer_ruc}}</div>
                </td>
                <td><b>FECHA: </b></td>
                <td>
                    <div id="txtTelefono">{{$data['cabecera']->date_created}}</div>
                </td>
            </tr>
            <tr>
                <td><b>HORA: </b></td>
                <td>
                    <div id="txtTelefono">{{$data['cabecera']->hour_created}}</div>
                </td>
            </tr>
        </table>
    </div>
    <table style="width: 100%;font-size: 9px;" class="table">
        <thead>
            <tr>
                <th style="width: 80%">DETALLE</th>
                <th style="width: 20%">VALOR</th>
            </tr>
        </thead>
        <tbody style="font-size: 9px;">
            <tr>
                <td style="text-align: left;">PRÉSTAMO / CAPITAL </td>
                <td style="text-align: right;">$ {{$data['cabecera']->valor_solicitado}}</td>
            </tr>
            <tr>
                <td style="text-align: left;">Encajes</td>
                <td style="text-align: right;">
                    $ {{$data['valorEncaje']}}
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">Gastos Administrativo</td>
                <td style="text-align: right;">$ {{$data['gastoAdministrativo']}}</td>
            </tr>
            @if($data['cabecera']->suma_valores_gastos_prestamo == 0)

            @if( isset($data['primer_gasto']) && $data['primer_gasto'] > 0)
            <tr>
                <td style="text-align: left;">{{$data['company']->nombre_primer_gasto_credito}}</td>
                <td style="text-align: right;">$ {{$data['primer_gasto']}}</td>
            </tr>
            @endif

            @if( isset($data['segundo_gasto']) && $data['segundo_gasto'] > 0)
            <tr>
                <td style="text-align: left;">{{$data['company']->nombre_segundo_gasto_credito}}</td>
                <td style="text-align: right;">$ {{$data['segundo_gasto']}}</td>
            </tr>
            @endif

            @if(isset($data['tercer_gasto']) && $data['tercer_gasto'] > 0)
            <tr>
                <td style="text-align: left;">{{$data['company']->nombre_tercer_gasto_credito}}</td>
                <td style="text-align: right;">$ {{$data['tercer_gasto']}}</td>
            </tr>
            @endif

            @endif
            <tr>
                <td style="text-align: right;">TOTAL</td>
                <td style="text-align: right;">$ {{number_format($data['entregar'] ,2 )}}</td>
            </tr>
        </tbody>
    </table>

    <div style="text-align: center;bottom: 9px;position: static;">
        <table style="font-size: 10px;width: 100%;">
            <tr>
                <td style="text-align: left;">La cantidad de <b>{{$data['cantidadLetras']}}</b><br></td>
            </tr>
        </table>
    </div>
    <div style="position: static;font-size: 6px;">
        <table style="width: 100%;">
            <tr>
                <td>
                    <b>
                        CONDICIONES QUE ACEPTA EL SOCIO AL SER BENEFICIARIO DEL CREDITO No. {{$data['cabecera']->code}}
                    </b>
                </td>
            </tr>
            <tr>
                <td style="padding-left: 5px;">
                    <b>Socio No.</b> {{$data['cliente']->id}} <br>
                    <b>NOMBRES:</b> {{$data['cabecera']->customer_name}} <br>
                </td>

            </tr>
            <tr>
                <td style="padding-left: 5px;">
                    Por medio de la presente acepto las siguientes condiciones sujetas al crédito que recibo referente al pagaré No. {{$data['cabecera']->code}} por un capital de
                    USD. {{$data['cabecera']->valor_solicitado}}; otorgado el {{$data['cabecera']->date_created}}, para un plazo de {{$data['cabecera']->cuotas_pagar}} cuotas.
                </td>
            </tr>
            <tr>
                <td style="padding-left: 25px;">
                    <ol>
                        <li>Mantener actualizados los datos de domicilio y números telefónicos si existiera variación o cambio. </li>
                        <li>Pagar el seguro de desgravamen correspondiente al conceder el crédito. </li>
                        <li>Por incumplimiento de pago, cancelar los valores establecidos para el proceso de recuperación. </li>
                    </ol>
                </td>
            </tr>
            <tr>
                <td style="padding-left: 5px;">
                    Autorizo y acepto que este documento sea utilizado para como instrumento legal de prueba, así como también para que los valores indicados en este, sean imputados al pago total del crédito, en el proceso que me siguieran en el caso de no pago de mi obligación.
                </td>
            </tr>
        </table>
    </div>
    <div style="text-align: center;bottom: 9px;position: static;font-size: 8px;">
        <table style="font-size: 10px;width: 100%;">
            <tr>
                <td style="text-align: center;"><br></td>
            </tr>
            <tr>
                <td style="text-align: center;">_______________________________<br>{{$data['cabecera']->customer_name}} <br>{{$data['cliente']->numero_documento}} </td>
            </tr>
        </table>
    </div>
</body>

</html>