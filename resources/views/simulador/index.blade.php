@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="callout callout-info">
                    <h5><i class="fas fa-info"></i> Simulador de Crédito:</h5>
                    Ingrese los datos para realizar la simulacion de Crédito
                </div>
            </div>   
        </div>
    </div>
</section>
<div id="formulario_fondo" class="card">
    <div class="card-body">
        <div class="col-12">
            <div class="row">
                <div class="col-lg-8 col-12">
                    <div class="col-sm-12 invoice-col">
                        <form id="form_simulador" autocomplete="false" autocomplete="off" class="validate-form" enctype="multipart/form-data">
                            @csrf
                            <div class="row col col-sm-12">
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="valor_prestamo">Valor De Prestamo</label>
                                        <input type="number" class="form-control text-uppercase" id="valor_prestamo" name="valor_prestamo"  step="0.01" placeholder="0.00" required="">
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="plazo_anual">Años</label>
                                        <input type="number" class="form-control text-uppercase" id="plazo_anual" name="plazo_anual"  step="1" placeholder="Años" required="">
                                    </div>
                                </section>
                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="cuotas_mensuales">Meses</label>
                                        <input type="number" class="form-control text-uppercase" id="cuotas_mensuales" name="cuotas_mensuales"  step="1" placeholder="Meses" required="" value="0">
                                    </div>
                                </section>
