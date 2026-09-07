@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<div class="botonesSuperiores" style="margin-bottom: 5px;">
    <a class="btn btn-labeled btn-success header-btn" href="{{URL::to('interes/historia/descargar')}}">
        <span class="btn-label"><i class="fa fa-download"></i></span>
        Generar
    </a>
</div>
<div  class="card card-primary shadow-lg bg-white rounded">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Historial de Movimientos</h3>
        </div>
        <div class="card-body">
            <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <div class="row">
                    <div class="col-sm-12" id="div_tabla_historia">
                        <table id="table_intereses" class="table table-bordered table-striped dataTable dtr-inline compact" role="grid" aria-describedby="example1_info">
                            <thead class="thead-primary" style="font-size: 10px;">
                                <tr role="row">                                                              
                                    <th scope="col">#</th>
                                    <th scope="col">FECHA</th>
                                    <th scope="col">CODE</th>
                                    <th scope="col">DETALLE</th>
                                    <!--<th scope="col">DETALLE</th>-->
                                    <!--<th scope="col">N. DE COMPO</th>-->
                                    <th scope="col">INGRESOS</th>
                                    <th scope="col">EGRESOS</th>
                                    <th scope="col">SALDO</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px;">
                                @foreach($historial as  $historia)
                                <tr class="odd">
                                    <td>{{$historia->id}}</td>
                                    <td>{{$historia->date_created}}</td>
                                    <td>{{$historia->code}}</td>
                                    <td><strong>{{$historia->observation}}</strong></td>
                                    <!--<td>{{$historia->id}}</td>-->
                                    <!--<td>{{$historia->id}}</td>-->
                                    @if($historia->type_transaction_action == 'S')
                                    <td>{{$historia->valor_movimiento}}</td>
                                    <td>0</td>
                                    @else
                                    <td>0</td>
                                    <td>{{$historia->valor_movimiento}}</td>
                                    @endif
                                    <td>{{$historia->saldo_general}}</td>
                                </tr>
                                @endforeach
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
    $(function () {
        $("#table_intereses").DataTable({
            "order": [[0, "desc"]],
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
    $(document).ready(function () {
        $('.sidebar-mini').addClass('sidebar-collapse');
    });
    function cambiarTipo() {
        var id = $('#tipo_transaccion').val();
        $.ajax({
            url: "{{URL::to('interes/verTabla')}}/" + id,
            type: 'GET',
            success: function (res) {
                console.log(res);
                var detalle = '';
                detalle += '<table id="tabla_historial_armado"  class="table table-bordered table-striped dataTable dtr-inline compact" role="grid" aria-describedby="example1_info">';
                detalle += '    <thead>';
                detalle += '        <tr>';
                detalle += '            <th scope="col">DETALLE</th>';
                detalle += '            <th scope="col">VALOR</th>';
                detalle += '            <th scope="col">FECHA</th>';
                detalle += '            <th scope="col">HORA</th>';
                detalle += '        </tr>';
                detalle += '    </thead>';
                detalle += '    <tbody>';
                $.each(res, function (key, value) {
                    detalle += '    <tr>';
                    detalle += '        <td>' + value.observation_created + '</td>';
                    detalle += '        <td>' + value.valor + '</td>';
                    detalle += '        <td>' + value.date_created + '</td>';
                    detalle += '        <td>' + value.hour_created + '</td>';
                    detalle += '    </tr>';
                });
                detalle += '     </tbody>';
                detalle += '</table>';
                $('#div_tabla_historia').html(detalle);
                $("#tabla_historial_armado").DataTable({
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

            }
        });
    }
</script>
@stop