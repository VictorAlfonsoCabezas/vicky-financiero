@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<div class="card card-success">
    <div class="card-header">
        Información de Empresas
    </div>
    <div class="card-body col-lg-12">
        <div class="col-12">
            <form action="{{URL::to('company/'.$company->id)}}" method="POST" autocomplete="false" autocomplete="off" class="validate-form" enctype="multipart/form-data">
                @csrf
                @method("put")
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-three-home-tab" data-bs-toggle="pill" href="#basic" role="tab" aria-controls="custom-tabs-three-home" aria-selected="false">Básica</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-three-profile-tab" data-bs-toggle="pill" href="#tax" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="true">Porcentajes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-three-profile-tab" data-bs-toggle="pill" href="#doc" role="doc" aria-controls="custom-tabs-three-profile" aria-selected="true">Documentos</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-three-tabContent">
                            <div class="tab-pane fade active show" id="basic" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                                <div class="row">
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="company_name">Nombre de la Empresa</label>
                                            <textarea class="form-control" rows="3" name="company_name" id="company_name" placeholder="Nombre de la Empresa" required value="{{old('company_name',$company->company_name)}}">{{old('company_name',$company->company_name)}}</textarea>
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="comercial_name">Nombre Comercial</label>
                                            <input type="text" class="form-control text-uppercase" name="comercial_name" id="comercial_name" placeholder="Nombre Comercial" required value="{{$company->comercial_name}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="ruc">RUC</label>
                                            <input type="number" class="form-control text-uppercase" name="ruc" id="ruc" placeholder="RUC" required value="{{old('company_name',$company->ruc)}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="legal_representative">Representante Legal</label>
                                            <input type="text" class="form-control text-uppercase" name="legal_representative" id="legal_representative" placeholder="Representante Legal" required value="{{old('company_name',$company->legal_representative)}}">
                                        </div>
                                    </section>
                                </div>
                                <hr class="my-10">
                                <div class="row">
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="ciudad">Ciudad de la Empresa</label>
                                            <input type="text" class="form-control text-uppercase" name="ciudad" id="ciudad" placeholder="Ciudad" required value="{{$company->ciudad}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="pais">País Comercial</label>
                                            <input type="text" class="form-control text-uppercase" name="pais" id="pais" placeholder="País" required value="{{$company->pais}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="obligar_garante">Obligar selección Garante sin ser Socio</label>
                                            <select class="form-control" name="obligar_garante" id="obligar_garante">
                                                <option value="">- Seleccione -</option>
                                                <option value="0" {{ ($company->obligar_garante == '0' ) ? 'selected':'' }}>No</option>
                                                <option value="1" {{ ($company->obligar_garante == '1' ) ? 'selected':'' }}>Si</option>
                                            </select>
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="porcentaje_retener_credito">Porcentajea Retener en Credito</label>
                                            <input type="number" class="form-control text-uppercase" name="porcentaje_retener_credito" id="porcentaje_retener_credito" placeholder="0.00" required value="{{old('company_name',$company->porcentaje_retener_credito)}}">
                                        </div>
                                    </section>
                                </div>
                                <hr class="my-10">
                                <div class="row col col-sm-12">
                                    <section class="col col-sm-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="genera_gastos_cobranza" name="genera_gastos_cobranza" @if($company->genera_gastos_cobranza) checked @endif>
                                            <label class="form-check-label">Genera Gastos Cobranza</label>
                                        </div>
                                    </section>
                                    <section class="col col-sm-2">
                                        <div class="form-group">
                                            <label for="valor_notificado">Valor Cobranza</label>
                                            <input type="number" class="form-control" name="valor_notificado" id="valor_notificado" placeholder="Valor Cobranza" value="{{$company->valor_notificado}}" step="0.01">
                                        </div>
                                    </section>
                                    <section class="col col-sm-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="active_cron" name="active_cron" @if($company->active_cron) checked @endif>
                                            <label class="form-check-label">Activar Cron</label>
                                        </div>
                                    </section>
                                    <section class="col col-sm-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="contabilidad" name="contabilidad" @if($company->contabilidad) checked @endif>
                                            <label class="form-check-label">Contabilidad</label>
                                        </div>
                                    </section>
                                    <section class="col col-sm-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enviar_mails" name="enviar_mails" @if($company->enviar_mails) checked @endif>
                                            <label class="form-check-label">Mails</label>
                                        </div>
                                    </section>
                                    <section class="col col-sm-2">
                                        <div class="form-group">
                                            <label for="time_cron">Hora Inicio Procesos</label>
                                            <input type="time" class="form-control" name="time_cron" id="time_cron" placeholder="Telefono" value="{{old('company_name',$company->time_cron)}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-2">
                                        <div class="form-group">
                                            <label for="time_cron">Fecha Inicio Contabilidad</label>
                                            <input type="date" class="form-control" name="fecha_inicio_contable" id="fecha_inicio_contable" placeholder="Telefono" value="{{old('company_name',$company->fecha_inicio_contable)}}">
                                        </div>
                                    </section>
                                </div>
                                <div class="row col col-sm-12">
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="phone">Telefono</label>
                                            <input type="number" class="form-control" name="phone" id="phone" placeholder="Telefono" required value="{{old('company_name',$company->phone)}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="email">E-mail</label>
                                            <input type="email" class="form-control" name="email" id="email" placeholder="Correo Electrónico" required value="{{old('company_name',$company->email)}}">
                                        </div>
                                    </section>
                                </div>
                                <div class="row col col-sm-12">
                                    <section class="col col-sm-12">
                                        <div class="form-group">
                                            <label for="address">Dirección</label>
                                            <textarea type="text" class="form-control text-uppercase" name="address" id="address" required="">{{old('company_name',$company->address)}}</textarea>
                                        </div>
                                    </section>
                                </div>
                                <hr class="my-10">
                                <div class="row col col-sm-12">
                                    <div class="form-group col-md-8">
                                        <label>Imagen</label>
                                        <div class="custom-file">
                                            <input id="photo" type="file" class="custom-file-input" onchange="$('#showImg').html(this.value)" name="photo" accept="image/*">
                                            <label class="custom-file-label" for="validatedCustomFile" id="showImg">Elegir Archivo...</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        @if($company->photo !== null)
                                        <img alt="{{$company->company_name}}" src="{{ URL::asset('/uploads/companies/'.$company->photo) }}" title="{!! $company->company_name !!}" id="prevPhoto" class="img-thumbnail" style="width: 300px;" /><br />
                                        @else
                                        <img alt="{{$company->company_name}}" src="{{ URL::asset('/img/no-disponible.png') }}" title="{!! $company->company_name !!}" id="prevPhoto" class="img-thumbnail" style="width: 300px;" /><br />
                                        @endif
                                        <div class="note" id="prevPhotoText"></div>
                                        <small class="note text-danger">Dimensiones de trabajo en <strong>Empresas</strong> (300px X 200px)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tax" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">
                                <div class="row">
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="desgravament">Fondo Desgravament</label>
                                            <input type="number" class="form-control text-uppercase" name="desgravament" id="desgravament" placeholder="Fondo de Desgravament" value="{{$company->porcentaje_desgravament}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="mora">Interés por Mora</label>
                                            <input type="text" class="form-control text-uppercase" name="mora" id="mora" placeholder="Interés por Mora" value="{{$company->porcentaje_mora}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="mora">Número Decimales</label>
                                            <input type="text" class="form-control text-uppercase" name="numero_decimales" id="numero_decimales" placeholder="Número de decimales" value="{{$company->numero_decimales}}" onchange="javascript:completarDecimal();">
                                        </div>
                                    </section>
                                </div>
                                <hr class="my-10">
                                <div class="row">
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="nombre_primer_gasto_credito">Primer Gasto Créditos</label>
                                            <input type="text" class="form-control text-uppercase" name="nombre_primer_gasto_credito" id="nombre_primer_gasto_credito" placeholder="Primer Gasto de Créditos" value="{{$company->nombre_primer_gasto_credito}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="nombre_segundo_gasto_credito">Segundo Gasto Créditos</label>
                                            <input type="text" class="form-control text-uppercase" name="nombre_segundo_gasto_credito" id="nombre_segundo_gasto_credito" placeholder="Segundo Gasto de Crédito" value="{{$company->nombre_segundo_gasto_credito}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="nombre_tercer_gasto_credito">Primer Gasto Créditos</label>
                                            <input type="text" class="form-control text-uppercase" name="nombre_tercer_gasto_credito" id="nombre_tercer_gasto_credito" placeholder="Tercer Gasto de Crédito" value="{{$company->nombre_tercer_gasto_credito}}">
                                        </div>
                                    </section>
                                </div>
                                <hr class="my-10">
                                <div class="row">
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="penalidad_plazo_fijo">Penalidad Plazo Fijo (Interes %)</label>
                                            <input type="number" class="form-control text-uppercase" name="penalidad_plazo_fijo" id="penalidad_plazo_fijo" placeholder="Penalidad Plazo Fijo (Interes %)" value="{{$company->penalidad_plazo_fijo}}" step="0.01">
                                        </div>
                                    </section>
                                </div>
                                <hr class="my-10">
                                <div class="row">
                                    <div class="col-sm-6">

                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="interes_fijo_parametrizado" name="interes_fijo_parametrizado" @if($company->interes_fijo_parametrizado) checked @endif>
                                                <label class="form-check-label">Calculo del interes Fijo Parametrizado</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-10">
                                <div class="row">
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="select_tipo_interes">Tipo de máximo de interes</label>
                                            <select class="form-control" name="select_tipo_interes" id="select_tipo_interes" onchange="javascript:cambioSelect();">
                                                <option value="">- Seleccione -</option>
                                                <option value="M" {{ ($company->select_tipo_interes == 'M' ) ? 'selected':'' }}>FIN DE MES</option>
                                                <option value="D" {{ ($company->select_tipo_interes == 'D' ) ? 'selected':'' }}>DIAS</option>
                                                <option value="S" {{ ($company->select_tipo_interes == 'S' ) ? 'selected':'' }}>SIGUIENTE FECHA</option>
                                            </select>
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="dias_inicio_cobro">Días Inicio Cobro</label>
                                            <input type="text" class="form-control text-uppercase" name="dias_inicio_cobro" id="dias_inicio_cobro" placeholder="Interés por Mora" value="{{$company->dias_inicio_cobro}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="numero_dias_interes">Días Maximo Cobro</label>
                                            <input type="text" class="form-control text-uppercase" name="numero_dias_interes" id="numero_dias_interes" placeholder="Interés por Mora" value="{{$company->numero_dias_interes}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="dias_gracia">Días Gracia</label>
                                            <input type="text" class="form-control text-uppercase" name="dias_gracia" id="dias_gracia" placeholder="Días Gracia" value="{{$company->dias_gracia}}" onchange="javascript:completarGracia();">
                                        </div>
                                    </section>
                                </div>
                                <hr class="my-10">
                                <div class="row">
                                    <div class="col-sm-6">

                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="letra_cambio" name="letra_cambio" @if($company->letra_cambio) checked @endif>
                                                <label class="form-check-label">Letra de Cambio</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="pagare" name="pagare" @if($company->pagare) checked @endif>
                                                <label class="form-check-label">Pagaré</label>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="doc" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">
                                <div class="row">
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="cartola_a">Cartola Cara A</label>
                                            <input type="number" class="form-control text-uppercase" name="cartola_a" id="cartola_a" placeholder="Lado A Cartola" value="{{$company->cartola_a}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="cartola_b">Cartola Cara B</label>
                                            <input type="number" class="form-control text-uppercase" name="cartola_b" id="cartola_b" placeholder="Lado B Cartola" value="{{$company->cartola_b}}">
                                        </div>
                                    </section>

                                </div>
                                <hr>
                                <div class="row">
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="margen_top">Margen Arriba</label>
                                            <input type="number" class="form-control text-uppercase" name="margen_top" id="margen_top" placeholder="Margen Arriba" value="{{$company->margen_top}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="margen_dow">Margen Abajo</label>
                                            <input type="number" class="form-control text-uppercase" name="margen_dow" id="margen_dow" placeholder="Margen Abajo" value="{{$company->margen_dow}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="margen_right">Margen Derecha</label>
                                            <input type="number" class="form-control text-uppercase" name="margen_right" id="margen_right" placeholder="Margen Derecha" value="{{$company->margen_right}}">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="margen_left">Margen Izquierda</label>
                                            <input type="number" class="form-control text-uppercase" name="margen_left" id="margen_left" placeholder="Margen Izquierda" value="{{$company->margen_left}}">
                                        </div>
                                    </section>
                                </div>
                                <hr>
                                <div class="row">
                                    <section class="col col-sm-3">
                                        <label for="color_texto">Selecciona un color para el texto:</label>
                                        <input type="color" id="color_texto" name="color_texto" value="{{$company->color_texto}}">
                                    </section>
                                    <section class="col col-sm-3">
                                        <label for="color_tabla">Selecciona un color para la tabla:</label>
                                        <input type="color" id="color_tabla" name="color_tabla" value="{{$company->color_tabla}}">
                                    </section>
                                </div>
                                <hr class="my-10">
                                <h4>Reporte de cartera</h4>
                                <div class="row col col-sm-12">
                                    <section class="col col-sm-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="reporte_cartera_unido" name="reporte_cartera_unido" @if($company->reporte_cartera_unido) checked @endif>
                                            <label class="form-check-label">Reporte de cartera unido</label>
                                        </div>
                                    </section>
                                </div>
                                <hr class="my-10">
                                <h4>Transferencias de caja a bóveda</h4>
                                <div class="row col col-sm-12">
                                    <section class="col col-sm-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="caja_boveda" name="caja_boveda" @if($company->caja_boveda) checked @endif>
                                            <label class="form-check-label">Permitir Transferencias de caja</label>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <button type="reset" class="btn btn-danger"><a href="/company" style="color: white;">Cancelar</a></button>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        cambioSelect();
        $("#photo").change(function() {
            readURL(this);
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                var fileName = input.files[0].name;
                var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
                var fileSize = input.files[0].size;
                var fileType = input.files[0].type;
                reader.onload = function(e) {
                    console.log(e);
                    $('#prevPhoto').attr('src', e.target.result);
                    $('#prevPhotoText').html("<span class='info'><b>Archivo para subir:</b> " + fileName + "<br> <b>Peso total:</b> " + fileSize + " bytes. <br><b>Tipo: </b>" + fileType + ",<br> <b>Extención:</b> " + fileExtension + "</span>");
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    });

    function cambioSelect() {
        var tipo = $('#select_tipo_interes').val();
        if (tipo == 'M') {
            $('#numero_dias_interes').val('');
            $('#numero_dias_interes').prop('disabled', true);
        } else {
            $('#numero_dias_interes').prop('disabled', false);
        }
    }

    function completarDecimal() {
        var cantidad = $("#numero_decimales").val();
        if (cantidad < 2) {
            Swal.fire({
                title: "Alerta! ",
                text: "¡No puede trabajar con menos de 2 decimales!",
                type: "warning",
                confirmButtonText: "Aceptar"
            });
            $("#numero_decimales").val(2);
        }
    }

    function completarGracia() {
        var dias = $("#dias_gracia").val();
        if (dias == '') {
            Swal.fire({
                title: "Alerta! ",
                text: "¡No puede dejar este campo sin un numero!",
                type: "warning",
                confirmButtonText: "Aceptar"
            });
            $("#dias_gracia").val(0);
        }
    }
</script>
@stop
