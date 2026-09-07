<div>
    <div class="col-3 mt-2 mb-2">
        <button wire:click="tipoAhorro(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i> Nuevo</button>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Tipos de ahorros</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" wire:model="search" id="search" class="form-control float-end" placeholder="Buscar">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                        <thead>
                            <tr>
                                <th style="width: 1%;">#</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tipos as $tip)
                            <tr>
                                <td>{{ $tip->id }}</td>
                                <td>{{ $tip->name }}</td>
                                <td>{{ $tip->description }}</td>
                                <td>
                                    <button wire:click="tipoAhorro({{ $tip->id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button" class="btn bg-light btn-xs"><i class="fa fa-pen"></i></button>
                                    <button wire:click="ver({{ $tip->id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral2" type="button" class="btn bg-light btn-xs"><i class="fa fa-bars"></i></button>
                                    @if ($tip->programado)
                                    <button wire:click="calculadora({{ $tip->id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral3" type="button" class="btn bg-light btn-xs"><i class="fa fa-calculator" aria-hidden="true"></i></button>
                                    @endif
                                    <button wire:click="borrarTipo({{ $tip->id }})" class="btn bg-light btn-xs"><i class="fa fa-trash"></i></button>
                                </td>
                                <td>
                                    @if ($tip->status)
                                    <small class="badge bg-primary" wire:click="cambioEstado({{ $tip->id }})"></i>Activo</small>
                                    @else
                                    <small class="badge bg-danger" wire:click="cambioEstado({{ $tip->id }})"></i>Desactivado</small>
                                    @endif
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $tipos->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Tipos de Ahorros.</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        @if ($errors->any())
                        <div class="callout callout-warning">
                            <h5>Verifica estas observaciones.</h5>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </div>
                        @endif
                        <div class="row mb-4">
                            <div class="col-4">
                                <label>Nombre</label>
                                <input type="text" class="form-control" placeholder="Nombre" wire:model="name" id="name">
                            </div>
                            <div class="col-3">
                                <label>Edad</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" wire:model="edad_min" id="edad_min">
                                    <div class="input-group-append">
                                        <span class="input-group-text" placeholder="Edad Min">Min.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <label>Edad</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" wire:model="edad_max" id="edad_max">
                                    <div class="input-group-append">
                                        <span class="input-group-text" placeholder="Edad Min">Max.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <label>Color</label>
                                <select class="form-control" wire:model="class" id="class">
                                    <option disabled>-Seleccione-</option>
                                    <option value="info">Celeste</option>
                                    <option value="success">Verde</option>
                                    <option value="warning">Amarillo</option>
                                    <option value="danger">Rojo</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label>Interés</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="0.00" wire:model="interes" id="interes">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <label>Cantidad</label>
                                <select class="form-control" wire:model="rango_valor" id="rango_valor">
                                    <option disabled>-Seleccione-</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label>Tiempo</label>
                                <select class="form-control" wire:model="rango_tiempo" id="rango_tiempo">
                                    <option disabled>-Seleccione-</option>
                                    <option value="day">Dia(s)</option>
                                    <option value="week">Semana(s)</option>
                                    <option value="month">Mes(es)</option>
                                    <option value="year">Año(s)</option>
                                </select>
                            </div>
                            <hr>
                            <div class="col-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="programado" wire:change="desactivarCuentaEncaje">
                                    <label class="form-check-label">Ahorro programado</label>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="descargo_creditos" wire:change="desactivarCuentaEncaje">
                                    <label class="form-check-label">Cuenta descargo créditos</label>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="cuenta_certificado" wire:change="desactivarCuentaEncaje">
                                    <label class="form-check-label">Certificados de aportación</label>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="ahorro_prestamo" wire:change="desactivarCuentaEncaje">
                                    <label class="form-check-label">Cuenta ahorro de prestamos</label>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="cuenta_encaje" wire:change="actualizarCuentaEncaje">
                                    <label class="form-check-label">Cuenta de encaje</label>
                                </div>
                            </div>
                            <hr>

                            <div class="col-4">
                                <label>Porcentaje Retención encaje</label>
                                <div class="input-group mb-3">
                                    <input type="number" class="form-control" wire:model="porcentaje_encaje" step="0.01" @if( !$this->cuenta_encaje) disabled @endif>
                                    <div class="input-group-append">
                                        <span class="input-group-text" placeholder="Valor Máximo">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-4">
                                <label>Valor Máximo (Certificados Aportacion)</label>
                                <div class="input-group mb-3">
                                    <input type="number" class="form-control" wire:model="cuenta_certificado_valor_max" step="0.01">
                                    <div class="input-group-append">
                                        <span class="input-group-text" placeholder="Valor Máximo">Max.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <label>Valor Periódico Mensual</label>
                                <div class="input-group mb-3">
                                    <input type="number" class="form-control" wire:model="valor_periodico" step="0.01">
                                    <div class="input-group-append">
                                        <span class="input-group-text" placeholder="Valor Máximo">Val.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <label>Descripción</label>
                                <textarea class="form-control" rows="3" placeholder="Descripcion del Tipo de ahorro" wire:model="description" id="description"></textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="cuentaPrincipal">
                                    <label class="form-check-label">Cuenta Principal</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="cuentaTransaccional">
                                    <label class="form-check-label">Cuentas Débitos</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        @if ($this->mostrarReglas)
                        <div class="row">
                            <div class="col-3">
                                <label>Concepto</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control form-control-sm" wire:model="concepto">
                                </div>
                            </div>
                            <div class="col-3">
                                <label>%. / V Transacción</label>
                                <select class="form-control form-control-sm" wire:model="porcentaje_recaudacion" id="porcentaje_recaudacion">
                                    <option value=""> --Seleccione-- </option>
                                    <option value="0">Valor</option>
                                    <!-- <option value="1">Porcentaje</option>--> 
                                </select>
                            </div>
                            <div class="col-3">
                                <label>V. Transacción</label>
                                <div class="input-group mb-3">
                                    <input type="number" class="form-control form-control-sm" wire:model="valor_recaudacion" step="0.01">
                                </div>
                            </div>
                            <div class="col-3">
                                <label>Día Mensual transacción</label>
                                <input type="number" class="form-control form-control-sm" placeholder="Día Débito Mensual" wire:model="dia_mes" id="dia_mes">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3">
                                <label>Días gracia</label>
                                <div class="input-group mb-3">
                                    <input type="number" class="form-control form-control-sm" wire:model="dias_multa" step="0.01">
                                </div>
                            </div>
                            <div class="col-3">
                                <label>%. / V Multa</label>
                                <select class="form-control form-control-sm" wire:model="porcentaje_multa" id="porcentaje_multa">
                                    <option value=""> --Seleccione-- </option>
                                    <option value="0">Valor</option>
                                    <!-- <option value="1">Porcentaje</option> -->
                                </select>
                            </div>
                            <div class="col-3">
                                <label>V. Multa</label>
                                <div class="input-group mb-3">
                                    <input type="number" class="form-control form-control-sm" wire:model="valor_multa" step="0.01">
                                </div>
                            </div>
                            <div class="col-3">
                                <label>Banco</label>
                                <select class="form-control form-control-sm" wire:model="banco_id">
                                    <option value=""> --Seleccione-- </option>
                                    @foreach ($bancos as $ban)
                                    <option value="{{ $ban->id }}">{{ $ban->nombre }} #{{(empty($ban->numero_cuenta) ? '' : $ban->numero_cuenta)}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr>
                        <a wire:click="agregarReglaCuentaTransaacional()" class="btn bg-success btn-xs" style="color: white;" title="Agregar regla de ahorro">
                            <i class="fa fa-plus"></i>
                        </a>
                        <hr>
                        <div class="row">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Concepto</th>
                                        <th>Dia Cobro</th>
                                        <th>%. / V Transacción</th>
                                        <th>Valor Cobro</th>
                                        <th>Días Gracia</th>
                                        <th>%. / V Multa</th>
                                        <th>Valor Multa</th>
                                        <th>Banco</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($this->reglasAhorro as $reglasAhorro)
                                    <tr>
                                        <td>{{$reglasAhorro->concepto}}</td>
                                        <td>{{$reglasAhorro->dia_mes}}</td>
                                        <td>
                                            @if($reglasAhorro->porcentaje_recaudacion)
                                            PORCENTAJE
                                            @else
                                            VALOR
                                            @endif
                                        </td>
                                        <td>
                                            @if($reglasAhorro->porcentaje_recaudacion)
                                            {{$reglasAhorro->valor_recaudacion}} %
                                            @else
                                            {{$reglasAhorro->valor_recaudacion}} $                                            
                                            @endif
                                        </td>
                                        <td>{{$reglasAhorro->dias_multa}} días</td> 
                                        <td>
                                            @if($reglasAhorro->porcentaje_multa)
                                            PORCENTAJE
                                            @else
                                            VALOR
                                            @endif
                                        </td>
                                        <td>
                                            @if($reglasAhorro->porcentaje_multa)
                                            {{$reglasAhorro->valor_multa}} %
                                            @else
                                            {{$reglasAhorro->valor_multa}} $                                            
                                            @endif
                                        </td>
                                        <td>
                                            {{$reglasAhorro->banco_nombre}} 
                                            <br>                                         
                                            {{$reglasAhorro->banco_numero}} 
                                            
                                        </td>
                                        <td>
                                            <a title="Eliminar regla {{ $reglasAhorro->concepto }}" wire:click="eliminarFormaPagarLiquidar({{ $reglasAhorro->id }})"class="btn bg-danger btn-xs" style="color: white;">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </td>

                                    </tr>
                                    @endforeach                                   
                                </tbody>
                            </table>

                        </div>
                        @endif
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL DETALLES --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral2" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Valores Iniciales</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form>
                    <div class="modal-body">
                        @if ($errors->any())
                        <div class="callout callout-warning">
                            <h5>Verifica estas observaciones.</h5>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </div>
                        @endif
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-2">
                                                <label>Tipo</label>
                                                <select class="form-control" wire:model="detalle_tipo">
                                                    <option value="0">-Seleccione-</option>
                                                    <option value="E">EMPRESA</option>
                                                    <option value="C">CLIENTES</option>
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <label>Nombre</label>
                                                <input type="text" class="form-control" placeholder="Nombre" wire:model="detalle_nombre">
                                            </div>
                                            <div class="col-2">
                                                <label>Valor</label>
                                                <input type="number" class="form-control" placeholder="0.00" wire:model="detalle_valor">
                                            </div>
                                            <div class="col-2">
                                                <label>Abreviación</label>
                                                <input type="text" class="form-control" placeholder="APT" wire:model="detalle_siglas">
                                            </div>
                                            <div class="col-1">
                                                <div class="form-check mt-5">
                                                    <input class="form-check-input" type="checkbox" wire:model="detalle_bloqueado">
                                                    <label class="form-check-label">Bloqueado</label>
                                                </div>
                                            </div>
                                            <div class="col-1">
                                                <div class="form-check mt-5">
                                                    <input class="form-check-input" type="checkbox" wire:model="detalle_suma">
                                                    <label class="form-check-label">Suma</label>
                                                </div>
                                            </div>
                                            <div class="col-1 mt-4">
                                                <a wire:click="guardarValor()" class="btn btn-primary" style="color: white;"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body p-0">
                                        <table class="table table-striped">
                                            <tbody>
                                                @forelse ($detalle as $det)
                                                <tr>
                                                    <td>
                                                        @if ($det->afecta == 'E')
                                                        <span class="badge bg-primary">EMPRESA</span>
                                                        @else
                                                        <span class="badge bg-warning">CLIENTE</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $det->nombre }}</td>
                                                    <td><b>${{ $det->valor }}<b></td>
                                                    <td><i>{{ $det->siglas }}</i></td>
                                                    <td>
                                                        @if ($det->bloqueado)
                                                        <i class="fa fa-check" wire:click="cambioBloqueado({{ $det->id }})"></i>
                                                        @else
                                                        <i class="fa fa-times" wire:click="cambioBloqueado({{ $det->id }})"></i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($det->suma)
                                                        <i class="fa fa-check" wire:click="cambioSuma({{ $det->id }})"></i>
                                                        @else
                                                        <i class="fa fa-times" wire:click="cambioSuma({{ $det->id }})"></i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a wire:click="eliminarDetalle({{ $det->id }})" class="btn btn-danger" style="color: white;"><i class="fa fa-times"></i></a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No existen Registros
                                                    </td>
                                                </tr>
                                                @endforelse

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL PROGRAMADO --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral3" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Calculadora de Ahorro Programado</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form>
                    <div class="modal-body">
                        @if ($errors->any())
                        <div class="callout callout-warning">
                            <h5>Verifica estas observaciones.</h5>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </div>
                        @endif
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">

                                            <div class="col-4">
                                                <label>Interes</label>
                                                <input type="number" class="form-control" placeholder="0.00" wire:model="interes_programado">
                                            </div>
                                            <div class="col-4">
                                                <label>Rango Min. (días)</label>
                                                <input type="number" class="form-control" placeholder="min" wire:model="rango_min">
                                            </div>
                                            <div class="col-3">
                                                <label>Rango Max. (días)</label>
                                                <input type="text" class="form-control" placeholder="max" wire:model="rango_max">
                                            </div>
                                            <div class="col-1 mt-4">
                                                <a wire:click="guardarProgramado()" class="btn btn-primary" style="color: white;"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body p-0">
                                        <table class="table table-striped">
                                            <tbody>
                                                @forelse ($detalleProgramado as $prog)
                                                <tr>
                                                    <td>{{ $prog->interes }}%</td>
                                                    <td>{{ $prog->rango_min }} dias</td>
                                                    <td>{{ $prog->rango_max }} dias</td>
                                                    <td>
                                                        <a wire:click="eliminarProgramado({{ $prog->id }})" class="btn btn-danger" style="color: white;"><i class="fa fa-times"></i></a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No existen Registros
                                                    </td>
                                                </tr>
                                                @endforelse

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>