<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CODEV - Pagaré - {{$data['cabecera']->code}}</title>
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

    <div id="row" style="font-size:12px ;width: 100%;border: 0.5px solid; padding: 10px;border-radius: 14px;">
        <center style="font-size: 18px;"><b>PAGARÉ A LA ORDEN</b></center>
        <br>
        <table style="font-size: 14px;width: 100%">
            <tr>
                <td><b>PAGARÉ N°: {{$data['cabecera']->code}} </b></td>
                <td><b>POR: USD {{$data['cabecera']->valor_solicitado}} </b></td>
                <td><b>SOCIO N°: {{ str_pad($data['customer']->id, 7, '0', STR_PAD_LEFT) }} </b></td>
            </tr>
        </table>
        <br>
        <p style="text-align: justify;">
            Debo (emos) y pagaré (mos) incondicionalmente, a la {{$data['company']->comercial_name}}, en un plazo de <b>**30 días**</b> vista, en la
            ciudad de {{$data['company']->ciudad}}, o en el lugar donde se me (nos) reconvenga, la cantidad de {{$data['cabecera']->valor_solicitado}} DOLARES
            AMERICANOS, en monedas del curso legal; cantidad que he (mos) recibido en la línea de {{(isset($data['prestamo']->name))? $data['prestamo']->name : '________________' }} a mi (nuestra) entera satisfacción, por el presente crédito.
        </p>

        <p style="text-align: justify;">
            {{$data['textoPlazo']}}, mediante {{$data['cabecera']->cuotas_pagar}} cuotas fijas de capital de {{$data['detallePrimera']->valor_cuota}} dólares americanos más los intereses
            respectivos, a una tasa de interés del
            @if($data['company']->comercial_name == "CAJA DE AHORRO Y CREDITO NUEVA ERA")
            25%
            @else
            {{(isset($data['prestamo']->interes))?$data['prestamo']->interes:'_____'}}%
            @endif
            anual sobre saldos vigentes a esta fecha. La tasa de interés podrá ser ajustable
            conforme el interés máximo permisible que se fijase posteriormente por el Banco Central del Ecuador, desde la presente
            fecha.
        </p>
        <p style="text-align: justify;">
            El interés efectivo es del {{(isset($data['prestamo']->interes)) ? $data['prestamo']->interes :'_____'}}%. Para cualquier cálculo se tomará en cuenta el interés nominal del {{(isset($data['prestamo']->interes_anual)) ? $data['prestamo']->interes_anual:'_____'}}% anual sobre saldos.
            En caso de mora reconoceré (mos) el interés del {{$data['company']->porcentaje_mora}} % a la tasa activa hasta la cancelación total del crédito solicitado.
        </p>
        <p style="text-align: justify;">
            ACELERACION DE PAGO: Al incumplimiento en el pago de una o más cuotas, o de haberse comprobado que el destino del dinero no ha sido el que se encuentra estipulado en esté contrato, la {{$data['company']->comercial_name}} podrá declarar vencidos los plazos de ésta obligación, procederé al cobro inmediato de las obligaciones contenidas en esté PAGARÉ A LA ORDEN y demandar la completa he inmediata cancelación de los valores contante en esté título, siendo necesario para este efecto la sola aseveración de acreedor. Autorizo (amos) a la {{$data['company']->comercial_name}} debitar de los certificados de aportación, como de las cuentas de ahorro del deudor y codeudor y garantes los valores correspondientes a las cuotas vencidas de capital e interés, así como gastos judiciales, extrajudiciales que se ocasionen con el fin de recuperar las cuotas vencidas, bastando para determinar esté monto la sola aseveración del acreedor.
        </p>
        <p style="text-align: justify;">
            En caso de cobro vía judicial me (nos) someto (emos) a los jueces del lugar que elija el acreedor, así como al trámite ejecutivo o verbal sumario, para lo cual renuncio (amos) voluntariamente a fuero y domicilio. SIN PROTESTO. Dejo constancia expresa que el plazo de vista corre desde la fecha en que, en señal de conformidad y aceptación suscribo este documento. Exímese la presentación para el pago, así como aviso por falta de este hecho. Sin protesto
        </p>
        <p style="text-align: right;">{{$data['company']->ciudad}}, {{$data['fechaFormateada']}}</p>
        <br>

        <table>
            <tr>
                <td>FIRMA: _____________________</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>FIRMA: _____________________</td>
                @endif
            </tr>
            <tr>
                <td>NOMBRE: {{$data['customer']->nombres .' '.$data['customer']->apellidos }}</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>NOMBRE: {{strtoupper($data['customer']->conyugue_nombre) }}</td>
                @endif
            </tr>
            <tr>
                <td>IDENTIFICACIÓN: {{$data['customer']->numero_documento}}</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>IDENTIFICACIÓN: {{$data['customer']->conyugue_identificacion}}</td>
                @endif
            </tr>
            <tr>
                <td>DIRECCIÓN: {{$data['customer']->direccion}}</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>DIRECCIÓN: {{$data['customer']->direccion}}</td>
                @endif
            </tr>
            <tr>
                <td>CANTÓN:</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>CANTÓN:</td>
                @endif
            </tr>
            <tr>
                <td>TELÉFONO: {{$data['customer']->telefono}}</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>TELÉFONO: {{$data['customer']->conyugue_telefono}}</td>
                @endif
            </tr>
            <tr>
                <td>VISTO BUENO</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>VISTO BUENO</td>
                @endif
            </tr>
            <tr>
                <td>{{$data['company']->ciudad}}, {{$data['fechaFormateada']}}</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>{{$data['company']->ciudad}}, {{$data['fechaFormateada']}}</td>
                @endif
            </tr>
        </table>
        <br>

        <table>
            <tr>
                <td>FIRMA: _____________________</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>FIRMA: _____________________</td>
                @endif
            </tr>
            <tr>
                <td>NOMBRE: {{$data['customer']->nombres .' '.$data['customer']->apellidos }}</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>NOMBRE: {{strtoupper($data['customer']->conyugue_nombre) }}</td>
                @endif
            </tr>
            <tr>
                <td>IDENTIFICACIÓN: {{$data['customer']->numero_documento}}</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>IDENTIFICACIÓN: {{$data['customer']->conyugue_identificacion}}</td>
                @endif
            </tr>
            <tr>
                <td>DIRECCIÓN: {{$data['customer']->direccion}}</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>DIRECCIÓN: {{$data['customer']->direccion}}</td>
                @endif
            </tr>
            <tr>
                <td>CANTÓN:</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>CANTÓN:</td>
                @endif
            </tr>
            <tr>
                <td>TELÉFONO: {{$data['customer']->telefono}}</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>TELÉFONO: {{$data['customer']->conyugue_telefono}}</td>
                @endif
            </tr>
            <tr>
                <td>VISTO BUENO</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>VISTO BUENO</td>
                @endif
            </tr>
            <tr>
                <td>{{$data['company']->ciudad}}, {{$data['fechaFormateada']}}</td>
                @if($data['customer']->conyugue_nombre != null)
                <td>{{$data['company']->ciudad}}, {{$data['fechaFormateada']}}</td>
                @endif
            </tr>
        </table>



        </p>
        <p style="text-align: justify;">
            En esta misma fecha GARANTIZO (AMOS) el cumplimiento de las obligaciones constantes en el PAGARÉ A LA ORDEN, que antecede, en iguales términos y condiciones, constituyéndome (nos) en aval (es) solidario (s) del deudor, haciendo de deuda ajena deuda propia, renunciando las excepciones de orden y excusión de bienes del deudor principal. Exímo (imos) al acreedor de la obligación de formalizar el protesto y estipulo (amos) expresamente que el tenedor no podrá ser obligado a recibir el pago por partes ni aún por mis (nuestros) herederos.
        </p>
        @if(isset($data['garante']))
        @if($data['cabecera']->customer_garante_id != 0)
        <br>
        <br>
        <br>
        <table style="width: 100%;">
            <tbody style="font-size: 12px;">
                <tr>
                    <td><b>FIRMA GARANTE: _______________________________</b></td>
                    @if($data['garante']->conyugue_nombre != "")
                    <td><b> FIRMA CO GARANTE: _______________________________</b></td>
                    @endif
                </tr>
                <tr>
                    <td>NOMBRE: Sr.(a) &nbsp; {{$data['garante']->nombres .' '.$data['garante']->apellidos }} </td>
                    @if($data['garante']->conyugue_nombre != "")
                    <td>NOMBRE: Sr.(a) &nbsp; {{strtoupper($data['garante']->conyugue_nombre)}}</td>
                    @endif
                </tr>
                <tr>
                    <td>IDENTIFICACIÓN: {{$data['garante']->numero_documento}}</td>
                    @if($data['garante']->conyugue_nombre != "")
                    <td>IDENTIFICACIÓN: {{$data['garante']->conyugue_identificacion}}</td>
                    @endif
                </tr>
                <tr>
                    <td>DIRECCIÓN: {{$data['garante']->direccion}}</td>
                    @if($data['garante']->conyugue_nombre != "")
                    <td>DIRECCIÓN: {{$data['garante']->direccion}}</td>
                    @endif
                </tr>
                <tr>
                    <td>TELÉFONO: {{$data['customer']->telefono}}</td>
                    @if($data['garante']->conyugue_nombre != "")
                    <td>DIRECCIÓN: {{$data['garante']->conyugue_telefono}}</td>
                    @endif
                </tr>
            </tbody>
        </table>
        @endif
        @endif

        <table style="width: 100%;">
            <tbody style="font-size: 11px;">
            @foreach($data['garantesTabla'] as $garantes)

                <tr>
                    <td><b>FIRMA GARANTE: _______________________________</b></td>
                    @if($garantes->customer_conyuge_name != "")
                    <td><b> FIRMA CO GARANTE: _______________________________</b></td>
                    @endif
                </tr>
                <tr>
                    <td>NOMBRE: Sr.(a) &nbsp; {{$garantes->customer_name }}  </td>
                    @if($garantes->customer_conyuge_name != "")
                    <td>NOMBRE: Sr.(a) &nbsp; {{$garantes->customer_conyuge_name }}</td>
                    @endif
                </tr>
                <tr>
                    <td>IDENTIFICACIÓN: {{$garantes->customer_identificacion }}</td>
                    @if($garantes->customer_conyuge_name != "")
                    <td>IDENTIFICACIÓN: {{$garantes->customer_conyuge_identificacion }}</td>
                    @endif
                </tr>
                <tr>
                    <td>DIRECCIÓN: {{$garantes->direccionCliente }}</td>
                    @if($garantes->customer_conyuge_name != "")
                    <td>DIRECCIÓN:{{$garantes->direccionConyuge }}</td>
                    @endif
                </tr>
                <tr>
                    <td>TELÉFONO: {{$garantes->telefonoCliente }}</td>
                    @if($garantes->customer_conyuge_name != "")
                    <td>DIRECCIÓN: {{$garantes->telefonoConyuge }}</td>
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


    </div>

</body>

</html>