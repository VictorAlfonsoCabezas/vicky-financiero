<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CERTIFICADO DE AHORRO PROGRAMADO -</title>
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
        <img id="bcTarget" class="img_bar_code" src="data:image/png;base64,{{ $data['imagen'] }}" alt=""
            width="100%" />
    </div>
<br>
<br>
<br>
<br>
    <div id="row" style="font-size:14px ;width: 100%;border: 0.5px solid; padding: 10px;border-radius: 14px;">
        <center><b>CERTIFICADO DE AHORRO PROGRAMADO</b></center>
        <table style="font-size: 14px;width: 100%">
            <tr>
                <td>No:{{$data['movimiento']->code}} </td>
                <td></td>
                <td>Cédula / Ruc: {{ $data['company']->ruc }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>Teléfono: {{ $data['company']->phone }}</td>
            </tr>
        </table>
        <center><b>VALOR NOMINAL USD: {{ $data['movimiento']->valor_movimiento }}********</b></center>
        <hr>
        <table style="font-size: 14px;width: 100%">
            <tr>
                <td>Cliente: <b>{{ $data['customer']->nombres . ' ' . $data['customer']->apellidos }}</b></td>
                <td>Fecha de Emisión: <b> {{$data['fechaFormateadaInicio']}}</b></td>

            </tr>
            <tr>
                <td>Beneficiario:<b> {{ ($data['beneficiarioProgramado'] != '') ? $data['beneficiarioProgramado'] : "..............................................." }}  <b></td>
                <td>Fecha de Vencimiento: <b>{{$data['fechaFormateadaFin']}}</b></td>
            </tr>
            <tr>
                <td>Capital: <b>{{$data['cantidadLetras']}}</b></td>
                <td>Plazo: <b>{{$data['movimiento']->plazo_dias_programado}} días </b></td>
            </tr>
        </table>
        <p>
            Tasa Nominal: <b>{{$data['detallePlazo']->interes}}</b> %
        </p>
        <p>
            En virtud de la facultad conferida en el Literal b) del articulo 51 de la Ley General de Instituciones del
            Sistema Financiero,
            {{$data['company']->comercial_name}}. emite el presente Certificado de Ahorro Programado.
        </p>
        <p>
            Al vencimiento el plazo se pagará al cliente el importe del capital más los intereses generados de acuerdo
            al calendario de
            pagos que se detallan en este documento, luego de deducir los impuestos de ley correspondientes previa la
            presentación y
            entrega de este certificado más la identificación del cliente. El presente título dejará de generar
            intereses desde la fecha de
            su vencimiento.
        </p>
