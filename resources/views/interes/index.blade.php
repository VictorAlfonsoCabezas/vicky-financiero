@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<div  class="card card-primary shadow-lg bg-white rounded">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Tabla de Intereses </h3>
        </div>
        <div class="card-body">
            <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="table_intereses" class="table table-bordered table-striped dataTable dtr-inline compact" role="grid" aria-describedby="example1_info">
                            <thead class="thead-primary" style="font-size: 10px;">
                                <tr role="row">                                                              
                                    <th scope="col">VALOR</th>
                                    <th scope="col">CLIENTE</th>
                                    <th scope="col">MES</th>
                                    <th scope="col">AÑO</th>

                                </tr>
                            </thead>
                            <tbody style="font-size: 14px;">
                                @foreach($intereses as  $interes)
                                <tr class="odd">
                                    <th>{{ $interes->valor}}</td>                         
                                    <td>{{ $interes->customer_name}}</td>
                                    <td>{{ $interes->mes}}</td>
                                    <td>{{ $interes->anio}}</td>
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
</script>
@stop