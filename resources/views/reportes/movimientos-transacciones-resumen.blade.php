<div class="table-responsive">
    <table class="table table-striped" width="100%" cellpadding="5" cellspacing="0" border="1">
        <thead>
            <tr>
                <th>Tipo de transacción</th>
                <th>Movimientos</th>
                <th>Valor registrado</th>
                <th>Entradas activas</th>
                <th>Salidas activas</th>
                <th>Neto activo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($summary as $row)
            <tr>
                <td>{{ $row->tipo_nombre }}</td>
                <td>{{ $row->cantidad }}</td>
                <td>{{ number_format($row->valor, 2, '.', '') }}</td>
                <td>{{ number_format($row->entradas, 2, '.', '') }}</td>
                <td>{{ number_format($row->salidas, 2, '.', '') }}</td>
                <td>{{ number_format($row->entradas - $row->salidas, 2, '.', '') }}</td>
            </tr>
            @empty<tr>
                <td colspan="6">No hay movimientos para los filtros seleccionados.</td>
            </tr>@endforelse
        </tbody>
        <tfoot>
            <tr>
                <th>Total del período</th>
                <td>{{ $summary->sum('cantidad') }}</td>
                <td>{{ number_format($summary->sum('valor'), 2, '.', '') }}</td>
                <td>{{ number_format($summary->sum('entradas'), 2, '.', '') }}</td>
                <td>{{ number_format($summary->sum('salidas'), 2, '.', '') }}</td>
                <td>{{ number_format($summary->sum('entradas') - $summary->sum('salidas'), 2, '.', '') }}</td>
            </tr>
        </tfoot>
    </table>
</div>