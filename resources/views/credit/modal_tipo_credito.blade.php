<div class="modal fade" id="formTipoPrestamoNew" tabindex="1" role="dialog" aria-labelledby="PagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tipo Prestamo.</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_prestamo_new" autocomplete="false" autocomplete="off" class="validate-form" enctype="multipart/form-data">
                    <input type="hidden" id="tipoPestamoID" name="tipoPestamoID" value="">
                    @csrf
                    <div class="row col col-sm-12">
                        <section class="col col-sm-3">
                            <div class="form-group">
                                <label for="name">Nombres</label>
                                <input type="text" class="form-control text-uppercase" id="name" name="name" placeholder="Nombre" required="">
                            </div>
                        </section>
                        <section class="col col-sm-3">
                            <div class="form-group">
                                <label for="interes">Interes</label>
                                <input type="number" class="form-control text-uppercase" id="interes" name="interes" placeholder="0.00" required="">
                            </div>
                        </section>
                        <section class="col col-sm-3">
                            <div class="form-group">
                                <label for="interes_anual">Interes Anual</label>
                                <input type="number" class="form-control text-uppercase" id="interes_anual" name="interes_anual" placeholder="0.00" required="">
                            </div>
                        </section>
                        <section class="col col-sm-3">
                            <div class="form-group">
                                <label for="fondo_desgravamen">Fondo Desgravamen</label>
                                <input type="number" class="form-control text-uppercase" id="fondo_desgravamen" name="fondo_desgravamen" placeholder="0.00" required="">
                            </div>
                        </section>
                    </div>
                    <div class="row col col-sm-12">
                        <section class="col col-sm-3">
                            <div class="form-group">
                                <label for="type">Tipo</label>
                                <select type="text" id="type" name="type" class="form-control" required>
                                    <option value="F" selected=""> FRANCES</option>
                                    <option value="A"> ALEMÁN</option>
                                </select>
                            </div>
                        </section>
                        <section class="col col-sm-3">
                            <div class="form-group">
                                <label for="valor_minimo">Valor Mínimo</label>
                                <input type="number" class="form-control text-uppercase" id="valor_minimo" name="valor_minimo" placeholder="0.00" required="">
                            </div>
                        </section>
                        <section class="col col-sm-3">
                            <div class="form-group">
                                <label for="valor_maximo">Valor Máximo</label>
                                <input type="text" class="form-control text-uppercase" id="valor_maximo" name="valor_maximo" placeholder="0.00" required="">
                            </div>
                        </section>
                        <section class="col col-sm-3">
                            <div class="form-group">
                                <label for="edad_minima">Edad Mínima</label>
                                <input type="number" class="form-control text-uppercase" id="edad_minima" name="edad_minima" placeholder="0.00" required="">
                            </div>
                        </section>
                    </div>
                    <div class="row col col-sm-12">
                        <section class="col col-sm-3">
                            <div class="form-group">
                                <label for="edad_maxima">Edad Máximo</label>
                                <input type="text" class="form-control text-uppercase" id="edad_maxima" name="edad_maxima" placeholder="0.00" required="">
                            </div>
                        </section>
                        <section class="col col-sm-3">
                            <div class="form-group">
                                <label for="type">Diario</label>
                                <select type="text" id="diario" name="diario" class="form-control" required>
                                    <option value="" selected="">-- Seleccione-- </option>
                                    <option value="1"> SI </option>
                                    <option value="0"> NO </option>
                                </select>
                            </div>
                        </section>
                        <section class="col col-sm-3">
                            <div class="form-group">
                                <label for="type">Recurrencia</label>
                                <select type="text" id="periodo_id" name="periodo_id" class="form-control" required>
                                    <option value="" selected="">-- Seleccione-- </option>
                                    @foreach($recurrencia as $key => $prestamo)
                                    <option value="{{$prestamo->id}}"> {{$prestamo->name}} </option>
                                    @endforeach

                                </select>
                            </div>
                        </section>
                        <section class="col col-sm-3">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="letra_cambio" name="letra_cambio" >
                                    <label class="form-check-label">Letra de Cambio</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="pagare" name="pagare">
                                    <label class="form-check-label">Pagaré</label>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <a type="button" class="btn btn-primary" href="javascript:guardarNuewTipoPrestamo()" style="color: white" id="boton_generar">Generar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>