<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Movimientos por transacción</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
        }

        table {
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        th {
            background: #e8eef3;
        }

        td,
        th {
            border: 1px solid #aaa;
            overflow-wrap: break-word;
        }

        thead {
            display: table-header-group;
        }
    </style>
</head>

<body>
    <h1>Movimientos por tipo de transacción</h1>
    <p>Desde {{ $filters['desde'] }} hasta {{ $filters['hasta'] }} · Estado: {{ $filters['estado'] }} · Tipo: {{ $filters['tipo'] ? optional($summary->first())->tipo_nombre : 'Todos' }} · Búsqueda: {{ $filters['buscar'] ?: 'Todas' }}</p>
    <h2>Resumen del período</h2>
    <p>Entradas (S) y salidas (R): solo movimientos activos. El valor registrado incluye los anulados seleccionados.</p>
    @include('reportes.movimientos-transacciones-resumen')
    <h2>Detalle completo de movimientos</h2>
    @include('reportes.movimientos-transacciones-detalle')
</body>

</html>