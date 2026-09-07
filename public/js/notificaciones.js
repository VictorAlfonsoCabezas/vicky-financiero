$(document).ready(function () {
 //   consultarAlertas();
//    atrasosLetras();
    

    $('#buscar_global').keydown(function (event) {
        if (event.shiftKey && event.keyCode === 224) {
            event.preventDefault();
        }
        if (event.keyCode === 13) {
            if ($(this).val() !== '') {
                loading();
                var dato = $(this).val();
                $.ajax({
                    url: "/customer/buscar/" + dato,
                    type: 'GET',
                    success: function (res) {
                        stoploading();
                        if (res != '') {
                            var customers = '';
                            $.each(res, function (key, value) {
                                customers += '<tr>';
                                customers += '     <td>' + value.code + '</td>';
                                customers += '     <td>' + value.nombres + ' ' + value.apellidos + '</td>';
                                customers += '     <td>' + value.numero_documento + '</td>';
                                customers += '     <td class="text-center"><a href="/creditos/' + value.id + '"  class="btn btn-lg btn-default"><i class="fas fa-check text-primary"></i></a></td>';
                                customers += '     <td class="text-center"><a href="/cuentas/' + value.id + '" class="btn btn-lg btn-default"><i class="fas fa-check text-primary"></i></a></td>';
                                customers += '     <td class="text-center"><a href="/clientes/' + value.id + '" class="btn btn-lg btn-default" ><i class="fas fa-check text-primary"></i></a></td>';
                                customers += '</tr>';
                            });
                            $('#customer_table >tbody').html(customers);
                            $('#formCustomerIngresos').modal('show');
                        } else {
                            Swal.fire({
                                title: "SIN REGISTROS DE ESTE CLIENTE",
                                type: "warning",
                                confirmButtonText: "ACEPTAR"
                            });

                        }
                    }
                });
            }
        }
    });

});

function irCuenta(id) {
    window.location.href = (__URL() + 'historial/movimientos/' + id);
}

function buscarCredito(id) {
    $.ajax({
        url: "/credit/verCredito/" + id,
        type: 'GET',
        success: function (res) {
            stoploading();
            if (res != '') {
                loading();
                window.location.href = (__URL() + 'credit/prestamos/' + res);
            } else {
                Swal.fire('Cliente no Cuenta con Prestamos');
            }
        }
    });
}

function consultarAlertas() {
    $.ajax({
        url: "/alertas/fechas",
        type: 'GET',
        async: false,
    }).done(function (res) {
        if (res.numero > 0) {
            $('#contadorCampana').html(res.numero);
            $('#contadorCampanaList').html(res.numero);
            listarNotificaciones();
        } else {
            $('#contadorCampana').html('0');
            $('#contadorCampanaList').html('0');
        }
    });
}

function listarNotificaciones() {
    $.ajax({
        url: "/alertas/nuevas",
        type: 'GET',
        async: false,
    }).done(function (res) {
        var alerta = '';
        $.each(res, function (key, value) {
            var validar = $('#listaCedditos').find('#pro_' + value.id).length;
            if (validar == 0) {
                alerta += '<div class="dropdown-item" id="pro_' + value.id + '" style="font-size: 12px;">';
                alerta += '       <i class="fas fa-bell" style="color:red"></i> ' + value.description;
                alerta += '     <span class="float-right text-muted text-sm">';
                alerta += '         <button class="btn btn-default btn-xs" onclick="javascript:updateAlert(' + value.id + ')" style="text-align: center;"><i class="fas fa-eye"></i></button>';
                alerta += '     </span>';
                alerta += '     <span class="float-right text-muted text-sm">';
                alerta += '         <button class="btn btn-default btn-xs" onclick="javascript:irPrestamos(' + value.id + ')" style="text-align: center;"><i class="fas fa-list-ul"></i></button>';
                alerta += '     </span>';
                alerta += '</div>';
            }
        });
        $('#listaCedditos').prepend(alerta);
    });
}

function updateAlert(id) {
    $.ajax({
        url: "/alertas/nuevas/" + id,
        type: 'GET',
        async: false,
    }).done(function (res) {
        if (res) {
            $('#pro_' + res).remove();
        }
    });
}

function irPrestamos(id) {
    $.ajax({
        url: "/alertas/prestamos/" + id,
        type: 'GET',
        async: false,
    }).done(function (res) {
        if (res) {
            window.location.href = (__URL() + 'credit/prestamos/' + res);
        }
    });
}

