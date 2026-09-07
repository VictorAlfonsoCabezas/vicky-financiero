<div>
    <div class="row">
        <div class="col-lg-3">
            <div class="card mt-1">
                <div class="card-header border-0">
                    <div class="input-group input-group-sm">
                        <input type="search" wire:model="search" class="form-control form-control-sm" placeholder="Buscar">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-sm btn-default">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-valign-middle">
                        <tbody>
                            @foreach ($clientes as $cli)
                            <tr wire:click="seleccionarCliente({{ $cli->id }})">
                                <td class="small">
                                    {{ $cli->nombres }}
                                    {{ $cli->apellidos }}<br><b>{{ $cli->numero_documento }}<b>
                                </td>
                                <td>
                                    <a wire:click="seleccionarCliente({{ $cli->id }})" class="text-muted">
                                        <i class="fa fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $clientes->links() }}
                </div>
            </div>
        </div>


        <div class="col-lg-9 mt-6">
            <div class="card">
                <div class="card-body" style="background-color: #ededf3;">
                    <div class="tab-content">
                        @if ($errors->any())
                        <div class="callout callout-warning">
                            <h5>Verifica estas observaciones.</h5>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </div>
                        @endif
                        <div class="post">
                            <h5>Creditos<i class="fa fa-edit"></i></h5>
                            <div class="user-block">
                                <span class="username">
                                    <a>{{ $this->nombres }} {{ $this->apellidos }}</a>
                                </span>
                                <span class="description">Contacto {{ $this->telefono }}</span>
                                <span class="username">
                                    <a>Total Créditos {{ $this->totalCreditos }}</a>
                                </span>
                            </div>
                            <div class="user-block">
                                <span class="username">
                                    <a>Número de cuotas: {{ $this->cuotas_simulador }}</a>
                                </span>
                                <span class="username">
                                    <a>Total interés a pagar {{ $this->interesSuma }}</a>
                                </span>
                            </div>
                            <div class="row  mt-3">
                                <div class="col-4">
                                    <label class="small">Código</label>
                                    <div class="input-group input-group-sm">
                                        <input id="codigoPrestamo" type="text" wire:model="codigoPrestamo" class="form-control" placeholder="Codigo Credito" wire:change="verificarCodigo">
                                    </div>
                                    <small id="codigoHelp" class="form-text text-muted">Código sugerido {{ $this->codigoSugerido }}.</small>
                                </div>
                                <div class="col-4">
                                    <label class="small">Valor</label>
                                    <div class="input-group input-group-sm">
                                        <input id="valor_simulador" type="number" wire:model="valor_simulador" class="form-control" placeholder="Valor">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="small">Cuotas</label>
                                    <div class="input-group input-group-sm">
                                        <input id="cuotas_simulador" type="number" wire:model="cuotas_simulador" class="form-control" placeholder="Número de Cuotas">
                                    </div>
                                </div>


                            </div>
                            <div class="row  mt-3">
                                <div class="col-4">
                                    <label class="small">Fecha Prestamo</label>
                                    <div class="input-group input-group-sm">
                                        <input id="fecha_prestamo" type="date" wire:model="fecha_prestamo" class="form-control" placeholder="Fecha Prestamo">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="small">Tipo </label>
                                    <select id="tipo_simulador" wire:model="tipo_simulador" class="form-control form-control-sm" wire:change="obtenerDatos" wire:key="tipo_simulador">
                                        <option value=""> --SELECCIONE--</option>
                                        <option value="F">FRANCESA (CUOTA FIJA)</option>
                                        <option value="A">ALEMANA (CAPITAL FIJO)</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="small">Prestamo </label>
                                    <select id="prestamo_simulador" wire:model="prestamo_simulador" class="form-control form-control-sm" wire:change="obtenerDatosCredito" wire:key="prestamo_simulador">
                                        <option value=""> --SELECCIONE--</option>
                                        @foreach ($opcionesPrestamo as $opcion)
                                        <option value="{{ $opcion->id }}">{{ $opcion->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="small">Garante</label>
                                    <select id="garante_prestamo" wire:model="garante_prestamo" class="form-control form-control-sm select2">
                                        <option value=""> -- Seleccione -- </option>
                                        @foreach($garantes as $garante)
                                        <option value="{{$garante->id}}">{{$garante->nombres}} {{$garante->apellidos}} - {{$garante->numero_documento}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="small">Valor Encaje</label>
                                    <div class="input-group input-group-sm">
                                        <input id="encaje_valor" type="number" wire:model="encaje_valor" class="form-control" placeholder="Valor">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="small">Valor Ahorrar</label>
                                    <div class="input-group input-group-sm">
                                        <input id="valor_ahorrar_credito" type="number" wire:model="valor_ahorrar_credito" class="form-control" placeholder="Valor">
                                    </div>
                                </div>
                            </div>
                            <div class="row  mt-3">
                                <div class="col-6">
                                    <button type="button" wire:click="simular" class="btn btn-block btn-primary btn-xs" wire:loading.attr="disabled">Generar</button>
                                </div>
                                <div class="col-6">
                                    <button type="button" wire:click="crearPrestamo" class="btn btn-block btn-success btn-xs" wire:loading.attr="disabled">Guardar</button>
                                </div>
                            </div>
                            <hr>
                            <div class="row  mt-3">
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Fecha Vencimiento</th>
                                            <th>Interés del período</th>
                                            @if ($this->diarioLetras == false)
                                            <th>Capital Amortizado</th>
                                            @endif
                                            @if($this->tipo_simulador != "A")
                                            <th>Fondo de Desgravamen</th>
                                            @endif
                                            <th>Cuota a pagar</th>
                                            @if ($this->diarioLetras == false)
                                            <th>Saldo remanente </th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($this->listaLetras as $letras)
                                        <tr>
                                            <td class="small">
                                                {{ $letras['cuotas'] }}
                                            </td>
                                            <td class="small">
                                                {{ $letras['fechas'] }}
                                            </td>
                                            <td class="small">
                                                {{ $letras['interes'] }}
                                            </td>
                                            @if ($this->diarioLetras == false)
                                            <td class="small">
                                                {{ $letras['amoritizado'] }}
                                            </td>
                                            @endif
                                            @if($this->tipo_simulador != "A")
                                            <td class="small">
                                                {{ $letras['desgravamen'] }}
                                            </td>
                                            @endif
                                            <td class="small">
                                                {{ $letras['cuotaPago'] }}
                                            </td>
                                            @if ($this->diarioLetras == false)
                                            <td class="small">
                                                {{ $letras['deuda'] }}
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('livewire:load', function() {
        initializeSelect2();
    });

    document.addEventListener('livewire:update', function() {
        initializeSelect2();
    });

    document.addEventListener('livewire:reconnected', function() {
        initializeSelect2();
    });


    function initializeSelect2() {
        $('.select2').select2();

        // Listener para cambiar el valor de Livewire al seleccionar una opción en Select2
        $('.select2').on('change', function(e) {
            var data = $(this).val();
            @this.set('garante_prestamo', data);
        });
    }
</script>