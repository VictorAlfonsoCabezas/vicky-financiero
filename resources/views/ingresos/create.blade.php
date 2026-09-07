@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
    <div class="alert alert-primary">
        <h5><i class="icon fas fa-hand-holding-usd"></i> INGRESOS</h5>
        En este apartado podemos ingresar dinero en la cuenta de nuestros Clientes
    </div>
    <div class="card card-dark">
        <div class="card-body col-sm-12">
            <div class="row col col-sm-12">
                <div class="description-block border-right col col-sm-8">
                    <section class="col col-sm-12">
                        <div class="error-content">
                            <h3><i class="fas fa-user text-primary"></i> Busqueda del Cliente</h3>
                            <p>
                                Puede buscar por: <a href="">Cédula, Nombres o Código</a> para encontrar el cliente.
                            </p>
                            <div class="input-group">
                                <input type="text" name="search-customer" id="search-customer" class="form-control"
                                    placeholder="Buscar Cliente" style="height: 65px;font-size: 50px;">
                            </div>
                            <small>Presione <code>ENTER</code> para realizar la busqueda</small>
                        </div>
                    </section>
                </div>
                <div class="description-block border-right col col-sm-4">
                    <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> BALANCE DEL
                        CLIENTE</span>
                    <h5 class="description-header" id="nombre_customer"></h5>
                    <table id="table_balance" class="table m-0" style="display: none">
                        <tbody>
                            <tr>
                                <td><b>Ingresos</b></td>
                                <td>
                                    <span class="description-text" id="valance_signo"></span><span class="description-text"
                                        id="valance_customer"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Egresos</b></td>
                                <td>
                                    <span class="description-text" id="valance_signo"></span><span class="description-text"
                                        id="egresos_customer"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Total</b></td>
                                <td>
                                    <span class="description-text" id="valance_signo"></span><span class="description-text"
                                        id="valor_total"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="content" class="card card-primary" style="display: none;">
        <div class="card-header">
            Crear Deposito
        </div>
        <div class="card-body col-sm-12">
            <form id="form_ingreso" method="POST" autocomplete="false" autocomplete="off" class="validate-form"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_customer" id="id_customer" value="0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <a onclick="javascript:verTransacciones();" class="btn btn-warning btn-lg pull-right"
                            style="color: white;"><i class="fa fa-eye"></i> VER TRANSACCIONES</a>
                        <!--<a onclick="javascript:verCuenta();" class="btn btn-default btn-lg pull-right" style="color: black;"><i class="fa fa-folder"></i> CARTOLA</a>-->
                        <br>
                        <br>
                        <table class="table m-0">
                            <tbody>
                                <tr>
                                    <td>Fecha de la Transaccion: </td>
                                    <td colspan="3">
                                        <input class="form-control" type="date" value="{{ date('Y-m-d') }}"
                                            id="date_created" name="date_created">
                                        <small>Temporalmente Activado</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>CÓDIGO</b></td>
                                    <td>
                                        <span class="product-description badge bg-primary" id="code_label"></span>
                                    </td>
                                    <td><b>NOMBRE</b></td>
                                    <td>
                                        <span class="product-description" id="nombre_label"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>TELEFONO</b></td>
                                    <td>
                                        <span class="product-description" id="telefono_label"></span>
                                    </td>
                                    <td><b>DIRECCIÓN</b></td>
                                    <td>
                                        <span class="product-description" id="addres_label"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr class="my-10">
                <div class="row col col-sm-12">
                    <section class="col col-sm-6">
                        <div class="form-group">
                            <label for="description">Valor a Depositar</label>
                            <input type="number" class="form-control text-uppercase" id="valor_movimiento"
                                name="valor_movimiento" step="0.01" placeholder="0.00" required=""
                                style="height: 86px;font-size: 50px;color: blue;">
                        </div>
                    </section>
                    <section class="col col-sm-6">
                        <div class="form-group">
                            <label>Observación</label>
                            <textarea id="observation" name="observation" class="form-control" rows="3"
                                placeholder="Si existe alguna observación, puede ingresarla en este apartado" maxlength="1000"></textarea>
                        </div>
                    </section>
                </div>
                <hr class="my-10">
                <a onclick="javascript:guardarIngreso();" class="btn btn-danger btn-lg"
                    style="color: white;">DEPOSITAR</a>
                <button type="reset" class="btn btn-default btn-lg"><a href="/ingresos"
                        style="color: black;">CANCELAR</a></button>
            </form>
        </div>
    </div>
    @include('customer/modal_cartilla')
    @include('ingresos/modal_customer')
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.sidebar-mini').addClass('sidebar-collapse');
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
                            url: "{{ URL::to('customer/buscar') }}/" + dato,
                            type: 'GET',
                            success: function(res) {
                                stoploading();
                                if (res != '') {
                                    if (res.length > 1) {
                                        var customers = '';
                                        $.each(res, function(key, value) {
                                            customers += '<tr>';
                                            customers += '     <td>' + value.code +
                                                '</td>';
                                            customers += '     <td>' + value.nombres +
                                                '' + value.apellidos + '</td>';
                                            customers += '     <td>' + value
                                                .numero_documento + '</td>';
                                            customers +=
                                                '     <td class="text-center"><a class="btn btn-lg btn-default" onclick="seleccionarCustomer(' +
                                                value.id +
                                                ');"><i class="fas fa-check text-primary"></i></a></td>';
                                            customers += '</tr>';
                                        });
                                        $('#customer_table >tbody').html(customers);
                                        $('#formCustomerIngresos').modal('show');
                                    } else {
                                        var id = res[0].id;
                                        seleccionarCustomer(id);
                                    }
                                } else {
                                    Swal.fire({
                                        title: "SIN REGISTROS DE ESTE CLIENTE",
                                        type: "warning",
                                        confirmButtonText: "ACEPTAR"
                                    })
                                    $('#content').hide();
                                    $('#id_customer').val('');
                                    $('#code_label').html('');
                                    $('#nombre_label').html('');
                                    $('#telefono_label').html('');
                                    $('#addres_label').html('');
                                    $('#customer_ruc').val('');
                                    $('#nombre_customer').html('');
                                    $('#valance_signo').html('');
                                    $('#valor_total').html('');
                                    $('#search-customer').val('');
                                    $('#table_balance').hide();
                                }
                            }
                        });
                    }
                }
            });
        });

        function seleccionarCustomer(id) {
            loading();
            $.ajax({
                url: "{{ URL::to('customer/seleccionar') }}/" + id,
                type: 'GET',
                success: function(res) {
                    stoploading();
                    var nombre = res.customer.nombres + ' ' + res.customer.apellidos;
                    $('#id_customer').val(res.customer.id);
                    $('#code_label').html(res.customer.code);
                    $('#nombre_label').html(nombre);
                    $('#telefono_label').html(res.customer.telefono);
                    $('#addres_label').html(res.customer.direccion);
                    $('#customer_ruc').val(res.customer.numero_documento);
                    $('#nombre_customer').html(nombre);
                    $('#valance_customer').html('$ ' + RoundNumber(res.ingresos, 2).toFixed(2));
                    $('#egresos_customer').html('$ ' + RoundNumber(res.egresos, 2).toFixed(2));
                    $('#valor_total').html('$ ' + RoundNumber(res.valor, 2).toFixed(2));
                    $('#search-customer').val(res.customer.numero_documento);
                    $('#formCustomerIngresos').modal('hide');
                    $('#content').show();
                    $('#table_balance').show();
                }
            });
        }

        function guardarIngreso() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: "{{ URL::to('/ingresos') }}",
                data: $('#form_ingreso').serialize()
            }).done(function(res) {
                stoploading();
                location.href = "{{ URL::to('historial/movimientos') }}/" + res.customer_ruc;
            });

        }

        function verTransacciones() {
            let ruc = $('#search-customer').val();
            window.open('/historial/movimientos/' + ruc, '_blank');
        }

        function verCuenta() {
            let code = $('#customer_code').val();
            console.log(code);
            loading();
            $.ajax({
                url: "{{ URL::to('customer/cartilla') }}/" + code,
                type: 'GET',
                success: function(res) {
                    console.log(res);
                    if (res != '') {
                        $('#customer_name').html(res.cartolaHeader.customer_name);
                        $('#customer_code').html(res.customer.code);
                        $('#customer_ruc').html(res.customer.numero_documento);
                        $('#nombre_titular').html(res.cartolaHeader.customer_name);
                        $('#cartola_code').html(res.cartolaHeader.code);
                        var imprimr = '<a class="btn btn-success btn-sm" onclick="javascript:imprimirCartola(' +
                            res.cartolaHeader.id +
                            ');" title="Aprovar" style="color: white"><i class="fas fa-print"></i> IMPRIMIR</a>'
                        $('#imprimir_cartola').html(imprimr);
                        var detalle = '';
                        $.each(res.cartolaDetalle, function(key, value) {
                            detalle += '<tr>';
                            detalle += '     <td></td>';
                            detalle += '     <td>' + value.date_transaction + '</td>';
                            if (value.type_transaction_action == 'S' && value.type_transaction_id !=
                                6) {
                                detalle += '     <td>' + value.valor_transaction + '</td>';
                            } else {
                                detalle += '     <td></td>';
                            }
                            if (value.type_transaction_id == 6) {

                                detalle += '     <td>' + value.valor_transaction + '</td>';
                            } else {

                                detalle += '     <td>' + value.interes_valor + '</td>';
                            }
                            if (value.type_transaction_action == 'R') {
                                detalle += '     <td>' + value.valor_transaction + '</td>';
                            } else {
                                detalle += '     <td></td>';
                            }
                            detalle += '     <td>' + value.saldo_transaction + '</td>';
                            detalle += '</tr>';
                        });
                        $('#catola_detail >tbody').html(detalle);
                        $('#modal_cartilla').modal('show');
                        stoploading();
                    } else {
                        Swal.fire({
                            title: "NO DISPONE DE CARTOLA",
                            text: 'Para generar una cartola realice algún movimiento ...',
                            type: "warning",
                            confirmButtonText: "ACEPTAR"
                        });
                        stoploading();

                    }

                }
            });
        }
    </script>

@stop
