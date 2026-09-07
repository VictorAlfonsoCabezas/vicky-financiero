<div>
    <div>
        <div>
            <div class="row">
                <div class="form-group col-4">
                    <label>Cuentas</label>
                    <select class="form-control" wire:model="cuenta">
                        @foreach ($tipoCuenta as $tip)
                            <option value="{{ $tip->id }}">{{ $tip->tipoAhorros->name }}</option>
                        @endforeach
                    </select>

                </div>
                <div class="form-group col-4">
                    <label for="fechaInicio">Fecha Inicio</label>
                    <input type="date" class="form-control" id="fechaInicio" wire:model="fechaInicio"
                        wire:model="fechaInicio">
                </div>

                <div class="form-group col-4">
                    <label for="fechaFin">Fecha Fin</label>
                    <input type="date" class="form-control" id="fechaFin" wire:model="fechaFin"
                        wire:model="fechaFin">
                </div>
            </div>
            <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                <thead>
                    <thead>
                        <tr class="text-center">
                            <th style="width: 1%;">id</th>
                            <th>Tipo de transaccion</th>
                            <th>Movimiento</th>
                            <th>Observación</th>
                            <th>Valor movimiento</th>
                        </tr>
                    </thead>
                <tbody>
                    @foreach ($movimientos as $movi)
                        <tr class="text-center">
                            <td>{{ $movi->id }}</td>
                            <td style="font-size: 30px;">
                                @if ($movi->type_transaction_name == 'INGRESOS')
                                    <span class="badge bg-primary">{{ $movi->type_transaction_name }}</span>
                                @else
                                    <span class="badge bg-danger">{{ $movi->type_transaction_name }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $movi->customer_name }}
                                <br>
                                <b><i class="fa fa-calendar"></i> {{ $movi->date_created }}</b>
                                <br>{{ $movi->hour_created }}</b>
                            </td>
                            <td>
                                {{ $movi->observation }}
                            </td>
                            <td style="font-size: 29px;">
                                @if ($movi->typeTransaction->action == 'S')
                                    <b> + $ {{ $movi->valor_movimiento }}</b>
                                @else
                                    <b> - $ {{ $movi->valor_movimiento }}</b>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $movimientos->links() }}
        </div>
    </div>
</div>
