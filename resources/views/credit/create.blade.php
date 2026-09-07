@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<input type="hidden" id="tipo_tarifa_customer" name="tipo_tarifa_customer" value="0">
<input type="hidden" id="garanteObligar" name="garanteObligar" value="{{$company->obligar_garante}}">
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
                            <div class="col-lg-4 col-12">
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
                                </div>
                            </div>
                            <div class="col-lg-8 col-12">
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
                                                    <input type="number" class="form-control text-uppercase" id="valor_prestamo" name="valor_prestamo"  step="0.01" placeholder="0.00" required="">
                                                </div>
                                            </section>
                                            <section class="col col-sm-6">
                                                <div class="form-group">
                                                    <label for="plazo_anual">Años</label>
                                                    <input type="number" class="form-control text-uppercase" id="plazo_anual" name="plazo_anual"  step="1" placeholder="Años" required="">
                                                </div>
                                            </section>
                                            <section class="col col-sm-6">
                                                <div class="form-group">
                                                    <label for="cuotas_mensuales">Meses</label>
                                                    <input type="number" class="form-control text-uppercase" id="cuotas_mensuales" name="cuotas_mensuales"  step="1" placeholder="Meses" required="">
                                                </div>
                                            </section>
                                            <section class="col col-sm-6">
                                                <div class="form-group" id="div_garante">
                                                    <label for="garante">Garante</label>
                                                    <select class="form-control select2bs4" type="text" id="garante" name="garante" style="width: 100%; height: 100%;">
                                                        <option value="" > -- Seleccione -- </option>
                                                        @foreach($garantes as $garante)
                                                        <option value="{{$garante->id}}" >{{$garante->nombres}} {{$garante->apellidos}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </section>
                                            <section class="col col-sm-6">
                                                <div class="input-group">

                                                    <section class="col col-sm-10">
                                                        <div class="form-group">
                                                            <label for="prestamo_select">Prestamos</label>
                                                            <select id="prestamo_select" name="prestamo_select" class="form-control" required onchange="javascript:resumenSelec();">
                                                                <option value="" > -- Seleccione -- </option>
                                                                @foreach($prestamos as $val)
                                                                <option value="{{$val->id}}" >{{$val->name}} -> <b>{{$val->tipo}}</b></option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </section>
                                                    <input type="hidden" id="maxioPrestar" name="maxioPrestar" value="0">
                                                    <div class="card-footer" id="detallePrestamoSelect">
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <a type="button" class="btn btn-primary" onclick="javascript:realizarCredito();" style="color: white" id="boton_generar">Generar</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fas fa-globe"></i> Crédito Generado:
                                <small class="float-end">Fecha: {{ date('Y-m-d') }}</small>
                            </h4>
                        </div>
                    </div>
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            <address>
                                <strong id="name_customer"></strong><br>
                                Cédula: <label id="cedulaCustomer"></label><br>
                                Teléfono: <label id="telefonoCustomer"></label><br>
                                <!--Interes: <label id="interesCustomer"></label> %<br>-->
                            </address>
                        </div>                       
                    </div>
                    <div class="row">
                        <div class="col-12 table-responsive" id="div_tabla_cuotas">

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                        </div>
                        <div class="col-6">
                            <p class="lead">Datos del Crédito {{date('Y-m-d')}}</p>

                            <div class="table-responsive">
                                <table class="table">
                                    <tbody><tr>
                                            <th style="width:50%"><label id='desCuotaPagar'>Cuota a pagar periódicamente:</label></th>
                                            <td><label id="cabecera_valor_cuota"></lable></td>
                                        </tr>
                                        <tr>
                                            <th>Cantidad de cuotas:</th>
                                            <td><label id="cabecera_numero_cuotas"></lable></td>
                                        </tr>
                                        <tr>
                                            <th>Total interés a pagar:</th>
                                            <td><label id="cabecera_interes_total"></lable></td>
                                        </tr>
                                        <tr>
                                            <th>Valor del préstamo:</th>
                                            <td><label id="cabecera_valor_prestamo"></lable></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('credit/modal_customer_new')
@include('credit/modal_tipo_credito')
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('#garante').select2({
            theme: "bootstrap4"
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
                            stoploading();
                            if (res != '') {
                                var id = res[0].apellidos;
                                $('#name_customer').html(res[0].nombres + ' ' + res[0].apellidos);
                                $('#cedulaCustomer').html(res[0].numero_documento);
                                $('#telefonoCustomer').html(res[0].telefono);
//                                $('#interesCustomer').html(res[0].customer_tarifa_interes);
                                $('#id_customer_generar').val(res[0].id);
                                $('#tipo_tarifa_customer').val(res[0].customer_tarifa_name);
                                var name_tarifa = res[0].customer_tarifa_name;
                                if ($('#garanteObligar').val() == '0') {
                                    if (name_tarifa == 'SOCIO') {
                                        $('#div_garante').hide();
                                    } else {
                                        $('#div_garante').show();
                                    }
                                }
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
    function realizarCredito() {
        var crear = '';
//        if ($('#tipo_tarifa_customer').val() == 'PARTICULAR' && $('#garante').val() == '') {
//            crear = false;
//        }
//        if ($('#tipo_tarifa_customer').val() == 'PARTICULAR' && $('#garante').val() != '') {
//            crear = true;
//        }
//        if ($('#tipo_tarifa_customer').val() == 'SOCIO') {
//            crear = true;
//        }
        var maximo = $('#maxioPrestar').val();

        if ($('#cuotas_mensuales').val() == '' || $('#cuotas_mensuales').val() <= 11) {
            if ($('#valor_prestamo').val() !== '' && $('#plazo_anual').val() !== '') {
                if (parseFloat($('#valor_prestamo').val()) <= parseFloat($('#maxioPrestar').val())) {
                    Swal.fire({
                        title: "Seguro de realizar esta acción?",
                        text: "¡Tu no podrás revertir esta operación!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Aceptar"
                    }).then(function (result) {
                        if (result.value) {
                            loading();
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                method: "POST",
                                url: "{{URL::to('credit/creditos')}}",
                                data: $('#form_credito').serialize()
                            }).done(function (res) {
                                $('#boton_generar').hide();
                                $('#cabecera_valor_cuota').html(res.cabecera.valor_cuota);
                                if (res.prestamo.tipo != 'A') {
                                    $('#desCuotaPagar').html('Cuota a pagar capital promedio');
                                }
                                
                                $('#cabecera_numero_cuotas').html(res.cabecera.cuotas_pagar);
                                $('#cabecera_interes_total').html(res.cabecera.valor_interes_pago);
                                $('#cabecera_valor_prestamo').html(res.cabecera.valor_solicitado);
                                var detalle = '';
                                detalle += '<table id="tabla_cuotas"  class="table table-bordered table-striped dataTable dtr-inline compact" role="grid" aria-describedby="example1_info">';
                                detalle += '    <thead>';
                                detalle += '        <tr>';
                                detalle += '            <th>Número Cuota</th>';
                                detalle += '            <th>Fecha Vencimiento</th>';
                                detalle += '            <th>Interés del período</th>';
                                detalle += '            <th>Capital Amortizado</th>';
                                if (res.prestamo.tipo != 'A') {
                                    detalle += '            <th>Fondo de Desgravamen</th>';
                                }
                                detalle += '            <th>Cuota a pagar</th>';
                                detalle += '            <th> Saldo remanente </th>';
                                detalle += '        </tr>';
                                detalle += '    </thead>';
                                detalle += '    <tbody>';
                                $.each(res.detalle, function (key, value) {
                                    detalle += '    <tr>';
                                    detalle += '        <td>' + value.numero_cuota + '</td>';
                                    detalle += '        <td>' + value.date_vencimiento + '</td>';
                                    detalle += '        <td>' + value.interes_periodo + '</td>';
                                    detalle += '        <td>' + value.capital_amortizado + '</td>';
                                    if (res.prestamo.tipo != 'A') {
                                        detalle += '        <td>' + value.fondo_desgravamen + '</td>';
                                    }
                                    detalle += '        <td>' + value.valor_cuota + '</td>';
                                    detalle += '        <td>' + value.saldo_remanente + '</td>';
                                    detalle += '    </tr>';
                                });
                                detalle += '     </tbody>';
                                detalle += '</table>';
                                $('#div_tabla_cuotas').html(detalle);
                                $("#tabla_cuotas").DataTable({
                                    "responsive": true, "lengthChange": false, "autoWidth": false,
                                    "buttons": ["copy", "excel", "pdf", "print"],
                                    "language": {
                                        "emptyTable": "No hay datos disponibles en la tabla.",
                                        "info": "Del _START_ al _END_ de _TOTAL_ ",
                                        "infoEmpty": "Mostrando 0 registros de un total de 0.",
                                        "infoFiltered": "(filtrados de un total de _MAX_ registros)",
                                        "infoPostFix": "(actualizados)",
                                        "lengthMenu": "Mostrar _MENU_ registros",
                                        "loadingRecords": "Cargando...",
                                        "processing": "Procesando...",
                                        "search": "Buscar:",
                                        "searchPlaceholder": "Dato para buscar",
                                        "zeroRecords": "No se han encontrado coincidencias.",
                                        "paginate": {
                                            "first": "Primera",
                                            "last": "Última",
                                            "next": "Siguiente",
                                            "previous": "Anterior"
                                        },
                                        "aria": {
                                            "sortAscending": "Ordenación ascendente",
                                            "sortDescending": "Ordenación descendente"
                                        }
                                    },
                                }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
                            });
                            stoploading();
                        }
                    });
                } else {
                    Swal.fire('Valor maximo de <b> ' + maximo + '</b>');
                }
            } else {
                Swal.fire('Asegúrese de llenar todos los datos para realizar esta acción.');
            }
        } else {
            Swal.fire('Los meses no pueden ser mayores a 11');
            $('#cuotas_mensuales').val('0');
        }

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
                $('#id_customer_generar').val(res.id);
                $('#search-customer').val(res.numero_documento);
                $('#tipo_tarifa_customer').val(res.customer_tarifa_name);
                var name_tarifa = res.customer_tarifa_name;
                if (name_tarifa == 'SOCIO') {
                    $('#div_garante').hide();
                } else {
                    $('#div_garante').show();
                }
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
    function agrgarTipoPrestamo() {
        $('#formTipoPrestamoNew').modal('show');
    }
    function guardarNuewTipoPrestamo() {
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: "POST",
            url: "{{URL::to('credit/createTipoPrestamo')}}",
            data: $('#form_prestamo_new').serialize()
        }).done(function (res) {
            $('#formTipoPrestamoNew').modal('hide');
            var detalle = '';
            detalle += '<option value=""> --SELECCIONE-- </option>';
            $.each(res, function (key, value) {
                detalle += '<option value="' + value.id + '">' + value.name + '</option>';
            });
            $('#prestamo_select').html(detalle);
        });
    }
    function resumenSelec() {
        var prestmo = $('#prestamo_select').val();
        $.ajax({
            url: "{{URL::to('credit/buscarPrestamo')}}/" + prestmo,
            type: 'GET',
            success: function (res) {
                var detalle = 'EL PRESTAMO <b>' + res.name + '</b> TIENE TAZA DE INTERÉS : <b>' + res.interes + ' %</b>\n\
         , FONDO DE DESGAVAMEN : <b>' + res.fondo_desgravamen + '%</b><br> VALOR MÍNIMO <b>' + res.valor_minimo + '$</b> DE PRESTAMO VALOR MÁXIMO DE PRESTAMO <b>' + res.valor_maximo + '$</b>';
                $('#detallePrestamoSelect').html(detalle);
                $('#maxioPrestar').val(res.valor_maximo);
            }
        });
    }
</script>
@stop