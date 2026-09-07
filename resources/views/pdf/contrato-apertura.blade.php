@php
$dia = date('d');
$meses = [
'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
];
$mesIngles = date('F');
$mes = $meses[$mesIngles] ?? $mesIngles;
$anio = date('Y');

// Detectar IP real
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
$ciudad = 'Ciudad no disponible';

if ($data = @file_get_contents("http://ip-api.com/json/{$ip}?fields=city")) {
$json = json_decode($data);
if (!empty($json->city)) {
$ciudad = $json->city;
}
}
@endphp


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Contrato de Apertura</title>
    <style>
        @page {
            margin: 40px 75px;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            position: relative;
        }


        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
        }

        footer {
            margin-top: 120px;
            width: 100%;
        }

        footer table {
            width: 100%;
            table-layout: fixed;
        }

        footer td {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            line-height: 1;
            padding: 0 30px;
            vertical-align: top;
            text-align: center;
        }

        footer hr {
            border-top: 0.1px solid black;
        }

        footer p {
            text-emphasis: center;
        }

        .watermark {
            position: fixed;
            top: 20%;
            left: 1%;
            width: 100%;
            opacity: 0.14;
            z-index: -1000;
        }

        .watermark img {
            width: 100%;
        }
    </style>

</head>

<body style="text-align: justify;">

    <div class="watermark">
        <img src="data:image/png;base64,{{$imagen}}" alt="" width="100%">

    </div>

    <h1>CONTRATO DE APERTURA DE CUENTA DE AHORROS Y SERVICIOS FINANCIEROS</h1>
    <p><strong>CLAUSULA PRIMERA.</strong> Interviene el señor/a {{ $company->legal_representative }} Representante Legal de la {{ $company->comercial_name }}. Quien suscriben este contrato, parte a la que en adelante se podrá denominar como la Caja, por otra parte, la persona o personas que suscriben este contrato, parte a la que en adelante podrá denominar como el socio / cliente, quienes libre y voluntariamente acuerdan celebrar el presente contrato con las siguientes CLAUSULAS:</p>

    <p><strong>CLAUSULA SEGUNDA.</strong> El Socio /Cliente declara que el origen de los fondos entregados a la Caja, son lícitos y consecuentemente no provienen de ninguna actividad de lavado de activos o relacionada con la producción, consumo, comercialización de sustancias de estupefacientes y psicotrópicas o cualquier actividad tipificada en la Ley de Sustancias Estupefacientes y Psicotrópicas, ni de actividades delictivas o ilícitas; tipificadas por el Código Civil o Penal. Por tanto, el Socio /Cliente exime a la Caja de toda responsabilidad inclusive respecto a los terceros, si en este contrato la declaración que hace fuere falsa o errónea, facultada expresamente a la Caja para que en caso de que se inicie en su contra investigaciones relacionadas con las actividades ilícitas antes mencionadas pueda proporcionar a las autoridades competentes las informaciones que estas demande, así como también a cerrar las cuentas que el Socio/Cliente mantenga a favor de la Caja.</p>

    <p><strong>CLAUSULA TERCERA.</strong> El Socio/Cliente declara bajo juramento que los datos consignados en el anverso de este documento son verídicos auténticos y correctos y que en base a esta declaración que la Caja ha aceptado de buena fe, abrir una cuenta de ahorros y mantener una relación con el Socio/Cliente. Por tanto, el Socio/Cliente se somete a las Leyes Penales Y Civiles si esta declaración juramentada resulte falsa.</p>

    <p><strong>CLAUSULA CUARTA.</strong> El Socio/Cliente autoriza a la {{ $company->comercial_name }}, a realizar toda la investigación financiera, incluyendo cualquier operación, ya sea en cuenta de Ahorro, Créditos Y Depósitos a Largo Plazo que mantuviere en cualquier entidad del sistema financiero y que la Caja juzgue necesario consultar a través de Buros de información Crediticia, cuya custodia y administración esta a cargo de la superintendencia de bancos y seguros y/o la superintendencia de Economía Popular y Solidaria e incluso manejo de cuentas por pagar de casas comerciales.</p>

    <p><strong>CLAUSULA QUINTA.</strong> Cualquier remesa de divisas para abonar en la cuenta del Socio/Cliente será revisada previamente para ser aceptada por la Caja.</p>

    <p><strong>CLAUSULA SEXTA.</strong> Los valores por la cobranza extrajudicial para los socios beneficiarios de un crédito serán cobrados según las tablas de costos financieros y operativos legalmente establecidos y aprobados por la {{ $company->comercial_name }} se establece las siguientes formas de notificación:</p>

    <p>*Visita en el domicilio o lugar de trabajo a los deudores.</p>

    <p>* Visita en el domicilio o lugar de trabajo a los garantes.</p>

    <p>*Llamadas telefónicas al socio y garantes.</p>

    <p>*Mensajes de texto y WhatsApp y redes sociales socio y garante.</p>

    <p><strong>CLAUSULA SEPTIMA.</strong> El Socio/Cliente podrá autorizar a terceras personas a firmar sobre su cuenta de ahorro y a ordenar deposito a LARGO PLAZO (Ahorro Programado), los valores que, por concepto del servicio financiero, y otras obligaciones determinadas, directas o indirectas.</p>

    <p><strong>CLAUSULA OCTAVA.</strong> La Caja de Ahorro tramitara la solicitud de apertura requerida con todos los datos generales del Socio/Cliente y referencias, si la Caja la solicitare. La Caja hará efectiva la apertura de la cuenta por medio de una libreta de ahorros, para que en ella Socio/Cliente registre el movimiento de su cuenta.</p>

    <p><strong>CLAUSULA NOVENA.</strong> El plazo del contrato es indefinido quedando, ha facultad de cualesquiera de las partes para darlo por terminado de forma unilateral en cualquier momento.</p>

    <p>La calidad de Socio/Cliente se pierde por:</p>

    <p>*Retiro voluntario expresado en forma escrita y bajo la firma de responsabilidad de Socio/Cliente.</p>

    <p>*Por expulsión.</p>

    <p>*Por muerte.</p>

    <p>De los literales antes indicados para liquidación de la cuenta deben cumplir con el tiempo establecido por la ley teniendo en cuenta que los valores por la apertura no son reembolsables</p>

    <p><strong>CLAUSULA DECIMA.</strong> El Socio/Cliente podrá realizar depósitos en su cuenta de ahorros en efectivo, deposito a largo plazo (AHORRO PROGRAMADO), cheques girados sobre Bancos Nacionales.</p>

    <p><strong>CLAUSULA DECIMA PRIMERA.</strong> El Socio/Cliente podrá realizar en su Cuenta de ahorros: Depósitos Retiros, Transferencias entre cuentas, Débitos y Créditos.</p>

    <p><strong>CLAUSULA DECIMA SEGUNDA.</strong> La Caja podrá rechazar en cualquier momento un depósito hecho por Socio/Cliente o un tercero si incumple la clausula segunda. Todo deposito será hecho en el formato único de papeleta de depósito para cuenta de ahorros que para el efecto dispone la Caja. El Socio /Cliente no podrá hacer retiros sobre el valor de un deposito que no haya sido efectivizado.</p>

    <p><strong>CLAUSULA DECIMA TERCERO.</strong> En el caso de divergencia las partes se someten a la jurisdicción y competencia de los jueces y Tribunales Civiles del domicilio de la oficina de la Caja donde tuviere su cuenta el Socio/Cliente. EL Socio/Cliente renuncia al fuero de su domicilio.</p>

    <p><strong>CLAUSULA DECIMA CUARTA.</strong> Una vez suscrito este contrato por las partes, el Socio/Cliente podrá recibir de los servicios indicados en las clausulas precedentes, siendo necesarios que exista una solicitud previa para cada uno de los servicios. Si el Socio/Cliente hiciere uso de los servicios de la Caja por una sola vez se entenderá que acepta todos los términos y condiciones que la Caja le haya notificado al Socio/Cliente referente a cada servicio y que está de acuerdo con los mismos, obligándose a cumplirlos, respetarlos y acogerse a las sanciones derivadas del incumplimiento, así como a pagar el costo del servicio respectivo mediante el debito a su cuenta en la Caja. En el caso de que el Socio/CLIENTE efectué depósitos, retiros de fondos, créditos, débitos y cualquier otra transacción permitida en la cuenta de ahorros a través de medios electrónicos, asume responsabilidad exclusiva respecto a las transacciones que efectuare a través de estos medios, así como de mantener en secreto la clave o seguridades a el asignadas o adicionales por el solicitadas y otorgadas por la CAJA.</p>

    <p>
        Para constancia de todo lo anteriormente expuesto, las partes se ratifican y suscriben en la ciudad de {{ $ciudad }}, a los {{ $dia }} días del mes de {{ $mes }} del año {{ $anio }}.
    </p>

</body>
<footer>
    <table>
        <tr>
            <td>
                <hr>
                <p><strong>EL SOCIO/CLIENTE</strong></p>
                <p>C.I: {{ $cliente->numero_documento }}</p>
                <p>{{ $cliente->nombres }} {{ $cliente->apellidos }}</p>
            </td>
            <td>
                <hr>
                <p><strong>{{ $company->comercial_name }}</strong></p>
            </td>
        </tr>
    </table>
</footer>


</html>