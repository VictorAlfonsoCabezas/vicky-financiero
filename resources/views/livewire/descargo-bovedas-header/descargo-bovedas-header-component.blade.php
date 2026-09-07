<div>
    <div class="container-fluid mt-2">
        <div class="row flex-nowrap overflow-auto">
            @foreach ($bovedas as $bov)
            <div class="col-12 col-md-4">
                <div class="card card-row card-{{ $bov->principal ? 'primary' : 'warning' }}">
                    <div class="card-header">
                        <h3 class="card-title">
                            <b>{{ $bov->nombre }}</b> <i class="fa fa-{{ $bov->principal ? 'server' : 'archive' }}"
                                aria-hidden="true"></i><br>
                            <small>{{ $bov->descripcion }}</small>
                        </h3>
                        <div class="text-end">
                            @if ($bov->principal)
                            <h4>${{ number_format($bov->saldoBoveda - $this->creditosVigentes, 2, '.', '') }}</h4>
                            @else
                            <h4>${{ $bov->saldoBoveda }}</h4>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Bodega Principal --}}
                        @if ($bov->principal)
                        {{-- Cargas Iniciales --}}
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h5 class="card-title">Cargas Iniciales</h5>
                                <div class="card-tools">
                                    <a wire:click="seleccionarBoveda({{ $bov->id }})"
                                        class="btn btn-tool btn-link" data-bs-toggle="modal"
                                        data-bs-target="#modalGeneral">Agregar</a>
                                    <a wire:click="seleccionarBoveda({{ $bov->id }})" class="btn btn-tool"
                                        data-bs-toggle="modal" data-bs-target="#modalGeneral">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <label for="" style="color: blue;"> Balance Inicial</label>
                                <table class="table">
                                    <tbody>
                                        @foreach ($valoresInicialesEmpresa as $carga)
                                        <tr>
                                            <td><b>{{ $carga->nombre_banco }}</b></td>
                                            <td><b>$ {{ $carga->valor }}</b></td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                <hr style="color: blue;">
                                <label for="" style="color: green;">Balance Actual</label>
                                <table class="table">
                                    <tbody>
                                        @if ($bov->principal)
                                        @foreach ($bancosValores as $bancos)
                                        <tr>
                                            <td><b>{{ $bancos->nombre }}</b></td>
                                            <td><b>$ {{App\Http\Controllers\DescargoBovedasHeader\DescargoBovedasHeaderController::valorVancoTotal($bancos->id, $bov->id)}}</b></td>
                                        </tr>
                                        @endforeach
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- Gastos Iniciales --}}
                        <div class="card card-danger card-outline">
                            <div class="card-header">
                                <h5 class="card-title"><i class="fa fa-minus"></i> Gastos Iniciales</h5>
                                <div class="card-tools">
                                    <a wire:click="gastoInicial({{ $bov->id }})"
                                        class="btn btn-tool btn-link" data-bs-toggle="modal"
                                        data-bs-target="#modalGeneral1">Agregar</a>
                                    <a wire:click="gastoInicial({{ $bov->id }})" class="btn btn-tool"
                                        data-bs-toggle="modal" data-bs-target="#modalGeneral1">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tbody>
                                        @foreach ($gastosIniciales as $gast)
                                        <tr>
                                            <td><b>Gasto Inicial:</b></td>
                                            <td><b>$ {{ $gast->valor }}</b></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- Créditos Vigentes --}}
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h5 class="card-title"><i class="fa fa-plus"></i> Créditos Vigentes</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td><b>Creditos Vigentes: </b></td>
                                            <td><b>$ {{ $creditosVigentes }}</b></td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        @endif
                        {{-- Transferencias --}}
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h5 class="card-title">Transferencias</h5>
                                <div class="card-tools">
                                    <a wire:click="seleccionarBoveda({{ $bov->id }})"
                                        class="btn btn-tool btn-link" data-bs-toggle="modal"
                                        data-bs-target="#modalGeneral2">Transferir</a>
                                    <a wire:click="seleccionarBoveda({{ $bov->id }})" class="btn btn-tool"
                                        data-bs-toggle="modal" data-bs-target="#modalGeneral2">
                                        <i class="fa fa-truck" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- Solicitudes de Caja --}}
                        @if ($bov->boveda)
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h5 class="card-title">Solicitudes Caja</h5>
                                <div class="card-tools">
                                    <a wire:click="seleccionarBoveda({{ $bov->id }})"
                                        class="btn btn-tool btn-link">Enviar Caja</a>
                                    <a wire:click="seleccionarBoveda({{ $bov->id }})"
                                        class="btn btn-tool">
                                        <i class="fa fa-cash-register" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                valores iniciales de los bancos
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- MODAL CARGAS INICIALES --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Cargas Iniciales </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeCarga">
                    <div class="modal-body">
                        @if ($errors->any())
                        <div class="callout callout-warning">
                            <h5>Verifica estas observaciones.</h5>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </div>
                        @endif
                        <div class="form-group">
                            <label>Seleccione el Banco</label>
                            <select class="form-control" wire:model="banco_id">
                                <option> -Selecciones uno- </option>
                                @foreach ($this->bancos as $banco)
                                <option value="{{ $banco['id'] }}">{{ $banco['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label>Valor</label>
                                <input type="number" step="0.01" class="form-control" placeholder="0.00"
                                    wire:model="valor">
                            </div>
                            <div class="col-6">
                                <label>Operacion</label>
                                <select class="form-control" wire:model="operacion_id">
                                    <option> -Selecciones uno- </option>
                                    @foreach ($this->cargaInicial as $carga)
                                    <option value="{{ $carga['id'] }}">{{ $carga['nombre'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL GASTOS INICIALES --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral1" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Gastos Iniciales </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeGastoInicial">
                    <div class="modal-body">
                        @if ($errors->any())
                        <div class="callout callout-warning">
                            <h5>Verifica estas observaciones.</h5>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-12">
                                <label>Valor Gasto</label>
                                <input type="number" step="0.01" class="form-control" placeholder="0.00"
                                    wire:model="valor_gasto">
                            </div>
                            <div class="col-12">
                                <label>Descripción</label>
                                <textarea type="text" class="form-control" placeholder="Ingrese una Descripción" wire:model="descripcion_gasto"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL TRANSFERECNIAS --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral2" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Transferencia </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeTransferencia">
                    <div class="modal-body">
                        @if ($errors->any())
                        <div class="callout callout-warning">
                            <h5>Verifica estas observaciones.</h5>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </div>
                        @endif
                        <div class="form-group">
                            <label>Seleccione el Boveda</label>
                            <select class="form-control" wire:model="trans_boveda_id">
                                <option> -Selecciones uno- </option>
                                @foreach ($this->bovedasTransferencia as $bov)
                                <option value="{{ $bov['id'] }}">{{ $bov['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Seleccione el Banco</label>
                            <select class="form-control" wire:model="trans_banco_id">
                                <option> -Selecciones uno- </option>
                                @foreach ($this->bancos as $banco)
                                <option value="{{ $banco['id'] }}">{{ $banco['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label>Valor</label>
                                <input type="number" step="0.01" class="form-control" placeholder="0.00"
                                    wire:model="trans_valor">
                            </div>
                            <div class="col-12">
                                <label>Observacion</label>
                                <input type="text" class="form-control" placeholder="Ingrese Observacion"
                                    wire:model="trans_observacion">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>