@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<div class="card card-primary shadow-lg bg-white rounded">
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
                                <button onclick="javascript:agrgarTipoPrestamo();" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Agregar</button>
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
                                    <th scope="col">Interes</th>
                                    <th scope="col">Interes Anual</th>
                                    <th scope="col">Fondo Desgravamen</th>
                                    <th scope="col">Valor Máximo</th>
                                    <th scope="col">Valor Mínimo</th>
                                    <th scope="col">Edades</th>
                                    <th scope="col">Tipo</t< /th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prestamos as $prestamo)
                                <tr>
                                    <td>{{ $prestamo->id }}</td>
                                    <td>{{ $prestamo->name }}</td>
                                    <td>{{ $prestamo->interes }}</td>
                                    <td>{{ $prestamo->interes_anual }}</td>
                                    <td>{{ $prestamo->fondo_desgravamen }}</td>
                                    <td>{{ $prestamo->valor_maximo }}</td>
                                    <td>{{ $prestamo->valor_minimo }}</td>
                                    <td>{{ 'DESDE ' . $prestamo->edad_minima . 'AÑOS HASTA ' . $prestamo->edad_maxima . ' AÑOS' }}
                                    </td>
                                    <td>
                                        {{ $prestamo->tipo != 'F' ? 'ALEMANA ' : 'FRANCESA ' }}
                                        @if ($prestamo->diario)
                                        DIARIO
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ URL::to('prestamos/' . $prestamo->id) }}" method="POST">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-primary btn-xs" style="color: white;"><i class="far fa-trash-alt"></i>
                                                Eliminar</button>
                                        </form>
                                        <a onclick="editarPrestamo('{!! $prestamo->id !!}')" class="btn btn-success btn-xs" style="color: white">
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


@include('credit/modal_tipo_credito')
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('.sidebar-mini').addClass('sidebar-collapse');
        $("#table_prestamos").DataTable({
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

    function agrgarTipoPrestamo() {
        $('#name').val('');
        $('#interes').val('');
        $('#fondo_desgravamen').val('');
        $('#valor_minimo').val('');
        $('#valor_maximo').val('');
        $('#tipoPestamoID').val('')
        $('#formTipoPrestamoNew').modal('show');
    }

    function guardarNuewTipoPrestamo() {
        console.log($('#tipoPestamoID').val());
        var url = '';
        if ($('#tipoPestamoID').val() == '') {
            url = "{{ URL::to('credit/createTipoPrestamo') }}";
        } else {
            url = "{{ URL::to('credit/updateTipoPrestamo') }}";
        }
        console.log(url);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: "POST",
            url: url,
            data: $('#form_prestamo_new').serialize()
        }).done(function(res) {
            $('#formTipoPrestamoNew').modal('hide');
            location.reload();
        });

    }

    function editarPrestamo(id) {
        console.log(id);
        $.ajax({
            url: "{{ URL::to('credit/consultarPrestamo') }}/" + id,
            type: 'GET',
            success: function(res) {
                console.log(res);
                if(res.prestamo.letra_cambio){
                    $('#letra_cambio').prop('checked', true);
                }else{
                    $('#letra_cambio').prop('checked', false);
                }
                if(res.prestamo.pagare){
                    $('#pagare').prop('checked', true);
                }else{
                    $('#pagare').prop('checked', false);
                }
                $('#tipoPestamoID').val(id);
                $('#name').val(res.prestamo.name);
                $('#interes_anual').val(res.prestamo.interes_anual);
                $('#interes').val(res.prestamo.interes);
                $('#fondo_desgravamen').val(res.prestamo.fondo_desgravamen);
                $('#valor_minimo').val(res.prestamo.valor_minimo);
                $('#valor_maximo').val(res.prestamo.valor_maximo);

                $('#edad_minima').val(res.prestamo.edad_minima);
                $('#edad_maxima').val(res.prestamo.edad_maxima);

                var presFrances = (res.prestamo.tipo == "F") ? 'selected = ""' : "";
                var presAleman = (res.prestamo.tipo == "A") ? 'selected = ""' : "";
                var detalle = '';
                detalle += '<option value=""> --SELECCIONE-- </option>';
                detalle += '<option value="F" ' + presFrances + '> FRANCES</option>';
                detalle += '<option value="A" ' + presAleman + '> ALEMÁN</option>';
                $('#type').html(detalle);


                var diarioSi = (res.prestamo.diario == 1) ? 'selected = ""' : "";
                var diarioNo = (res.prestamo.diario == 0) ? 'selected = ""' : "";
                var diario = '';
                diario += '<option value=""> --SELECCIONE-- </option>';
                diario += '<option value="1" ' + diarioSi + '> SI </option>';
                diario += '<option value="0" ' + diarioNo + '> NO </option>';
                $('#diario').html(diario);




                var periodoSel = res.prestamo.periodo_id;
                var periodo = '';
                periodo += '<option value=""> --SELECCIONE-- </option>';
                $.each(res.recu, function(key, value) {
                    var selectRecu = (periodoSel == value.id) ? 'selected = ""' : "";
                    periodo += '<option value="' + value.id + '" ' + selectRecu + '>' + value.name + '</option>';
                });
                $('#periodo_id').html(periodo);
                $('#formTipoPrestamoNew').modal('show');
            }
        });
    }
</script>
@stop