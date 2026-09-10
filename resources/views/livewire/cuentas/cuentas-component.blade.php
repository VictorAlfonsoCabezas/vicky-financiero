<div class="savings-module">
    <div class="savings-heading"><div><div class="text-muted small mb-1">SOCIOS / AHORROS</div><h1>Cuentas de ahorro</h1><p>Consulta saldos y administra las operaciones de cada socio.</p></div><a href="/tipo-ahorros" class="btn btn-outline-primary"><i class="fa fa-sliders-h me-2"></i>Tipos de ahorro</a></div>
    <div wire:loading.delay class="savings-loading" role="status">Actualizando datos...</div>
    <div class="row">
        @if(session('message'))
        <div class="alert alert-success" role="status">{{ session('message') }}</div>
        @endif
        <div class="col-12 col-xl-3 savings-sidebar">
            <div class="input-group input-group-sm mb-2 mt-2">
                <input type="text" wire:model.debounce.350ms="search" aria-label="Buscar socio por nombre o documento" id="search" class="form-control" placeholder="Buscar Socio">
                <div class="input-group-append">
                    <div class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de socios</h3>
                </div>
                <div class="card-body p-0" style="display: block;">
                    <ul class="nav nav-pills flex-column">
                        @forelse ($customer as $cus)
                        <li class="nav-item" wire:key="customer-{{ $cus->id }}">
                            <button type="button" wire:click="seleccionarCliente({{ $cus->id }})" class="nav-link savings-customer {{ $customer_selec == $cus->id ? 'is-selected' : '' }}" aria-pressed="{{ $customer_selec == $cus->id ? 'true' : 'false' }}"
                                style="font-size: 14px;">
                                <i class="fas fa-user"></i> <b>{{ $cus->apellidos }}</b> {{ $cus->nombres }}
                                @if ($cus->nuevo)
                                <span class="badge bg-success"> Nuevo</span>
                                @endif
                                <br>
                                <small>{{ $cus->numero_documento }}</small>
                                <span class="badge bg-primary float-end">{{ $cus->total_tipos_ahorro }}</span>
                                @if ($customer_selec == $cus->id)
                                <span class="savings-customer-selected"><i class="fas fa-check-circle me-1" aria-hidden="true"></i>Socio seleccionado</span>
                                @endif
                            </button>
                        </li>
                        @empty
                        <li class="p-4 text-muted">No se encontraron socios.</li>
                        @endforelse
                    </ul>
                    {{ $customer->links() }}
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-9">
            <div class="card card-primary card-outline mt-2">
                <div class="card-header savings-customer-heading">
                    <h3 class="card-title">Información</h3>
                    <div class="card-title" style="display: flex; align-items: center; gap: 20px;">
                        <h3 class="card-title">Contrato Apertura</h3>
                        <b wire:click="generarPdfContratoApertura" class="btn btn-info btn-xs" style="color: white;">
                            <i class="fas fa-print"></i>
                        </b>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($cliente)
                    <table class="table m-0 table-sm" style="font-size: 13px;">
                        <tbody>
                            <tr>
                                <td><b>Identificación</b></td>
                                <td>
                                    <span class="product-description badge bg-primary"
                                        id="code_label">{{ $cliente->numero_documento }}</span>
                                </td>
                                <td><b>Nombres</b></td>
                                <td>
                                    <span class="product-description"
                                        id="nombre_label">{{ $cliente->apellidos . ' ' . $cliente->nombres }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Celular</b></td>
                                <td>
                                    <span class="product-description"
                                        id="telefono_label">{{ $cliente->telefono }}</span>
                                </td>
                                <td><b>Dirección</b></td>
                                <td>
                                    <span class="product-description" id="addres_label">{{ $cliente->direccion }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    @else
                    <h5 class="text-center mt-2"><i class="fa fa-user"></i> Seleccione un Cliente</h5>
                    @endif
                    <hr>
                    @if ($cliente)
                    <div class="ms-2">
                        <a href="/tipo-ahorros" target="_blank" style="font-size: 12px;">Ver Tipo Cuentas</a>
                        <a wire:click="historialAhorros()" data-bs-toggle="modal" data-bs-target="#modalGeneral7">
                            <i style="font-size:20px;" class="fa fa-fw fa-sitemap"></i>
                        </a>
                    </div>
                    <div class="savings-accounts">
                        <button type="button" wire:click="abrirModalTipo" class="savings-add" data-bs-toggle="modal" data-bs-target="#modalGeneral"><i class="fa fa-plus mb-2"></i><span>Abrir cuenta</span></button>
                        @foreach ($cuentas as $cue)
                        <div class="savings-account {{ $cuenta_selec == $cue->id ? 'is-selected' : '' }}" style="{{ \App\Support\SavingsPalette::style(optional($cue->tipoAhorros)->class) }}" wire:key="account-{{ $cue->id }}">
                            <button type="button" wire:click="cuentaSeleccionada({{ $cue->id }})" class="savings-account-select" aria-pressed="{{ $cuenta_selec == $cue->id ? 'true' : 'false' }}">
                                <span class="savings-account-name"><i class="fa fa-wallet me-2"></i>{{ optional($cue->tipoAhorros)->name ?: 'Tipo no disponible' }}</span>
                                <span class="text-muted small">{{ $cue->codigo }}</span>
                                <strong class="savings-balance">$ {{ number_format($cue->saldo, 2) }}</strong>
                                @if ($cue->dias_plazo)<span class="text-muted small">{{ $cue->dias_plazo }} d&iacute;as &middot; {{ $cue->porcentaje }} %</span>@endif
                                @if ($cuenta_selec == $cue->id)<span class="text-primary small mt-2"><i class="fa fa-check-circle me-1"></i>Cuenta seleccionada</span>@endif
                            </button>
                            <button type="button" class="btn btn-sm text-danger savings-account-delete" title="Eliminar cuenta" aria-label="Eliminar cuenta {{ $cue->codigo }}" onclick="if (confirm('Eliminar esta cuenta? Solo se permite si no tiene movimientos.')) { @this.call('eliminarCuenta', {{ $cue->id }}) }"><i class="fa fa-trash-alt"></i></button>
                        </div>
                        @endforeach
                    </div>
                    @if ($this->cuenta_selec > 0)
                    <div class="savings-actions">
                        @if($this->programado)
                        @if ($this->cumplimiento)
                        <small class="btn btn-outline-primary btn-block"
                            wire:click="cambioRenovacion({{ $this->cuenta_selec }})"
                            style="width: 94px;height: 62px;"><i class="fa fa-bell"></i>Se renueva</small>
                        @else
                        <small class="btn btn-outline-danger btn-block btn-sm"
                            wire:click="cambioRenovacion({{ $this->cuenta_selec }})"
                            style="width: 94px;height: 62px;"><i class="fa fa-book"></i>No se renueva</small>
                        @endif
                        @endif


                        <a wire:click="tipoTransaccion('IN')" class="btn btn-app" data-bs-toggle="modal"
                            data-bs-target="#modalGeneral2">
                            <i class="fas fa-piggy-bank"></i> Ahorrar
                        </a>


                        @if (!$this->certificados && !$this->programado && $this->retirarValores)
                        <a wire:click="tipoTransaccion('EG')" class="btn btn-app" data-bs-toggle="modal"
                            data-bs-target="#modalGeneral2">
                            <i class="fas fa-money-bill"></i> Retiros.
                        </a>
                        @endif

                        <a href="/descargar-cartola/{{ $this->customer_selec }}/{{ $this->cuenta_selec }}"
                            class="btn btn-app bg-secondary" title="En esta opcion podrá visulizar la Cartilla">
                            <i class="fa fa-id-card"></i> Cartola
                        </a>
                        <a class="btn btn-app bg-warning" data-bs-toggle="modal" data-bs-target="#modalGeneral4"
                            title="Archivos de esta cuenta">
                            <i class="fa fa-paperclip"></i> Archivos
                        </a>
                        <a class="btn btn-app bg-info" data-bs-toggle="modal" data-bs-target="#modalGeneral5"
                            title="Archivos de esta cuenta" wire:click="consultarCartolas()">
                            <i class="fa fa-paperclip"></i> Lista cartolas
                        </a>
                        @if($this->retirarValoresEncaje)
                        <a class="btn btn-app bg-success" data-bs-toggle="modal" data-bs-target="#modalGeneral9"
                            title="Entregar Valores Encaje" wire:click="entregarValoresEncaje()">
                            <i class="fa fa-dollar-sign"></i> Liquidación Encaje
                        </a>
                        <a class="btn btn-app bg-info" data-bs-toggle="modal" data-bs-target="#modalGeneral10"
                            title="Entregar Valores Encaje" wire:click="historialEncaje()">
                            <i class="fa fa-sitemap"></i> Historial Encaje
                        </a>
                        @endif
                    </div>
                    @endif

                    @if ($this->cuenta_selec > 0)
                    <div class="table-responsive savings-movements"><table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th colspan="7" style="text-align: center;font-size: 21px;">
                                    @if ($this->numeroCartolaActiva != '') CARTOLA # {{$this->numeroCartolaActiva}}@else
                                    Sin cartola activa @endif
                                </th>
                            </tr>
                            <tr>
                                <th style="text-align: center;">Fecha</th>
                                <th style="text-align: center;">Comprobante</th>
                                <th style="text-align: center;">Detalle</th>
                                <th style="text-align: center;">Ahorros</th>
                                <th style="text-align: center;">Retiros</th>
                                <th style="text-align: center;">Saldo</th>
                                <th style="text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($detalle->reverse() as $key => $det)
                            @if($det->carga_masiva)
                            <tr style="background-color: #abebc6;">
                                @else
                            <tr>
                                @endif
                                <td style="width: 280px;">
                                    {{ $this->convertirFecha($det->date_created) }}
                                    <small>{{ $det->hour_created }}</small><br>
                                </td>
                                <td>
                                    <small>{{ $det->comprobante }}</small>
                                </td>
                                <td style="width: 280px;">
                                    <small>{{ $det->observation }}</small>
                                </td>
                                @if($det->type_transaction_action == 'S')
                                <td>
                                    <b style="color: green;">{{ $det->valor_movimiento }} $</b>
                                </td>
                                <td>
                                    <b></b>
                                </td>
                                @else
                                <td>
                                    <b></b>
                                </td>
                                <td style="color: red;">
                                    <b>{{ $det->valor_movimiento }} $</b>
                                </td>
                                @endif
                                <td>
                                    <b>{{ number_format($det->saldoValor, 2) }} $</b>
                                </td>
                                <td>
                                    <a wire:click="envioWhatsapp({{ $det->id }})"
                                        class="btn btn-default btn-sm custom-btn" title="Enviar Whatsapp"
                                        style="color: white;">
                                        <i class="fa fa-paper-plane" aria-hidden="true" style="color: black;"></i>
                                    </a>

                                    @if ($this->programado)
                                    <a class="btn btn-default btn-sm"
                                        href="/customer/certificadoAhorroProgramado/{{ $det->id }}" title="Transacción"
                                        style="color: white;">
                                        <i class="fas fa-file" style="color: black;"></i></a>

                                        <a wire:click="pagosProgramados({{ $det->id }})" class="btn btn-success btn-sm"
                                            style="color: white;" data-bs-toggle="modal" data-bs-target="#modalGeneral6">
                                            <i class="fa fa-dollar-sign"></i>
                                            <span class="badge bg-white">{{ $det->total }}</span>
                                        </a>
                                    @endif
                                    <a wire:click="llenarChat({{ $det->id }})" class="btn btn-success btn-sm"
                                        style="color: white;" data-bs-toggle="modal" data-bs-target="#modalGeneral3">
                                        <i class="fa fa-comment" aria-hidden="true"></i>
                                        <span class="badge bg-white">{{ $det->total }}</span>
                                    </a>
                                    <a class="btn btn-default btn-sm"
                                        href="{{ URL::to('historial/transacciones/movimientos/' . $det->id) }}"
                                        title="Transacción" style="color: white;">
                                        <i class="fas fa-print" style="color: black;"></i>
                                    </a>
                                    <a class="btn btn-danger btn-sm" onclick="imprimirCartilla({{ $det->id }})"
                                        title="Transacción" style="color: white;">
                                        <i class="fas fa-print" style="color: white;"></i>
                                    </a>
                                    <a class="btn btn-primary btn-sm" title="Entregar Valores" wire:click="imprimirTicket({{ $det->id }})"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalGeneral11">
                                        <i class="fas fa-print" style="color: white;"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center" colspan="7">No hay movimientos</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table></div>
                    @endif
                    @else
                    <h5 class="text-center"><i class="fa fa-sack-dollar"></i> Cuentas</h5>
                    @endif
                </div>
                <div class="card-footer p-0">
                </div>
            </div>
        </div>
    </div>


    {{-- MODAL AGREGAR CUENTA --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header {{ $this->tipoAhorroColor !== 0 ? 'bg-' . $this->tipoAhorroColor : '' }}">
                    <h4 class="modal-title"> Crear Cuenta </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form wire:submit.prevent="storeCuenta">
                    <div class="modal-body">
                        @if ($errors->any())
                        <div class="callout callout-warning">
                            <h5>Verifica estas observaciones.</h5>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </div>
                        @endif
                        @if ($this->tipoAhorroCuenta !== '')
                        <div class="callout callout-info">
                            @if ($this->tipoAhorroCuenta == 'CERTIFICADO')
                            <h5><i class="fa fa-lock"></i> Certificados:</h5>
                            @elseif($this->tipoAhorroCuenta == 'PROGRAMADO')
                            <h5><i class="fa fa-calculator"></i> Ahorro Programado:</h5>
                            @else
                            <h5><i class="fa fa-sim-card"></i> Ahorro General:</h5>
                            @endif
                        </div>
                        @endif
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Tipo de Cuenta</label>
                                    <select class="form-control" wire:model="selectedTipoAhorro"
                                        wire:change="cambioTipoAhorro($event.target.value)">
                                        <option selected>- Seleccione -</option>
                                        @foreach ($tipoAhorros as $tipoAhorro)
                                        <option value="{{ $tipoAhorro->id }}">{{ $tipoAhorro->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            @if ($this->tipoAhorroCuenta == 'PROGRAMADO')
                            <div class="col-12">
                                <h5>Calculadora de Ahorro Programado <i class="fa fa-calculator"></i></h5>
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <label>Valor</label>
                                        <input type="number" class="form-control" placeholder="0.00"
                                            wire:model="cuenta_valor">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label>Tasa</label>
                                        <select class="form-control" wire:model="tipo_ahorros_programados_detalle_id">
                                            <option selected>- Seleccione -</option>
                                            @foreach ($this->interes as $val)
                                            <option value="{{ $val->id }}">
                                                {{ $val->interes . '% ' . 'DESDE ' . $val->rango_min . ' HASTA ' . $val->rango_max . ' DÍAS' }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label>Pago</label>
                                        <select id="pagoPlazo" wire:model="cuenta_pago" class="form-control">
                                            <option value=""> --SELECCIONE-- </option>
                                            <option value="C"> Al Cumplimiento </option>
                                            <option value="M"> Mensualizado </option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label>Forma de Pago</label>
                                        <select class="form-control" wire:model="forma_pago_id">
                                            <option value="">- Seleccione -</option>
                                            @foreach ($formasPago as $fp)
                                            <option value="{{ $fp->id }}">{{ $fp->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Para FP en Ahorro Programado -->
                                    @if ($pagoTipoBanco)
                                    <div class="col-12 col-md-4">
                                        <label># Comprobante</label>
                                        <input type="text" class="form-control" placeholder="# Comprobante" wire:model="comprobante" id="comprobante">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label>Bancos</label>
                                        <select class="form-control" wire:model="banco_id">
                                            <option value="">- Seleccione -</option>
                                            @foreach ($bancos as $ban)
                                            <option value="{{ $ban->id }}">{{ $ban->nombre }} #{{(empty($ban->numero_cuenta) ? '' : $ban->numero_cuenta)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label># Deposito</label>
                                        <input type="text" class="form-control" placeholder="# Deposito" wire:model="numero_deposito" id="numero_deposito">
                                    </div>
                                    @endif

                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-3"> <!-- Contenedor para el input y el checkbox -->
                                        <label class="small">Plazo Manual </label>
                                        <div class="input-group input-group-sm">
                                            @if ($this->activarManual)
                                            <input type="number" id="diasManual" wire:model="diasManual"
                                                class="form-control" placeholder="Días Plazo Opcional"
                                                wire:change="onDiasManualChange">
                                            @endif
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <input type="checkbox" id="usarManual" wire:model="usarManual"
                                                        wire:click="toggleUsarManual">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <label>Beneficiario</label>
                                        <select id="garante" wire:model="beneficiarioProgramado"
                                            class="form-control form-control-sm">

                                            <option value=""> --SELECCIONE--</option>
                                            @foreach($clientesTodos as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->nombres }}
                                                {{ $cliente->apellidos }} - {{ $cliente->numero_documento }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($this->tipoAhorroCuenta !== '')
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label># Cuenta
                                        {!! (Auth::user()->permiso_numero_cuentas) ? '<i class="fa fa-edit" style="color: #c58787;"></i>' : '' !!}</label>
                                    <input type="number" class="form-control" wire:model="numeroCuenta"
                                        placeholder="0000000001" maxlength="10"
                                        oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);"
                                        {{(Auth::user()->permiso_numero_cuentas) ? '' : 'readonly'}}
                                        style="font-size: 30px;"></input>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Crear Cuenta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL AHORROS RETIROS --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral2" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                {{-- <div class="modal-content {{ $this->tipo_transaccion == 'IN' ? 'bg-primary' : 'bg-danger' }}"> --}}
                <div class="modal-header">
                    <h4 class="modal-title"> {{ $this->tipo_transaccion == 'IN' ? 'AHORROS' : 'RETIROS' }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form wire:submit.prevent="storeAhorroRetiro">
                    <div class="modal-body">
                        <div class="modal-body">
                            @if ($errors->any())
                            <div class="callout callout-warning">
                                <h5>Verifica estas observaciones:</h5>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </div>
                            @endif
                            @if ($this->primerAhorroValores)
                            <div class="alert alert-warning alert-dismissible">
                                <h5><i class="icon fas fa-exclamation-triangle"></i> Importante!</h5>
                                Este es el primer deposito, por lo que los valores deben ser:<br>
                                @foreach ($this->detalleValores as $val)
                                <b>{{ $val['nombre'] }} : </b> ${{ $val['valor'] }}<br>
                                @endforeach
                                <b>TOTAL : </b> ${{ $this->sumaValores }}<br>
                            </div>
                            @endif
                            <div class="row mb-4">
                                <div class="col-12 col-md-8">
                                    <label>Valor</label>
                                    <input type="text" class="form-control" placeholder="0.00"
                                        wire:model="valor_transaccion" id="valor_transaccion">
                                </div>
                                @if ($this->tipo_transaccion == 'EG')
                                <div class="col-12 col-md-4">
                                    <label>Nota Débito</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="nota_debito"
                                            id="nota_debito">
                                        <label class="form-check-label">Débito Interno</label>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="row mb-4">
                                <div class="col-12 col-md-6">
                                    <label>Forma de Pago</label>

                                    <select class="form-control" wire:model="forma_pago_id"
                                        wire:change="cambioFormaPago($event.target.value)">
                                        <option selected>- Seleccione -</option>
                                        @foreach ($formasPago as $fp)
                                        <option value="{{ $fp->id }}">{{ $fp->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label># Comprobante</label>
                                    <input type="text" class="form-control" placeholder="# Comprobante"
                                        wire:model="comprobante" id="comprobante">
                                </div>
                            </div>

                            <!-- Para FP tipo Banco -->
                            @if ($pagoTipoBanco)
                            <div class="row mb-4">
                                <div class="col-12 col-md-6">
                                    <label>Bancos</label>
                                    <select class="form-control" wire:model="banco_id">
                                        <option value="">- Seleccione -</option>
                                        @foreach ($bancos as $ban)
                                        <option value="{{ $ban->id }}">{{ $ban->nombre }} #{{(empty($ban->numero_cuenta) ? '' : $ban->numero_cuenta)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label># Depósito</label>
                                    <input type="text" class="form-control" placeholder="# Deposito"
                                        wire:model="numero_deposito" id="numero_deposito">
                                </div>
                            </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-12">
                                    <label>Descripción</label>
                                    <textarea class="form-control" rows="2" placeholder="Descripcion"
                                        wire:model="observacion_transaccion"
                                        id="observacion_transaccion"></textarea>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label>Whatsapp</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="envio_whatsapp"
                                            id="envio_whatsapp" readonly>
                                        <label class="form-check-label">Envio al guardar.</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit"
                            class="btn btn-default">{{ $this->tipo_transaccion == 'IN' ? 'AHORRAR' : 'RETIRAR' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL WHATSAPP --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral3" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Envios de Whatsapp</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="card card-success card-outline direct-chat direct-chat-success shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title">Mensajes Enviados</h3>
                                <div class="card-tools">
                                    <span title="3 New Messages" class="badge bg-success">1</span>
                                    <button type="button" class="btn btn-tool" title="Contacts"
                                        data-widget="chat-pane-toggle">
                                        <i class="fas fa-comments"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="direct-chat-messages">

                                    <div class="direct-chat-msg">
                                        <div class="direct-chat-infos clearfix">
                                            <span class="direct-chat-name float-start">Codev</span>
                                            <span class="direct-chat-timestamp float-end">
                                                @if ($this->fechaWhatsapp !== '')
                                                {{ $this->fechaWhatsapp }}
                                                @else
                                                &nbsp
                                                @endif
                                            </span>
                                        </div>
                                        <img class="direct-chat-img" src="../codev/negro.png"
                                            alt="Message User Image">
                                        <div class="direct-chat-text">
                                            @if ($this->whatsapp !== '')
                                            {{ $this->whatsapp }}
                                            @else
                                            &nbsp
                                            @endif
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <div class="card-footer">
                                <form action="#" method="post">
                                    <div class="input-group">
                                        <input type="text" name="message" placeholder="Escribe un mensaje ..."
                                            class="form-control">
                                        <span class="input-group-append">
                                            <button type="submit" class="btn btn-success">Reenviar</button>
                                        </span>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ARCHIVOS --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral4" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Archivos</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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
                            <div class="col-12">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Nombre Archivo</label>
                                                <input type="text" class="form-control" wire:model="descrpcion"
                                                    placeholder="Nombre del Archivo">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile">Archivo</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input"
                                                            wire:model="archivo" id="exampleInputFile">
                                                        <label class="custom-file-label"
                                                            for="exampleInputFile">Elegir
                                                            Archivo</label>
                                                    </div>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"
                                                            wire:click="subirArchivo()">Subir</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card-body p-0">
                                    <table class="table table-sm">
                                        <tbody>
                                            @foreach ($filesCuenta as $fil)
                                            @if ($fil->formato == 'doc' || $fil->formato == 'docx')
                                            <tr>
                                                <td>
                                                    <a href="{{ $fil->path }}" class="btn-link text-secondary"
                                                        target="_blank"><i class="far fa-fw fa-file-word"></i>
                                                        {{ $fil->descripcion }} </a>
                                                </td>
                                                <td>
                                                    <a href="{{ $fil->path }}" target="_blank">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                                <td class="text-center"><a class="btn btn-danger btn-xs text-white"
                                                        wire:click="eliminarFile({{ $fil->id }})"><i
                                                            class="fa fa-times" aria-hidden="true"></i>
                                                        Eliminar</a></td>
                                            </tr>
                                            @elseif ($fil->formato == 'pdf')
                                            <tr>
                                                <td>
                                                    <a href="{{ $fil->path }}" class="btn-link text-secondary"
                                                        target="_blank"><i class="fa fa-file-pdf"
                                                            aria-hidden="true"></i>
                                                        {{ $fil->descripcion }} </a>
                                                </td>
                                                <td>
                                                    <a href="{{ $fil->path }}" target="_blank">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                                <td class="text-center"><a class="btn btn-danger btn-xs text-white"
                                                        wire:click="eliminarFile({{ $fil->id }})"><i
                                                            class="fa fa-times text-white" aria-hidden="true"></i>
                                                        Eliminar</a></td>
                                            </tr>
                                            @elseif ($fil->formato == 'jpg' || $fil->formato == 'jpeg' || $fil->formato == 'png' || $fil->formato == 'gif')
                                            <tr>
                                                <td>
                                                    <a href="{{ $fil->path }}" class="btn-link text-secondary"
                                                        target="_blank"><i class="far fa-fw fa-image "></i>
                                                        {{ $fil->descripcion }} </a>
                                                </td>
                                                <td>
                                                    <a href="{{ $fil->path }}" target="_blank">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                                <td class="text-center"><a class="btn btn-danger btn-xs text-white"
                                                        wire:click="eliminarFile({{ $fil->id }})"><i
                                                            class="fa fa-times text-white" aria-hidden="true"></i>
                                                        Eliminar</a></td>
                                            </tr>
                                            @endif
                                            @endforeach
                                        </tbody>
                                    </table>
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
    {{-- MODAL CARTOLAS --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral5" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Cartolas</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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
                            <div class="col-12">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">NÚMERO CARTOLA</label>
                                                <label class="small">

                                                    <a class="btn btn-sm btn-info" title="Pago Crédito"
                                                        wire:click="guardarNuevaCartola" style="color: white;">
                                                        <i class="fa fa-plus-circle"></i>
                                                    </a>
                                                </label>
                                                <input type="text" class="form-control"
                                                    wire:model="numeroNewCartola" placeholder="Numero cartola">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card-body p-0">
                                    <table class="table table-sm">
                                        <thead>
                                            <th>Número</th>
                                            <th>Fecha/Hora Creado</th>
                                            <th>Estado</th>
                                        </thead>
                                        <tbody>
                                            @foreach ($this->listaCartolas as $lista)
                                            <tr>
                                                <td>
                                                    @if($lista->status == 'ACTIVA')
                                                    <div class="form-group">
                                                        <input type="text" class="form-control"
                                                            wire:change="cambiarValorCartola({{ $lista->id }}, $event.target.value)"
                                                            placeholder="Numero cartola" style="height: 80%;"
                                                            value="{{$lista->numero}}">
                                                    </div>
                                                    @else
                                                    {{$lista->numero}}
                                                    @endif

                                                </td>
                                                <td style="width: 60%;">
                                                    {{$lista->date_create}}/{{$lista->hour_create}}
                                                </td>
                                                <td>
                                                    @if($lista->status == 'ACTIVA')
                                                    <small
                                                        class="badge bg-primary"></i>{{$lista->status }}</small>
                                                    @else
                                                    <small
                                                        class="badge bg-danger"></i>{{$lista->status }}</small>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
    {{-- MODAL PAGO PLAZOZ FIJO --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral6" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pagos Plazo Fijo</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                @if ($errors->any())
                <div class="callout callout-warning">
                    <h5>Verifica estas observaciones.</h5>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </div>
                @endif
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="card card-success card-outline direct-chat direct-chat-success shadow-sm">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Cuenta</label>
                                    <select class="form-control" wire:model="cuentaPagarPlazoLista">
                                        <option selected>- Seleccione -</option>
                                        @foreach ($this->cuentaPagarPlazo as $valCuenta)
                                        <option value="{{ $valCuenta->id }}">
                                            {{ $valCuenta->codigo}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="card-body" style="background-color: #ededf3;">
                                <div class="tab-content">
                                    <table class="table table-striped table-valign-middle"
                                        style="text-align: center;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>FECHA DE PAGO</th>
                                                <th>RENTABILIDAD</th>
                                                <th>ADMINISTRATIVOS (-)</th>
                                                <th>TOTAL</th>
                                                <th>ACCION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                @if($this->seleccionPlazoPagar)
                                                <td class="small">
                                                    <b>
                                                        1
                                                    </b>
                                                </td>
                                                <td colspan="3" style="text-align: center;">VALOR PRINCIPAL</td>
                                                <td>{{ $this->cuentaMaestraPlazoFijo->valor_movimiento }}</td>
                                                <td>
                                                    @if($this->pagarMaestra)
                                                    @if($this->cuentaMaestraPlazoFijo->status_programado == 'PENDIENTE')
                                                    <a wire:click="desembolsarValorPlazo({{ $this->cuentaMaestraPlazoFijo->id }}, '0')"
                                                        class="btn btn-success btn-sm" style="color: white;">
                                                        <i class="fa fa-dollar-sign"></i>
                                                    </a>
                                                    @else
                                                    <span class="badge bg-primary float-end">PAGADO</span>
                                                    @endif
                                                    @endif
                                                </td>
                                                @endif
                                            </tr>
                                            @foreach ($this->detallePlazoFijo as $key => $plazoPago)
                                            <tr>
                                                <td class="small">
                                                    <b>
                                                        {{ $key + 2 }}
                                                    </b>
                                                </td>
                                                <td>{{ $plazoPago->fecha_pago }}</td>
                                                <td>{{ $plazoPago->rentabilidad }}</td>
                                                <td>{{ $plazoPago->penalizado }}</td>
                                                <td>{{ $plazoPago->total }}</td>
                                                <td>
                                                    @if($plazoPago->status == 'PENDIENTE')
                                                    <a wire:click="desembolsarValorPlazo({{ $plazoPago->id }}, '1')"
                                                        class="btn btn-success btn-sm" style="color: white;">
                                                        <i class="fa fa-dollar-sign"></i>
                                                    </a>
                                                    @else
                                                    <span class="badge bg-primary float-end">PAGADO</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>

                    <a class="btn btn-primary " title=" Novar Plazo Fijo" wire:click="novarPlazoFijo()"
                        data-bs-toggle="modal" data-bs-target="#modalGeneral8">
                        <i class="nav-icon fa fa-handshake" style="color: white;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL HISTORIAL CUENTAS --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral7" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Historial de cuentas</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="modal-body">
                        <div class="card card-success card-outline direct-chat direct-chat-success shadow-sm">


                            <div class="card-body" style="background-color: #ededf3;">
                                <div class="tab-content">
                                    <table class="table table-striped table-valign-middle"
                                        style="text-align: center;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>FECHA CREACION</th>
                                                <th>CODIGO</th>
                                                <th>NOMBRE</th>
                                                <th>TIPO</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($this->historialCuenrtas as $key => $cuentas)
                                            <tr>
                                                <td class="small">
                                                    <b>
                                                        {{ $key + 1 }}
                                                    </b>
                                                </td>
                                                <td>{{ $cuentas->created_at }}</td>
                                                <td>{{ $cuentas->codigo }}</td>
                                                <td>{{ $cuentas->name }}</td>
                                                <td>
                                                    @if($cuentas->programado)
                                                    <a wire:click="verpagosPlazoFijo({{ $cuentas->id }})"
                                                        data-bs-toggle="modal" data-bs-target="#modalGeneral6">
                                                        Programado
                                                    </a>
                                                    @else
                                                    Ahorro
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach

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
            </div>
        </div>
    </div>
    {{-- Novar Plazo Fijo --}}

    <div wire:ignore.self class="modal fade" id="modalGeneral8" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Novar Plazo Fijo </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                @if ($errors->any())
                <div class="callout callout-warning">
                    <h5>Verifica estas observaciones.</h5>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </div>
                @endif
                <div class="modal-body">
                    <div class="card-body p-0" style="height: 55vh; overflow: auto;">
                        <div class="card card-success card-outline direct-chat direct-chat-success shadow-sm">
                            <h6><b>Resumen (PLAZO FIJO)</b></h6>
                            <div class="card-body" style="background-color: #ededf3;">
                                <div class="tab-content">
                                    <table class="table table-striped table-valign-middle"
                                        style="text-align: center;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>FECHA DE PAGO</th>
                                                <th>RENTABILIDAD</th>
                                                <th>ADMINISTRATIVOS (-)</th>
                                                <th>TOTAL</th>

                                            </tr>
                                        </thead>
                                        @php
                                        $total = 0;
                                        $total += (isset($this->cuentaMaestraPlazoFijo->valor_movimiento)) ? $this->cuentaMaestraPlazoFijo->valor_movimiento : 0;
                                        @endphp
                                        @php
                                        $totalValores = 0;

                                        @endphp
                                        <tbody>
                                            <tr>
                                                @if($this->seleccionPlazoPagar)

                                                @if($this->cuentaMaestraPlazoFijo->status_programado == 'PENDIENTE')
                                                <td class="small">
                                                    <b>
                                                        1
                                                    </b>
                                                </td>
                                                <td colspan="3" style="text-align: center;">VALOR PRINCIPAL</td>
                                                <td>$ {{ $this->cuentaMaestraPlazoFijo->valor_movimiento }}</td>
                                                @endif
                                                @endif
                                            </tr>
                                            @foreach ($this->detallePlazoFijo as $key => $plazoPago)

                                            <tr>
                                                @if($plazoPago->status == 'PENDIENTE')
                                                @php
                                                $total += $plazoPago->total;
                                                @endphp
                                                @php
                                                $totalValores += $plazoPago->total;
                                                @endphp
                                                <td class="small">
                                                    <b>
                                                        {{ $key + 2 }}
                                                    </b>
                                                </td>
                                                <td>{{ $plazoPago->fecha_pago }}</td>
                                                <td>{{ $plazoPago->rentabilidad }}</td>
                                                <td>{{ $plazoPago->penalizado }}</td>
                                                <td>$ {{ $plazoPago->total }}</td>

                                                @endif
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tbody>
                                            <tr>
                                                <td colspan="4" style="text-align: right;"><b>TOTAL</b></td>
                                                <td>$ {{$total }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card card-success card-outline direct-chat direct-chat-success shadow-sm">
                            <h6><b>Resumen Valores Novación</b></h6>
                            <p>Valor calculado <b>no pagado</b> para novación de plazo fijo <b>$ {{$total }}</b></p>
                            <p>Porcentaje de penalidad <b>{{$company->penalidad_plazo_fijo}} %</b> Total <b>$
                                    {{number_format((($company->penalidad_plazo_fijo / 100) * $totalValores), 2) }}</b>
                            </p>
                            <p>Valor sugerido
                                <b>{{number_format($total - (($company->penalidad_plazo_fijo / 100) * $totalValores), 2, '.', '') }}</b>
                            </p>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Cuenta</label>
                                    <select class="form-control" wire:model="cuentaPagarPlazoListaNovar">
                                        <option selected>- Seleccione -</option>
                                        @foreach ($this->cuentaPagarPlazo as $valCuenta)
                                        <option value="{{ $valCuenta->id }}">
                                            {{ $valCuenta->codigo}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row  mt-3">
                                <div class="col-12 col-md-4">
                                    <label>Valor Sugerido</label>
                                    <input type="number" class="form-control" placeholder="0.00"
                                        wire:model="valorCalculadoPrestamoFijo" id="valorCalculadoPrestamoFijo"
                                        disabled>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label>Valor Acreditar/Retirar</label>
                                    <input type="number" class="form-control" placeholder="0.00"
                                        wire:model="valor_acreditar_retirar" id="valor_acreditar_retirar"
                                        wire:blur="actualizarValorNovacion">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label>Total</label>
                                    <input type="number" class="form-control" placeholder="0.00"
                                        wire:model="valor_total_acreditar_retirar"
                                        id="valor_total_acreditar_retirar" disabled>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card card-success card-outline direct-chat direct-chat-success shadow-sm">
                            <h6><b>Detalles del nuevo Producto</b></h6>
                            <div class="col-12">
                                <h5>Calculadora de Ahorro Programado <i class="fa fa-calculator"></i></h5>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <label>Tasa de Interés</label>
                                        <select class="form-control"
                                            wire:model="tipo_ahorros_programados_detalle_id_novacion">
                                            <option value=""> --SELECCIONE-- </option>
                                            @foreach ($this->interes as $val)
                                            <option value="{{ $val->id }}">
                                                {{ $val->interes . '% ' . 'DESDE ' . $val->rango_min . ' HASTA ' . $val->rango_max . ' DÍAS' }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label>Pago</label>
                                        <select id="pagoPlazoNOvacion" wire:model="cuenta_pago_novacion"
                                            class="form-control">
                                            <option value=""> --SELECCIONE-- </option>
                                            <option value="C"> Al Cumplimiento </option>
                                            <option value="M"> Mensualizado </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-3"> <!-- Contenedor para el input y el checkbox -->
                                        <label class="small">Plazo Manual </label>
                                        <div class="input-group input-group-sm">
                                            @if ($this->activarManualNovacion)
                                            <input type="number" id="diasManualNovacion"
                                                wire:model="diasManualNovacion" class="form-control"
                                                placeholder="Días Plazo Opcional"
                                                wire:change="onDiasManualChangeNovacion">
                                            @endif
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <input type="checkbox" id="usarManualNovacion"
                                                        wire:model="usarManualNovacion"
                                                        wire:click="toggleUsarManualNovacion">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <label>Beneficiario</label>
                                        <select id="beneficiarioProgramadoNovar"
                                            wire:model="beneficiarioProgramadoNovar"
                                            class="form-control form-control-sm">

                                            <option value=""> --SELECCIONE--</option>
                                            @foreach($clientesTodos as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->nombres }}
                                                {{ $cliente->apellidos }} - {{ $cliente->numero_documento }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                    <a wire:click="storeCuentaNovacion()" class="btn btn-success btn-sm" style="color: white;">
                        <i class="fa fa-dollar-sign"></i> Novar
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL PAGO ENCAJES --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral9" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">LIQUIDACION ENCAJE </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                @if ($errors->any())
                <div class="callout callout-warning">
                    <h5>Verifica estas observaciones.</h5>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </div>
                @endif
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="card card-success card-outline direct-chat direct-chat-success shadow-sm">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Cuenta</label>
                                    <select class="form-control" wire:model="cuentasCliente">
                                        <option selected>- Seleccione -</option>
                                        @foreach ($this->cuentaClienteAhorro as $valCuenta)
                                        <option value="{{ $valCuenta->id }}">
                                            {{ $valCuenta->codigo}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="card-body" style="background-color: #ededf3;">
                                <div class="tab-content">
                                    <table class="table table-striped table-valign-middle"
                                        style="text-align: center;">
                                        <thead>
                                            <tr>
                                                <th>Fecha entrega encaje</th>
                                                <th>Valor</th>
                                                <th>Porcentaje retención</th>
                                                <th>Valor Retenido</th>
                                                <th>Porcentaje Plazo Fijo</th>
                                                <th>Valor Plazo Fijo</th>
                                                <th>Valor Entregar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{$this->fechaRetiroEncaje }} / {{$this->horaRetiroEncaje }}</td>
                                                <td>{{$this->ValorDepositoEncaje }} </td>
                                                <td>{{$this->porcentajeRetencion }} %</td>
                                                <td>{{$this->ValorRetenidoEncaje }} </td>
                                                <td>{{$this->porcentaje_plazo_fijo }} %</td>
                                                <td>{{$this->valor_plazo_fijo }} </td>
                                                <td>{{$this->ValorEntregarEncaje }} </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>

                    <a class="btn btn-primary " title="Entregar Valores" wire:click="entregarEncaje()"
                        data-bs-toggle="modal">
                        <i class="nav-icon fa fa-handshake" style="color: white;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL COMPROBANTES ENCAJES --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral10" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">LIQUIDACION ENCAJE </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                @if ($errors->any())
                <div class="callout callout-warning">
                    <h5>Verifica estas observaciones.</h5>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </div>
                @endif
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="card card-success card-outline direct-chat direct-chat-success shadow-sm">


                            <div class="card-body" style="background-color: #ededf3;">
                                <div class="tab-content">
                                    <table class="table table-striped table-valign-middle"
                                        style="text-align: center;">
                                        <thead>
                                            <tr>
                                                <th>Fecha entrega encaje</th>
                                                <th>Valor</th>
                                                <th>Porcentaje retención</th>
                                                <th>Valor Retenido</th>
                                                <th>Valor Entregar</th>
                                                <th>Accion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($this->encajesEntregados as $valEncajes)
                                            <tr>
                                                <td>{{$valEncajes->date_created}} / {{$valEncajes->hour_created}}</td>
                                                <td>{{$valEncajes->valor}}</td>
                                                <td>{{$valEncajes->porcentaje_retenido}} %</td>
                                                <td>{{$valEncajes->valor_retenido}}</td>
                                                <td>{{$valEncajes->valor_entregado}}</td>
                                                <td>

                                                    <a wire:click="imprimirEntregaEncaje({{ $valEncajes->id }})" class="btn btn-sm btn-warning" style="color: white;">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                </td>

                                            </tr>
                                            @endforeach
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
            </div>
        </div>
    </div>
    {{-- MODAL TICKET LETRA--}}
    <div wire:ignore.self class="modal fade" id="modalGeneral11" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Ticket </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form>
                    <div class="modal-body">
                        <iframe id="pdfFrame" style="display: none; width: 100%; height: 600px;"></iframe>
                    </div>
                </form>
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
    //Livewire evento whatsapp
    window.addEventListener('whatsapp', function(event) {
        var data = event.detail;
        var celular = data.telefono;
        var mensaje = data.mensaje;
        var baseUrl = 'https://web.whatsapp.com/send?phone=';
        var formattedPhoneNumber = celular.replace(/\s/g, '');
        var finalUrl = baseUrl + formattedPhoneNumber + '&text=' + encodeURIComponent(mensaje);
        var newWindow = window.open(finalUrl, '_blank', 'width=600,height=600');
        if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
            alert('Por favor, permita que se abra la ventana emergente para continuar.');
        }
    });

    function initializeSelect2() {
        $('.select2').select2();

        // Listener para cambiar el valor de Livewire al seleccionar una opción en Select2
        $('.select2').on('change', function(e) {
            var data = $(this).val();
            @this.set('garante', data);
        });
    }

    function imprimirCartilla(id) {
        @this.customer_selec;
        @this.cuenta_selec;
        $.ajax({
            url: "/customer/epson/" + @this.customer_selec + '/' + @this.cuenta_selec + '/' + id,
            type: 'GET',
            success: function(res) {
                //console.log(res);
                var data = JSON.parse(res);
                var texto = '';
                var cara = '';
                var ruta = 'A';
                $.each(data, function(key, value) {
                    if (value.detectado) {
                        console.log(value);
                        texto = '¿Estás seguro de imprimir el movimiento de ' + value.nombres + ' por el valor de ' + value.valor_movimiento + ' en la posición ' + value.posicion + '?';
                        ruta = value.cara;
                        if (value.cara == "A") {
                            cara = ' , En la cara Frontal';
                        } else {
                            cara = ' , En la cara Trasera';
                        }
                    }


                });

                Swal.fire({
                    title: 'Confirmar Impresión' + cara,
                    text: texto,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        if (ruta == "A") {
                            madarImpreasionA(res);
                        } else {
                            mandarImpresionB(res);
                        }
                    }
                });


            }
        });
    }

    function madarImpreasionA(res) {
        $.post("http://localhost/epson/prueba.php", {
            datos: res
        });
    }

    function mandarImpresionB(res) {
        $.post("http://localhost/epson/pruebaB.php", {
            datos: res
        });
    }

    window.addEventListener('mostrar-pdf', event => {
        let pdfBase64 = event.detail.pdf;
        let pdfUrl = "data:application/pdf;base64," + pdfBase64;

        let iframe = document.getElementById('pdfFrame');
        iframe.src = pdfUrl;
        iframe.style.display = 'block';

        setTimeout(() => {
            iframe.contentWindow.print(); // Abrir opciones de impresión
        }, 500);
    });
</script>
