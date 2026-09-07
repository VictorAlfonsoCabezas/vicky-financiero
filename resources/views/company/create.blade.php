@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<div class="card card-dark">
    <div class="card-header">
        Información de Empresas
    </div>
    <div class="card-body col-lg-12">
        <div class="col-12">
            <form action="/company" method="POST" autocomplete="false" autocomplete="off" class="validate-form" enctype="multipart/form-data">
                @csrf
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-three-home-tab" data-bs-toggle="pill" href="#basic" role="tab" aria-controls="custom-tabs-three-home" aria-selected="false">Básica</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-three-profile-tab" data-bs-toggle="pill" href="#tax" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="true">Porcentajes</a>
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
                                            <textarea class="form-control" rows="3" name="company_name" id="company_name" placeholder="Nombre de la Empresa" required value=""></textarea>
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="comercial_name">Nombre Comercial</label>
                                            <input type="text" class="form-control text-uppercase" name="comercial_name" id="comercial_name" placeholder="Escribe Comercial" required>
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="ruc">RUC</label>
                                            <input type="number" class="form-control text-uppercase" name="ruc" id="ruc" placeholder="RUC" required>
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="legal_representative">Representante Legal</label>
                                            <input type="text" class="form-control text-uppercase" name="legal_representative" id="legal_representative" placeholder="Representante Legal" required>
                                        </div>
                                    </section>
                                </div>
                                <hr class="my-10">
                                <div class="row">
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="ciudad">Ciudad de la Empresa</label>
                                            <input type="text" class="form-control text-uppercase" name="ciudad" id="ciudad" placeholder="Ciudad" required value="">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="pais">País Comercial</label>
                                            <input type="text" class="form-control text-uppercase" name="pais" id="pais" placeholder="País" required value="">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="obligar_garante">Obligar selección Garante sin ser Socio</label>
                                            <select class="form-control" name="obligar_garante" id="obligar_garante">
                                                <option value="">- Seleccione -</option>
                                                <option value="0">No</option>
                                                <option value="1">Si</option>
                                            </select>
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="porcentaje_retener_credito">Porcentajea Retener en Credito</label>
                                            <input type="number" class="form-control text-uppercase" name="porcentaje_retener_credito" id="porcentaje_retener_credito" placeholder="0.00" required value="">
                                        </div>
                                    </section>
                                </div>
                                <hr class="my-10">
                                <div class="row col col-sm-12">
                                    <section class="col col-sm-12">
                                        <div class="form-group">
                                            <label for="address">Dirección</label>
                                            <textarea class="form-control text-uppercase" name="address" id="address" required></textarea>
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="phone">Telefono</label>
                                            <input type="number" class="form-control text-uppercase" name="phone" id="phone" placeholder="Telefono" required>
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="email">E-mail</label>
                                            <input type="email" class="form-control" name="email" id="email" placeholder="Correo Electrónico" required>
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label>Tipo Empresa</label>
                                            <select class="form-control" id="company_type" name="company_type" onchange="javascript:cambioLayapa();">
                                                <option value="1">CAJA DE AHORROS</option>
                                                <!--<option value="2">LAYAPA </option>-->
                                            </select>
                                        </div>
                                    </section>
                                    <section class="col col-sm-3 category_layapa">
                                        <div class="form-group">
                                            <label>Categoria Layapa</label>
                                            <select class="form-control" id="category_type" name="category_type">
                                                @foreach($category as $value)
                                                <option value="{{$value->id}}">{{$value->title}} | LAYAPA </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </section>
                                    <section class="col col-sm-3 category_layapa">
                                        <div class="form-group">
                                            <label for="domi">Domicilio</label>
                                            <input type="number" class="form-control" name="domicilio" id="domicilio" value="0.00" required>
                                        </div>
                                        <small class="note text-primary">DOMICILIO LAYAPA (Por Defecto)</small>
                                    </section>
                                </div>
                                <hr class="my-10">

                                <div class="row col col-sm-12">
                                    <div class="form-group col-md-8">
                                        <label>Imagen</label>
                                        <div class="custom-file">
                                            <input id="photo" type="file" class="custom-file-input"  onchange="$('#showImg').html(this.value)" name="photo" accept="image/*">
                                            <label class="custom-file-label" for="validatedCustomFile" id="showImg">Elegir Archivo...</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <div class="product-image">
                                            <img src="{{ URL::asset('/img/no-disponible.png') }}" id="prevPhoto" class="img-thumbnail" style="width: 300px;">
                                        </div>
                                        <div class="note" id="prevPhotoText"></div>
                                        <small class="note text-danger">Dimensiones de trabajo en <strong>Empresas</strong> (300px X 200px)</small>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox" checked="">
                                                <input class="custom-control-input" type="checkbox" id="electronica" name="electronica">
                                                <label for="electronica" class="custom-control-label">Facturació Electrónica</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="conexion" name="conexion" checked="">
                                                <label for="conexion" class="custom-control-label">Conexión Internet</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tax" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">
                                <div class="row">
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="company_name">PORCENTAJE</label>
                                            <input type="text" class="form-control text-uppercase" name="company_name" id="company_name" placeholder="Nombre de la Empresa">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="comercial_name">PORCENTAJE</label>
                                            <input type="text" class="form-control text-uppercase" name="comercial_name" id="comercial_name" placeholder="Escribe Comercial">
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="ruc">PORCENTAJE</label>
                                            <input type="number" class="form-control text-uppercase" name="ruc" id="ruc" placeholder="RUC">
                                        </div>
                                    </section>
                                </div>
                                <hr class="my-10">
                                <div class="row">
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="select_tipo_interes">Tipo de máximo de interes</label>
                                            <select class="form-control" name="select_tipo_interes" id="obligar_garante">
                                                <option value="">- Seleccione -</option>
                                                <option value="M">FIN DE MES</option>
                                                <option value="D">DIAS</option>
                                            </select>
                                        </div>
                                    </section>
                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="numero_dias_interes">Días</label>
                                            <input type="text" class="form-control text-uppercase" name="numero_dias_interes" id="numero_dias_interes" placeholder="Interés por Mora" value="">
                                        </div>
                                    </section>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Registrar</button>
                <button type="reset" class="btn btn-danger"><a href="/company" style="color: white;">Cancelar</a></button>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        cambioLayapa();
        $("#form-company").validate({
            errorPlacement: function (error, element) {
//                error.insertAfter(element.parent());
            }
        });

        $("#photo").change(function () {
            readURL(this);
        });

        function readURL(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();
                var fileName = input.files[0].name;
                var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
                var fileSize = input.files[0].size;
                var fileType = input.files[0].type;
                reader.onload = function (e) {
                    $('#prevPhoto').attr('src', e.target.result);
                    $('#prevPhotoText').html("<span class='info'><b>Archivo para subir:</b> " + fileName + "<br> <b>Peso total:</b> " + fileSize + " bytes. <br><b>Tipo: </b>" + fileType + ",<br> <b>Extención:</b> " + fileExtension + "</span>");
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#electronicaS").change(function () {
            if ($('#electronicaS').is(":checked")) {
                $('#electronica').val(1);
            } else {
                $('#electronica').val(0);
            }
        });

        $("#conexionS").change(function () {
            if ($('#conexionS').is(":checked")) {
                $('#conexion').val(1);
            } else {
                $('#conexion').val(0);
            }
        });
    });
    function cambioLayapa() {
        console.log($('#company_type').val());
        if ($('#company_type').val() == 1) {
            $('.category_layapa').hide();
        } else {
            $('.category_layapa').show();
        }
    }
</script>
@stop