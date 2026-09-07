@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
    <div class="card card-primary shadow-lg bg-white rounded">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Registro Pagos Vencidos</h3>
            </div>
            <div class="card-body">

                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-lg-5">
                                    <label>Fecha Inicio:</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="inicio"
                                            value="{{ $primero }}">
                                    </div>
                                </div>
                                <div class="form-group col-lg-5">
                                    <label>Fecha Actual:</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="fin"
                                            value="{{ $ultimo }}">
                                    </div>
                                </div>
                                <div>
                                    <button onclick="javascript:reporteInteresMora();"class="btn btn-primary"
                                        style="position: relative;top: 31px;right: -10px;"><i
                                            class="fas fa-clipboard-list"></i> Consultar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div id="botones" class="dataTables_wrapper dt-bootstrap4">
                    <div class="row">
                        <div class="col-sm-12" id="div_cajas">
                            <table id="table_cobros" class="table table-bordered table-striped dataTable dtr-inline compact"
                                role="grid" aria-describedby="example1_info">
                                <thead class="thead-primary" style="font-size: 10px;">
                                    <tr role="row">
                                        <th scope="col">ID</th>
                                        <th scope="col">Cliente</th>
                                        <th scope="col">No. Cuota</th>
                                        <th scope="col">Carpeta</th>
                                        <th scope="col">Fecha Vencimiento</th>
                                        <th scope="col">Dias Mora</th>
                                        <th scope="col">Valor Cuota</th>
                                        <th scope="col">Interés</th>
                                        <th scope="col">Estado</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px;">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.sidebar-mini').addClass('sidebar-collapse');
            $("#table_cobros").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
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
                    "searchPlaceholder": "Datos para Buscar",
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
            }).buttons().container().appendTo('#botones .col-md-6:eq(0)');
        });

        function reporteInteresMora() {
            loading();
            var inicio = $('#inicio').val();
            var fin = $('#fin').val();
            $.ajax({
                method: "GET",
                url: "{{ URL::to('cobranza/reporteInteresMora') }}/" + inicio + '/' + fin
            }).done(function(res) {
                stoploading();
                console.log(res);
                var div = '';
                div +=
                    '<table id="table_cobros" class="table table-bordered table-striped dataTable dtr-inline compact" role="grid" aria-describedby="example1_info">';
                div += '    <thead class="thead-primary" style="font-size: 10px;">';
                div += '        <tr role="row">';
                div += '            <th scope="col">ID</th>';
                div += '            <th scope="col">Cliente</th>';
                div += '            <th scope="col">No. Cuota</th>';
                div += '            <th scope="col">Carpeta</th>';
                div += '            <th scope="col">Fecha Vencimiento</th>';
                div += '            <th scope="col">Dias Mora</th>';
                div += '            <th scope="col">Valor Cuota</th>';
                div += '            <th scope="col">Interes</th>';
                div += '            <th scope="col">Estado</th>';
                div += '        </tr>';
                div += '    </thead>';
                div += '    <tbody style="font-size: 14px;">';
                $.each(res, function(data, value) {
                    div += '        <tr>';
                    div += '            <td class="text-center">' + value.id + '</td>';
                    div += '            <td class="text-center"><a href="/credit/prestamos/' + value
                        .customer_id +
                        '" target="_blank"><i class="fas fa-eye" aria-hidden="true"></i><b>' + value
                        .customer_name + '</b><p>' + value.customer_ruc + '</a></td>';
                    div += '            <td class="text-center"><span class="badge bg-primary">' + value
                        .numero_cuota + '</span></td>';
                    div += '            <td class="text-center">' + value.code_folder_header + '</td>';
                    div += '            <td class="text-center"><span class="badge bg-danger">' + value
                        .date_vencimiento + '</span></td>';
                    div += '            <td class="text-center"><span class="badge bg-danger">' + value
                        .dias_mora + '</span></td>';
                    div += '            <td class="text-center">$ ' + value.valor_cuota + '</td>';
                    div += '            <td class="text-center">$ ' + value.interes + '</td>';
                    div += '            <td class="text-center"><span class="badge bg-danger">' + value
                        .status + '</span>';
                    div += '            <a class="btn  btn-xs" onclick="javascript:envioWhatsappWeb(' +
                        value.id +
                        ')" ><span class="badge badge-default"><i class="fa fa-paper-plane"></i></span></a>';
                    div += '            <a class="btn  btn-xs" onclick="javascript:notificarLetra(' + value
                        .id +
                        ')" ><span class="badge bg-danger"><i class="fas fa-envelope"></i></span></a>';
                    div += '            </td>';
                    div += '        </tr>';
                });
                div += '    </tbody>';
                div += '</table>';
                $('#div_cajas').html(div);
                $("#table_cobros").DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
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
                        "searchPlaceholder": "Datos para Buscar",
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
                }).buttons().container().appendTo('#botones .col-md-6:eq(0)');
            });
        }

        function envioWhatsappWeb(id) {
            $.ajax({
                url: "{{ URL::to('cobranza/envioWhatsappWeb') }}/" + id,
                type: 'GET',
                async: false,
            }).done(function(res) {
                if (res.code == '200') {
                    console.log(res);
                    var data = res;
                    var celular = data.telefono;
                    var mensaje = data.mensaje;
                    var baseUrl = 'https://web.whatsapp.com/send?phone=';
                    var formattedPhoneNumber = celular.replace(/\s/g, '');
                    var finalUrl = baseUrl + formattedPhoneNumber + '&text=' + encodeURIComponent(mensaje);
                    var newWindow = window.open(finalUrl, '_blank', 'width=600,height=600');
                    if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
                        alert('Por favor, permita que se abra la ventana emergente para continuar.');
                    }
                }

            });
        }
    </script>
@stop
