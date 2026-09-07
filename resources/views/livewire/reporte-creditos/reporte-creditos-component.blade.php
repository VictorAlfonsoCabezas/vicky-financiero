<div>
    <div class="row col col-sm-12">

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
            <div class="form-group">
                <label for="fecha_inicio">Desde:</label>
                <input type="date" class="form-control text-uppercase" id="fecha_inicio" name="fecha_inicio" wire:model="fecha_inicio">
            </div>
        </section>
        <section class="col col-sm-3">
            <div class="form-group">
                <label for="fecha_fin">Hasta:</label>
                <input type="date" class="form-control text-uppercase" id="fecha_fin" name="fecha_fin" wire:model="fecha_fin">
            </div>
        </section>
        <section class="col col-sm-3">
            <div class="col-12">
                <label class="small">Nivel </label>
                <select id="tipo_reporte" wire:model="tipo_reporte" class="form-control text-uppercase" wire:change="obtenerDatos" wire:key="tipo_reporte">
                    <option value="0"> --SELECCIONE--</option>
                    <option value="1">CREDITOS VIGENTES</option>
                    <option value="2">LETRAS IMPAGAS</option>
                    <option value="3">CREDITOS TOTALIZADO</option>
                </select>
            </div>
        </section>
    </div>
    <div class="row col col-sm-12">
        <div class="row">
            <a class="btn btn-primary btn-xs" style="color: white;" wire:click="export">
                <i class="fas fa-plus"></i> Exportar
            </a>
        </div>
    </div>
    <div class="row col col-sm-12">
        <div class="card-body" style="background-color: #ededf3;">
            <div class="tab-content">
                <div class="card-body table-responsive p-0">
                    @if($this->tipo_reporte != 0)
                    @if($this->tipo_reporte == 1)
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th><b>Usuario</b></th>
                                <th><b>Número Crédito </b></th>
                                <th><b>Cedula</b></th>
                                <th><b>Apellidos y Nombres</b></th>
                                <th><b>TIPO PRESTAMO</b></th>
                                <th><b>TIEMPO (meses)</b></th>
                                <th><b>CAPITAL</b></th>
                                <th><b>INTERES</b></th>
                                <th><b>TOTAL PRESTAMO </b></th>
                                <th><b>FECHA DE CREACION</b></th>
                                <th><b>VALOR DE LA CUOTA </b></th>
                                <th><b>INTERES </b></th>
                                <th><b>RECAUDACION </b></th>
                                <th><b>SALDO </b></th>
                                <th><b>LETRAS PENDIENTES </b></th>
                            </tr>
                            <tr style="font-size: 11px;">
                                <th><b></b></th>
                                <th><b> </b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$sumaValorSolicitado}} $</label></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$valorInteresesCreditos}} $</label> </b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$valorInteresesCreditos + $sumaValorSolicitado }} $</label> </b></th>
                                <th><b></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$valorvalorCuotaTotal}} $</label></b></th>
                                <th><b></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$valorPagadoCreditos}} $</label> </b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$valorPendienteCreditos}} $</label> </b></th>
                                <th><b> </b></th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            @foreach($creditos as $key => $credit)
                            @if($credit->pendientesFecha != 0)
                            <tr>
                                <td>{{ $credit->user_created_name }}</td>
                                <td>{{ $credit->code }}</td>
                                <td>{{ $credit->customerIdentificacion }}</td>
                                <td>{{ $credit->customerNombre }}</td>
                                <td>{{ $credit->prestamoNombre }}</td>
                                <td>{{ $credit->cuotas_pagar }}</td>
                                <td>{{ $credit->valor_solicitado }}</td>
                                <td>{{ $credit->valorintereses }}</td>
                                <td>{{ number_format($credit->valor_solicitado + $credit->valorintereses, 2, '.', '') }}</td>
                                <td>{{ $credit->date_created }}</td>
                                <td>{{ $credit->valor_cuota }}</td>
                                <td>{{ $credit->prestamoInteres }} %</td>
                                <td>{{ $credit->valorPagado }} </td>
                                <td>{{ $credit->valorPendiente }} </td>
                                <td>{{ $credit->pendientesFecha }} </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                    {{ $creditos->links() }}

                    @elseif($this->tipo_reporte == 2)
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th><b>#</b></th>
                                <th><b>CREDITO</b></th>
                                <th><b>CEDULA</b></th>
                                <th><b>NOMBRES</b></th>
                                <th><b>VALOR LETRA</b></th>
                                <th><b>INTERES MORA</b></th>
                                <th><b>TOTAL A PAGAR</b></th>
                                <th><b>FECHA</b></th>
                                <th><b>DIAS ATRASO</b></th>
                                <th><b>PLAZO EN MESES</b></th>
                                <th><b># CUOTA</b></th>
                            </tr>
                            <tr style="font-size: 11px;">
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$sumaTotalValorLetra}} $</label> </b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$interesCalculadoLetras}} $</label></b></th>
                                <th><b> <label style="color:rgb(52, 129, 44);">{{$sumaTotalValorLetra + $interesCalculadoLetras}} $</label></b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            @foreach ($letrasImpagas as $key => $credit)
                            <tr>
                                <td style="background-color: {{ $credit->colorLetra }};">{{ $key + 1 }}</td>
                                <td>{{$credit->code_folder_header}}</td>
                                <td>{{$credit->numeroDocumento}}</td>
                                <td>{{$credit->customerName}}</td>
                                <td>{{$credit->valor_cuota}}</td>
                                <td>{{$credit->interesCalculado}}</td>
                                <td>{{$credit->totalPagar}}</td>
                                <td>{{$credit->date_vencimiento}}</td>
                                <td>{{$credit->diasVencido}}</td>
                                <td>{{$credit->totalLetras}}</td>
                                <td>{{$credit->numero_cuota}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $letrasImpagas->links() }}

                    @elseif($this->tipo_reporte == 3)
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th><b>Usuario</b></th>
                                <th><b>Número Crédito </b></th>
                                <th><b>Cedula</b></th>
                                <th><b>Apellidos y Nombres</b></th>
                                <th><b>TIPO PRESTAMO</b></th>
                                <th><b>TIEMPO (meses)</b></th>
                                <th><b>CAPITAL</b></th>
                                <th><b>INTERES</b></th>
                                <th><b>TOTAL PRESTAMO </b></th>
                                <th><b>FECHA DE CREACION</b></th>
                                <th><b>VALOR DE LA CUOTA </b></th>
                                <th><b>INTERES </b></th>
                                <th><b>RECAUDACION </b></th>
                                <th><b>SALDO </b></th>
                                <th><b>LETRAS PENDIENTES </b></th>
                                <th><b>ESTADO</b></th>
                            </tr>
                            <tr style="font-size: 11px;">
                                <th><b></b></th>
                                <th><b> </b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$sumaValorSolicitadoTotalizado}} $</label></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$valorInteresesCreditosTotalizado}} $</label> </b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$valorInteresesCreditosTotalizado + $sumaValorSolicitadoTotalizado }} $</label> </b></th>
                                <th><b></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$valorvalorCuotaTotalTotalizado}} $</label></b></th>
                                <th><b></b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$valorPagadoCreditosTotalizado}} $</label> </b></th>
                                <th><b><label style="color:rgb(52, 129, 44);">{{$valorPendienteCreditosTotalizado}} $</label> </b></th>
                                <th><b> </b></th>
                                <th><b> </b></th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            @foreach($creditosTotalizado as $key => $credit)
                            <tr>
                                <td>{{ $credit->user_created_name }}</td>
                                <td>{{ $credit->code }}</td>
                                <td>{{ $credit->customerIdentificacion }}</td>
                                <td>{{ $credit->customerNombre }}</td>
                                <td>{{ $credit->prestamoNombre }}</td>
                                <td>{{ $credit->cuotas_pagar }}</td>
                                <td>{{ $credit->valor_solicitado }}</td>
                                <td>{{ $credit->valorintereses }}</td>
                                <td>{{ number_format($credit->valor_solicitado + $credit->valorintereses, 2, '.', '') }}</td>
                                <td>{{ $credit->date_created }}</td>
                                <td>{{ $credit->valor_cuota }}</td>
                                <td>{{ $credit->prestamoInteres }} %</td>
                                <td>{{ $credit->valorPagado }} </td>
                                <td>{{ $credit->valorPendiente }} </td>
                                <td>{{ $credit->pendientesFecha }} </td>
                                <td>
                                    @if($credit->pendientesFecha != 0 || $credit->status == 'NEGADO')
                                    {{ $credit->status }}
                                    @else
                                    Pagado
                                    @endif
                                    .:. {{ $credit->status }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $creditosTotalizado->links() }}
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>