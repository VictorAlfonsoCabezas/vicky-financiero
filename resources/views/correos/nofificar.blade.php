<div class="">
    <div class="aHl">

    </div>
    <div id=":qu" tabindex="-1">

    </div>
    <div class="text-align-left" style="position: absolute;z-index: 0;top: 0;left: 0;width: 100%; opacity: 0.07;">
        <img id="bcTarget" class="img_bar_code" src="data:image/png;base64,{{$imagen}}" alt="" width="150px" />
    </div>
    <div bgcolor="#ffffff" marginwidth="0" marginheight="0">
        <table id=""  cellspacing="0" cellpadding="0" width="850" border="0">
            <tbody>
                <tr>
                    <td>
                        <table cellspacing="40" width="100%">
                            <tbody>
                                <tr>
                                    <td><span class="im">
                                            <p>Estimado/a</p>
                                            <p>{{$name}}</p>
                                        </span>
                                        <p>Fecha y Hora: {{$date}}</p>
                                        <span class="im">
                                            <p>Transacción: <strong>{{$transaccion}}</strong></p>
                                            <p>Canal: {{$medio}}</p>
                                            <p>
                                            {{$mensaje}}
                                            </p>
                                            <p>
                                            {!! html_entity_decode($contenido) !!}
                                            </p>
                                            <p>Atentamente {{$nombreCaja}}</p>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p>&nbsp;</p>
                    </td>
                </tr>

            </tbody>
        </table>
        <div class="adL">
            <div class="h5">
                <table width="720" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
                    <tbody>
                        <tr>
                            <td><br></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                                    <tbody>
                                        <tr>
                                            <td colspan="2">
                                                <div align="justify">Si tienes alguna consulta con respecto a esta información no dudes en comunicarte con nosotros, caso contrario no es necesario responder a este correo electrónico.</div>
                                                <div align="justify">
                                                    <div align="justify">
                                                        <div align="justify">La información y adjuntos contenidos en este mensaje son confidenciales y reservados; por tanto no pueden ser usados, reproducidos o divulgados por otras personas distintas a su(s) destinatario(s). Si no eres el destinatario de este email, te solicitamos comedidamente eliminarlo. Cualquier opinión expresada en este mensaje, corresponde a su autor y no necesariamente a la entidad financiera.</div>
                                                        <div align="justify">Recuerda que {{$nombreCaja}} nunca te requerirá por ningún medio, tu usuario o clave de acceso a sus sitios web o aplicaciones móviles.</div>
                                                        <div align="justify">Te recomendamos no imprimir este correo electrónico a menos que sea estrictamente necesario.</div>
                                                        <div><br></div>
                                                    </div>
                                                    <div><br></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="hi"></div>
    <div class="WhmR8e" data-hash="0"></div>
</div>

