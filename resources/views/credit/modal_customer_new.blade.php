<div class="modal fade" id="formCustomerNew" tabindex="1" role="dialog" aria-labelledby="PagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">No hay registros con este numero de cedula Ingrese los datos para crear uno nuevo.</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_customer_new" autocomplete="false" autocomplete="off" class="validate-form" enctype="multipart/form-data">
                    <input type="hidden" id="civil_val" name="civil_val" value="0">
                    @csrf
                    <div class="row col col-sm-12">
                        <section class="col col-sm-4">
                            <div class="form-group">
                                <label for="valor_prestamo">Nombres</label>
                                <input type="text" class="form-control text-uppercase" id="name" name="name"   placeholder="Nombre" required="">
                            </div>
                        </section>
                        <section class="col col-sm-4">
                            <div class="form-group">
                                <label for="plazo_anual">Apellidos</label>
                                <input type="text" class="form-control text-uppercase" id="last_name" name="last_name"  placeholder="Apelledos" required="">
                            </div>
                        </section>
                        <section class="col col-sm-4">
                            <div class="form-group">
                                <label for="plazo_anual">Numero de C.I/R.U.C</label>
                                <input type="number" class="form-control text-uppercase" id="number_ship" name="number_ship"  step="1" placeholder="Numero de C.I/R.U.C" required="" onblur="buscarCustomer();">
                            </div>
                        </section>
                    </div>
                    <div class="row col col-sm-12">

                        <section class="col col-sm-4">
                            <div class="form-group">
                                <label for="plazo_anual">Telefono</label>
                                <input type="number" class="form-control text-uppercase" id="phone" name="phone"  placeholder="Teléfono" required="">
                            </div>
                        </section>
                        <section class="col col-sm-4">
                            <div class="form-group">
                                <label for="porcentaje">Tarifas</label>
                                <select type="text" id="tarifas" name="tarifas" class="form-control" required>
                                    <option value="" > -- Seleccione -- </option>
                                    @foreach($tarifas as $tarifa)
                                    <option value="{{$tarifa->id}}" >{{$tarifa->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </section>
                        <section class="col col-sm-4">
                            <label for="porcentaje">Estado Civil</label>
                            <select type="text" id="estado_civil" name="estado_civil" class="form-control text-uppercase" required onchange="javascript:cambioEstadoCivil();">
                                @foreach(config('constants.ESTOS_CIVIL') as  $type)
                                <option value="{{$type['code']}}"> {{$type['name']}} </option>
                                @endforeach
                            </select>
                        </section>
                    </div>
                    <div class="row col col-sm-12">
                        <section class="col col-sm-12">
                            <div class="form-group">
                                <label for="valor_prestamo">Dirección</label>
                                <textarea class="form-control text-uppercase" id="adress" name="adress"   placeholder="Dirección" required=""></textarea>
                            </div>
                        </section>
                    </div>
                    <hr>
                    <div class="row" id="conyugue" style="display: none">
                        <div class="form-group col-md-4">
                            <div class="form-group">
                                <label>Nombres Conyugue</label>
                                <input type="text" id="nombres_conyugue" name="nombres_conyugue" class="form-control text-uppercase" placeholder="Nombres Conyugue"  value="{{old('nombres')}}">
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <div class="form-group">
                                <label for="plazo_anual">Cedula Conyugue</label>
                                <input type="number" class="form-control text-uppercase" id="identificacion_conyugue" name="identificacion_conyugue"  placeholder="Identificacion del parentesco">
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <div class="form-group">
                                <label for="plazo_anual">Telefono Conyugue</label>
                                <input type="number" class="form-control text-uppercase" id="telefono_conyugue" name="telefono_conyugue"  placeholder="Telefono Conyugue">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row col col-sm-12">
                        <section class="col col-sm-4">
                            <div class="form-group">
                                <label for="porcentaje">Parentesco</label>
                                <select type="text" id="parentesco" name="parentesco" class="form-control" required>
                                    <option value="" > -- Seleccione -- </option>
                                    @foreach(config('constants.PARENTESCO') as $type)
                                    <option value="{{$type}}"> {{$type}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </section>
                        <section class="col col-sm-4">
                            <div class="form-group">
                                <label for="plazo_anual">Nombre Parentesco</label>
                                <input type="text" class="form-control text-uppercase" id="name_parentesco" name="name_parentesco"  step="1" placeholder="Nombre del parentesco" required="">
                            </div>
                        </section>
<!--                        <section class="col col-sm-4">
                            <div class="form-group">
                                <label for="plazo_anual">Cedula Parentesco</label>
                                <input type="number" class="form-control text-uppercase" id="identificacion_parentesto" name="identificacion_parentesto"  placeholder="Identificacion del parentesco" required="" onblur="compararDocumento();">
                            </div>
                        </section>-->
                        <section class="col col-sm-4">
                            <div class="form-group">
                                <label for="plazo_anual">Teléfono Parentesco</label>
                                <input type="number" id="telefono_parentesco" name="telefono_parentesco" class="form-control" placeholder="Telefono Parentesco" required>
                            </div>
                        </section>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <a type="button" class="btn btn-primary" href="javascript:guardarNuew()" style="color: white" id="boton_generar">Generar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>