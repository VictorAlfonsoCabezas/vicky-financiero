@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<div  class="card card-primary shadow-lg bg-white rounded">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">{{$texto}}</h3>
        </div>
        <div class="card-body">
            <hr>
            <div id="botones" class="dataTables_wrapper dt-bootstrap4">
                <div class="row">
                    <div class="col-sm-12" id="div_cajas">
                        <table id="table_prestamos" class="table table-bordered table-striped dataTable dtr-inline compact" role="grid" aria-describedby="example1_info">
                            <thead class="thead-primary" style="font-size: 10px;">
                                <tr role="row">
                                    <th scope="col"># Cuota</th>
                                    <th scope="col">Carpeta</th>
                                    <th scope="col">Fecha de Vencimiento</th>
                                    <th scope="col">Nombre Clinete</th>
                                    <th scope="col">Valor Capital</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detalles as $dato)
                                <tr>
                                    <td>{{$dato->numero_cuota}}</td>
                                    <td>{{$dato->code_folder_header}}</td>
                                    <td>{{$dato->date_vencimiento}}</td>
                                    <td>{{$dato->nombresClinetes}}</td>
                                    <td>{{$dato->capital_amortizado}}</td>
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


@include('recurrencia-cartera/modal_recurrencia')
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('.sidebar-mini').addClass('sidebar-collapse');
        $("#table_prestamos").DataTable({
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
</script>
@stop