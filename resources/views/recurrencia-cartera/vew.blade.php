@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<div  class="card card-primary shadow-lg bg-white rounded">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Carteras</h3>
        </div>
        <div class="card-body">

            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="row">

                            <div>
                                <button onclick="javascript:calcularDatos();"class="btn btn-primary"><i class="fas fa-plus-circle"></i> Calcular</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div id="botones" class="dataTables_wrapper dt-bootstrap4">
                <div class="row">
                    <div class="col-sm-12" id="">
                        <section class="content">
                            <div class="container-fluid">

                                <div class="card card-default">
                                    <div class="card-header">
                                        <h3 class="card-title">Pendientes</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="row">
                                            <table id="" class="table table-bordered table-striped dataTable dtr-inline compact" role="grid" aria-describedby="example1_info">
                                                <thead class="thead-primary" style="font-size: 10px;">
                                                    <tr role="row">
                                                        <th scope="col"></th>
                                                        @foreach(App\Http\Controllers\RecurrenciaCartera\RecurrenciaCarteraController::datosRcurrente() As $recu)
                                                        <td><b>{{$recu->desde .' AL '. $recu->hasta}}</b></td>
                                                        @endforeach                                                        
                                                        <th scope="col">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($datosPendientes as $header)
                                                    <?php $totalSuma = 0 ?>
                                                    <tr>
                                                        <td>{{$header->mes_name .' - '.$header->year}}</td>
                                                        @foreach(App\Http\Controllers\RecurrenciaCartera\RecurrenciaCarteraController::datosTabla($header->id) As $data)
                                                        <td>
                                                            <a href="{{URL::to('recurrenciaCartera/show') .'/'. $header->id.'/'.$data->id}}" class="btn btn-sm btn-primary">
                                                                $ {{$data->sumatoria}}
                                                            </a>
                                                        </td>
                                                        <?php $totalSuma = $totalSuma + $data->sumatoria ?>
                                                        @endforeach
                                                        <td>$ {{$totalSuma}}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>


                                <div class="card card-default">
                                    <div class="card-header">
                                        <h3 class="card-title">Vencidos</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="row">
                                            <table id="" class="table table-bordered table-striped dataTable dtr-inline compact" role="grid" aria-describedby="example1_info">
                                                <thead class="thead-primary" style="font-size: 10px;">
                                                    <tr role="row">
                                                        <th scope="col"></th>
                                                        @foreach(App\Http\Controllers\RecurrenciaCartera\RecurrenciaCarteraController::datosRcurrente() As $recu)
                                                        <td><b>{{$recu->desde .' AL '. $recu->hasta}}</b></td>
                                                        @endforeach                                                        
                                                        <th scope="col">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($datosVencidos as $header)
                                                    <?php $totalSuma = 0 ?>
                                                    <tr>
                                                        <td>{{$header->mes_name .' - '.$header->year}}</td>
                                                        @foreach(App\Http\Controllers\RecurrenciaCartera\RecurrenciaCarteraController::datosTabla($header->id) As $data)
                                                        <td>
                                                            <a href="{{URL::to('recurrenciaCartera/show') .'/'. $header->id.'/'.$data->id}}" class="btn btn-sm btn-primary">
                                                                $ {{$data->sumatoria}}
                                                            </a>
                                                        </td>
                                                        <?php $totalSuma = $totalSuma + $data->sumatoria ?>
                                                        @endforeach
                                                        <td>$ {{$totalSuma}}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </section>
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
    function calcularDatos() {
        $.ajax({
            url: "{{URL::to('recurrenciaCartera/calcular')}}",
            type: 'GET',
            success: function (res) {
                location.reload();
            }
        });
    }
</script>
@stop