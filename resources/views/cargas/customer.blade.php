@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="callout callout-info">
                    <h5><i class="fas fa-info"></i> Nota:</h5>
                    Antes de cargar el archivo verifique que todos los datos esten llenos <b>NO SE PODRAN REVERTIR LOS CAMBIOS</b>
                </div>
                <div id="formulario_fondo" class="card">
                    <div class="">
                        <div class="row">                            
                            <div class="col-lg-12 col-12">
                                <div class="col-sm-12 invoice-col">
                                    <form id="form_credito" autocomplete="false" autocomplete="off" class="validate-form" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" id="id_customer_generar" name="id_customer_generar" value="0">

                                        <div class="modal-footer justify-content-between">
                                            <div class="custom-file">
                                                <label>Archivos</label>
                                                <div class="custom-file">
                                                    <input id="file2" type="file" class="custom-file-input"  onchange="$('#showImg').html(this.value)" name="file2">
                                                    <label class="custom-file-label" for="validatedCustomFile" id="showImg">Elegir Archivo...</label>
                                                </div>
                                            </div>
                                            <br>
                                            <br>
                                            <div class="custom-file">
                                                <a class="btn btn-info" href="{{URL::to('custom/plantilla')}}" style="color: white;"><i class="fa fa-download"></i>Descargar Plantilla</a>
                                                <a class="btn btn-success" onclick="javascript:subirArchivo();" style="color: white;"><i class="fa fa-upload"></i> Subir Archivo</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script type="text/javascript">
    $(function () {
        $("#file2").change(function () {
            readURL(this, '');
        });
        $('#search-customer').focus();
        $('#search-customer').keydown(function (event) {
            if (event.shiftKey && event.keyCode === 224) {
                event.preventDefault();
            }
            if (event.keyCode === 13) {
                loading();
                if ($(this).val() !== '') {
                    var dato = $(this).val();
                    $.ajax({
                        url: "{{URL::to('customer/buscar')}}/" + dato,
                        type: 'GET',
                        success: function (res) {
                            console.log(res);
                            stoploading();
                            if (res != '') {
                                var id = res[0].apellidos;
                                $('#name_customer').html(res[0].nombres + ' ' + res[0].apellidos);
                                $('#cedulaCustomer').html(res[0].numero_documento);
                                $('#telefonoCustomer').html(res[0].telefono);
                                $('#interesCustomer').html(res[0].customer_tarifa_interes);
                                $('#id_customer_generar').val(res[0].id);
                            } else {
//                                $('#formCustomerNew').modal({backdrop: 'static', keyboard: false})
                                $('#formCustomerNew').modal('show');
                            }
                        }
                    });
                }
            }
        });
    });
    function readURL(input, tipo) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            var fileName = input.files[0].name;
            var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
            var fileSize = input.files[0].size;
            var fileType = input.files[0].type;

            reader.readAsDataURL(input.files[0]);
        }
    }

    function subirArchivo() {

        var file = $("#file2")[0].files[0];
        var formData = new FormData();
        console.log(formData);
        formData.append("file", file);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{URL::to('custom/cargar')}}",
            type: "post",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        }).done(function (res) {
            window.location.href = "{{ URL::to('/') }}";
        });
    }
    function guardarNuew() {
        var estado = $('#civil_val').val();
        var validar = '';
        if (estado == 1) {
            if ($('#nombres_conyugue').val() != '' && $('#identificacion_conyugue').val() != '' && $('#telefono_conyugue').val() != '') {
                validar = true;
            } else {
                validar = false;

            }
        } else {
            validar = true;
        }
        console.log(validar);
        if (validar == true) {
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                method: "POST",
                url: "{{URL::to('customer/new')}}",
                data: $('#form_customer_new').serialize()
            }).done(function (res) {
                console.log(res);
                $('#formCustomerNew').modal('hide');
                $('#id_customer_generar').val(res.id);
                $('#name_customer').html(res.nombres + ' ' + res.apellidos);
                $('#cedulaCustomer').html(res.numero_documento);
                $('#telefonoCustomer').html(res.telefono);
                $('#interesCustomer').html(res.customer_tarifa_interes);
                $('#search-customer').val(res.numero_documento);
            });
        } else {
            Swal.fire('Asegurese de llenar todos los datos');
        }
    }
    function buscarCustomer() {
        var dato = $('#number_ship').val();
        $.ajax({
            url: "{{URL::to('customer/buscarCedulas')}}/" + dato,
            type: 'GET',
            success: function (res) {
                console.log(res);
                stoploading();
                if (res == false) {
                    Swal.fire({
                        title: "Cliente ya existe",
                        text: "¡Ingrese nuevos Datos!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Aceptar"
                    });
                    $('#name').val('');
                    $('#last_name').val('');
                    $('#number_ship').val('');
                    $('#phone').val('');
                }
            }
        });
    }
    function compararDocumento() {
        var documento_titular = $('#number_ship').val();
        var documento_parentezco = $('#identificacion_parentesto').val();
        if (documento_titular == documento_parentezco) {
            Swal.fire({
                title: "El cliente no puede ser su Referenaci",
                text: "¡Ingrese nuevos datos!",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar"
            });
            $('#name').val('');
            $('#last_name').val('');
            $('#number_ship').val('');
            $('#phone').val('');
            $('#identificacion_parentesto').val('');
            $('#name_parentesco').val('');
        }
    }
    function cambioEstadoCivil() {
        var estado = $('#estado_civil').val();
        if (estado != 'SOLTERO/A' && estado != 'VIUDO/A') {
            $('#conyugue').show();
            $('#civil_val').val('1');
        } else {
            $('#conyugue').hide();
            $('#civil_val').val('0');
        }
    }

</script>
@stop