<!--        <p>
            El depósito representado por este documento, SI () NO () se encuentra amparado por la Agencia de Garantía de
            Depósitos,
            respecto a la tasa de interés.
        </p>-->
        <p>
        {{ $data['company']->ciudad }}, {{$data['fechaFormateadaInicio']}}
        </p>
        <br>
        <br>
        <table style="font-size: 14px;width: 50%">
            <tr>
                <td>___________________________</td>
                <td></td>

                <td>___________________________</td>
            </tr>
            <tr>
                <td><b>CLIENTE</b></td>
                <td></td>
                <td><b>FIRMA AUTORIZADA</b></td>
            </tr>
        </table>
        <br>
        <center><b>TERMINOS Y CONDICIONES</b></center>
        <p>
            El presente certificado de depósito de ahorros a plazo, está sujeto a los siguientes términos y condiciones,
            a lo General del
            Sistema financiero: dispuesto por la Superintendencia de Bancos y por la Ley General del Sistema Financiero.
        </p>
        <p>
            1.- El (los titular(es) ha(n) solicitado a {{$data['company']->comercial_name}}, la administración de sus fondos de
            conformidad a lo
            convenido en este documento. Los valores entregados en cheques están sujetos a verificación, los que no sean
            pagados por
            el banco girado causarán la anulación del presente documento y los costos generados serán asumidos por
            el(los) titular(es)
            sin que {{$data['company']->comercial_name}}. tenga que llenar formalidad alguna.
        </p>
        <p>
            2.- Declaro(amos) que los fondos entregados a {{$data['company']->comercial_name}}. tiene origen lícito, no provienen ni
            serán
            destinados en la fecha de su cancelación a ninguna actividad relacionada con el cultivo, producción,
            fabricación,
            almacenamiento, transporte o tráfico ilícito de sustancias estupefacientes o psicotrópicas, eximiendo a
            {{ $data['company']->comercial_name }},
            de la comprobación de esta declaración, Autorizo(amos) a {{$data['company']->comercial_name}}, para que en caso de
            que se inicien en mi (nuestra) contra, investigaciones relacionadas con las actividades antes mencionadas,
            pueda
            proporcionar a las autoridades competentes, la información que de estos demanden, además se apliquen todas
            las
            resoluciones y Normas para prevención de lavado de dinero provenientes de actividades ilícitas.
        </p>
        <p>
            3.- Este certificado no podrá ser cancelado en forma anticipada a su vencimiento.
        </p>
        <p>
            4.- En caso de controversia entre las partes. esta se dilucirá mediante la vía de mediación y arbitraje en
            conformidad con la
            ley de la materia.
        </p>
        <p>
            5.- Autorizo(amos) a {{$data['company']->comercial_name}}. a disponer de los recursos provenientes de esta inversión o
            cualquier
            reinversión, para abonar o cancelar las obligaciones impagas que por cualquier motivo mantenga(mos) con
            {{ $data['company']->comercial_name }}.
        </p>
        <p>
            6.- Para validez de la cesión de derechos sobre este certificado, el registro se realizará en
            {{ $data['company']->comercial_name }}.,
            para lo cual el cedente y cesionario presentarán sus documentos de identificación y suficiencia legal para
            legitimar sus derechos
            sobre este certificado.
        </p>
        <p>
            7.- En caso de pérdida, destrucción o robo de este certificado, su titular deberá notificar por escrito a
            {{ $data['company']->comercial_name }}.
            sobre el particular y someterse al trámite establecido por la Superintendencia de Economía Popular y
            Solidaria.
        </p>
        <p>
            8.- Teniendo acuerdo mutuo entre el cliente y la Corporación se da paso al pago anticipado del valor total
            del interés
            generado con la tasa de interés respectiva, dando lugar que el cliente retire a la fecha de vencimiento solo
            el valor del Capital.
        </p>
        <p>
            Calendario de Pagos:
        </p>
        <center><b>Transferencia de Cesión</b></center>
        <table style="width: 100%;" class="table">
            <thead>
                <tr>
                    <th style="width: 50px">Vencimiento</th>
                    <th style="width: 100px">Fecha</th>
                    <th style="width: 50px">Capital</th>
                    <th style="width: 50px">Intereses</th>
                    <th style="width: 50px">Impuesto</th>
                    <th style="width: 50px">Total a Pagar</th>

                </tr>
            </thead>
            <tbody style="font-size: 11px;">
                @foreach($data['movimientoCalculadora'] as $value)
                <tr>
                    <td class="small">{{$data['movimiento']->plazo_dias_programado}}</td>
                    <td class="small">{{ $value['fecha_pago'] }}</td>
                    <td class="small">{{ $data['movimiento']->valor_movimiento }}</td>
                    <td class="small">{{ $value['rentabilidad'] }}</td>
                    <td class="small">{{ $value['penalizado'] }}</td>
                    <td class="small">{{ $value['total'] }}</td>
                </tr>
                @endforeach
                


            </tbody>
        </table>
        <p>
            Cedo la Totalidad de los derechos contenidos en el presente Certificado de Depósito a favor de:
        </p>
        <table style="font-size: 14px;width: 100%">
            <tr>
                <td>___________________________</td>
                <td>___________________________</td>
                <td>___________________________</td>
            </tr>
            <tr>
                <td>Cedente</td>
                <td>{{$data['company']->comercial_name}}</td>
                <td>Cesionario</td>
            </tr>
        </table>
        <br>
        <table style="font-size: 14px;width: 100%">
            <tr>
                <td>Nombre: </td>
                <td>Nombre:</td>
            </tr>
            <tr>
                <td>CI/Ruc/Pas: </td>
                <td>CI/Ruc/Pas: </td>
            </tr>
            <tr>
                <td>Lugar y Fecha:</td>
                <td></td>
            </tr>
        </table>
    </div>

</body>

</html>
