<div>
    <div class="row col col-sm-12">
        <section class="col col-sm-4">
            <a data-bs-toggle="modal" data-bs-target="#modalGeneral1" class="btn btn-xs btn-success" title="Entregarc Credito" wire:click="creditoSelect(0)" style="color: white;">
                <i class="fas fa-plus-circle"></i> Agregar
            </a>
        </section>

    </div>

    <div class="row col col-sm-12">
        <div class="card-body" style="background-color: #ededf3;">
            <div class="tab-content">
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-valign-middle">
                        <thead class="thead-primary" style="font-size: 10px;">
                            <tr role="row">
                                <th scope="col">ID</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Interes</th>
                                <th scope="col">Interes Anual</th>
                                <th scope="col">Fondo Desgravamen</th>
                                <th scope="col">Valor Máximo</th>
                                <th scope="col">Valor Mínimo</th>
                                <th scope="col">Edades</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Letra de Cambio</th>
                                <th scope="col">Pagaré</th>
                                <th scope="col">Contrato</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prestamos as $prestamo)
                            <tr>
                                <td>{{ $prestamo->id }}</td>
                                <td>{{ $prestamo->name }}</td>
                                <td>{{ $prestamo->interes }}</td>
                                <td>{{ $prestamo->interes_anual }}</td>
                                <td>{{ $prestamo->fondo_desgravamen }}</td>
                                <td>{{ $prestamo->valor_maximo }}</td>
                                <td>{{ $prestamo->valor_minimo }}</td>
                                <td>{{ 'DESDE ' . $prestamo->edad_minima . 'AÑOS HASTA ' . $prestamo->edad_maxima . ' AÑOS' }}
                                </td>
                                <td>
                                    {{ $prestamo->tipo != 'F' ? 'ALEMANA ' : 'FRANCESA ' }}
                                    @if ($prestamo->diario)
                                    DIARIO
                                    @endif
                                </td>
                                <td>
                                    @if($prestamo->letra_cambio)
                                    <small class="badge bg-primary" wire:click="cambioLetra({{ $prestamo->id }})"></i>Activo</small>
                                    @else
                                    <small class="badge bg-danger" wire:click="cambioLetra({{ $prestamo->id }})"></i>Desactivado</small>
                                    @endif
                                </td>
                                <td>
                                    @if($prestamo->pagare)
                                    <small class="badge bg-primary" wire:click="cambioPagare({{ $prestamo->id }})"></i>Activo</small>
                                    @else
                                    <small class="badge bg-danger" wire:click="cambioPagare({{ $prestamo->id }})"></i>Desactivado</small>
                                    @endif

                                </td>
                                <td>
                                    @if($prestamo->contrato)
                                    <small class="badge bg-primary" wire:click="cambioContrato({{ $prestamo->id }})"></i>Activo</small>
                                    @else
                                    <small class="badge bg-danger" wire:click="cambioContrato({{ $prestamo->id }})"></i>Desactivado</small>
                                    @endif

                                </td>
                                <td>
                                    <a data-bs-toggle="modal" data-bs-target="#modalGeneral1" class="btn btn-xs btn-success" title="Editar Credito {{ $prestamo->name}}" wire:click="creditoSelect({{ $prestamo->id}})" style="color: white;">
                                        <i class="fas fa-hand-holding-usd" style="color: white;"></i> Editar
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

    {{-- MODAL ENTREGAR CREDITO--}}
    <div wire:ignore.self class="modal fade" id="modalGeneral1" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Tipo Prestamo </h4>
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
                            <div class="row col col-sm-12">
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="name">Nombres</label>
                                        <input type="text" class="form-control text-uppercase" id="name" name="name" placeholder="Nombre" wire:model="name">
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="interes">Interes</label>
                                        <input type="number" class="form-control text-uppercase" id="interes" name="interes" placeholder="0.00" wire:model="interes">
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="interes_anual">Interes Anual</label>
                                        <input type="number" class="form-control text-uppercase" id="interes_anual" name="interes_anual" placeholder="0.00" wire:model="interes_anual">
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="fondo_desgravamen">Fondo Desgravamen</label>
                                        <input type="number" class="form-control text-uppercase" id="fondo_desgravamen" name="fondo_desgravamen" placeholder="0.00" wire:model="fondo_desgravamen">
                                    </div>
                                </section>
                            </div>
                            <div class="row col col-sm-12">
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="tipo">Tipo</label>
                                        <select type="text" id="tipo" name="tipo" class="form-control" wire:model="tipo" wire:key="tipo">
                                            <option value="" selected=""> --SELECCIONE-- </option>
                                            <option value="F"> FRANCES</option>
                                            <option value="A"> ALEMÁN</option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="account_id_contable">Cuenta Contable</label>
                                        <select id="plan_cuenta_id" name="plan_cuenta_id" class="form-control" wire:model="plan_cuenta_id" wire:key="plan_cuenta_id">
                                            <option value="" selected=""> --SELECCIONE-- </option>
                                            @foreach($planCuentas as $cuenta)
                                                <option value="{{ $cuenta->id }}"> {{ $cuenta->codigo }} - {{ $cuenta->nombre }} </option>
                                            @endforeach
                                        </select>
                                        @error('plan_cuenta_id') 
                                            <span class="text-danger">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="valor_minimo">Valor Mínimo</label>
                                        <input type="number" class="form-control text-uppercase" id="valor_minimo" name="valor_minimo" placeholder="0.00" wire:model="valor_minimo">
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="valor_maximo">Valor Máximo</label>
                                        <input type="text" class="form-control text-uppercase" id="valor_maximo" name="valor_maximo" placeholder="0.00" wire:model="valor_maximo">
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="edad_minima">Edad Mínima</label>
                                        <input type="number" class="form-control text-uppercase" id="edad_minima" name="edad_minima" placeholder="0.00" wire:model="edad_minima">
                                    </div>
                                </section>

                            </div>
                            <div class="row col col-sm-12">
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="edad_maxima">Edad Máximo</label>
                                        <input type="text" class="form-control text-uppercase" id="edad_maxima" name="edad_maxima" placeholder="0.00" wire:model="edad_maxima">
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="diario">Diario</label>
                                        <select type="text" id="diario" name="diario" class="form-control" wire:model="diario" wire:key="diario">
                                            <option value="" selected="">-- Seleccione-- </option>
                                            <option value="1"> SI </option>
                                            <option value="0"> NO </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="periodo_id">Recurrencia</label>
                                        <select id="periodo_id" name="periodo_id" class="form-control" wire:model="periodo_id" wire:key="periodo_id">
                                            <option value="" selected="">-- Seleccione-- </option>
                                            @foreach($recurrencia as $key => $prestamo)
                                            <option value="{{$prestamo->id}}"> {{$prestamo->name}} </option>
                                            @endforeach

                                        </select>
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="administrativo_porcentaje_valor">Tipo Gasto Administrativo</label>
                                        <select id="administrativo_porcentaje_valor" name="administrativo_porcentaje_valor" class="form-control" wire:model="administrativo_porcentaje_valor" wire:key="administrativo_porcentaje_valor">
                                            <option value="" selected="">-- Seleccione-- </option>
                                            <option value="VALOR"> VALOR </option>
                                            <option value="PORCENTAJE"> PORCENTAJE </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="gasto_administrativo">Gasto Administrativo</label>
                                        <input type="number" class="form-control text-uppercase" id="gasto_administrativo" name="gasto_administrativo" placeholder="0.00" wire:model="gasto_administrativo">
                                    </div>
                                </section>

                            </div>
                        </div>
                        <hr>
                        <p>Control de Contabilidad</p>
                        <hr>
                        <div class="row">
                            <div class="row col col-sm-12">
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="lleva_contabilidad_switch" 
                                                wire:model.defer="lleva_contabilidad">
                                            <label class="custom-control-label" for="lleva_contabilidad_switch">
                                                Lleva Contabilidad
                                            </label>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                        <hr>
                        <p>Sumar Gastos al prestamo</p>
                        <hr>
                        <div class="row">
                            <div class="row col col-sm-12">
                                <section class="col col-sm-6">
                                    <div class="form-group">
                                        <select id="suma_valores_gastos_prestamo" name="suma_valores_gastos_prestamo" class="form-control" wire:model="suma_valores_gastos_prestamo" wire:key="suma_valores_gastos_prestamo">
                                            <option value="0"> NO </option>
                                            <option value="1"> SI </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col col-sm-6">
                                    <div class="form-group">
                                        <select id="letra_credito" name="letra_credito" class="form-control" wire:model="letra_credito" wire:key="letra_credito">
                                            <option value=""> --Seleccione-- </option>
                                            <option value="CREDITO"> CREDITO </option>
                                            <option value="LETRA"> LETRA </option>
                                        </select>
                                    </div>
                                </section>
                            </div>
                        </div>
                        <hr>
                        <p>DATOS DE ENCAJE</p>
                        <hr>
                        <div class="row">
                            <div class="row col col-sm-12">
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="encaje">Requiere Encaje</label>
                                        <select id="encaje" name="encaje" class="form-control" wire:model="encaje" wire:key="encaje">
                                            <option value="0"> NO </option>
                                            <option value="1"> SI </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="encaje_credito_cuenta" style="font-size: 10;">Crédito o Cuenta</label>
                                        <select id="encaje_credito_cuenta" name="encaje_credito_cuenta" class="form-control" wire:model="encaje_credito_cuenta" wire:key="encaje_credito_cuenta" @if($this->encaje == 0 ) disabled @endif>
                                            <option value="" selected="">-- Seleccione-- </option>
                                            <option value="CREDITO"> CREDITO </option>
                                            <option value="CUENTA"> CUENTA </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="encaje_porcentaje_valor" style="font-size: 10;">Valor o Porcentaje</label>
                                        <select id="encaje_porcentaje_valor" name="encaje_porcentaje_valor" class="form-control" wire:model="encaje_porcentaje_valor" wire:key="encaje_porcentaje_valor" @if($this->encaje == 0 ) disabled @endif>
                                            <option value="" selected="">-- Seleccione-- </option>
                                            <option value="VALOR"> VALOR </option>
                                            <option value="PORCENTAJE"> PORCENTAJE </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="encaje_cantidad">Cantidad del encaje</label>
                                        <input type="number" class="form-control text-uppercase" id="encaje_cantidad" name="encaje_cantidad" placeholder="0.00" wire:model="encaje_cantidad" @if($this->encaje == 0 ) disabled @endif>
                                    </div>
                                </section>

                            </div>
                        </div>
                        <hr>
                        <p>Gastos Adicionales</p>
                        <hr>
                        <div class="row">
                            <div class="row col col-sm-12">
                                <section class="col col-sm-6">
                                    <div class="form-group">
                                        <label for="porcentaje_primer_gasto" style="font-size: 10;">Valor o Porcentaje {{ $company->nombre_primer_gasto_credito}}</label>
                                        <select id="porcentaje_primer_gasto" name="porcentaje_primer_gasto" class="form-control" wire:model="porcentaje_primer_gasto" wire:key="porcentaje_primer_gasto">
                                            <option value="" selected="">-- Seleccione-- </option>
                                            <option value="0"> VALOR </option>
                                            <option value="1"> PORCENTAJE </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col col-sm-6">
                                    <div class="form-group">
                                        <label for="primer_gasto">{{ $company->nombre_primer_gasto_credito}}</label>
                                        <input type="number" class="form-control text-uppercase" id="primer_gasto" name="primer_gasto" placeholder="0.00" wire:model="primer_gasto">
                                    </div>
                                </section>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row col col-sm-12">
                                <section class="col col-sm-6">
                                    <div class="form-group">
                                        <label for="porcentaje_segundo_gasto" style="font-size: 10;">Valor o Porcentaje {{ $company->nombre_segundo_gasto_credito}}</label>
                                        <select id="porcentaje_segundo_gasto" name="porcentaje_segundo_gasto" class="form-control" wire:model="porcentaje_segundo_gasto" wire:key="porcentaje_segundo_gasto">
                                            <option value="" selected="">-- Seleccione-- </option>
                                            <option value="0"> VALOR </option>
                                            <option value="1"> PORCENTAJE </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col col-sm-6">
                                    <div class="form-group">
                                        <label for="segundo_gasto">{{ $company->nombre_segundo_gasto_credito}}</label>
                                        <input type="number" class="form-control text-uppercase" id="segundo_gasto" name="segundo_gasto" placeholder="0.00" wire:model="segundo_gasto">
                                    </div>
                                </section>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row col col-sm-12">
                                <section class="col col-sm-6">
                                    <div class="form-group">
                                        <label for="porcentaje_tercer_gasto" style="font-size: 10;">Valor o Porcentaje {{ $company->nombre_tercer_gasto_credito}}</label>
                                        <select id="porcentaje_tercer_gasto" name="porcentaje_tercer_gasto" class="form-control" wire:model="porcentaje_tercer_gasto" wire:key="porcentaje_tercer_gasto">
                                            <option value="" selected="">-- Seleccione-- </option>
                                            <option value="0"> VALOR </option>
                                            <option value="1"> PORCENTAJE </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col col-sm-6">
                                    <div class="form-group">
                                        <label for="tercer_gasto">{{ $company->nombre_tercer_gasto_credito}}</label>
                                        <input type="number" class="form-control text-uppercase" id="tercer_gasto" name="tercer_gasto" placeholder="0.00" wire:model="tercer_gasto">
                                    </div>
                                </section>
                            </div>
                        </div>
                        <hr>
                        <p>Ahorro</p>
                        <hr>
                        <div class="row">
                            <div class="row col col-sm-12">
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="ahorro">Valor de Ahorro </label>
                                        <input type="number" class="form-control text-uppercase" id="ahorro" name="ahorro" placeholder="0.00" wire:model="ahorro">
                                    </div>
                                </section>

                            </div>
                        </div>
                        <hr>
                        <p>Calculo</p>
                        <hr>
                        <div class="row">
                            <div class="row col col-sm-12">
                                <section class="col col-sm-6">
                                    <div class="form-group">
                                        <label for="calculo_simple" style="font-size: 10;">Calculo Tabla amortizacion</label>
                                        <select id="calculo_simple" name="calculo_simple" class="form-control" wire:model="calculo_simple" wire:key="calculo_simple">
                                            <option value="" selected="">-- Seleccione-- </option>
                                            <option value="0"> Compuesto </option>
                                            <option value="1"> Simple </option>
                                        </select>
                                    </div>
                                </section>

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>

                        <a class="btn btn-success" wire:click="guardarCredito()">
                            <i class="fas fa-save" style="color: white;"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>




</div>