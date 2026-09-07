<div>
    <div>
        <div>
            <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                <thead>
                    <tr>
                        <th style="width: 1%;">#</th>
                        <th>Compania</th>
                        <th>Cliente</th>
                        <th>Codigo</th>
                        <th>Fecha de Creacion</th>
                        <th>Fecha de Caducidad</th>
                        <th>Descripcion</th>
                        <th>Iva</th>
                        <th>Iva_0</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($header as $hea)
                        <tr>
                            <td>{{ $hea->id }}</td>
                            <td>{{ $hea->country_id }}</td>
                            <td>{{ $hea->customer_id }}</td>
                            <td>{{ $hea->codigo }}</td>
                            <td>{{ $hea->fecha_creacion }}</td>
                            <td>{{ $hea->descripcion }}</td>
                            <td>{{ $hea->subtotal }}</td>
                            <td>{{ $hea->iva }}</td>
                            <td>{{ $hea->iva_0 }}</td>
                            <td>{{ $hea->total }}</td>
                            <td>
                                @if ($hea->status)
                                    <small class="badge bg-primary"
                                        wire:click="cambioEstado({{ $hea->id }})"></i>Activo</small>
                                @else
                                    <small class="badge bg-danger"
                                        wire:click="cambioEstado({{ $hea->id }})"></i>Desactivado</small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $header->links() }}
        </div>
    </div>
</div>
