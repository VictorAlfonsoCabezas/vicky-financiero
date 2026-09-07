@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<div  class="card card-primary shadow-lg bg-white rounded">
    <div class="card card-dark">
        <div class="card-header">
            <h3 class="card-title">Historial de <b>{{$customer->nombres}}    {{$customer->apellidos}}</b></h3>
        </div>
        <div class="card-body">
            <hr>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">                       
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>Depositos</h3>
                                    <p>${{$data['ingresos']}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>Retiros</h3>
                                    <p>${{$data['egresos']}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>Saldo Total</h3>
                                    <p>${{$data['valor']}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="content">
                <div class="card-body">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Transacciones</h3>
                                    </div>
                                </div>
                                <table id="table_transacciones" class="table table-bordered table-striped dataTable dtr-inline compact" role="grid" aria-describedby="example1_info">
                                    <thead class="thead-primary" style="font-size: 10px;">
                                        <tr role="row">
                                            <th class="text-center" scope="col" style="width: 30px;">Print</th>
                                            <th class="text-center" scope="col">Fecha</th>
                                            <th class="text-center" scope="col">Hora</th>
                                            <th class="text-center" scope="col">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 14px;">
                                        @foreach($movimientos as $movimiento)
                                        @if($movimiento->type_transaction_name == 'INGRESOS' || $movimiento->type_transaction_name == 'EGRESOS')
                                        @if($movimiento->type_transaction_action == 'S')
                                        <tr class="bg-primary">
                                            @else
                                        <tr class="bg-danger">
                                            @endif
                                            <td class="text-center">
                                                <a class="btn btn-default btn-sm" href="{{URL::to('historial/transacciones/movimientos/'.$movimiento->id)}}" title="Transacción" style="color: white;">
                                                    <i class="fas fa-print" style="color: black;"></i>
                                                </a>
                                            </td> 
                                            <td class="text-center"><i class="fas fa-calendar"></i> <b>{{$movimiento->date_created}}</b></td> 
                                            <td class="text-center"><b>{{$movimiento->hour_created}}</b></td> 
                                            <td class="text-center">
                                                @if($movimiento->type_transaction_action == 'S')
                                                + {{$movimiento->valor_movimiento}}
                                                @else
                                                - {{$movimiento->valor_movimiento}}
                                                @endif
                                            </td> 
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-default">
                                            <td colspan="3" style="text-align: center;"><b>Total:</b></td>                                        
                                            <td style="text-align: center;"><b>${{$data['valor']}}</b></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    @include('retiros/modal_customer')
    @endsection
    @section('scripts')
    <script type="text/javascript">
        $(function () {
            $("#table_transacciones").DataTable({
                "responsive": true, "lengthChange": false, "autoWidth": false,
                "buttons": ["copy", "excel", "pdf", "print"],
                "order": [[0, "desc"]],
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