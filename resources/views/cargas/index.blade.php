@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="callout callout-info">
                    <h5><i class="fas fa-info"></i> Nota:</h5>
                    Verifique todos los datos ingresados, una vez aceptado no prodrá <b>REVERTIR EL CRÉDITO GENERADO</b>
                </div>
                <div id="formulario_fondo" class="card">
                    <div class="">
                        <div class="row">
                            <div class="col-lg-5 col-12">
                                <div class="description-block border-right col col-sm-12">
                                    <section class="col col-sm-12">
                                        <div class="error-content">
                                            <h3><i class="fas fa-user text-primary"></i> Busqueda del Cliente</h3>
                                            <p>
                                                Puede buscar por: <a href="">Cédula</a> para encontrar el cliente.
                                            </p>
                                            <div class="input-group">
                                                <input type="number" name="search-customer" id="search-customer" class="form-control" placeholder="Buscar Cliente" style="height: 65px;font-size: 45px;">
                                            </div>
                                            <small>Presione <code>ENTER</code> para realizar la busqueda</small>
                                        </div>
                                    </section>
                                    <div class="row invoice-info">
                                        <div class="col-sm-12 invoice-col">
                                            <address>
                                                <strong id="name_customer"></strong><br>
                                                Cédula: <label id="cedulaCustomer"></label><br>
                                                Teléfono: <label id="telefonoCustomer"></label><br>
                                                Interes: <label id="interesCustomer"></label> %<br>
                                            </address>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7 col-12">
                                <div class="col-sm-12 invoice-col">
                                    <form id="form_credito" autocomplete="false" autocomplete="off" class="validate-form" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" id="id_customer_generar" name="id_customer_generar" value="0">
                                        <div class="row col col-sm-12">
                                            <section class="col col-sm-6">
                                                <div class="form-group">
                                                    <label for="valor_prestamo">Fecha:</label>
                                                    <input type="date" class="form-control text-uppercase" id="date_created" name="date_created" required="" value="{{date('Y-m-d')}}">
                                                </div>
                                            </section>
                                            <section class="col col-sm-6">
                                                <div class="form-group">
                                                    <label for="valor_prestamo">Valor De Préstamo</label>
                                                    <input type="number" class="form-control text-uppercase" id="valor_prestamo" name="valor_prestamo" step="0.01" placeholder="0.00" required="">
                                                </div>
                                            </section>
                                            <section class="col col-sm-6">
                                                <div class="form-group">
                                                    <label for="garante">Garante</label>
                                                    <select class="form-control form-control-sm select2" type="text" id="garante" name="garante" style="width: 100%; height: 100%;" required>
                                                        <option value=""> -- Seleccione -- </option>
                                                        @foreach($garantes as $garante)
                                                        <option value="{{$garante->id}}">{{$garante->nombres}} {{$garante->apellidos}} - {{$garante->numero_documento}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </section>
                                            <section class="col col-sm-6">
                                                <div class="form-group">
                                                    <label for="valor_prestamo">Numero Carpeta</label>
                                                    <input type="number" class="form-control text-uppercase" id="carpeta_numero" name="carpeta_numero" step="0.01" placeholder="0.00" required="">
                                                </div>
                                            </section>
                                            <section class="col col-sm-6">
                                                <div class="form-group">
                                                    <label for="valor_encaje">Valor Encaje</label>
                                                    <input type="number" class="form-control text-uppercase" id="valor_encaje" name="valor_encaje" step="0.01" placeholder="0.00" required="" value="0.00">
                                                </div>
                                            </section>
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <div class="custom-file">
                                                <label>Archivos</label>
                                                <div class="custom-file">
                                                    <input id="file2" type="file" class="custom-file-input" onchange="$('#showImg').html(this.value)" name="file2">
                                                    <label class="custom-file-label" for="validatedCustomFile" id="showImg">Elegir Archivo...</label>
                                                </div>
                                            </div>
                                            <br>
                                            <br>
                                            <div class="custom-file">
                                                <a class="btn btn-info" href="{{URL::to('cargas/detalles')}}" style="color: white;"><i class="fa fa-download"></i>Descargar Plantilla</a>
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
@include('credit/modal_customer_new')
@endsection
@section('scripts')
<script type="text/javascript">
    $(function() {

        $('.select2').select2()

        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

        $("#file2").change(function() {
            readURL(this, '');
        });
        $('#search-customer').focus();
        $('#search-customer').keydown(function(event) {
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
                        success: function(res) {
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

        var i_customer = $('#id_customer_generar').val();
        var date = $('#date_created').val();
        var valor = $('#valor_prestamo').val();
        var garante = ($('#garante').val() != '') ? $('#garante').val() : 0;
        var carpeta = $('#carpeta_numero').val();
        var valor_encaje = $('#valor_encaje').val();

        var file = $("#file2")[0].files[0];
        var formData = new FormData();
        console.log(formData);
        formData.append("file", file);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{URL::to('cargas/credito')}}/" + i_customer + '/' + date + '/' + valor + '/' + garante + '/' + carpeta + '/' + valor_encaje,
            type: "post",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        }).done(function(res) {
            window.location.href = "{{ URL::to('/cargas') }}";

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
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: "{{URL::to('customer/new')}}",
                data: $('#form_customer_new').serialize()
            }).done(function(res) {
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
            success: function(res) {
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