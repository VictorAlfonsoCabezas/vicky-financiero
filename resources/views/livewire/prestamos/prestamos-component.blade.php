<div class="savings-module loans-module">
    <div class="savings-heading">
        <div>
            <div class="text-muted small mb-1">CONFIGURACI&Oacute;N / PR&Eacute;STAMOS</div>
            <h1>Tipos de pr&eacute;stamo</h1>
            <p>Administra condiciones, montos y documentos de tus productos de cr&eacute;dito.</p>
        </div><button type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral1" class="btn btn-primary" wire:click="creditoSelect(0)"><i class="fa fa-plus me-2"></i>Nuevo tipo de pr&eacute;stamo</button>
    </div>
    <div class="card">
        <div class="card-header savings-filters">
            <div>
                <h2 class="h5 mb-1">Cat&aacute;logo de pr&eacute;stamos</h2><span class="text-muted">{{ $prestamos->total() }} productos</span>
            </div>
            <div class="d-flex gap-2 flex-wrap"><input type="search" class="form-control" wire:model.debounce.350ms="search" placeholder="Buscar por nombre" aria-label="Buscar tipo de pr&eacute;stamo"><select class="form-select" wire:model="tipoFiltro" aria-label="Filtrar sistema de amortizaci&oacute;n">
                    <option value="">Todos los sistemas</option>
                    <option value="F">Francesa</option>
                    <option value="A">Alemana</option>
                </select></div>
        </div>
        <div wire:loading.delay class="px-4 py-2 text-primary" role="status">Actualizando datos...</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Intereses</th>
                        <th>Montos permitidos</th>
                        <th>Edades</th>
                        <th>Documentos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($prestamos as $prestamo)
                    <tr wire:key="loan-type-{{ $prestamo->id }}">
                        <td><strong>{{ $prestamo->name }}</strong>
                            <div class="text-muted small mt-1">{{ $prestamo->tipo == 'F' ? 'Francesa' : 'Alemana' }} @if($prestamo->diario)&middot; Diario @endif</div><span class="text-muted small">#{{ $prestamo->id }}</span>
                        </td>
                        <td>
                            <div>{{ $prestamo->interes }} % <span class="text-muted small">Inter&eacute;s</span></div>
                            <div>{{ $prestamo->interes_anual }} % <span class="text-muted small">Anual</span></div>
                            <div class="small text-muted">Desgravamen: {{ $prestamo->fondo_desgravamen }}</div>
                        </td>
                        <td>
                            <div class="text-nowrap">$ {{ number_format($prestamo->valor_minimo, 2) }} <span class="text-muted small">M&iacute;n.</span></div>
                            <div class="text-nowrap">$ {{ number_format($prestamo->valor_maximo, 2) }} <span class="text-muted small">M&aacute;x.</span></div>
                        </td>
                        <td>{{ $prestamo->edad_minima }} &ndash; {{ $prestamo->edad_maxima }} a&ntilde;os</td>
                        <td>
                            <div class="loan-documents">
                                @foreach (['letra_cambio' => ['Letra', 'cambioLetra'], 'pagare' => ['Pagar&eacute;', 'cambioPagare'], 'contrato' => ['Contrato', 'cambioContrato']] as $field => $document)
                                <button type="button" class="btn btn-sm {{ $prestamo->$field ? 'btn-outline-success' : 'savings-secondary-action' }}" wire:click="{{ $document[1] }}({{ $prestamo->id }})" aria-pressed="{{ $prestamo->$field ? 'true' : 'false' }}" title="Activar o desactivar documento"><i class="fa {{ $prestamo->$field ? 'fa-check-circle' : 'fa-minus-circle' }} me-1"></i>{!! $document[0] !!}: {{ $prestamo->$field ? 'Activo' : 'Inactivo' }}</button>
                                @endforeach
                            </div>
                        </td>
                        <td><button type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral1" class="btn btn-sm btn-outline-primary" wire:click="creditoSelect({{ $prestamo->id }})"><i class="fa fa-pen me-1"></i>Editar</button></td>
                    </tr>
                    @empty<tr>
                        <td colspan="6" class="text-center p-5 text-muted">No se encontraron tipos de pr&eacute;stamo con estos filtros.</td>
                    </tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $prestamos->links() }}</div>
    </div>
    {{-- MODAL ENTREGAR CREDITO--}}
    <div wire:ignore.self class="modal fade" id="modalGeneral1" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Tipo Prestamo </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form wire:submit.prevent="guardarCredito">
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
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="name">Nombres</label>
                                        <input type="text" class="form-control text-uppercase" id="name" name="name" placeholder="Nombre" wire:model="name">
                                    </div>
                                </section>
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="interes">Interes</label>
                                        <input type="number" class="form-control text-uppercase" id="interes" name="interes" placeholder="0.00" wire:model="interes">
                                    </div>
                                </section>
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="interes_anual">Interes Anual</label>
                                        <input type="number" class="form-control text-uppercase" id="interes_anual" name="interes_anual" placeholder="0.00" wire:model="interes_anual">
                                    </div>
                                </section>
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="fondo_desgravamen">Fondo Desgravamen</label>
                                        <input type="number" class="form-control text-uppercase" id="fondo_desgravamen" name="fondo_desgravamen" placeholder="0.00" wire:model="fondo_desgravamen">
                                    </div>
                                </section>
                            </div>
                            <div class="row col col-sm-12">
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="tipo">Tipo</label>
                                        <select type="text" id="tipo" name="tipo" class="form-control" wire:model="tipo" wire:key="tipo">
                                            <option value="" selected=""> --SELECCIONE-- </option>
                                            <option value="F"> FRANCES</option>
                                            <option value="A"> ALEMÁN</option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col-12 col-md-3">
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
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="valor_minimo">Valor Mínimo</label>
                                        <input type="number" class="form-control text-uppercase" id="valor_minimo" name="valor_minimo" placeholder="0.00" wire:model="valor_minimo">
                                    </div>
                                </section>
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="valor_maximo">Valor Máximo</label>
                                        <input type="text" class="form-control text-uppercase" id="valor_maximo" name="valor_maximo" placeholder="0.00" wire:model="valor_maximo">
                                    </div>
                                </section>
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="edad_minima">Edad Mínima</label>
                                        <input type="number" class="form-control text-uppercase" id="edad_minima" name="edad_minima" placeholder="0.00" wire:model="edad_minima">
                                    </div>
                                </section>

                            </div>
                            <div class="row col col-sm-12">
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="edad_maxima">Edad Máximo</label>
                                        <input type="text" class="form-control text-uppercase" id="edad_maxima" name="edad_maxima" placeholder="0.00" wire:model="edad_maxima">
                                    </div>
                                </section>
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="diario">Diario</label>
                                        <select type="text" id="diario" name="diario" class="form-control" wire:model="diario" wire:key="diario">
                                            <option value="" selected="">-- Seleccione-- </option>
                                            <option value="1"> SI </option>
                                            <option value="0"> NO </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col-12 col-md-3">
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
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="administrativo_porcentaje_valor">Tipo Gasto Administrativo</label>
                                        <select id="administrativo_porcentaje_valor" name="administrativo_porcentaje_valor" class="form-control" wire:model="administrativo_porcentaje_valor" wire:key="administrativo_porcentaje_valor">
                                            <option value="" selected="">-- Seleccione-- </option>
                                            <option value="VALOR"> VALOR </option>
                                            <option value="PORCENTAJE"> PORCENTAJE </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col-12 col-md-3">
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
                                <section class="col-12 col-md-3">
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
                                <section class="col-12 col-md-6">
                                    <div class="form-group">
                                        <select id="suma_valores_gastos_prestamo" name="suma_valores_gastos_prestamo" class="form-control" wire:model="suma_valores_gastos_prestamo" wire:key="suma_valores_gastos_prestamo">
                                            <option value="0"> NO </option>
                                            <option value="1"> SI </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col-12 col-md-6">
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
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="encaje">Requiere Encaje</label>
                                        <select id="encaje" name="encaje" class="form-control" wire:model="encaje" wire:key="encaje">
                                            <option value="0"> NO </option>
                                            <option value="1"> SI </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="encaje_credito_cuenta" style="font-size: 10;">Crédito o Cuenta</label>
                                        <select id="encaje_credito_cuenta" name="encaje_credito_cuenta" class="form-control" wire:model="encaje_credito_cuenta" wire:key="encaje_credito_cuenta" @if($this->encaje == 0 ) disabled @endif>
                                            <option value="" selected="">-- Seleccione-- </option>
                                            <option value="CREDITO"> CREDITO </option>
                                            <option value="CUENTA"> CUENTA </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="encaje_porcentaje_valor" style="font-size: 10;">Valor o Porcentaje</label>
                                        <select id="encaje_porcentaje_valor" name="encaje_porcentaje_valor" class="form-control" wire:model="encaje_porcentaje_valor" wire:key="encaje_porcentaje_valor" @if($this->encaje == 0 ) disabled @endif>
                                            <option value="" selected="">-- Seleccione-- </option>
                                            <option value="VALOR"> VALOR </option>
                                            <option value="PORCENTAJE"> PORCENTAJE </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col-12 col-md-3">
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
                                <section class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="porcentaje_primer_gasto" style="font-size: 10;">Valor o Porcentaje {{ $company->nombre_primer_gasto_credito}}</label>
                                        <select id="porcentaje_primer_gasto" name="porcentaje_primer_gasto" class="form-control" wire:model="porcentaje_primer_gasto" wire:key="porcentaje_primer_gasto">
                                            <option value="" selected="">-- Seleccione-- </option>
                                            <option value="0"> VALOR </option>
                                            <option value="1"> PORCENTAJE </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="primer_gasto">{{ $company->nombre_primer_gasto_credito}}</label>
                                        <input type="number" class="form-control text-uppercase" id="primer_gasto" name="primer_gasto" placeholder="0.00" wire:model="primer_gasto">
                                    </div>
                                </section>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row col col-sm-12">
                                <section class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="porcentaje_segundo_gasto" style="font-size: 10;">Valor o Porcentaje {{ $company->nombre_segundo_gasto_credito}}</label>
                                        <select id="porcentaje_segundo_gasto" name="porcentaje_segundo_gasto" class="form-control" wire:model="porcentaje_segundo_gasto" wire:key="porcentaje_segundo_gasto">
                                            <option value="" selected="">-- Seleccione-- </option>
                                            <option value="0"> VALOR </option>
                                            <option value="1"> PORCENTAJE </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="segundo_gasto">{{ $company->nombre_segundo_gasto_credito}}</label>
                                        <input type="number" class="form-control text-uppercase" id="segundo_gasto" name="segundo_gasto" placeholder="0.00" wire:model="segundo_gasto">
                                    </div>
                                </section>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row col col-sm-12">
                                <section class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="porcentaje_tercer_gasto" style="font-size: 10;">Valor o Porcentaje {{ $company->nombre_tercer_gasto_credito}}</label>
                                        <select id="porcentaje_tercer_gasto" name="porcentaje_tercer_gasto" class="form-control" wire:model="porcentaje_tercer_gasto" wire:key="porcentaje_tercer_gasto">
                                            <option value="" selected="">-- Seleccione-- </option>
                                            <option value="0"> VALOR </option>
                                            <option value="1"> PORCENTAJE </option>
                                        </select>
                                    </div>
                                </section>
                                <section class="col-12 col-md-6">
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
                                <section class="col-12 col-md-3">
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
                                <section class="col-12 col-md-6">
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

                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="guardarCredito"><i class="fa fa-save me-2"></i>Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




</div>