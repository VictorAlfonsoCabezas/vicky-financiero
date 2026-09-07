@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<section class="col-lg-9">
    @include('includes.mensaje')
</section>
<div class="botonesSuperiores" style="margin-bottom: 5px;">
    <a class="btn btn-labeled btn-warning header-btn" href="credit/create">
        <span class="btn-label"><i class="fa fa-plus"></i></span>
        Agregar Crédito
    </a>
    <a class="btn btn-labeled btn-info header-btn" href="{{URL::to('credit/download/creditos')}}">
        <span class="btn-label"><i class="fa fa-download"></i></span>
        Creditos
    </a>
</div>
<div  class="card card-primary shadow-lg bg-white rounded">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Ingresos por Cliente</h3>
        </div>
        <div class="card-body">
            <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="table_creditos" class="table table-bordered table-striped dataTable dtr-inline compact" role="grid" aria-describedby="example1_info">
                            <thead class="thead-primary" style="font-size: 10px;">
                                <tr role="row">
                                    <th class="text-center" scope="col">Nombre</th>                            
                                    <th class="text-center" scope="col">Identificación</th>
                                    <th class="text-center" scope="col">Total Prestamos</th>
                                    <th class="text-center" scope="col">Total Pagado</th>
                                    <th class="text-center" scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px;">
                                @foreach($creditos as $credito)
                                <tr>
                                    <td class="text-center">{{$credito->customer_name}}</td>
                                    <td class="text-center">{{$credito->customer_ruc}}</td>
                                    <td class="text-center">${{$credito->totalprestamo}}</td>
                                    <td class="text-center">${{$credito->totalpagando}}</td>
                                    <td class="text-center"><a href="{{URL::to('credit/prestamos') .'/'. $credito->customer_id}}" class="btn btn-sm btn-primary"><i class="fas fa-list"></i> Visualizar</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center"><b> ${{$prestamos}}</b></td>
                                    <td class="text-center"><b> ${{$deudas}}</b></td>
                                    <td></td>
                                </tr>
                            </tfoot>
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
        $("#table_creditos").DataTable({
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
