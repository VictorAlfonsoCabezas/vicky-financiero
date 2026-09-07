@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<div  class="card card-primary shadow-lg bg-white rounded">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Lista de Recurrencias</h3>
        </div>
        <div class="card-body">

            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="row">

                            <div>
                                <button onclick="javascript:agrgarRecurrencia();"class="btn btn-primary"><i class="fas fa-plus-circle"></i> Agregar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div id="botones" class="dataTables_wrapper dt-bootstrap4">
                <div class="row">
                    <div class="col-sm-12" id="div_cajas">
                        <table id="table_prestamos" class="table table-bordered table-striped dataTable dtr-inline compact" role="grid" aria-describedby="example1_info">
                            <thead class="thead-primary" style="font-size: 10px;">
                                <tr role="row">
                                    <th scope="col">Orden</th>
                                    <th scope="col">Desde</th>
                                    <th scope="col">Hasta</th>
                                    <th scope="col">Fecha Creación</th>
                                    <th scope="col">Usuario Crea</th>
                                    <th scope="col">Días</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datos as $dato)
                                <tr>
                                    <td>{{$dato->orden}}</td>
                                    <td>{{$dato->desde}}</td>
                                    <td>{{$dato->hasta}}</td>
                                    <td>{{$dato->date_created . ' '.$dato->hour_created }}</td>
                                    <td>{{$dato->user_created_name}}</td>
                                    <td>{{$dato->dias}}</td>
                                    <td>
                                        <form action="{{URL::to('recurrenciaCartera/'.$dato->id)}}" method="POST">
                                            @csrf
                                            @method("delete")
                                            <button type="submit" class="btn btn-primary btn-xs" style="color: white;"><i class="far fa-trash-alt"></i> Eliminar</button>
                                        </form>
                                    </td>
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
    function agrgarRecurrencia() {
        $('#name').val('');
        $('#interes').val('');
        $('#fondo_desgravamen').val('');
        $('#valor_minimo').val('');
        $('#valor_maximo').val('');
        $('#formRecurrenciaNew').modal('show');
    }
    function guardarNuewRecurrencia() {
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: "POST",
            url: "{{URL::to('recurrenciaCartera/store')}}",
            data: $('#form_prestamo_new').serialize()
        }).done(function (res) {
            $('#formRecurrenciaNew').modal('hide');
            location.reload();
        });
    }
</script>
@stop