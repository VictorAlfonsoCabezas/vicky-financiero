<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $nombreDocumento }}</title>
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
        <img id="bcTarget" class="img_bar_code" src="data:image/png;base64,{{$imagen}}" alt="" width="100%" />
    </div>
    <div id="row" style="width: 100%;border: 0 px solid; padding: 10px;border-radius: 15px;">
        <center>

            <h4>CAJA DE AHORRO "{!! html_entity_decode($company->company_name) !!}" <br><br>
                FORMATO OFICIAL DEL BALANCE GENERAL </h4>
        </center>
        <p>
            <b>Objetivo:</b> Presentar un formato estandarizado para la implementación del <b>Balance General</b> dentro del
            sistema financiero de la Caja de Ahorro "{!! html_entity_decode($company->company_name) !!}". El diseño contempla la estructura de ingresos, gastos y
            resultado neto del período, manteniendo compatibilidad con catálogos contables y generación automática de
            reportes.
        </p>
    </div>
    <br>

    <div style="text-align: center;bottom: 10px;">


        <table style="width: 100%;font-size: 16px;" class="table">
            <thead>
                <tr>
                    <th style="width: 20%">Entidad Financiera</th>
                    <th style="width: 80%">CAJA DE AHORRO "{!! html_entity_decode($company->company_name) !!}"</th>

                </tr>
            </thead>
            <tbody style="font-size: 15px;">
                <tr>
                    <td style="text-align: left;">RUC</td>
                    <td style="text-align: left;"> {{ $company->ruc }}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Ciudad </td>
                    <td style="text-align: left;">{{ $company->ciudad }} - {{ $company->pais }}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Período </td>
                    <td style="text-align: left;">Desde {{ $periodoDesde }} hasta {{ $periodoHasta }}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Moneda </td>
                    <td style="text-align: left;">Dólares de los Estados Unidos de América (USD)</td>
                </tr>

            </tbody>

        </table>

        <center>

            <h4>ESTRUCTURA DEL ESTADO DE RESULTADOS</h4>
        </center>




        <table style="width: 100%;font-size: 10px;" class="table">
            <thead>
                <tr>
                    <th style="width: 20%">Código</th>
                    <th style="width: 60%">Cuenta / Detalle</th>
                    <th style="width: 20%">Valor ($)</th>
                </tr>
            </thead>
            <tbody style="font-size: 10px;">
                @forelse ($planCuentas as $plan)
                <tr
                    @if ($plan->nivel == 1) style="background-color: #007bff!important;"
                    @elseif ($plan->nivel == 2)
                    style="background-color: #CCC;"
                    @elseif ($plan->nivel == 3)
                    style="background-color: rgb(255, 193, 7);" @endif>
                    <td><b>{{ $plan->codigo }}</b></td>
                    <td>
                        @if ($plan->nivel > 3)
                        <b>{{ $plan->nombre }}</b>
                        @else
                        {{ $plan->nombre }}
                        @endif
                    </td>
                    <td>${{ $plan->saldo }}</td>
                </tr>
                @empty
                <tr>
                    <td class="text-center" colspan="6">No hay</td>
                </tr>
                @endforelse
            </tbody>

        </table>

        <h4>FIRMAS DE RESPONSABILIDAD
        </h4>
        <br>
        <br>
        <table style="width: 100%;">
            <tr>
                <td>_____________________________</td>
                <td></td>
                <td>_____________________________</td>
            </tr>
            <tr>
                <td>Representante Legal </td>
                <td></td>
                <td>Contador</td>
            </tr>
            <tr>
                <td>C.I.: __________________</td>
                <td></td>
                <td>C.I.: __________________</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>Reg. CPA No.: ____________</td>
            </tr>
        </table>





    </div>
</body>

</html>