<!DOCTYPE html>
<html lang="en">

<head>
</head>

<body>
    <div class="text-align-left" style="position: absolute;z-index: 0;top: 100;left: 0;width: 100%; opacity: 0.07;">
        <img id="bcTarget" class="img_bar_code" src="data:image/png;base64,{{ $data['imagen'] }}" alt="" width="100%" />
    </div>



    <table style="font-size: 11px;width: 100%">
        <tr>
            <td style="text-align: center;width: 130px;">
                <div id="txtDireccion"><img src="data:image/png;base64,{{ $data['imagen'] }}" alt="" style="width: 60px;" /></div>
            </td>
            <td colspan="4" style="text-align: center;font-size: 9px;">
                <div id="txtDireccion">
                    {!! html_entity_decode($data['company']->company_name) !!}

                    <b>TIPO</b><br>
                    <b style="color: red">COMPROBANTE {{$data['tipoconcepto']->nombre}}</b><br>
                    <b style="color: red">2023-10-11 18:56:23</b><br>
                    <b># 00358218/ ID {{$data['header']->id}}</b><br>
                </div>
            </td>
        </tr>
    </table>
    <hr>


    <table style="width: 100%; border-collapse: collapse; margin-bottom: 60px; font-family: Arial, sans-serif;">
        <tbody style="font-size: 11px;">
            <tr>
                <td style="text-align: left; padding: 8px;" colspan="2"><b>Quito, {{$data['fechaActual']}}</b></td>
            </tr>
            <tr>
                <td style="text-align: left; padding: 8px;"><b>CONCEPTO:</b></td>
                <td style="text-align: left; padding: 8px;">{{$data['concepto']->nombre}}</td>
            </tr>
            <tr>
                <td style="text-align: left; padding: 8px;"><b>OBSERVACIONES:</b></td>
                <td style="text-align: left; padding: 8px;">{{$data['header']->descripcion}}</td>
            </tr>
        </tbody>
    </table>


    <table style="width: 100%; border-collapse: collapse; margin-bottom: 60px; font-family: Arial, sans-serif;">
        <thead style="font-size: 11px;">
            <tr>
                <th style="color:white; background-color: blue; width: 50px; text-align: center; border: 1px solid #ddd; padding: 8px;">
                    CODIGO </th>
                <th style="color:white; background-color: blue; width: 80px; text-align: center; border: 1px solid #ddd; padding: 8px;">
                    CUENTA</th>
                <th style="color:white; background-color: blue; width: 100px; text-align: center; border: 1px solid #ddd; padding: 8px;">
                    CENTRO COSTOS</th>
                <th style="color:white; background-color: blue; width: 10px; text-align: center; border: 1px solid #ddd; padding: 8px;">
                    DEBE</th>
                <th style="color:white; background-color: blue; width: 10px; text-align: center; border: 1px solid #ddd; padding: 8px;">
                    HABER</th>
            </tr>
        </thead>
        <tbody style="font-size: 11px;">
            @foreach ($data['detalle'] as $det)
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;"><b>{{$det->codigo}}</b></td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{$det->nombre}}</td>
                <td style="border: 1px solid #ddd; padding: 8px;"><b>{{$det->sedes_name}}</b> - {{$det->centro_costo}}</td>
                @if($det->debe_haber)
                <td style="text-align: center; border: 1px solid #ddd; padding: 8px;">{{$det->valor}}</td>
                <td style="text-align: center; border: 1px solid #ddd; padding: 8px;">0.00</td>
                @else
                <td style="text-align: center; border: 1px solid #ddd; padding: 8px;">0.00</td>
                <td style="text-align: center; border: 1px solid #ddd; padding: 8px;">{{$det->valor}}</td>
                @endif
            </tr>
            @endforeach

            <tr>
                <td style="text-align: left; border: 1px solid #ddd; padding: 8px;" colspan="3"><b>SUMAN: </b>{{$data['cantidad_texto']}}</td>
                <td style="text-align: center; border: 1px solid #ddd; padding: 8px;"><b>{{$data['debe']}}</b></td>
                <td style="text-align: center; border: 1px solid #ddd; padding: 8px;"><b>{{$data['haber']}}</b></td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-family: Arial, sans-serif;">
        <tbody style="font-size: 11px;">
            <tr>
                <td style="text-align: center; padding: 8px;">
                    ELABORADO POR:
                    <br>
                    {{$data['elaborado_por']}}
                    <br>
                    {{$data['elaborado_ruc']}}
                </td>
                <td style="text-align: center; padding: 8px;">
                    REVISADO POR:
                    <br>
                    {{$data['recibido_por']}}
                    <br>
                    {{$data['recibido_ruc']}}
                </td>
                <td style="text-align: center; padding: 8px;">RECIBI CONFORME</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align: center; padding: 8px;">____________________________</td>
                <td style="text-align: center; padding: 8px;">____________________________</td>
                <td style="text-align: center; padding: 8px;">____________________________</td>
            </tr>
        </tbody>
    </table>
</body>

</html>