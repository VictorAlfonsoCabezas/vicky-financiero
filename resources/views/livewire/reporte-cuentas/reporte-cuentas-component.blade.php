<div>
    <div class="row col col-sm-12">
        <div class="col-4">
            <label>Busqueda</label>
            <div class="input-group input-group-sm">
                <input type="text" wire:model="search" id="search" class="form-control"
                    placeholder="Buscar Socio">
                <div class="input-group-append">
                    <div class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
        </div>

        <section class="col col-sm-4">
            <div class="col-6">
                <label class="small">Nivel </label>
                <select id="tipo_cuenta" wire:model="tipo_cuenta" class="form-control text-uppercase" wire:change="obtenerDatos">
                    <option value="0"> --SELECCIONE--</option>
                    @foreach ($tipoAhorros as $ahorros)
                    <option value="{{$ahorros->id}}"> {{$ahorros->name}} </option>
                    @endforeach
                </select>
            </div>
        </section>
    </div>
    <div>
        <br>
        <div class="row">
            <a class="btn btn-primary btn-xs" style="color: white;" wire:click="export">
                <i class="fas fa-plus"></i> Exportar
            </a>
        </div>
        <br>
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><b>Lista de cuentas</b></h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 1%;">Número Cuenta</th>
                                    <th>Nombre Cuenta</th>
                                    <th>Fecha Creación</th>
                                    <th>Documento</th>
                                    <th>Propietario</th>
                                    <th>Valor</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cuentas as $cuenta)
                                <tr class="text-center">
                                    <td>{{ $cuenta->codigo }}</td>
                                    <td>{{ $cuenta->tipoAhorro }}</td>
                                    <td>{{ $cuenta->created_at }}</td>
                                    <td>{{ $cuenta->numero_documento }}</td>
                                    <td>{{ $cuenta->cliente }}</td>
                                    <td>{{ $cuenta->saldo }}</td>
                                    <td>
                                        @if($cuenta -> status == 1)
                                        <span class="badge bg-success">Aprobado</span>
                                        @else
                                        <span class="badge bg-warning">Stand By</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm" wire:click="verCuenta({{ $cuenta->id }})">Ver</button>
                                        @if($cuenta->status == 0)
                                        <button class="btn btn-success btn-sm" wire:click="aprobarCuenta({{ $cuenta->id }})">
                                            Aprobar
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div wire:ignore.self class="modal fade" id="modalCuenta" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">

                                    <div class="modal-header" style="background-color: cornflowerblue;">
                                        <h4 class="modal-title"> Detalle de Cuenta </h4>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <form>
                                        <div class="modal-body">

                                            @if ($cuentaSeleccionada)
                                            <div class="callout callout-info">
                                                @if ($cuentaSeleccionada->tipo_ahorros_id == 1)
                                                <h5><i class="fa fa-lock"></i> Ahorros a la Vista:</h5>
                                                @elseif($cuentaSeleccionada->tipo_ahorros_id == 2)
                                                <h5><i class="fa fa-calculator"></i> Ahorro Programado:</h5>
                                                @else
                                                <h5><i class="fa fa-sim-card"></i> Cuenta Encaje:</h5>
                                                @endif
                                            </div>
                                            @endif

                                            <div class="modal-body">

                                                @if($cuentaSeleccionada)

                                                <div class="row">
                                                    <div class="col-6">
                                                        <p><b>Cliente:</b> {{ $cuentaSeleccionada->cliente }}</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p><b>Código:</b> {{ $cuentaSeleccionada->codigo }}</p>
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <p><b>Documento:</b> {{ $cuentaSeleccionada->numero_documento }}</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p><b>Tipo:</b> {{ $cuentaSeleccionada->tipoAhorro }}</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p><b>Saldo:</b> {{ $cuentaSeleccionada->saldo }}</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p>
                                                            <b>Estado:</b>
                                                            @if($cuentaSeleccionada->status == 1)
                                                            <span class="badge bg-success">Aprobado</span>
                                                            @else
                                                            <span class="badge bg-warning">Stand By</span>
                                                            @endif
                                                        </p>
                                                    </div>

                                                    <div class="col-6">
                                                        <p><b>Forma de Pago:</b> {{ $cuentaSeleccionada->forma_pago_nombre }}</p>
                                                    </div>


                                                </div>

                                                @if(
                                                $cuentaSeleccionada->forma_pago_id == 2 || $cuentaSeleccionada->forma_pago_id == 3
                                                )
                                                <hr>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <p>
                                                            <b># Comprobante:</b>
                                                            {{ $cuentaSeleccionada->comprobante }}
                                                        </p>
                                                    </div>

                                                    <div class="col-6">
                                                        <p>
                                                            <b>Banco:</b>
                                                            {{ $cuentaSeleccionada->banco_nombre }}
                                                        </p>
                                                    </div>

                                                    <div class="col-6">
                                                        <p>
                                                            <b># Depósito:</b>
                                                            {{ $cuentaSeleccionada->numero_deposito }}
                                                        </p>
                                                    </div>
                                                </div>



                                                @endif

                                                @endif

                                            </div>

                                            <div class="modal-footer">

                                                @if($cuentaSeleccionada)

                                                @if($cuentaSeleccionada->status == 0)
                                                <button class="btn btn-success" wire:click="aprobarCuenta({{ $cuentaSeleccionada->id }})">Aprobar</button>
                                                @endif
                                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                @endif

                                            </div>

                                        </div>
                                        
                                    </form>
                                </div>
                            </div>
                            {{ $cuentas->links() }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <script>
            window.addEventListener('show-modal-cuenta', event => {
                $('#modalCuenta').modal('show');
            });

            window.addEventListener('close-modal', event => {
                $('#modalCuenta').modal('hide');
            });
        </script>
    </div