<!--                                <section class="col col-sm-3">
                                    <div class="form-group">
                                        <label for="porcentaje">Interés</label>
                                        <select type="text" id="porcentaje" name="porcentaje" class="form-control" required>
                                            <option value="" > -- Seleccione -- </option>
                                            @foreach($tarifas as $tarifa)
                                            <option value="{{$tarifa->porcentaje}}" >{{$tarifa->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </section>-->
                                <div class="input-group">

                                    <section class="col col-sm-3">
                                        <div class="form-group">
                                            <label for="prestamo_select">Prestamos</label>
                                            <select id="prestamo_select" name="prestamo_select" class="form-control" required onchange="javascript:resumenSelec();">
                                                <option value="" > -- Seleccione -- </option>
                                                @foreach($prestamos as $val)
                                                <option value="{{$val->id}}" >{{$val->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </section>
                                    <input type="hidden" id="maxioPrestar" name="maxioPrestar" value="0">
                                    <div class="card-footer" id="detallePrestamoSelect">
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer justify-content-between">
                                <a class="btn btn-primary" onclick="javascript:realizarSimulador();" style="color: white">Generar</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4 col-12">
                    <table id="" class="table m-0">
                        <tbody>
                            <tr>
                                <td><b><strong>Valor de la cuota periódicamente</strong></b></td>
                                <td>
                                    <span class="description-text" id="valance_signo"></span><span class="description-text" id="valorCuota"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><b><strong>Númerpo de cuotas</strong></b></td>
                                <td>
                                    <span class="description-text" id="valance_signo"></span><span class="description-text" id="numeroCuotas"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><b><strong>Total interés a pagar</strong></b></td>
                                <td>
                                    <span class="description-text" id="valance_signo"></span><span class="description-text" id="sumaInteres"></span> 
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fas fa-globe"></i> Numero de cuotas
                                <small class="float-end">Fecha: {{ date('Y-m-d') }}</small>
                            </h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                            <div class="row">
                                <div class="col-sm-12" id="div_tabla_cuotas">


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('credit/modal_tipo_credito')
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('.sidebar-mini').addClass('sidebar-collapse');
    });
    function realizarSimulador() {
        var maximo = $('#maxioPrestar').val();
        console.log($('#valor_prestamo').val(), $('#maxioPrestar').val());
        if (parseFloat($('#valor_prestamo').val()) <= parseFloat($('#maxioPrestar').val())) {

            if ($('#cuotas_mensuales').val() == '' || $('#cuotas_mensuales').val() <= 11) {
                if ($('#valor_prestamo').val() !== '') {
                    Swal.fire({
                        title: "Simulador",
                        text: "Simularemos un crédito...",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Aceptar"
                    }).then(function (result) {
                        if (result.value) {
                            loading();
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                method: "POST",
                                url: "{{URL::to('simulador/transacciones')}}",
                                data: $('#form_simulador').serialize()
                            }).done(function (res) {
                                stoploading();
                                generarPdf();
                                $('#valorCuota').html(res.valorCuota);
                                $('#numeroCuotas').html(res.nueroCUptas);
                                $('#sumaInteres').html(res.interesSuma);
                                var detalle = '';
                                detalle += '<table id="tabla_cuotas"  class="table table-bordered table-striped dataTable dtr-inline compact" role="grid" aria-describedby="example1_info">';
                                detalle += '    <thead>';
                                detalle += '        <tr>';
                                detalle += '            <th>Número Cuota</th>';
                                detalle += '            <th>Fecha Vencimiento</th>';
                                detalle += '            <th>Interés del período</th>';
                                detalle += '            <th>Capital Amortizado</th>';
                                if (res.tipo != 'A') {
                                    detalle += '            <th>Fondo de Desgravamen</th>';
                                }
                                detalle += '            <th>Cuota a pagar</th>';
                                detalle += '            <th> Saldo remanente </th>';
                                detalle += '        </tr>';
                                detalle += '    </thead>';
                                detalle += '    <tbody>';
                                $.each(res.pagos, function (key, value) {
                                    detalle += '    <tr>';
                                    detalle += '        <td>' + value.cuotas + '</td>';
                                    detalle += '        <td>' + value.fechas + '</td>';
                                    detalle += '        <td>' + value.interes + '</td>';
                                    detalle += '        <td>' + value.amoritizado + '</td>';
                                    if (res.tipo != 'A') {
                                        detalle += '        <td>' + value.desgravamen + '</td>';
                                    }
                                    detalle += '        <td>' + value.cuotaPago + '</td>';
                                    detalle += '        <td>' + value.deuda + '</td>';
                                    detalle += '    </tr>';
                                });
                                detalle += '     </tbody>';
                                detalle += '</table>';
                                $('#div_tabla_cuotas').html(detalle);
                                $("#tabla_cuotas").DataTable({
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
                            stoploading();
                        }
                    });
                } else {
                    Swal.fire('Asegúrese de llenar todos los datos para realizar esta acción.');
                }
            } else {
                Swal.fire('Los meses no pueden ser mayores a 11');
                $('#cuotas_mensuales').val('0');
            }
        } else {
            Swal.fire('Valor maximo de <b> ' + maximo + '</b>');
        }
    }
    function generarPdf() {
        var valor = $('#valor_prestamo').val();
        var anual = $('#plazo_anual').val();
        var mensual = $('#cuotas_mensuales').val();
        var id = $('#prestamo_select').val();
        location.href = "{{URL::to('simulador/letras') }}/" + valor + '/' + anual + '/' + mensual + '/' + id;
    }
    function agrgarTipoPrestamo() {
        $('#formTipoPrestamoNew').modal('show');
    }
    function guardarNuewTipoPrestamo() {
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: "POST",
            url: "{{URL::to('credit/createTipoPrestamo')}}",
            data: $('#form_prestamo_new').serialize()
        }).done(function (res) {
            $('#formTipoPrestamoNew').modal('hide');
            var detalle = '';
            detalle += '<option value=""> --SELECCIONE-- </option>';
            $.each(res, function (key, value) {
                detalle += '<option value="' + value.id + '">' + value.name + '</option>';
            });
            $('#prestamo_select').html(detalle);
        });
    }
    function resumenSelec() {
        var prestmo = $('#prestamo_select').val();
        $.ajax({
            url: "{{URL::to('credit/buscarPrestamo')}}/" + prestmo,
            type: 'GET',
            success: function (res) {
                var detalle = 'EL PRESTAMO <b>' + res.name + '</b> TIENE TAZA DE INTERÉS : <b>' + res.interes + ' %</b>\n\
         , FONDO DE DESGAVAMEN : <b>' + res.fondo_desgravamen + '%</b><br> VALOR MÍNIMO <b>' + res.valor_minimo + '$</b> DE PRESTAMO VALOR MÁXIMO DE PRESTAMO <b>' + res.valor_maximo + '$</b>';
                $('#detallePrestamoSelect').html(detalle);
                $('#maxioPrestar').val(res.valor_maximo);
            }
        });
    }
</script>
@stop
