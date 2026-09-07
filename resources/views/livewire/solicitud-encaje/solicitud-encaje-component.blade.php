<div>
    <div class="row">
        <div class="col-md-12">
            <div class="row mt-2">
                <div class="col-3">
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
                <section class="col col-sm-3">
                    <label class="small">Tipo </label>
                    <select id="tipo_reporte" wire:model="tipo_reporte" class="form-control text-uppercase">
                        <option value="0"> --SELECCIONE--</option>
                        <option value="1">Todas las cuentas</option>
                        <option value="2">Encajes Pendientes</option>
                        <option value="3">Encajes Entregados</option>
                        <option value="4">Encajes Cancelados</option>
                    </select>
                </section>
                @if($this->tipo_reporte == 2 || $this->tipo_reporte == 3 || $this->tipo_reporte == 4)
                <div class="col-3">
                    <label>Fecha Inicio</label>
                    <input type="date" class="form-control form-control-sm" placeholder="Fecha Inicio"
                        wire:model="fechaInicio">
                </div>
                <div class="col-3">
                    <label>Fecha Fin</label>
                    <input type="date" class="form-control form-control-sm" placeholder="Fecha Fin"
                        wire:model="fechaFin">
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row col col-sm-12">
        <div class="card-body" style="background-color: #ededf3;">
            <div class="tab-content">
                <div class="card-body table-responsive p-0">
                    @if($this->tipo_reporte == 2 || $this->tipo_reporte == 3 || $this->tipo_reporte == 4)
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th><b>N° Socio</b></th>
                                <th><b>Identificación</b></th>
                                <th><b>Nombre Socio</b></th>
                                <th><b>Cuenta</b></th>
                                <th><b>Valor </b></th>
                                <th><b>Porcentaje Retenido </b></th>
                                <th><b>Valor Retenido </b></th>
                                <th><b>Valor Entregar </b></th>
                                @if($this->tipo_reporte == 2 )
                                <th><b>Usuario Crea </b></th>
                                <th><b>Fecha / Hora Ingreso </b></th>
                                @endif
                                @if($this->tipo_reporte == 3 )
                                <th><b>Usuario Entrega </b></th>
                                <th><b>Fecha / Hora Entrega </b></th>
                                @endif
                                @if($this->tipo_reporte == 4 )
                                <th><b>Usuario Cancela </b></th>
                                <th><b>Fecha / Hora Cancela </b></th>
                                @endif

                                <th><b>Acciones</b></th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            @foreach($solicitudes as $solicitud)
                            <tr>
                                <td>{{$solicitud->numeroSocio}}</td>
                                <td>{{$solicitud->numero_documento}}</td>
                                <td>{{$solicitud->nombre_completo}}</td>
                                <td>{{$solicitud->codigoCuenta}}</td>
                                <td>{{$solicitud->valor}}</td>
                                <td>{{$solicitud->porcentaje_retenido}}</td>
                                <td>{{$solicitud->valor_retenido}}</td>
                                <td>{{$solicitud->valor_entregado}}</td>
                                @if($this->tipo_reporte == 2 )
                                <td>{{$solicitud->user_created_name}}</td>
                                <td>{{$solicitud->date_created}} / {{$solicitud->hour_created}} </td>
                                <td>
                                    <a class="btn btn-xs btn-danger" title="Negar Solicitud {{ $solicitud->code }}" wire:click="negarSolicitud({{ $solicitud->id }})" style="color: white;">
                                        <i class="far fa-trash-alt" style="color: white;"></i> Cancelar
                                    </a>
                                </td>
                                @endif
                                @if($this->tipo_reporte == 3 )
                                <td>{{$solicitud->user_entrega_name}}</td>
                                <td>{{$solicitud->date_entrega}} / {{$solicitud->hour_entrega}} </td>
                                <td>

                                    <a wire:click="imprimirEntregaEncaje({{ $solicitud->id }})" class="btn btn-sm btn-warning" style="color: white;">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                                @endif
                                @if($this->tipo_reporte == 4 )
                                <td>{{$solicitud->user_cancel_name}}</td>
                                <td>{{$solicitud->date_cancel}} / {{$solicitud->hour_cancel}} </td>
                                <td></td>
                                @endif


                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th><b>N° Socio</b></th>
                                <th><b>Identificación</b></th>
                                <th><b>Nombre Socio</b></th>
                                <th><b>Cuenta</b></th>
                                <th><b>Total Encaje </b></th>
                                <th><b>Acciones</b></th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            @foreach($cuentas as $cuenta)
                            @if($cuenta->cantSolicitudes > 0)
                            <tr style="background-color: #f9e79f;">
                                @else
                            <tr>
                                @endif
                                <td>{{$cuenta->numeroSocio}}</td>
                                <td>{{$cuenta->numero_documento}}</td>
                                <td>{{$cuenta->nombre_completo}}</td>
                                <td>{{$cuenta->codigo}}</td>
                                <td>{{$cuenta->saldo}}</td>
                                <td>
                                    <a data-bs-toggle="modal" data-bs-target="#modalGeneral" wire:click="generarSolicitudEncaje({{ $cuenta->id }},'{{ $cuenta->codigo }}', {{ $cuenta->saldo }} ,{{ $cuenta->porcentajeEncaje }})" class="btn btn-sm btn-warning">
                                        <i class="fa fa-paperclip"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL Solicitud de Encajes --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Solicitud de entrega de encajes</h4>
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
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Valor</label>
                                                <input disabled type="number" class="form-control" wire:model="valor" placeholder="0.00" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Procentaje Retenido</label>
                                                <input type="number" class="form-control" wire:model="porcentajeRetenido" placeholder="0.00000" step="0.00001" wire:change="calcularRetencion(1)">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Valor Retenido</label>
                                                <input type="number" class="form-control" wire:model="valorRetenido" placeholder="0.00000" step="0.00001" wire:change="calcularRetencion(2)">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Valor Entregar</label>
                                                <input disabled type="number" class="form-control" wire:model="valorEntregar" placeholder="0.00000" step="0.00001">
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Porcentaje Plazo Fijo</label>
                                                <input type="number" class="form-control" wire:model="porcentaje_plazo_fijo" placeholder="0.00" step="0.01" wire:change="calcularPlazo()">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label>Tasa de Interés</label>
                                                <select class="form-control"
                                                    wire:model="taza_plazo_fijo">
                                                    <option value=""> --SELECCIONE-- </option>
                                                    @foreach ($this->interesPlazoFijo as $val)
                                                    <option value="{{ $val->id }}">
                                                        {{ $val->interes . '% ' . 'DESDE ' . $val->rango_min . ' HASTA ' . $val->rango_max . ' DÍAS' }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <label>Pago</label>
                                            <select id="pago_plazo_fijo" wire:model="pago_plazo_fijo"
                                                class="form-control">
                                                <option value=""> --SELECCIONE-- </option>
                                                <option value="C"> Al Cumplimiento </option>
                                                <option value="M"> Mensualizado </option>
                                            </select>
                                        </div>
                                        <div class="col-3"> <!-- Contenedor para el input y el checkbox -->
                                            <label class="small">Plazo Manual </label>
                                            <div class="input-group input-group-sm">
                                                @if ($this->activarManualNovacion)
                                                <input type="number" id="dias_plazo_fijo"
                                                    wire:model="dias_plazo_fijo" class="form-control"
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
                                        <div class="col-4">
                                            <label>Beneficiario</label>
                                            <select id="beneficiario_plazo_fijo"
                                                wire:model="beneficiario_plazo_fijo"
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
                                    <hr>
                                    <div class="row">
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
                                                            <td>{{$this->valor }} </td>
                                                            <td>{{$this->porcentajeRetenido }} %</td>
                                                            <td>{{$this->valorRetenido }} </td>
                                                            <td>{{$this->porcentaje_plazo_fijo }} %</td>
                                                            <td>{{$this->valor_plazo_fijo }} </td>
                                                            <td>{{$this->valorEntregar }} </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
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
                </form>
            </div>
        </div>
    </div>


</div>