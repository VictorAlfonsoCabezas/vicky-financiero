<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CODEV - CONTRATO DE PRESTAMO - {{ $cabecera->code }}</title>
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
        <img id="bcTarget" class="img_bar_code" src="data:image/png;base64,{{ $imagen }}" alt="" width="100%" />

    </div>

    <div id="row" style="font-size:12px ;width: 100%;border: 0.5px solid; padding: 10px;border-radius: 14px;">
        <center style="font-size: 18px;"><b>CONTRATO DE PRESTAMO </b><br><b>N.-: {{ $cabecera->code }} </b></center>
        <br>

        <br>
        <p style="text-align: justify;">
            Conste por el presente documento las siguientes estipulaciones convenidas
            entre {{$company->comercial_name}}, en adelante simplemente “CAJA DE AHORRO”,
            legalmente representado por el señor {{$company->legal_representative}},
            de nacionalidad Ecuatoriana, en su calidad de
            Representante Legal, en la ciudad de Quito; y, por otra, el (los) señor (a) (es)
            {{ $cabecera->customer_name }} , de nacionalidad Ecuatoriana, en
            adelante simplemente el (los) deudor (s) comparecen también el (los) señor (es)
            (GARANTES SUSCRIPTORES) por sus propios derechos, a quienes en adelante se los podrá designar como el (los) garante (s) personal (es) y
            solidario (s).
        </p>

        
        <p style="text-align: justify;">
            <b>PRIMERA: ANTECEDENTES, PLAZO, INTERES Y FORMA DE PAGO.-</b> <br>
            <b>La Caja de ahorro </b>da en préstamo a
            favor de (los) deudor (es) la cantidad de {{$cabecera->valor_solicitado}} {{$cantidadLetras}}
            dólares de los Estados Unidos de América, cantidad
            que el (los) deudor (es) declara (n) recibirla a su entera satisfacción y se obliga
            (n) solidariamente a pagarla con los respectivos intereses y los costos varios
            a incurrirse, los cuales son aceptados desde ya por el (los) deudor (es) y
            constan detallados en la tabla de amortización adjunta, dentro del plazo de (x)
            día (s) contados desde la presente fecha.
        </p>
        <p style="text-align: justify;">
            La tasa de interés inicial es del ( {{$interesNominal}}%) anual, la misma que será
            reajustada de acuerdo a lo dispuesto más adelante en esta misma cláusula.

        </p>
        <p style="text-align: justify;">
            El capital y los intereses así como los costos a incurrirse que este declara
            conocer y aceptar a entera satisfacción, se pagarán mediante dividendos
            periódicos, de acuerdo a la tabla de amortización que se acompaña y
            forma parte integrante de este contrato.
        </p>
        <p style="text-align: justify;">
            El valor de los dividendos puede incrementarse o reducirse en forma
            sustancial por cada período en que se reajuste los intereses.
        </p>
        <p style="text-align: justify;">
            El (los) deudor (es) y el (los) garante (s) que suscribe (n) autoriza (n)
            expresamente a la CAJA DE AHORRO 
            para que por sí solo modifique el cálculo de intereses, tomando en cuenta el
            reajuste a la norma pactada y para que elabore las nuevas tablas y las
            adjunte al presente contrato sin más requisito, ni autorización,
            obligándose a pagar el valor constante en la liquidación que le presente LA
            CAJA DE AHORRO 
        </p>
        <p style="text-align: justify;">
            Si el (los) deudor (es) incurriere (n) en mora en el pago de un dividendo o
            fracción de éste, LA CAJA DE AHORRO cobrará dicho
            dividendo y además sobre el saldo principal del dividendo vencido
            cobrará la máxima tasa de mora que permitan las leyes y regulaciones
            pertinentes vigentes a la fecha del vencimiento y durante la mora.
        </p>
        <p style="text-align: justify;">
            <b>SEGUNDA: UTILIZACION DEL PRESTAMO.-</b> El deudor se obliga a
            emplear la cantidad que recibió en préstamo en ACTIVIDADES LICITAS,
            PERSONALES Y/O COMERCIALES siempre que se trate de un crédito
            original.
        </p>
        <p style="text-align: justify;">
            Se excluye de esta obligación cuando se trate de un contrato originado por
            una renovación o reestructura.
        </p>
        <p style="text-align: justify;">
            <b>TERCERA: PAGOS ANTICIPADOS.-</b> El (los) deudor (es) podrá (n) pagar
            total o parcialmente el préstamo, antes de su vencimiento, en ese caso el
            valor pagado se imputará en el siguiente orden: costos del crédito,
            intereses y el remanente se imputará a las cuotas de capital pendientes,
            sin que esto genere recargo o penalidad.
        </p>
        <p style="text-align: justify;">
            <b>CUARTA: VIGENCIA DE LAS GARANTIAS.-</b> Las garantías ofrecidas
            para el préstamo subsistirán hasta tanto no se extingan todas y cada una
            de las obligaciones que el deudor mantenga a favor o a la orden de LA
            CAJA DE AHORRO, incluyendo dentro de ellas el pago
            de intereses, gastos judiciales y honorarios profesionales. LA CAJA DE
            AHORRO podrá cuantas veces crea conveniente
            realizar inspecciones a los bienes gravados, así como también podrá
            exigir se mejoren o aumenten las garantías.
        </p>
        <p style="text-align: justify;">
            <b>QUINTA: CASOS DE VENCIMIENTO DEL PLAZO.-</b> LA CAJA DE
            AHORRO podrá declarar anticipadamente de plazo
            vencido la totalidad de la obligación y exigir su pago inmediato, debiendo
            re liquidarse los intereses a la tasa efectiva máxima por segmento desde
            la fecha de contabilización del crédito, por las siguientes causas: en caso
            de incumplimiento del prestamo dentro del plazo fijado por LA CAJA DE
            AHORRO, de producirse el desvío total o parcial del destino del
            préstamo; de impedirse a LA CAJA DE AHORRO
            la inspección o verificación de la adecuada inversión del
            crédito; de comprobarse que el deudor ha falseado en las declaraciones
            sobre estados financieros y demás información proporcionada para obtener
            el crédito; si se dispusiere los bienes constituidos en prenda; si se
            embargaren, transfirieren o limitare el dominio del lugar de la inversión; si se
            extinguieren las garantías o disminuyeren considerablemente su valor, o, por
            falta de pago de uno o más dividendos.
        </p>
        <p style="text-align: justify;">
            <b>SEXTA: JURISDICCION.-</b> Las partes para todos los efectos previstos en el
            presente contrato, fijan como su domicilio la ciudad de Quito y se someten
            a los jueces competentes en esta ciudad y a la jurisdicción coactiva prevista
            en la ley.
        </p>
        <p style="text-align: justify;">
            Para constancia y ratificación de lo convenido, las partes suscriben el
            presente convenio en tres ejemplares del mismo tenor y efecto, en la ciudad
            de Quito a los {{ $cabecera->date_created }}.
        </p>
        <p style="text-align: justify;">
            <b>OCTAVA: SEGURO DE DESGRAVAMEN.-</b> La parte deudora se obliga a
            PAGAR LOS VALORES QUE CORRESPONDAN PARA CUBRIR LAS
            PRIMAS QUE SERÁN CANCELADAS A LA COMPAÑÍA ASEGURADORA
            CONTRATADA POR LA CAJA DE AHORRO, para un
            Seguro de Desgravamen con el amparo de muerte por cualquier causa e
            incapacidad total y permanente, para el pago del saldo insoluto de la deuda a
            LA CAJA DE AHORRO al momento de su fallecimiento.
        </p>
        <p style="text-align: justify;">
            La póliza de seguro de desgravamen deberá estar vigente durante todo el
            plazo del presente contrato. Para este efecto, LA CAJA DE AHORRO, 
            podrá contratar, a su total discreción, la compañía
            aseguradora que considere conveniente en los términos y condiciones
            de la respectiva póliza, así como todos los endosos o cesiones que
            designen como beneficiario de dicha póliza a LA CAJA DE AHORRO;
             y, en general suscribirá cualquier tipo de documento
            necesario para el perfeccionamiento y contratación de la anotada póliza de
            seguros.
        </p>
        <p style="text-align: justify;">
            La parte deudora, autoriza expresamente a LA CAJA DE AHORRO,
             para que debite de sus cuentas corrientes o de
            ahorros o de cualquier otro crédito a su favor, el valor o valores necesarios
            para cancelar a la aseguradora las anualidades de dicha póliza. Si las
            cuentas corrientes o de ahorros no tuvieren los fondos disponibles
            necesarios para cubrir dichos valores, los mismos serán cobrados
            conjuntamente con los próximos doce dividendos, hasta un máximo
            de doce por año, de tal manera que, como ya quedó establecido, la Parte
            Deudora se mantenga permanentemente asegurada durante toda la vigencia
            del presente contrato con las coberturas indicadas.
        </p>
        <p style="text-align: justify;">
            La Parte Deudora, declara (n) conocer y aceptar que el valor del seguro de
            desgravamen, será calculado sobre el valor de capital más el interés generado
            por la deuda adquirida.
        </p>
        <p style="text-align: justify;">
            La Parte Deudora deja expresa constancia que se obliga (n) a cancelar a LA
            CAJA DE AHORRO, todos los valores correspondientes de la póliza de seguro de desgravamen por todo el
            tiempo que esté vigente el presente contrato, los mismos que están
            debidamente detallados en la Tabla de Amortización, que firmada por las
            partes forma parte del mismo.

        </p>
        <p style="text-align: justify;">
            LA CAJA DE AHORRO
        </p>
        <br>
        <br>
        <p style="text-align: justify;">
            __________________________ <br>
            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AUTORIZADO POR:
        </p>
        <p>
        <table style="width: 100%;text-align: center;">
            <tr>
                <td style="width: 50%;">Deudores</td>
                <td style="width: 50%;">Garantes</td>
            </tr>
            <tr>
                <td>
                    <br><br><br><br>
                    _____________________________________
                    <br>
                    {{ $cabecera->customer_name }}
                    <br>
                    Cédula/Ruc: {{ $cabecera->customer_ruc }}
                </td>
                <td>
                    @if(isset($garante))
                        @if($cabecera->customer_garante_id != 0)
                            <br><br><br><br>
                            _____________________________________
                            <br>
                            {{$garante->nombres .' '.$garante->apellidos }}
                            <br>
                            Cédula/Ruc: {{$garante->numero_documento}}
                        @endif
                    @else
                        <br><br><br><br>
                        _____________________________________
                        <br>
                        Nombre:
                        <br>
                        Cédula/Ruc:
                    @endif

                </td>
            </tr>
        </table>
        </p>





    </div>

</body>

</html>