<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CODEV - Entrega de encaje - {{$customer->nombres .' '. $customer->apellidos}}</title>
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

    <div class="text-align-left" style="position: absolute;z-index: 0;top: 0;left: 0;width: 100%; opacity: 0.07;">
        <img id="bcTarget" class="img_bar_code" src="data:image/png;base64,{{$imagen}}" alt="" width="100%" />
    </div>
    <table style="width: 100%;">
        <tr>
            <td style="text-align: right;">{{$fechaFormateada}}</td>
        </tr>
        <tr>
            <td style="text-align: right;"><br><br></td>
        </tr>
        <tr>
            <td style="text-align: center;"><b>DOCUMENTO DE LIQUIDACIÓN Y ACEPTACIÓN DE DEVOLUCIÓN DEL ENCAJE</b></td>
        </tr>
    </table>
    <br>
    <p>
        Yo,<b><i>{{$customer->nombres}} {{$customer->apellidos}}</i></b> , con numero de cédula de identidad No. {{$customer->numero_documento}}; en mi calidad
        de socio/a de la Caja de Ahorro, declaro que:
    </p>
    <p>
    <ol>
        <li>He decido desistir del crédito solicitado a Caja de Ahorro.</li>
        <li>Acepto la devolución del encaje entregado como requisito del crédito, una vez aplicada
            la retención correspondiente por concepto de gastos administrativos.</li>
        <li>Estoy conforme con los valores calculados y detallados a continuación: </li>
        <br>

        <table style="width: 100%;" class="table">
            <thead>
                <tr>
                    <th style="width: 50%;"><b>Descripción</b></th>
                    <th style="width: 50%"><b>Valor</b></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: left;">(+) Valor en la cuenta de encaje  </td>
                    <td style="text-align: left;">$ {{$encaje->valor}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">(-) Retención de gastos administrativos ({{$encaje->porcentaje_retenido}}%)</td>
                    <td style="text-align: left;">$ {{$encaje->valor_retenido}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">(-)Valor en cuentas de Plazo Fijo ({{$encaje->porcentaje_plazo_fijo}}%)</td>
                    <td style="text-align: left;">$ {{$encaje->valor_plazo_fijo}}</td>
                </tr>
                <tr>
                    <td style="text-align: left;"><b>TOTAL A DEVOLVER</b></td>
                    <td style="text-align: left;"><b>$ {{$encaje->valor_entregado}}</b></td>
                </tr>
            </tbody>

        </table>
        <br>

        <li>Declaro que, con la devolución del monto detallado como Total a Devolver, no tendré
            ningún reclamo posterior contra la Caja de Ahorro relacionado con este trámite.</li>
    </ol>
    </p>
    <br>
    <br>
    <br>
    <table style="width: 100%;">
        <tr>
            <td style="text-align: left;"><b>Recibí conforme:</b> </td>
        </tr>
        <tr>
            <td style="text-align: left;"><br><br><br><br></td>
        </tr>

        <tr>
            <td style="text-align: left;">___________________________________________ </td>
        </tr>
        <tr>
            <td style="text-align: left;"><b>{{$customer->nombres}} {{$customer->apellidos}} <br> {{$customer->numero_documento}}</b> </td>
        </tr>
    </table>



</body>

</html>