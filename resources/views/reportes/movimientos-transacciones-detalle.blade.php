<div class="table-responsive">
    <table class="table table-striped table-hover" width="100%" cellpadding="4" cellspacing="0" border="1">
        <thead>
            <tr>@foreach(['Fecha', 'Hora', 'Código', 'Comprobante', 'Socio', 'Identificación', 'Tipo de transacción', 'Acción', 'Valor', 'Estado', 'Usuario', 'Observación'] as $label)<th>{{ $label }}</th>@endforeach</tr>
        </thead>
        <tbody>
            @forelse($movimientos as $m)
            <tr>
                <td>{{ $m->date_created }}</td>
                <td>{{ $m->hour_created }}</td>
                <td>{{ $m->code }}</td>
                <td>{{ $m->comprobante }}</td>
                <td>{{ $m->customer_name }}</td>
                <td>{{ $m->customer_ruc }}</td>
                <td>{{ $m->tipo_nombre }}</td>
                <td>{{ $m->type_transaction_action }}</td>
                <td>{{ number_format($m->valor_movimiento, 2, '.', '') }}</td>
                <td>{{ $m->status ? 'Activo' : 'Anulado' }}</td>
                <td>{{ $m->user_created_name }}</td>
                <td>{{ $m->observation }}</td>
            </tr>
            @empty<tr>
                <td colspan="12">No hay movimientos para los filtros seleccionados.</td>
            </tr>@endforelse
        </tbody>
    </table>
</div>