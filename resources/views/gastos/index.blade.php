@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
    <div class="botonesSuperiores" style="margin-bottom: 5px;">
        <a class="btn btn-labeled btn-warning header-btn" onclick="javascript:agregarGasto();" title="Editar"
            style="color: white">
            <span class="btn-label"><i class="fa fa-plus"></i></span>
            Agregar Gasto
        </a>

    </div>
    <div class="card card-primary shadow-lg bg-white rounded">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    Total de Gastos: <b>{{ number_format($sumaGastos, 2, '.', '') }}</b>
                </h3>
            </div>
            <div class="card-body">
                <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="table_gastos" class="table table-bordered table-striped dataTable dtr-inline compact"
                                role="grid" aria-describedby="example1_info">
                                <thead class="thead-primary" style="font-size: 10px;">
                                    <tr role="row">
                                        <th scope="col">PRINT</th>
                                        <th scope="col">FECHA</th>
                                        <th scope="col">CODIGO</th>
                                        <th scope="col">NOMBRE</th>
                                        <th scope="col">TIPO TRANSACCION</th>
                                        <th scope="col">OBSERVACION</th>
                                        <th scope="col">FORMA PAGO</th>
                                        <th scope="col">PLAN CUENTAS</th>
                                        <th scope="col">CENTRO COSTOS</th>
                                        <th scope="col">BANCO</th>
                                        <th scope="col">VALOR</th </tr>
                                </thead>
                                <tbody style="font-size: 14px;">
                                    @foreach($gastos as $gasto)
                                        <tr class="odd">
                                            <th class="text-center">
                                                <a class="btn btn-info btn-sm" href="{{URL::to('gastos/tcket/' . $gasto->id)}}"
                                                    title="Ticket">
                                                    <i class="far fa-file-pdf"></i>
                                                </a>
                                            </th>
                                            <th>{{ $gasto->date_created}}</th>
                                            <th>{{ $gasto->code}}</th>
                                            <td>{!! $gasto->customer_name !!}</th>
                                            <td>{{ $gasto->type_transaction_name}}</th>
                                            <td>{{ $gasto->observation}}</th>
                                            <td>{{ $gasto->FormaPago}}</th>
                                            <td>{{ $gasto->PlanCuenta}}</th>
                                            <td>{{ $gasto->CentroCosto}}</th>
                                            <td>{{ $gasto->bancoNombre}}</th>
                                            <td>{{ number_format($gasto->valor_movimiento, 2, '.', '') }}</td>
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

    @include('gastos.modal_gastos')

@endsection
@section('scripts')
<script type="text/javascript">
    $(function () {
        $("#table_gastos").DataTable({
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
    function agregarGasto() {
        $('#frmModalGasto').modal('show');
    }
    function guardarGasto() {
        var valor = $('#valor_gasto').val();
        var razon = $('#razon_gasto').val();
        var forma_pago_id = $('#forma_pago_id').val();
        var gastos_plan_cuentas_id = $('#gastos_plan_cuentas_id').val();
        var gastos_centro_costos_id = $('#gastos_centro_costos_id').val();
        var mostrarTransferencia = $('#mostrarTransferencia').val();
        var banco_id = $('#banco_id').val();
        var numero_comprobante = $('#numero_comprobante').val();

        console.log(valor, razon, forma_pago_id)
        if (valor == '' || razon == '') {
            Swal.fire({
                title: "Alerta! ",
                text: "¡Asegurese que los campos esten llenos!",
                type: "warning",
                confirmButtonText: "Aceptar"
            });
        } else {

            loading();
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                method: "POST",
                url: "{{URL::to('/gastos')}}",
                data: $('#form_gasto').serialize()
            }).done(function (res) {
                $('#frmModalGasto').modal('hide');
                location.reload();
                stoploading();
            });
        }

    }
</script>
@stop