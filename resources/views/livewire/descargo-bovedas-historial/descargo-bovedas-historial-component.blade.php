<div>
    <div class="row">
        <div class="col-12 mt-2">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Historial de Movimientos</h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                        <thead>
                            <tr>
                                <th style="width: 1%;">#</th>
                                <th>Fecha</th>
                                <th>Operación</th>
                                <th>Bóveda Origen</th>
                                <th>Bóveda Destino</th>
                                <th>Caja</th>
                                <th>Banco</th>
                                <th># Cuenta</th>
                                <th>Salidas</th>
                                <th>Entradas</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($historial as $his)
                                <tr>
                                    <td>{{ $his->id }}</td>
                                    <td>{{ $his->created_at }}</td>
                                    <td>{{ $his->operacion_nombre }}</td>
                                    <td>{{ $his->bovedas_nombre }}</td>
                                    <td>{{ $his->bovedas_recibe_nombre }}</td>
                                    <td>{{ $his->caja_code }}</td>
                                    <td>{{ $his->banco_nombre }}</td>
                                    <td>{{ $his->numero_cuenta }}</td>
                                    <td>{{ $his->salidas }}</td>
                                    <td>{{ $his->entradas }}</td>
                                    <td>{{ $his->valor }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $historial->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
