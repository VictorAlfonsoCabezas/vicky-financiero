<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Resumen de calificación crediticia</title>
    <style>
        @page { margin: 30pt 34pt 38pt; }
        body { margin: 0; color: #233247; font: 9pt DejaVu Sans, sans-serif; }
        h1, h2, p { margin: 0; }
        h1 { color: #fff; font-size: 20pt; line-height: 1.2; }
        h2 { margin: 14pt 0 6pt; font-size: 12pt; color: #143457; page-break-after: avoid; }
        table { width: 100%; border-collapse: collapse; }
        th, td { vertical-align: top; }
        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
        .cabecera { background: #142f49; border-bottom: 4pt solid #0e8b84; color: #dce9f2; }
        .cabecera td { padding: 13pt 15pt; vertical-align: middle; }
        .fecha { width: 99pt; text-align: right; }
        .nombre { color: #143457; font-size: 13pt; font-weight: bold; }
        .ficha { background: #f1f6fa; margin-top: 10pt; }
        .ficha td { padding: 8pt 12pt; }
        .etiqueta { font-size: 8pt; }
        .tenue { color: #526479; }
        .resumen { margin-top: 10pt; border: 1pt solid #d7e2ed; }
        .resumen td { width: 50%; padding: 9pt 12pt; vertical-align: middle; }
        .resumen td + td { border-left: 1pt solid #d7e2ed; }
        .puntaje { color: #086f68; font-size: 26pt; font-weight: bold; line-height: 1.15; }
        .monto { color: #143457; font-size: 15pt; font-weight: bold; }
        .nota { margin-top: 6pt; color: #526479; font-size: 8pt; }
        .nota-alerta { border-left: 2pt solid #d29a3b; background: #fff8e9; padding: 5pt 8pt; color: #5d440f; }
        .datos th { padding: 5pt 6pt; background: #e8eff6; color: #143457; text-align: left; font-size: 8pt; }
        .datos td { padding: 5pt 6pt; border-bottom: .6pt solid #dde6ef; }
        .datos tbody tr:nth-child(even) { background: #f7f9fc; }
        .datos .numero { text-align: right; white-space: nowrap; }
        .datos .fecha-corta { white-space: nowrap; }
        .cuotas { font-size: 8pt; }
        .atraso { color: #a33a22; }
        .correcto { color: #086f68; }
        footer { position: fixed; bottom: -22pt; left: 0; right: 0; border-top: .6pt solid #ccd9e5; padding-top: 4pt; color: #526479; font-size: 8pt; }
    </style>
</head>
<body>
    <footer>Calificación de socios · Uso interno · Corte {{ $resultado['corte'] }}</footer>

    <table class="cabecera">
        <tr>
            <td>
                @if ($empresa)<div class="etiqueta">{{ $empresa }}</div>@endif
                <h1>Resumen de calificación crediticia</h1>
            </td>
            <td class="fecha"><div class="etiqueta">Fecha de corte</div><strong>{{ $resultado['corte'] }}</strong></td>
        </tr>
    </table>

    <table class="ficha">
        <tr>
            <td><div class="etiqueta tenue">Socio</div><div class="nombre">{{ $socio->nombres }} {{ $socio->apellidos }}</div></td>
            <td><div class="etiqueta tenue">Documento</div><strong>{{ $socio->numero_documento ?: '—' }}</strong></td>
            <td><div class="etiqueta tenue">Código</div><strong>{{ $socio->code ?: $socio->id }}</strong></td>
        </tr>
    </table>

    <table class="resumen">
        <tr>
            <td>
                <div class="etiqueta tenue">Score interno</div>
                @if ($resultado['score'] === null)
                    <strong class="nombre">Sin historial suficiente</strong>
                @else
                    <span class="puntaje">{{ number_format($resultado['score'], 0, ',', '.') }}</span> <span class="tenue">/ 1.000</span>
                    @if ($resultado['provisional'])<div><strong>Calificación provisional</strong></div>@endif
                @endif
            </td>
            <td>
                <div class="etiqueta tenue">Saldo vencido de créditos vigentes</div>
                <div class="monto">$ {{ number_format($resultado['saldo_vencido'] / 100, 2, '.', ',') }}</div>
                <div>{{ $resultado['atrasadas'] }} cuotas con saldo vencido</div>
            </td>
        </tr>
    </table>

    @if ($resultado['score'] !== null && $resultado['peso_disponible'] < 1000)
        <p class="nota">La calificación usa {{ $resultado['peso_disponible'] }} de los 1.000 puntos de ponderación. Los factores con evidencia se ajustan a la escala completa.</p>
    @endif
    @if ($resultado['datos_incompletos'])
        <p class="nota nota-alerta">{{ $resultado['datos_incompletos'] }} cuotas tienen datos incompletos para evaluar alguno de los factores.</p>
    @endif

    <h2>Desglose del puntaje</h2>
    <table class="datos">
        <thead><tr><th scope="col">Factor</th><th scope="col">Evidencia evaluada</th><th scope="col" class="numero">Puntaje</th></tr></thead>
        <tbody>
            @foreach ([
                'puntualidad' => ['Puntualidad histórica', $resultado['puntuales'] . ' de ' . $resultado['historial'] . ' cuotas completadas a tiempo.'],
                'liquidacion' => ['Créditos pagados por completo', 'Liquidaciones verificadas frente a cierres y reestructuraciones.'],
                'vigentes' => ['Cumplimiento de créditos vigentes', 'Importe cubierto y antigüedad del saldo de cuotas vencidas.'],
            ] as $clave => [$titulo, $evidencia])
                @php($factor = $resultado['factores'][$clave])
                <tr>
                    <td><strong>{{ $titulo }}</strong></td>
                    <td>{{ $factor['puntos'] === null ? 'Sin evidencia para calcular el factor.' : $evidencia }}</td>
                    <td class="numero">
                        @if ($factor['puntos'] === null)
                            <span class="tenue">— / {{ $factor['maximo'] }}</span>
                        @else
                            <strong>{{ number_format($factor['puntos'], 1, ',', '.') }}</strong> / {{ $factor['maximo'] }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="nota">Puntualidad por cuota completada: 100 % a tiempo, 75 % con 1–7 días de atraso, 50 % con 8–30 días, 25 % con 31–60 días y 0 % desde 61 días. En créditos vigentes, la cobertura pesa 70 % y la antigüedad del saldo pesa 30 %.</p>

    <h2>Créditos evaluados</h2>
    <table class="datos">
        <thead><tr><th scope="col">Crédito</th><th scope="col">Estado evaluado</th><th scope="col" class="numero">Cuotas cubiertas</th></tr></thead>
        <tbody>
            @forelse ($resultado['creditos'] as $credito)
                <tr><td><strong>{{ $credito['codigo'] }}</strong></td><td>{{ $credito['estado'] }}</td><td class="numero">{{ $credito['cubiertas'] }} / {{ $credito['cuotas'] }}</td></tr>
            @empty
                <tr><td colspan="3" class="tenue">El socio aún no tiene créditos evaluables.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Detalle de cuotas</h2>
    <table class="datos cuotas">
        <thead>
            <tr>
                <th scope="col">Crédito / cuota</th><th scope="col">Vencimiento</th><th scope="col">Pago completo</th>
                <th scope="col" class="numero">Importe</th><th scope="col" class="numero">Cubierto</th>
                <th scope="col" class="numero">Saldo</th><th scope="col">Evaluación</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($resultado['cuotas'] as $cuota)
                <tr>
                    <td><strong>{{ $cuota['credito'] }} / {{ $cuota['numero'] }}</strong></td>
                    <td class="fecha-corta">{{ $cuota['vencimiento'] ?? '—' }}</td>
                    <td class="fecha-corta">{{ $cuota['fecha_pago'] ?? '—' }}</td>
                    @foreach (['importe', 'cubierto', 'saldo'] as $campo)
                        <td class="numero">$ {{ number_format($cuota[$campo] / 100, 2, '.', ',') }}</td>
                    @endforeach
                    <td>
                        @if ($cuota['trasladada'])
                            Trasladada por novación
                        @elseif ($cuota['incompleta'])
                            Dato incompleto
                        @elseif ($cuota['dias_atraso'] > 0)
                            <span class="atraso">{{ $cuota['dias_atraso'] }} días de atraso</span>
                        @elseif ($cuota['completa'])
                            <span class="correcto">A tiempo</span>
                        @else
                            Por vencer
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="tenue">El socio aún no tiene cuotas para mostrar.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
