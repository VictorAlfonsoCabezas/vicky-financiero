<div>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Clientes Cuentas</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" wire:model="search" id="search" class="form-control float-end"
                                placeholder="Buscar">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                        <thead>
                            <tr>
                                <th style="width: 1%;">#</th>
                                <th>Identificación</th>
                                <th>Nombres</th>
                                <th>Tipo Cuenta</th>
                                <th>Cuenta</th>
                                <th>Fecha Creación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customerTipoAhorros as $cusTip)
                                <tr>
                                    <td>{{ $cusTip->id }}</td>
                                    <td>{{ $cusTip->numero_documento }}</td>
                                    <td>{{ $cusTip->full_name }}</td>
                                    <td>{{ $cusTip->tipo_ahorros_name }}</td>
                                    <td><b>{{ $cusTip->codigo }}</b></td>
                                    <td>{{ $cusTip->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $customerTipoAhorros->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
