<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Certificado</title>
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
    <div class="text-align-left" style="position: absolute;z-index: 0;top: 60;left: 0;width: 100%; opacity: 0.09;">
        <img id="bcTarget" class="img_bar_code" src="data:image/png;base64,{{$data['imagen']}}" alt="" width="100%" />
    </div>
    <div id="row" style="font-size:20px ;width: 100%;border: 0.5px solid; padding: 10px;border-radius: 14px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 30%;"><img id="bcTarget" class="img_bar_code" src="data:image/png;base64,{{$data['imagen']}}" alt="" width="100px" /></td>
                <td style="width: 40%;">
                    <center>

                        <p class="whitespace-nowrap font-bold text-main text-start">
                            <b>
                                {!! html_entity_decode($data['company']->company_name) !!}
                            </b>
                        </p>
                        <br> {{$data['company']->ciudad}} – Ecuador
                    </center>
                </td>
                <td style="width: 30%;"></td>
            </tr>
        </table>


    </div>
    <center>
        <b>
            CERTIFICADO DE APORTACION <br>
            SOCIOS ADHERENTES
        </b>
    </center>
    <p style="font-size:20px ;">
        El presente certificado acredita a: {{$data['customer']->nombres .' '.$data['customer']->apellidos}} con CI No. {{$data['customer']->numero_documento}} como titular
        de una aportación totalmente pagada con un valor nominal de $ {{$data['total'] }}, cada una que conforma el
        capital de la {{$data['company']->comercial_name}}, según artículo No 18 del Reglamento de la institución. Entidad que
        consta en los catastros de la Superintendencia de Economía Popular y Solidaria del Ecuador.
    </p>
    <p style="font-size:20px ;">
        Este certificado es válido a partir de la fecha de emisión y acredita la cantidad y la naturaleza de la aportación
        realizada por el socio. Tenga en cuenta que este certificado no es transferible y debe ser presentado en caso de
        retirar fondos en función a lo que establece el Reglamento de la Institución
    </p>
    <p style="font-size:20px ;">
        {{$data['company']->ciudad}}, {{$data['detalle']->created_at}}

    </p>

    <table style="width: 100%;font-size:20px ;"">
        <tr>
            <td>__________________________________</td>
            <td></td>
            <td>__________________________________</td>
        </tr>
        <tr>
            <td>Sr/a. {{$data['company']->legal_representative}}</td>
            <td></td>
            <td>Lcda. Verónica Yolanda De La Torre Cabascango</td>
        </tr>
        <tr>
            <td><b>PRESIDENTE</b></td>
            <td></td>
            <td><b>TESORERA</b></td>
        </tr>
        <tr>
            <td><b>{{$data['company']->comercial_name}}</b></td>
            <td></td>
            <td><b>{{$data['company']->comercial_name}}</b></td>
        </tr>
    </table>
</body>

</html>