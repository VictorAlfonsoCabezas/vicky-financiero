@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<div  class="card card-primary shadow-lg bg-white rounded">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Lista de Creditos</h3>
        </div>
        <div class="card-body">

            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="row">

                            <div>
                                <button onclick="javascript:agrgarTipoPrestamo();"class="btn btn-primary"><i class="fas fa-plus-circle"></i> Agregar</button>
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
                                    <th scope="col">ID</th>
                                    <th scope="col">Nombre</th>                                    
                                    <th scope="col">Recurrencia</th>
                                    <th scope="col">Tipo</t</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recurrencia as $key => $prestamo)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$prestamo->name}}</td>
                                    <td>{{$prestamo->separacion}}</td>
                                    <td>
                                        @if($prestamo->code == 'D')
                                        DIARIO
                                        @endif
                                        @if($prestamo->code == 'S')
                                        SEMANAL
                                        @endif
                                        @if($prestamo->code == 'M')
                                        MENSUAL
                                        @endif
                                    </td>
                                    <td>
                                        <a onclick="editarRecurrenciaPrestamo('{!! $prestamo->id !!}')" class="btn btn-success btn-xs" style="color: white">
                                            <i class="fas fa-pencil"></i> Editar
                                        </a>
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


@include('recurencias-prestamos/modal_periodos')
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
    function agrgarTipoPrestamo() {
        $('#id_recu_pres').val('');
        $('#name').val('');
        $('#separacion').val('');
        $('#code').val('');
        $('#formRecurrenciaPrestamo').modal('show');
    }
    function guardarRecurrenciaPrestamo() {
        console.log($('#id_recu_pres').val());
        var url = '';
        
            var url = "{{URL::to('recurrencia-prestamos/save')}}";
        
        console.log(url);
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: "POST",
            url: url,
            data: $('#form_recurrencia_prestamo').serialize()
        }).done(function (res) {
            $('#formRecurrenciaPrestamo').modal('hide');
            location.reload();
        });
    }
    function editarRecurrenciaPrestamo(id) {
        console.log(id);
        $.ajax({
            url: "{{URL::to('recurrencia-prestamos/editar')}}/" + id,
            type: 'GET',
            success: function (res) {
                $('#id_recu_pres').val(res.id);
                $('#name').val(res.name);
                $('#separacion').val(res.separacion);
                var diario = (res.code == "D") ? 'selected = ""' : "";
                var semanal = (res.code == "S") ? 'selected = ""' : "";
                var mensual = (res.code == "M") ? 'selected = ""' : "";
                var detalle = '';
                detalle += '<option value=""> --SELECCIONE-- </option>';
                detalle += '<option value="D" ' + diario + '> DIARIO </option>';
                detalle += '<option value="S" ' + semanal + '> SEMANAL </option>';
                detalle += '<option value="M" ' + mensual + '> MENSUAL</option>';
                $('#code').html(detalle);
                $('#formRecurrenciaPrestamo').modal('show');
               
            }
        });
    }
</script>
@stop