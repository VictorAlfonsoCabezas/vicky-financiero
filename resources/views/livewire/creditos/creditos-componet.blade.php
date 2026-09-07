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

        @if ($this->id_seleccionado !== 0)
        <div class="col-lg-9 mt-6">
            <div class="card">
                <div class="card-header p-2">
                    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                        <ul class="navbar-nav">
                            <li class="nav-item d-none d-sm-inline-block">
                                <a type="button" wire:click="abrirCreditos" class="nav-link {{ $this->styleMostrarSimulador }}" style="color: {{ $this->styleMostrarSimulador == '' ? 'black' : 'white' }}">
                                    Simulador y Creditos
                                </a>
                            </li>

                            <!--<li class="nav-item d-none d-sm-inline-block">
                                <a type="button" wire:click="abrirCalificacion" class="nav-link {{ $this->StyleMostrarCalificacion }}" style="color: {{ $this->StyleMostrarCalificacion == '' ? 'black' : 'white' }}">
                                    Información Socio Mensual
                                </a>
                            </li>-->

                            <li class="nav-item d-none d-sm-inline-block">
                                <a type="button" wire:click="abrirListaCreditos" class="nav-link {{ $this->StyleMostrarListaCreditos }}" style="color: {{ $this->StyleMostrarListaCreditos == '' ? 'black' : 'white' }}">
                                    Lista de Creditos
                                </a>
                            </li>

                        </ul>
                    </nav>
                </div>
                @if ($this->mostrarSimulador)
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
                                <div class="col-4">
                                    <label class="small">Fecha Prestamo</label>
                                    <div class="input-group input-group-sm">
                                        <input id="fecha_prestamo" type="date" wire:model="fecha_prestamo" class="form-control" placeholder="Fecha Prestamo">
                                    </div>
                                </div>

                            </div>
                            <div class="row  mt-3">
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
                                    <label class="small">Valor Ahorrar</label>
                                    <div class="input-group input-group-sm">
                                        <input id="valor_ahorrar_credito" type="number" wire:model="valor_ahorrar_credito" class="form-control" placeholder="Valor">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="small">Generar </label>
                                    <select wire:model="generar" class="form-control form-control-sm" wire:change="obtenerDatosFamilares" wire:key="generar">
                                        <option value=""> --SELECCIONE--</option>
                                        <option value="0">Simular</option>
                                        <option value="1">Crear</option>
                                    </select>
                                </div>

                            </div>
                            <hr>
                            <label class="small">GARANTES </label>
                            <div class="form-group">
                                <table style="width: 50%;">
                                    <tr>
                                        <td style="width: 80%;">
                                            <div class="form-group">
                                                <label class="small">Garante</label>
                                                <select id="garante" wire:model="garante" class="form-control form-control-sm">
                                                    <option value=""> --SELECCIONE--</option>
                                                    @foreach($garantes as $garante)
                                                    <option value="{{ $garante->id }}">{{ $garante->apellidos }} {{ $garante->nombres }} - {{ $garante->numero_documento }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td style="width: 20%;padding-top: 14px;">
                                            <a wire:click="agregarGarante()" class="btn bg-success btn-xs" style="color: white;">
                                                <i class="fa fa-plus"></i>
                                            </a>

                                        </td>
                                    </tr>
                                    <tr>

                                    </tr>
                                </table>
                            </div>
                            <div class="form-group">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Garante</th>
                                            <th>Cónyuge</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($this->garantesArreglo as $garante)
                                        <tr>
                                            <td>{{$garante['customerName']}}</td>
                                            <td>{{$garante['customerConyugeNombre']}}</td>
                                            <td>
                                                <a wire:click="eliminarGarante({{ $garante['customerId'] }})" class="btn bg-danger btn-xs" style="color: white;">
                                                    <i class="fa fa-plus"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <hr>
                            <div class="row  mt-3">

                                <div class="user-block">
                                    <span class="username">
                                        <a>{{ $this->mensajeCreacion }}</a>
                                    </span>
                                </div>

                                @if ($this->verBtnGenerar == true)
                                <div class="col-12">
                                    <button type="button" wire:click="simular" class="btn btn-block btn-primary btn-xs" wire:loading.attr="disabled">Generar</button>
                                </div>
                                @endif
                            </div>
                            <div class="row  mt-3">
                                @if ($this->verPdf == true)
                                <div class="col-12">
                                    <!-- <a href="{{ URL::to('/creditos/pdfLetras/' . $this->valor_simulador . '/' . $this->cuotas_simulador . '/' . $this->fecha_prestamo . '/' . $this->tipo_simulador . '/' . $this->prestamo_simulador . '/' . $this->generar . '/' . $this->id_seleccionado . '/' . $this->codigoPrestamo) }}" class="btn btn-info btn-xs" style="color: white;"><i class="fas fa-eye"></i></a>-->
                                    <a wire:click="generarPdfLetrasCreditos" class="btn btn-info btn-xs" style="color: white;"><i class="fas fa-eye"></i></a>
                                </div>
                                @endif
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
                @endif

                @if ($this->mostrarListaCreditos)
                <div class="card-body" style="background-color: #ededf3;">
                    <div class="tab-content">

                        <div class="card-body table-responsive p-0">
                            <div class="user-block">
                                <span class="username">
                                    <a>{{ $this->nombres }} {{ $this->apellidos }}</a>
                                </span>
                                <span class="description">Contacto {{ $this->telefono }}</span>
                                <span class="username">
                                    <a>Total Créditos {{ $this->totalCreditos }}</a>
                                </span>
                            </div>
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>CODE</th>
                                        <th>PRESTAMO</th>
                                        <th>CUOTAS</th>
                                        <th>TIPO</th>
                                        <th>VALOR CREDITO</th>
                                        <th>VALOR GASTOS</th>
                                        <th>DEBE</th>
                                        <th>ESTADO</th>
                                        <th>DOCUMENTACION</th>
                                        <th>ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listaCreditos as $credit)
                                    <tr>
                                        <td class="small">
                                            <b>
                                                {{ $credit->code }}
                                            </b>
                                            @if(Auth::user()->eliminar_creditos)
                                            <a class="btn btn-xs btn-danger" title="Negar Credito {{ $credit->id }}" wire:click="borrarTodoCredito({{ $credit->id }})" style="color: white;">
                                                <i class="far fa-trash-alt" style="color: white;"></i> Borrar
                                            </a>
                                            @endif


                                        </td>
                                        <td>
                                            {{ $credit->nombrePrestamo }}
                                        </td>
                                        <td>
                                            {{ $credit->cuotas_pagar }}
                                        </td>
                                        <td class="small">
                                            {{ $credit->tipo_pago }}
                                        </td>
                                        <td>
                                            $ {{ $credit->valor_solicitado }}
                                        </td>
                                        <td>
                                            $ {{ $credit->suma_valores_gastos_prestamo }}
                                        </td>
                                        <td>
                                            $ {{ $credit->valorDeve }}
                                        </td>
                                        <td>
                                            @if ($credit->totalLetrasImpagas > 0)
                                            <span class="badge badge-{{ config('constants.STATUS_PRESTAMO.' . $credit->status . '.color') }}">
                                                {{ config('constants.STATUS_PRESTAMO.' . $credit->status . '.label') }}
                                            </span>
                                            @else
                                            <span class="badge bg-success"> PAGADO </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($credit->status == 'ENTREGADO' || $credit->status =='APROBADO' || $credit->status =='ENPRUEBA')
                                            <a class="btn btn-info btn-sm" href="{{ URL::to('credit/printDetalleCredito/' . $credit->id) }}" title="Tabla de Amortización">
                                                <i class="far fa-file-pdf"></i>
                                            </a>
                                            @if ($credit->contrato)
                                            <a class="btn btn-info btn-sm" wire:click="exportarContrato({{ $credit->id }})" title="Contrato">
                                                <i class="far fa-file-pdf"></i>
                                            </a>
                                            @endif

                                            @if ($credit->letraCambio)
                                            <a class="btn btn-success btn-sm" href="{{ URL::to('credit/print/letra/' . $credit->id) }}" title="Letra de Cambio">
                                                <i class="far fa-file-alt"></i>
                                            </a>
                                            @endif
                                            @if ($credit->pagare)
                                            <a class="btn btn-warning btn-sm" href="{{ URL::to('credit/print/pagare/' . $credit->id) }}" title="Pagaré">
                                                <i class="far fa-file-alt"></i>
                                            </a>
                                            @endif
                                            <a class="btn btn-sm btn-danger" href="{{ URL::to('credit/pdfVerEncaje/' . $credit->id) }}" title="Encaje">
                                                <i class="far fa-file-alt"></i>
                                            </a>
                                            @elseif($credit->totalLetrasImpagas == 0)
                                            <a class="btn btn-info btn-sm" href="{{ URL::to('credit/printDetalleCredito/' . $credit->id) }}" title="Tabla de Amortización">
                                                <i class="far fa-file-pdf"></i>
                                            </a>
                                            @if ($credit->letraCambio)
                                            <a class="btn btn-success btn-sm" href="{{ URL::to('credit/print/letra/' . $credit->id) }}" title="Letra de Cambio">
                                                <i class="far fa-file-alt"></i>
                                            </a>
                                            @endif
                                            @if ($credit->pagare)
                                            <a class="btn btn-warning btn-sm" href="{{ URL::to('credit/print/pagare/' . $credit->id) }}" title="Pagaré">
                                                <i class="far fa-file-alt"></i>
                                            </a>
                                            @endif
                                            <a class="btn btn-sm btn-danger" href="{{ URL::to('credit/pdfVerEncaje/' . $credit->id) }}" title="Encaje">
                                                <i class="far fa-file-alt"></i>
                                            </a>
                                            @endif
                                        </td>
                                        <td class="text-center">

                                            @if($credit->status == 'APROBADO')
                                            @if(Auth::user()->permiso_reversar_creditos)



                                            <a wire:click="notificacionReversoCredito({{ $credit->id }})" class="btn btn-sm btn-warning" style="color: white;">
                                                <i class="fas fa-thumbs-down"></i> Reversar Aprobación
                                            </a>

                                            @endif
                                            @endif

                                            @if($credit->valor_liquidar > 0)
                                            @if ($credit->totalLetrasImpagas > 0)
                                            <a wire:click="novacionNotificar({{ $credit->id }})" class="btn btn-sm btn-success" style="color: white;">
                                                <i class="fas fa-hand-holding-usd"></i>Pagar Valores
                                            </a>
                                            @endif
                                            @endif

                                            @if($credit->valor_novacion > 0)
                                            <a class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalGeneral4" title="Pago Crédito" wire:click="generarNuevoNovacion({{ $credit->id }})" style="color: white;">
                                                <i class="far fa-money-bill-alt"></i>Generar Novación
                                            </a>

                                            @endif

                                            @if ($credit->totalLetrasImpagas > 0)
                                            @if ($credit->status == 'APROBADO')
                                            <a data-bs-toggle="modal" data-bs-target="#modalGeneral6" class="btn btn-xs btn-success" title="Entregarc Credito {{ $credit->code }}" wire:click="entregarDineroModal({{ $credit->id }})" style="color: white;">
                                                <i class="fas fa-hand-holding-usd" style="color: white;"></i> Entregar Dinero
                                            </a>
                                            @endif
                                            @if ($credit->status == 'ENPRUEBA')
                                            <button disabled class="btn btn-xs btn-secondary" title="Crédito en prueba. Faltan pagos de encaje/cuotas para el desembolso.">
                                                <i class="fas fa-hand-holding-usd"></i> Entregar Dinero
                                            </button>
                                            @endif
                                            <!--@if ($credit->status == 'ENTREGADO' || $credit->status == 'ENPRUEBA')
                                            <a class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalPagos" title="Pago Crédito" wire:click="cargarDatosPrestamo({{ $credit->id }})" style="color: white;">
                                                <i class="far fa-money-bill-alt"></i> Pagar
                                            </a>
                                            @if(Auth::user()->permiso_credito_aprobar)
                                            <a class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalGeneral3" title="Pago Crédito" wire:click="cargarDatosNovacion({{ $credit->id }})" style="color: white;">
                                                <i class="far fa-money-bill-alt"></i> Novación
                                            </a>
                                            @endif
                                            <a class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#modalGeneral5" title="Liquidar Credito" wire:click="liquidarDeuda({{ $credit->id }})" style="color: white;">
                                                <i class="fas fa-unlock-alt"></i> Liquidar Credito
                                            </a>
                                            @endif-->
                                            @if ($credit->status == 'ENTREGADO' || $credit->status == 'ENPRUEBA')

                                                @if ($credit->liquidacion_pendiente > 0)

                                                <span class="badge bg-warning" style="padding: 8px; font-size: 11px;">
                                                    <i class="fas fa-clock"></i> Liquidación en Revisión
                                                </span>

                                                @else

                                                <a class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalPagos" title="Pago Crédito" wire:click="cargarDatosPrestamo({{ $credit->id }})" style="color: white;">
                                                    <i class="far fa-money-bill-alt"></i> Pagar
                                                </a>

                                                @if(Auth::user()->permiso_credito_aprobar)
                                                <a class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalGeneral3" title="Pago Crédito" wire:click="cargarDatosNovacion({{ $credit->id }})" style="color: white;">
                                                    <i class="far fa-money-bill-alt"></i> Novación
                                                </a>
                                                @endif

                                                <a class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#modalGeneral5" title="Liquidar Credito" wire:click="liquidarDeuda({{ $credit->id }})" style="color: white;">
                                                    <i class="fas fa-unlock-alt"></i> Liquidar Credito
                                                </a>

                                                @endif

                                            @endif
                                            @if ($credit->status == 'PENDIENTE')
                                            @if(Auth::user()->permiso_credito_aprobar)
                                            <a class="btn btn-xs btn-success" title="Aprobar Credito {{ $credit->code }}" wire:click="aprobarCredito({{ $credit->id }})" style="color: white;">
                                                <i class="fas fa-user-check" style="color: white;"></i> Aprobar
                                            </a>
                                            <a data-bs-toggle="modal" data-bs-target="#modalGeneral2" class="btn btn-xs btn-info" title="Editar Credito {{ $credit->code }}" style="color: white" wire:click="editarCredito({{ $credit->id }})">
                                                <i class="fas fa-pen-fancy"></i> Editar
                                            </a>
                                            @endif
                                            <a class="btn btn-xs btn-danger" title="Negar Credito {{ $credit->code }}" wire:click="negarCredito({{ $credit->id }})" style="color: white;">
                                                <i class="far fa-trash-alt" style="color: white;"></i> Rechazar
                                            </a>
                                            @endif
                                            @else
                                            <a class="btn btn-success btn-sm" wire:click="generatePdfLiquidado({{ $credit->id }})" title="Recibo de Pago">
                                                <i class="far fa-file-alt"></i>
                                            </a>

                                            @endif
                                            <a data-bs-toggle="modal" data-bs-target="#modalGeneral1" wire:click="seleccionarCredito({{ $credit->id }})" class="btn btn-sm btn-warning">
                                                <i class="fa fa-paperclip"></i>
                                            </a>
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                @endif
                @if ($this->mostrarCalificacion)
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
                            <div class="row  mt-3">
                                <div class="col-4">
                                    <label class="small">Ingresos Fijo</label>
                                    <div class="input-group input-group-sm">
                                        <input id="ingresos_netos" type="number" wire:model="ingresos_netos" class="form-control" placeholder="Ingresos Netos">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="small">Gastos Fijos</label>
                                    <div class="input-group input-group-sm">
                                        <input id="gastos_mensuales" type="number" wire:model="gastos_mensuales" class="form-control" placeholder="Gastos Mensuales">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="small">Activos</label>
                                    <div class="input-group input-group-sm">
                                        <input id="activos" type="number" wire:model="activos" class="form-control" placeholder="Activos">
                                    </div>
                                </div>
                            </div>
                            <div class="row  mt-3">
                                <div class="col-4">
                                    <label class="small">Pasivos</label>
                                    <div class="input-group input-group-sm">
                                        <input id="pasivos" type="number" wire:model="pasivos" class="form-control" placeholder="Pasivos">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="small">Prestamo </label>
                                    <select id="tasa_interes" wire:model="tasa_interes" class="form-control form-control-sm">
                                        <option value=""> --SELECCIONE--</option>
                                        @foreach ($prestamosListado as $opcion)
                                        <option value="{{ $opcion->id }}">{{ $opcion->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="small">Plazo Prestamo</label>
                                    <div class="input-group input-group-sm">
                                        <input id="plazo_prestamo" type="number" wire:model="plazo_prestamo" class="form-control" placeholder="Plazo Prestamo">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row  mt-3">


                                <div class="col-12">
                                    <button type="button" wire:click="calcularCalificacion" class="btn btn-block btn-primary btn-xs" wire:loading.attr="disabled">Generar</button>
                                </div>

                            </div>
                            <div class="row  mt-3">




                            </div>

                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- MODAL PAGOS --}}
    <div wire:ignore.self class="modal fade" id="modalPagos" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Letras del Crédito <b>{{ $this->headerPagoCode }}</b></h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form>
                    <div class="modal-body">
                        <section class="content">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-lg-7 col-12">
                                        <div class="card-body p-0" style="height: 100vh; overflow: auto;">
                                            <table class="table table-striped table-valign-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Pagar</th>
                                                        <th>No.</th>
                                                        <th>Fecha</th>
                                                        <th>Valor</th>
                                                        <!--
                                                        <th>Interés Generado</th>
                                                        -->
                                                        <th>Interés Mora</th>
                                                        <th>Total</th>
                                                        <th>Pagado</th>
                                                        <th>Estado</th>
                                                        <th>Acciones</th>
                                                        <th>Aviso</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($this->detallePago as $detalle)
                                                    <tr>
                                                        <td>
                                                            @if ($detalle->status == 'PENDIENTE')
                                                            @if ($detalle->armado)
                                                            <a class="btn btn-primary btn-sm" title="Pagar Letra {{ $detalle->numero_cuota }}" wire:click="consultarDatosLetra({{ $detalle->id }})">
                                                                <i class="fas fa-check" style="color: white;"></i>
                                                            </a>
                                                            @else
                                                            <a class="btn btn-primary btn-sm" title="Pagar Letra {{ $detalle->numero_cuota }}" wire:click="consultarDatosLetra({{ $detalle->id }})" style="pointer-events: none;opacity: 0.5;">
                                                                <i class="fas fa-check" style="color: white;"></i>
                                                            </a>
                                                            @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ $detalle->numero_cuota }}
                                                        </td>
                                                        <td>
                                                            {{ $detalle->date_vencimiento }}
                                                        </td>
                                                        <td>
                                                            {{ $detalle->valor_cuota }}
                                                        </td>
                                                        <!--
                                                        <td>
                                                            {{ $detalle->interes_periodo }}
                                                        </td>
                                                        -->
                                                        <td>
                                                            {{ $detalle->interes_mora }}
                                                        </td>
                                                        <td>
                                                            {{ $detalle->pago_general }}
                                                        </td>
                                                        <td>
                                                            @if ($detalle->valor_pagado > 0)
                                                            $ {{ $detalle->valor_pagado }}
                                                            @else
                                                            $ ---
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($detalle->status == 'PENDIENTE')
                                                            <span class="badge bg-warning">{{ $detalle->status }}</span>

                                                            @elseif ($detalle->status == 'STAND BY')
                                                            <span class="badge bg-info">{{ $detalle->status }}</span>

                                                            @elseif ($detalle->status == 'PAGADA')
                                                            <span class="badge bg-success">{{ $detalle->status }}</span>

                                                            @else
                                                            <span class="badge bg-secondary">{{ $detalle->status }}</span>
                                                            @endif
                                                        </td>

                                                        <td>
                                                            @if ($detalle->status == 'PAGADA')
                                                            <a class="btn btn-default btn-sm" wire:click="envioWhatsapp({{ $detalle->id }})" title="enviar Whatsapp Web">
                                                                <i class="fa fa-paper-plane"></i>
                                                            </a>
                                                            <a class="btn btn-default btn-sm" href="{{ URL::to('credit/pdfCuotaVer/' . $detalle->id . '/' . $this->customerID) }}" title="verTicket">
                                                                <i class="fas fa-print"></i>
                                                            </a>

                                                            <a class="btn btn-primary btn-sm" title="Entregar Valores" wire:click="imprimirTicket({{ $detalle->id }})"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalGeneral7">
                                                                <i class="fas fa-print" style="color: white;"></i>
                                                            </a>



                                                            <!--<button onclick="javascript:imprimirTicket({{ $detalle->id }});" class="btn btn-default btn-sm"><i class="fas fa-print"></button>-->
                                                            @endif
                                                            @if ($detalle->status == 'PENDIENTE')
                                                            <a class="btn btn-default btn-sm" wire:click="notificar({{ $detalle->id  }}, '{{ $detalle->notificado }}')" title="Notificar">
                                                                <i class="fa fa-paperclip"></i>
                                                            </a>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($detalle->notificado >= 1)
                                                            <a class="badge bg-info" href="{{ URL::to('credit/pdfNotificaciones/' . $detalle->id . '/' . $this->customerID) }}" title="verTicket">
                                                                {{ $detalle->notificado }}
                                                            </a>
                                                            @else
                                                            <span class="badge bg-info">{{ $detalle->notificado }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-12">
                                        <div class="row col col-lg-12">
                                            <table class="table m-0">
                                                <tbody>
                                                    <tr>
                                                        <td>NÚMERO DE CUOTA {{ $this->numeroDeLetra }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="small">FORMA DE PAGO </label>
                                                            <div class="form-group">
                                                                <table style="width: 100%;">
                                                                    <tr>
                                                                        <td style="width: 80%;">
                                                                            <div class="form-group">
                                                                                <select wire:model="formaPago" class="form-control form-control-sm">
                                                                                    <option value=""> --SELECCIONE--</option>
                                                                                    @foreach($formasPago as $value)
                                                                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                        <td style="width: 20%;">
                                                                            <a wire:click="agregarFormaPago()" class="btn bg-success btn-xs" style="color: white;">
                                                                                <i class="fa fa-plus"></i>
                                                                            </a>

                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td style="width: 80%;" colspan="2">
                                                                            <div class="col-12">
                                                                                <label class="small">Bancos</label>
                                                                                <select class="form-control form-control-sm" wire:model="banco_pago_id">
                                                                                    <option value=""> --SELECCIONE--</option>
                                                                                    @foreach ($bancos as $ban)
                                                                                    <option value="{{ $ban->id }}">{{ $ban->nombre }} #{{(empty($ban->numero_cuenta) ? '' : $ban->numero_cuenta)}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </td>

                                                                    </tr>

                                                                    @if($mostrarComprobante)
                                                                    <tr>
                                                                        <td colspan="2">
                                                                            <div class="col-12">
                                                                                <label class="small"># Comprobante</label>
                                                                                <div class="input-group input-group-sm">
                                                                                    <input type="text" wire:model="numero_comprobante" class="form-control" placeholder="Número de Comprobante">
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    @endif

                                                                    <tr>
                                                                        <td colspan="2">
                                                                            <div class="col-12">
                                                                                <label class="small">Valor</label>
                                                                                <div class="input-group input-group-sm">
                                                                                    <input id="valor_forma_pago" type="number" wire:model="valor_forma_pago" class="form-control" placeholder="Valor Cancelar">
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            <div class="form-group">
                                                                <table class="table table-hover">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Forma</th>
                                                                            <th># Comprobante</th>
                                                                            <th>Valor</th>
                                                                            <th>Acción</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($this->formasPagarCredito as $index => $formas)
                                                                        <tr>
                                                                            <td>{{ $formas['formaName'] }}</td>
                                                                            <td>{{ $formas['numero_comprobante'] ?? '' }}</td>
                                                                            <td>{{ $formas['valor'] }}</td>
                                                                            <td>
                                                                                <a wire:click="eliminarFormaPagar({{ $index }})" class="btn bg-danger btn-xs" style="color: white;">
                                                                                    <i class="fa fa-trash"></i>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="small">FECHA DE PAGO</label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="date" wire:model="fechaPagoLetra" class="form-control" placeholder="Fecha Pago" readonly="">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="small">INTERES GENERADO (MORA)</label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" wire:model="interesMora" class="form-control" placeholder="Interes Mora" wire:blur="actualizarValorPagar">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @if($company->genera_gastos_cobranza)
                                                    <tr>
                                                        <td>
                                                            <label class="small">GASTOS DE COBRANZA</label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" wire:model="gasto_cobranza" class="form-control" placeholder="Interes Mora" wire:blur="actualizarValorPagar" readonly>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                    <tr>
                                                        <td>
                                                            <label class="small">VALOR AHORRO</label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" wire:model="valor_ahorro" class="form-control" placeholder="" disabled>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="small">VALOR SUGERIDO A PAGAR</label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" wire:model="valorPagarLetra" class="form-control" placeholder="" disabled>
                                                            </div>
                                                            <p>{!! $this->descLetra !!}</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="small">OBSERVACION</label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" wire:model="observacionPagoLetra" class="form-control" placeholder="OBSERVACION">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            @if($this->verCuentaAhorroPrestamo == true)
                                                            <p style="color: red;"> No puede continuar con el pago no tiene una cuenta configurada para ingresar el ahorro</p>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            @if ($errors->any())
                                                            <div class="callout callout-warning">
                                                                <h5>Verifica estas observaciones.</h5>
                                                                @foreach ($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                                @endforeach
                                                            </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            @if ($mensaje)
                                                            <div class="alert alert-danger">{{ $mensaje }}
                                                            </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            @if ($this->verBotonlistaPrestamos == false)
                                                            <div class="alert alert-danger">No se puede realizar
                                                                esta operacion hasta que tenga una caja abierta
                                                            </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-dark btn-lg" data-bs-dismiss="modal" wire:click="reiniciarVista()">Cerrar</button>
                            <a class="btn btn-danger btn-lg" title="Pagar Letra" wire:click="realizarPago()">
                                <i class="fas fa-money-bill-alt" style="color: white;">Pagar</i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL ARCHIVOS --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral1" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Archivos</h4>
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
                            <div class="col-12">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Nombre Archivo</label>
                                                <input type="text" class="form-control" wire:model="descripcion" placeholder="Nombre del Archivo">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile">Archivo</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input onchange="validateFile(this)" type="file" class="custom-file-input" wire:model.defer="archivo" id="exampleInputFile">
                                                        <label class="custom-file-label" for="exampleInputFile">Elegir
                                                            Archivo</label>
                                                    </div>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" wire:click="subirArchivo()">Subir</span>
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
                                            @foreach ($filesCreditos as $fil)
                                            @if ($fil->formato == 'doc' || $fil->formato == 'docx')
                                            <tr>
                                                <td>
                                                    <a href="{{ $fil->path }}" class="btn-link text-secondary" target="_blank"><i class="far fa-fw fa-file-word"></i>
                                                        {{ $fil->descripcion }} </a>
                                                </td>
                                                <td>
                                                    <a href="{{ $fil->path }}" target="_blank">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                                <td class="text-center"><a class="btn btn-danger btn-xs text-white" wire:click="eliminarFile({{ $fil->id }})"><i class="fa fa-times" aria-hidden="true"></i>
                                                        Eliminar</a></td>
                                            </tr>
                                            @elseif ($fil->formato == 'pdf')
                                            <tr>
                                                <td>
                                                    <a href="{{ $fil->path }}" class="btn-link text-secondary" target="_blank"><i class="fa fa-file-pdf" aria-hidden="true"></i>
                                                        {{ $fil->descripcion }} </a>
                                                </td>
                                                <td>
                                                    <a href="{{ $fil->path }}" target="_blank">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                                <td class="text-center"><a class="btn btn-danger btn-xs text-white" wire:click="eliminarFile({{ $fil->id }})"><i class="fa fa-times text-white" aria-hidden="true"></i> Eliminar</a></td>
                                            </tr>
                                            @elseif ($fil->formato == 'jpg' || $fil->formato == 'jpeg' || $fil->formato == 'png' || $fil->formato == 'gif')
                                            <tr>
                                                <td>
                                                    <a href="{{ $fil->path }}" class="btn-link text-secondary" target="_blank"><i class="far fa-fw fa-image "></i>
                                                        {{ $fil->descripcion }} </a>
                                                </td>
                                                <td>
                                                    <a href="{{ $fil->path }}" target="_blank">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                                <td class="text-center"><a class="btn btn-danger btn-xs text-white" wire:click="eliminarFile({{ $fil->id }})"><i class="fa fa-times text-white" aria-hidden="true"></i> Eliminar</a></td>
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

    {{-- MODAL EDITAR CREDITO --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral2" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> EDITAR CREDITO {{ $this->codigoEdit }}</h4>
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
                        <div class="row">
                            <div class="row col-12">
                                <div class="col-4">
                                    <label class="small">Valor</label>
                                    <div class="input-group input-group-sm">
                                        <input id="valorEditar" type="number" wire:model="valorEditar" class="form-control" placeholder="Valor">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="small">Cuotas</label>
                                    <div class="input-group input-group-sm">
                                        <input id="cuotasEditar" type="number" wire:model="cuotasEditar" class="form-control" placeholder="Número de Cuotas">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="small">Fecha Prestamo</label>
                                    <div class="input-group input-group-sm">
                                        <input id="fechaEditar" type="date" wire:model="fechaEditar" class="form-control" placeholder="Fecha Prestamo">
                                    </div>
                                </div>

                            </div>
                            <div class="row col-12">
                                <div class="col-4">
                                    <label class="small">Tipo </label>
                                    <select id="tipoCreditoEditar" wire:model="tipoCreditoEditar" class="form-control form-control-sm" wire:change="obtenerDatos" wire:key="tipoCreditoEditar">
                                        <option value=""> --SELECCIONE--</option>
                                        <option value="F">FRANCESA</option>
                                        <option value="A">ALEMANA</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="small">Prestamo </label>
                                    <select id="prestamoEditar" wire:model="prestamoEditar" class="form-control form-control-sm">
                                        <option value="" selected> --SELECCIONE--</option>
                                        @foreach ($opcionesPrestamoEditar as $opcion)
                                        <option value="{{ $opcion->id }}">{{ $opcion->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="small">Porcentaje interes</label>
                                    <div class="input-group input-group-sm">
                                        <input id="porcentajeEditar" type="number" wire:model="porcentajeEditar" class="form-control" placeholder="0.00" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <a class="btn btn-success" data-bs-dismiss="modal" title="Editar" wire:click="guardarEdicionPrestamo()">
                            <i class="fas fa-save" style="color: white;"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- MODAL NOVACION --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral3" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> GENERAR NOVACION DEL CREDITO {{$this->creditoIdnovacionDatos}}</h4>
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
                        <div class="row">
                            <div class="row col-12">
                                <div class="col-4">
                                    <label class="small">Valores Vencidos</label>
                                    <div class="input-group input-group-sm">
                                        <input id="valorVencido" type="number" wire:model="valorVencido" class="form-control" placeholder="Valor Vencido">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="small">Valores Cumplidos</label>
                                    <div class="input-group input-group-sm">
                                        <input id="valorCumplido" type="number" wire:model="valorCumplido" class="form-control" placeholder="valor cumplido">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="small">Capital Amortizado</label>
                                    <div class="input-group input-group-sm">
                                        <input id="valorCapitalAmortizado" type="number" wire:model="valorCapitalAmortizado" class="form-control" placeholder="valor amortizado">
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <a class="btn btn-success" data-bs-dismiss="modal" title="GENERAR" wire:click="guardarNovacion()">
                            <i class="fas fa-save" style="color: white;"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- MODAL NOVACION Generar--}}
    <div wire:ignore.self class="modal fade" id="modalGeneral4" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> GENERAR NUEVO CREDITO DE NOVACION CON LOS VALORES {{$this->valorprestamoNova }}</h4>
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
                        <div class="row">
                            <div class="row col-12">
                                <div class="col-4">
                                    <label class="small">Cuotas</label>
                                    <div class="input-group input-group-sm">
                                        <input id="cuotasNovaNuew" type="number" wire:model="cuotasNovaNuew" class="form-control" placeholder="">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="small">Tipo </label>
                                    <select id="tipoNovaCredito" wire:model="tipoNovaCredito" class="form-control form-control-sm" wire:change="obtenerDatosNova" wire:key="tipoNovaCredito">
                                        <option value=""> --SELECCIONE--</option>
                                        <option value="F">FRANCESA</option>
                                        <option value="A">ALEMANA</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="small">Prestamo </label>
                                    <select id="prestamoNova" wire:model="prestamoNova" class="form-control form-control-sm">
                                        <option value=""> --SELECCIONE--</option>
                                        @foreach ($opcionesPrestamo as $opcion)
                                        <option value="{{ $opcion->id }}">{{ $opcion->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <a class="btn btn-success" data-bs-dismiss="modal" title="GENERAR" wire:click="guardarCreditoNovacion()">
                            <i class="fas fa-save" style="color: white;"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- MODAL LIQUIDAR CREDITO--}}
    <div wire:ignore.self class="modal fade" id="modalGeneral5" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> GENERAR NUEVO CREDITO DE NOVACION CON LOS VALORES {{$this->valorprestamoNova }}</h4>
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

                        <div class="row">
                            <div class="col-8">
                                <div class="card-body p-0">
                                    <!--<div class="card-body p-0" style="height: 50vh; overflow: auto;">-->
                                    <table id="credit_detalle_table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No. Cuota</th>
                                                <th>Fecha Vencimiento</th>
                                                <th>Capital</th>
                                                <th>Desgravamen</th>
                                                <th>Interés</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($this->detalleLiquidar as $value)
                                            <tr>
                                                <td class="text-center">{{$value->numero_cuota}}</td>
                                                <td class="text-center"><b>$ {{$value->date_vencimiento}}</b></td>
                                                <td class="text-center"><b>$ {{$value->capital_amortizado}}</b></td>
                                                <td class="text-center"><b>$ {{$value->fondo_desgravamen}}</b></td>
                                                @if ($value->date_vencimiento <= $this->feacha_actualLiquidar)
                                                    <td class="text-center"><b>${{$value->interes_periodo}}</b></td>
                                                    @else
                                                    <td class="text-center"><span class="badge bg-danger">No Aplica interés</span></td>
                                                    @endif
                                                    <td class="text-center">
                                                        @if ($value->status == 'PENDIENTE')
                                                        <span class="badge bg-warning">{{$value->status}}</span>
                                                        @else
                                                        <span class="badge bg-success">{{$value->status}}</span>
                                                        @endif
                                                    </td>
                                            </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-4">
                                <form id="frmLiquidar">
                                    <div class="row col col-lg-12">
                                        <input type="hidden" id="carpeta_liquidar" value="0">
                                        <table class="table m-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" colspan="2">DETALLE DE LIQUDACION </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>CAPITAL </td>
                                                    <td>
                                                        <label id="capital_liquidar">$ {{$this->capitalLiquidar }}</label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>FONDO DE DESGRAVAMEN</td>
                                                    <td>
                                                        <label id="desgravament_liquidar">$ {{$this->desgravamenLiquidar}}</label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>INTERESES HASTA LA FECHA</td>
                                                    <td>
                                                        <label id="interes_liquidar">$ {{$this->interesLiquidar}}</label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>VALOR A PAGAR</td>
                                                    <td>
                                                        @if(Auth::user()->permiso_credito_aprobar)
                                                        <div class="input-group input-group-sm">
                                                            <input step="0.01" id="totalLiquidar" type="number" wire:model="totalLiquidar" class="form-control" placeholder="Valor" readonly>
                                                        </div>
                                                        @else
                                                        <label id="total_liquidar">$ {{$this->totalLiquidar}}</label>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <label class="small">FORMA DE PAGO </label>
                                                        <div class="form-group">
                                                            <table style="width: 100%;">
                                                                <tr>
                                                                    <td style="width: 80%;">
                                                                        <div class="form-group">
                                                                            <select wire:model="formaPagoLiquidar" class="form-control form-control-sm" wire:change="generarListadeCreditos">
                                                                                <option value=""> --SELECCIONE--</option>
                                                                                @foreach($formasPago as $value)
                                                                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td style="width: 20%;">
                                                                        <a wire:click="agregarFormaPagoLiquidar()" class="btn bg-success btn-xs" style="color: white;">
                                                                            <i class="fa fa-plus"></i>
                                                                        </a>

                                                                    </td>
                                                                </tr>

                                                                @if($mostrarComprobante)
                                                                <tr>
                                                                    <td style="width: 80%;" colspan="2">
                                                                        <div class="col-12">
                                                                            <label class="small">Bancos</label>
                                                                            <select class="form-control form-control-sm" wire:model="banco_pago_id">
                                                                                <option value=""> --SELECCIONE--</option>
                                                                                @foreach ($bancos as $ban)
                                                                                <option value="{{ $ban->id }}">{{ $ban->nombre }} #{{(empty($ban->numero_cuenta) ? '' : $ban->numero_cuenta)}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </td>

                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2">
                                                                        <div class="col-12">
                                                                            <label class="small"># Comprobante</label>
                                                                            <div class="input-group input-group-sm">
                                                                                <input type="text" wire:model="numero_comprobante" class="form-control" placeholder="Número de Comprobante">
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                @endif
                                                                <tr>
                                                                    <td colspan="2">
                                                                        <div class="col-12">
                                                                            <label class="small">Valor</label>
                                                                            <div class="input-group input-group-sm">
                                                                                <input id="valor_forma_pago_liquidar" type="number" wire:model="valor_forma_pago_liquidar" class="form-control" placeholder="Valor Cancelar">
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        <div class="form-group">
                                                            <table class="table table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Forma</th>
                                                                        <th>Valor</th>
                                                                        <th>Acción</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($this->formasPagarCreditoLiquidar as $formas)
                                                                    <tr>
                                                                        <td>{{$formas['formaName']}}</td>
                                                                        <td>{{$formas['valor']}}</td>
                                                                        <td>
                                                                            <a wire:click="eliminarFormaPagarLiquidar({{ $formas['formaId'] }}, '{{ $formas['formaName'] }}')" class="btn bg-danger btn-xs" style="color: white;">
                                                                                <i class="fa fa-plus"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        @if ($this->verBotonlistaPrestamos == false)
                                                        <div class="alert alert-danger">No se puede realizar
                                                            esta operacion hasta que tenga una caja abierta
                                                        </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        @if ($this->verBotonlistaPrestamos != false)
                        <a class="btn btn-success" data-bs-dismiss="modal" title="GENERAR" wire:click="guardarLiquidacion()">Guardar
                            <i class="fas fa-save" style="color: white;"></i>
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- MODAL ENTREGAR CREDITO--}}
    <div wire:ignore.self class="modal fade" id="modalGeneral6" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> ENTREGAR EL DINERO DEL PRESTAMO </h4>
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
                        <div class="row">
                            <div class="col-4">
                                <div class="card-body p-0">
                                    <div class="col-12">
                                        <label class="small">Forma Acreditación</label>
                                        <select wire:model="formaPagoEntregaCredito" class="form-control form-control-sm" wire:change="formaLiquidacionCredito">
                                            <option value=""> --SELECCIONE--</option>
                                            @foreach($formasPago as $value)
                                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card-body p-0">
                                    <div class="col-12">
                                        <label class="small">Bancos</label>
                                        <select class="form-control form-control-sm" wire:model="banco_id" @if($this->obligarCuentaInterna == 1 ) disabled @endif>
                                            <option value=""> --SELECCIONE--</option>
                                            @foreach ($bancos as $ban)
                                            <option value="{{ $ban->id }}">{{ $ban->nombre }} #{{(empty($ban->numero_cuenta) ? '' : $ban->numero_cuenta)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card-body p-0">
                                    <div class="col-12">
                                        <label class="small">Cuenta a acreditar</label>
                                        <select id="cuenta_credito" wire:model="cuenta_credito" class="form-control form-control-sm" wire:key="cuenta_credito">
                                            <option value=""> --SELECCIONE--</option>
                                            @foreach ($this->cuentasDes as $opcion)
                                            <option value="{{ $opcion->id }}">{{$opcion->name .'-'.$opcion->codigo}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($this->obligarCuentaInterna == 0 )
                        <div class="row">
                            <div class="col-6">
                                <label class="small">Documento Transacción</label>
                                <div class="input-group input-group-sm">
                                    <input id="documento_desembolso" type="number" wire:model="documento_desembolso" class="form-control" placeholder="Documento Transacción">
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>

                        <a class="btn btn-success" wire:click="entregarDinero()">
                            <i class="fas fa-save" style="color: white;"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- MODAL TICKET LETRA--}}
    <div wire:ignore.self class="modal fade" id="modalGeneral7" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Ticket </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
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

    function initializeSelect2() {
        $('.select2').select2();

        // Listener para cambiar el valor de Livewire al seleccionar una opción en Select2
        $('.select2').on('change', function(e) {
            var data = $(this).val();
            @this.set('garante', data);
        });
    }
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

    function generarPdf() {
        var valor = $('#valor_simulador').val();
        var cuota = $('#cuotas_simulador').val();
        var fecha = $('#fecha_prestamo').val();
        var tipo = $('#tipo_simulador').val();
        var prestamo = $('#prestamo_simulador').val();
        console.log('ssaaasas');
        location.href = "{{ URL::to('creditos/letras') }}/" + valor + '/' + cuota + '/' + fecha + '/' + tipo + '/' +
            prestamo;
    }

    window.addEventListener('notificarAccion', function(event) {
        var cedito = event.detail.cedito;
        console.log(cedito);
        Swal.fire({
            title: 'Confirmar Novación del crédito ' + cedito.code + '',
            text: '¿Estás seguro de que deseas pagar la novación por el valor de ' + cedito.valor_liquidar + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                Livewire.emit('pagarnovacion', cedito.id);
            }
        });
    });
    window.addEventListener('notificarAccionReversoCredito', function(event) {
        var cedito = event.detail.cedito;
        console.log(cedito);
        Swal.fire({
            title: 'Confirmar el reverso del crédito ' + cedito.code + '',
            text: '¿Estás seguro de que deseas reversar la aprobación de este crédito?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                Livewire.emit('reversarCredito', cedito.id);
            }
        });
    });
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