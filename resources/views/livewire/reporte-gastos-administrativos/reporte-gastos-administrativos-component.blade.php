<div>
    <div class="row col col-sm-12">
        <section class="col col-sm-4">

            <label class="small">Tipo </label>
            <select id="cuenta_prestamo" wire:model="cuenta_prestamo" class="form-control text-uppercase" wire:change="obtenerDatos">
                <option value="0"> --SELECCIONE--</option>
                <option value="1"> Cuentas </option>
                <option value="2"> Prestamos </option>
            </select>
        </section>
        <section class="col col-sm-4">
            <div class="form-group">
                <label for="fecha_inicio">Desde:</label>
                <input type="date" class="form-control text-uppercase" id="fecha_inicio" name="fecha_inicio" wire:model="fecha_inicio">
            </div>
        </section>
        <section class="col col-sm-4">
            <div class="form-group">
                <label for="fecha_fin">Hasta:</label>
                <input type="date" class="form-control text-uppercase" id="fecha_fin" name="fecha_fin" wire:model="fecha_fin">
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
                    @if($this->cuenta_prestamo == '1')
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th><b>Origen</b></th>
                                <th><b>Socio</b></th>
                                <th><b>Identificacion</b></th>
                                <th><b>Fecha</b></th>
                                <th><b>Observacion</b></th>
                                <th> <b>Valor</b></th>
                            </tr>
                            <tr style="font-size: 11px;">
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th>
                                    <b>
                                        <label style="color:rgb(52, 129, 44);">{{$totalValorMovimiento}} $</label>
                                    </b>
                                </th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            @foreach ($cuentas as $cuenta)
                            <tr>
                                <td>{{ $cuenta->nombreHorro }}<br>{{$cuenta->numeroCuenta }}</td>
                                <td>{{ $cuenta->customer_name }}</td>
                                <td>{{ $cuenta->customer_ruc }}</td>
                                <td>{{ $cuenta->date_created }}</td>
                                <td>{{ $cuenta->observation }}</td>
                                <td>{{ $cuenta->total_valor_movimiento }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $cuentas->links() }}
                    @endif
                    @if($this->cuenta_prestamo == '2')
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th><b>Número Crédito </b></th>
                                <th><b>Cédula</b></th>
                                <th><b>Apellidos y Nombres</b></th>
                                <th><b>TIPO PRESTAMO</b></th>
                                <th><b>Fecha Creación </b></th>
                                <th><b>Gasto Administrativo</b></th>
                                <th><b>Primer Gasto</b></th>
                                <th><b>Segundo Gasto</b></th>
                                <th><b>Tercer Gasto </b></th>
                            </tr>
                            <tr style="font-size: 11px;">
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b></b></th>
                                <th><b></b></th>


                                <th><b><br><br><label style="color:rgb(52, 129, 44);">{{$creditosTotales->sum('gasto_administrativo')}} $</label></b></th>
                                <th><b><br><br><label style="color:rgb(52, 129, 44);">{{$creditosTotales->sum('primer_gasto')}} $</label></b></th>
                                <th><b><br><br><label style="color:rgb(52, 129, 44);">{{$creditosTotales->sum('segundo_gasto')}} $</label></b></th>
                                <th><b><br><br><label style="color:rgb(52, 129, 44);">{{$creditosTotales->sum('tercer_gasto')}} $</label></b></th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            @foreach($creditos as $key => $credit)
                            <tr>
                                <td>{{ $credit->code }}</td>
                                <td>{{ $credit->customerIdentificacion }}</td>
                                <td>{{ $credit->customerNombre }}</td>
                                <td>{{ $credit->prestamoNombre }}</td>
                                <td>{{ $credit->date_created }}</td>
                                <td>{{ $credit->gasto_administrativo }}</td>
                                <td>{{ $credit->primer_gasto }}</td>
                                <td>{{ $credit->segundo_gasto }}</td>
                                <td>{{ $credit->tercer_gasto }}</td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $creditos->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>