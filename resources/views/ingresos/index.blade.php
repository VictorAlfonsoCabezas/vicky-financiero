@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<section class="col-lg-9">
    @include('includes.mensaje')
</section>
<div  class="card card-primary shadow-lg bg-white rounded">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Ingresos por Cliente</h3>
        </div>
        <div class="card-body">
            <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="table_ingresos" class="table table-bordered table-striped dataTable dtr-inline compact" role="grid" aria-describedby="example1_info">
                            <thead class="thead-primary" style="font-size: 10px;">
                                <tr role="row">
                                    <th scope="col">CODIGO</th>                            
                                    <th scope="col">CLIENTE</th>
                                    <th scope="col">CEDULA CLIENTE</th>                    
                                    <th scope="col">TELEFONO</th>
                                    <th scope="col">TIPO TRANSACCION</th>
                                    <th scope="col">VALOR</th>
                                    <th scope="col">ESTADO</th>
                                    <th scope="col">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px;">
                                @foreach($movimientos as  $movimiento)
                                <tr class="odd">
                                    <th>{{ $movimiento->code}}</td>                         
                                    <td>{{ $movimiento->customer_name}}</td>
                                    <td>{{ $movimiento->customer_ruc}}</td>
                                    <td>{{ $movimiento->customer_telefono}}</td>
                                    <td class="text-center"><span class="badge bg-primary">{{ $movimiento->type_transaction_name}}</span></td>
                                    <td class="text-center">${{ $movimiento->valor_movimiento}}</td>
                                    <td class="text-center">
                                        @if( $movimiento->status)
                                        <span class="badge bg-success">Aprobado</span>
                                        <br>
                                        <small class="note">{{ $movimiento->user_created_name}}</small>
                                        @else
                                        <span class="badge bg-warning">Anulado</span>
                                        <br>
                                        <small class="note">{{ $movimiento->user_cancel_name}}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($movimiento->status !== 0)
                                        <a onclick="javascript:cancelMovimiento('{!! $movimiento->id !!}');" class="btn btn-danger btn-sm" style="color: white;">
                                            <i class="fas fa-times"></i>
                                        </a>  
                                        <a onclick="javascript:cancelMovimiento('{!! $movimiento->id !!}');" class="btn btn-dark btn-sm" style="color: white;">
                                            <i class="fas fa-print"></i>
                                        </a>  
                                        @endif
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
@include('ingresos/modal_anular')
@endsection
@section('scripts')
<script type="text/javascript">
    $(function () {
        $("#table_ingresos").DataTable({
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
        $('#table_productos').DataTable();
        $('.sidebar-mini').addClass('sidebar-collapse');
    });
    function cancelMovimiento(id) {
        Swal.fire({
            title: "Seguro de Anular esta transacción?",
            text: "¡Tu no podrás revertir esta operacion!",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Si, Anular"
        }).then(function (result) {
            if (result.value) {
                $('#formAnulacion').modal('show');
                $('#movimiento_id').val(id);
            }
        });
    }
    function cabiarEstado() {
        if ($('#razon_ingreso').val() !== '') {
            var id = $('#movimiento_id').val();
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                method: "DELETE",
                url: "{{URL::to('ingresos')}}/" + id,
                data: $('#form_razon').serialize()
            }).done(function (res) {
                loading();
                location.reload();
            });
        } else {
            Swal.fire('La razón de la Anulación debe ser ingresada de manera obligatoria');
        }


    }
</script>
@stop
