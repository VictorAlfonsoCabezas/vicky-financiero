@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<div class="row">
    <a class="btn btn-app bg-success" href="/clientes">
        <span class="badge bg-purple">{{ $cantidad }}</span>
        <i class="fas fa-users"></i> + Agregar
    </a>
    <section class="col-lg-9">
        @include('includes.mensaje')
    </section>
</div>



<div class="card card-primary shadow-lg bg-white rounded">
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

                                    <th style='width: 50 px;'>Código</th>
                                    <th scope="col">Nombres</th>
                                    <th scope="col">Apellidos</th>
                                    <th scope="col">Documento</th>
                                    <th scope="col">Dirección</th>
                                    <th scope="col">Teléfono</th>
                                    <th scope="col">Acciones</th>

                                </tr>
                            </thead>
                            <tbody style="font-size: 14px;">
                                @foreach ($clientes as $cliente)
                                <tr id='{{ $cliente->code }}'>
                                    <td style="text-align:center;">
                                        <a onclick="javascript:verCuenta('{!! $cliente->code !!}');" title="Aprovar" style="color: blue">
                                            {{ $cliente->code }}
                                        </a>
                                    </td>
                                    <td>{{ $cliente->nombres }}</td>
                                    <td>{{ $cliente->apellidos }}</td>
                                    <td>{{ $cliente->numero_documento }}</td>
                                    <td>
                                        @if ($cliente->direccion != 'NULL')
                                        {{ $cliente->direccion }}
                                        @endif
                                    </td>

                                    <td>
                                        @if ($cliente->telefono != 'NULL')
                                        {{ $cliente->telefono }}
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ URL::to('customer/' . $cliente->id) }}" method="POST">
                                            @csrf
                                            @method('delete')
                                            <a href="{{ route('customer.show', $cliente->id) }}" method="GET" class="btn btn-default btn-xs" title="Ver Cliente"><i class="fas fa-eye" title="Ver Pedido"></i> Ver</a><br>
                                            <a href="{{ URL::to('customer/' . $cliente->id . '/edit') }}" method="GET" class="btn btn-warning btn-xs" title="Editar Cliente"><i class="fas fa-highlighter"></i> Editar</a><br>
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



@include('customer/modal_cartilla')
@endsection
@section('scripts')
<script type="text/javascript">
    $(function() {
        $("#table_creditos").DataTable({
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
    $(document).ready(function() {
        $('.sidebar-mini').addClass('sidebar-collapse');
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.sidebar-mini').addClass('sidebar-collapse');
        var table = $('#table_clientes').DataTable({
            bRetrieve: true,
            scrollX: true,
            "language": {
                url: '//cdn.datatables.net/plug-ins/1.10.9/i18n/Spanish.json'
            }
        });
    });

    function verCuenta(code) {
        loading();
        $.ajax({
            url: "{{ URL::to('customer/cartilla') }}/" + code,
            type: 'GET',
            success: function(res) {
                console.log(res);
                if (res != '') {
                    $('#customer_name').html(res.cartolaHeader.customer_name);
                    $('#customer_code').html(res.customer.code);
                    $('#customer_ruc').html(res.customer.numero_documento);
                    $('#nombre_titular').html(res.cartolaHeader.customer_name);
                    $('#cartola_code').html(res.cartolaHeader.code);
                    var imprimr = '<a class="btn btn-success btn-sm" onclick="javascript:imprimirCartola(' +
                        res.cartolaHeader.id +
                        ');" title="Aprovar" style="color: white"><i class="fas fa-print"></i> IMPRIMIR</a>'
                    $('#imprimir_cartola').html(imprimr);
                    var detalle = '';
                    $.each(res.cartolaDetalle, function(key, value) {
                        detalle += '<tr>';
                        detalle += '     <td></td>';
                        detalle += '     <td>' + value.date_transaction + '</td>';
                        if (value.type_transaction_action == 'S' && value.type_transaction_id !=
                            6) {
                            detalle += '     <td>' + value.valor_transaction + '</td>';
                        } else {
                            detalle += '     <td></td>';
                        }
                        if (value.type_transaction_id == 6) {

                            detalle += '     <td>' + value.valor_transaction + '</td>';
                        } else {

                            detalle += '     <td>' + value.interes_valor + '</td>';
                        }
                        if (value.type_transaction_action == 'R') {
                            detalle += '     <td>' + value.valor_transaction + '</td>';
                        } else {
                            detalle += '     <td></td>';
                        }
                        detalle += '     <td>' + value.saldo_transaction + '</td>';
                        detalle += '</tr>';
                    });
                    $('#catola_detail >tbody').html(detalle);
                    $('#modal_cartilla').modal('show');
                    stoploading();
                } else {
                    Swal.fire({
                        title: "NO DISPONE DE CARTOLA",
                        text: 'Para generar una cartola realice algún movimiento ...',
                        type: "warning",
                        confirmButtonText: "ACEPTAR"
                    });
                    stoploading();

                }

            }
        });
    }

    function imprimirCartola(code) {
        console.log(code);
        location.href = "{{ URL::to('customer/cartilla/imprimir') }}/" + code;

    }
</script>
@stop