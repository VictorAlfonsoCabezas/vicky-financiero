<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>CODEV - Letra de cambio - {{$data['cabecera']->code}}</title>
        <style>
            .table thead tr th{
                border-bottom: 1px dotted #000;
                background: #CCC
            }
            .table tbody tr td{
                border-bottom: 1px dotted #000;
                border-left: 1px dotted #000;
                border-right: 1px dotted #000;
            }
        </style>
    </head>
    <body>
        <div class="text-align-left" style="position: absolute;z-index: 0;top: 0;left: 0;width: 100%; opacity: 0.07;">
            <img id="bcTarget" class="img_bar_code" src="data:image/png;base64,{{$data['imagen']}}" alt="" width="100%"/>
        </div>
        <div style="text-align: left;">
            <img src="data:image/png;base64,{{$data['imagen']}}" alt="" style="width: 75px;"/>
        </div>
        <div id="row" style="width: 100%;border: 0.5px solid; padding: 10px;border-radius: 15px;">
            <table style="font-size: 16px;width: 100%">
                <tr>
                    <td><b>N°: </b> <b style="color: red">{{$data['cabecera']->code}}</b></td>
                    <td><b>VENCE: </b> <b>{{$data['ultimo']->date_vencimiento}}</b></td>
                    <td><b>POR:</b><label style="width: 200px;">$ <b>{{$data['cabecera']->valor_solicitado}}</b> </label></td>                    
                </tr>
                <tr>
                    <td colspan="4"><div id="txtDireccion"><b> LUGAR Y FECHA:</b> {{ucfirst ($data['company']->ciudad)}} {{ucfirst ($data['company']->pais)}}  <b>{{$data['cabecera']->date_created}}</b></div></td>
                </tr>
                <tr>
                    <td colspan="4"><div id="txtDireccion"><b>A</b> <b>{{$data['dias']}}</b> días se servirá(n) Ud(s), pagar por esta LETRA DE CAMBIO  </div></td>
                </tr>
                <tr>
                    <td colspan="4"><div id="txtDireccion">A la orden de <b>{{$data['cabecera']->customer_name}}</b> </div></td>
                </tr>
                <tr>
                    <td colspan="4"><div id="txtDireccion">La cantidad de  <b> {{$data['cabecera']->valor_solicitado}}Dólares de los estados Unido de América</b> </div></td>
                </tr>
                <tr>
                    <td colspan="4"><div id="txtDireccion">Con interés del <b> {{  ($data['interesCredito'] != '') ? $data['interesCredito']   : '________' }} % </b> por ciento anual, desde {{$data['primero']->date_vencimiento}} </div></td>
                </tr>
                <tr>
                    <td colspan="4"><div id="txtDireccion">Sin protesto. Exímese de presentación para aceptación y pago; así como de avisos por falta de estos hechos. </div></td>
                </tr>
                <tr>
                    <td style="width: 15%;"><div id="txtDireccion"><b>A: </b> </div></td>
                    <td colspan="2"><div id="txtDireccion">{{$data['cabecera']->customer_name}}</div></td>
                    <td ><div id="txtDireccion">Atentamente</div></td>
                </tr>
                <tr>
                    <td style="width: 15%;"><div id="txtDireccion"><b>Dirección: </b></div></td>
                    <td colspan="2"><div id="txtDireccion">{{$data['cabecera']->customer_address}}</div></td>
                    <td ><div id="txtDireccion"></div></td>
                </tr>
                <tr>
                    <td style="width: 15%;"><div id="txtDireccion"><b>Ciudad: </b></div></td>
                    <td colspan="2"><div id="txtDireccion">{{strtoupper ($data['company']->ciudad)}}</div></td>
                    <td ><div id="txtDireccion">_________________</div></td>
                </tr>               
            </table>
        </div>
        <br>
        <div id="row" style="width: 100%;border: 0.5px solid; padding: 10px;border-radius: 15px;">
            <table style="font-size: 16px;width: 100%">
                <tr>
                    <td colspan="4"><div id="txtDireccion"><b>ACEPTADA.-</b>Valor recibido. El pago no podrá hacerse por partes ni aún por mi __________________ __________________ (nuestros) herederos. Me(nos) sujet(amos) a los jueces de esta ciudad y al trámite judicial que corresponda de aceurdo a la Ley, a eleccion del demandante. </div></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center"><div id="txtDireccion">Lugar y fecha: <b>{{ucfirst ($data['company']->ciudad)}} {{ucfirst ($data['company']->pais)}} {{$data['fechaFormateadaFinal']}}</b></div></td>
                </tr>
                <tr>
                    <td>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center"><div id="txtDireccion">____________________ </div></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center"><div id="txtDireccion">Firma</div></td>
                </tr>
                <tr>
                    <td colspan="4"><div id="txtDireccion"><b>POR AVAL</b> Me(nos) constituyo(imos) solidariamente responsable(s) con _____________________. Sin protesto; el pago no podrá hacerse por partes ni aún por ________ herederos. Estipulo(amos) las demás condiciones constantes de la letra y de aceptación.</div></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center"><div id="txtDireccion">Lugar y fecha: <b>{{ucfirst ($data['company']->ciudad)}} {{ucfirst ($data['company']->pais)}} {{$data['fechaFormateadaFinal']}}</b></div></td>
                </tr>
                <tr>
                    <td>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center"><div id="txtDireccion"><b>F.</b>_________________</div></td>
                </tr>
                <tr>
                    <td>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center"><div id="txtDireccion">_________________</div></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center"><div id="txtDireccion">{{$data['company']->comercial_name}}</div></td>
                </tr>
                <tr>
                    <td colspan="4"><div id="txtDireccion">PÁGUESE a la orden de  {{$data['cabecera']->customer_name}} el valor de  <b>$ {{$data['cabecera']->valor_solicitado}}</b>. Sin Protesto.</div></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center"><div id="txtDireccion">Lugar y fecha: <b>{{ucfirst ($data['company']->ciudad)}} {{ucfirst ($data['company']->pais)}} {{$data['fechaFormateadaFinal']}}</b></div></td>
                </tr>
                <tr>
                    <td>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center"><div id="txtDireccion"> _________________</div></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center"><div id="txtDireccion"> Firma</div></td>
                </tr>
            </table>
        </div>
    </body>
</html>