function atrasosLetras() {
    $.ajax({
        url: "/cobranza/listaLetras",
        type: 'GET',
        async: false,
    }).done(function (res) {
        var mora = '';
        $.each(res, function (data, value) {
            mora += '<tr>';
            mora += '<td class="text-center">' + value.customer_name + '<p><b>' + value.customer_ruc + '</b></td>';
            mora += '<td><span class="badge badge-danger"> ' + value.date_vencimiento + '</span></td>';
            mora += '<td><span class="badge badge-danger">  ' + value.dias_mora + '</span></td>';
            mora += '<td class="text-center">$ ' + value.valor_cuota + '</td>';
            mora += '<td class="text-center"><a class="btn  btn-xs" onclick="javascript:notificarLetra(' + value.id + ')" ><span class="badge badge-danger"><i class="fas fa-envelope"></i></span></a></td>';
            mora += '<tr>';
        });
        $('#mora_table > tbody').html(mora);

    });
}
//function atrasosLetrasMes() {
//    $.ajax({
//        url: "/cobranza/listaLetrasMes",
//        type: 'GET',
//        async: false,
//    }).done(function (res) {
//        var mora = '';
//        $.each(res.letras, function (data, value) {
//            mora += '<tr>';
//            mora += '<td class="text-center">' + value.customer_name + '<p><b>' + value.customer_ruc + '</b></td>';
//            mora += '<td><span class="badge badge-danger"> ' + value.date_vencimiento + '</span></td>';
//            mora += '<td><span class="badge badge-danger">  ' + value.dias_mora + '</span></td>';
//            mora += '<td class="text-center">$ ' + value.valor_cuota + '</td>';
//            mora += '<td class="text-center"><a class="btn  btn-xs" onclick="javascript:notificarLetra(' + value.id + ')" ><span class="badge badge-danger"><i class="fas fa-envelope"></i></span></a></td>';
//            mora += '<tr>';
//        });
//        $('#mora_table_mes > tbody').html(mora);
//        $('#mesLetrasVen').html(res.mes);
//    });
//}
function notificarLetra(id) {
    $.ajax({
        url: "/cobranza/notificarLetra/" + id,
        type: 'GET',
        async: false,
    }).done(function (res) {
        if (res) {
            Swal.fire({
                title: "Notificación enviada a: " + res.cabecera.customer_name,
                text: "¡Envío exitoso!",
                type: "warning",
                confirmButtonText: "Aceptar"
            });
        }

    });
}
function listarTabla() {
    var oTable = $('#tabla_pending_pay').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [[2, "DESC"]],
        "ajax": {
            url: "/cobranza/listaLetrasMesBuscar",
            "dataType": "json",
            "type": "GET",
            "async": false
        },
        columns: [
            {data: 'code_folder_header'},
            {data: 'date_vencimiento'},
            {data: 'numero_cuota'},
            {data: 'valor_cuota'},
        ]
    });

    $('#tabla_pending_pay thead #busqueda .filtre').each(function () {
        var title = $(this).text();

        if (title === 'Fecha Vencimiento') {
            $(this).html('<input type="text" placeholder="' + title + '" class="form-control datepicker"/>');
            // Aplicar el datepicker a los campos de filtro
            $(".datepicker").datepicker({
                dateFormat: 'yy-mm-dd', // Establece el formato de la fecha
                changeMonth: true,
                changeYear: true
            });
        } else {
            $(this).html('<input type="text" placeholder="' + title + '" class="form-control"/>');
        }
    });
}
function atrasosLetrasMes() {
    var oTable = $('#mora_table_mes').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [[2, "DESC"]],
        "ajax": {
            url: "/cobranza/listaLetrasMes",
            "dataType": "json",
            "type": "GET",
            "async": false
        },
        columns: [
            {data: 'code_folder_header'},
            {data: 'date_vencimiento'},
            {data: 'numero_cuota'},
            {data: 'valor_cuota'},
        ]
    });

    $('#mora_table_mes thead #busqueda .filtre').each(function () {
        var title = $(this).text();

        if (title === 'Fecha Vencimiento') {
            $(this).html('<input type="text" placeholder="' + title + '" class="form-control datepicker"/>');
            // Aplicar el datepicker a los campos de filtro
            $(".datepicker").datepicker({
                dateFormat: 'yy-mm-dd', // Establece el formato de la fecha
                changeMonth: true,
                changeYear: true
            });
        } else {
            $(this).html('<input type="text" placeholder="' + title + '" class="form-control"/>');
        }
    });
}
