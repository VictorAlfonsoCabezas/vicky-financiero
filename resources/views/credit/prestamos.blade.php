@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<section class="col-lg-9">
    @include('includes.mensaje')
</section>
<input type="hidden" id="porcentajeRetenerCreditoCompany" name="porcentajeRetenerCreditoCompany" value="{{$company->porcentaje_retener_credito}}">
<div  class="card card-primary shadow-lg bg-white rounded">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Pago de Créditos</h3>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" id="customer_id" value="{{$customer->id}}">
                            <table class="table m-0">
                                <tbody>
                                    <tr>
                                        <td>Nombres: </td>
                                        <td>{{$customer->nombres .' '. $customer->apellidos}}</td>
                                        <td>Telénos: </td>
                                        <td>{{$customer->nombres}}</td>
                                    </tr>
                                    <tr>
                                        <td>Telénos</td>
                                        <td>{{$customer->telefono}}</td>
                                        <td>Dirección:</td>
                                        <td>{{$customer->direccion}}</td>
                                    </tr>
                                    <tr>
                                        <td>Valor Prestamos</td>
                                        <td>${{$valorPrestamos}}</td>
                                        <td>Valor Deudas:</td>
                                        <td>${{$valorDeudas}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="card-body">
                <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="table_ingresos" class="table table-bordered table-striped dataTable dtr-inline compact" role="grid" aria-describedby="example1_info">
                                <thead class="thead-primary" style="font-size: 10px;">
                                    <tr role="row">
                                        <th class="text-center" scope="col">Carpeta</th>

                                        <th class="text-center" scope="col">Nombres</th>
                                        <th class="text-center" scope="col">Contactos</th>
                                        <th class="text-center" scope="col">Cuotas</th>
                                        <th class="text-center" scope="col">Tipo Pago</th>
                                        <th class="text-center" scope="col">Valor Préstamo</th>
                                        <th class="text-center" scope="col">Valor Encaje</th>
                                        <th class="text-center" scope="col">Valor Pagado</th>
                                        <th class="text-center" scope="col">Estado</th>
                                        <th class="text-center" scope="col">Documentos</th>
                                        <th class="text-center" scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px;">
                                    @foreach($creditos as $credito)
                                    <tr>
                                        <td class="text-center">{{$credito->code}}</td>

                                        <td class="text-center">{{$credito->customer_name}}</td>
                                        <td class="text-center">{{$credito->customer_phone}}</td>
                                        <td class="text-center">{{$credito->cuotas_pagar}}</td>
                                        <td class="text-center"><span class="badge bg-primary">{{$credito->tipo_pago}}</span></td>
                                        <td class="text-center">${{$credito->valor_solicitado}}</td>
                                        <td class="text-center">${{$credito->valor_encaje}}</td>
                                        <td class="text-center">${{$credito->total_pagando}}</td>
                                        <td class="text-center">
                                            @if($credito->totalLetrasImpagas > 0)
                                            <span class="badge badge-{{config('constants.STATUS_PRESTAMO.'.$credito->status.'.color')}}">   {{config('constants.STATUS_PRESTAMO.'.$credito->status.'.label')}}  </span> 
                                            @else
                                            <span class="badge bg-success">   PAGADO  </span>
                                            @endif
                                        </td>
                                        <td class="text-center">                                            
                                            @if($credito->status == 'ENTREGADO')
                                            <a class="btn btn-info btn-sm" href="{{URL::to('credit/print/'.$credito->id)}}" title="Tabla de Amortización">
                                                <i class="far fa-file-pdf"></i>
                                            </a>
                                            <a class="btn btn-success btn-sm" href="{{URL::to('credit/print/letra/'.$credito->id)}}" title="Letra de Cambio">
                                                <i class="far fa-file-alt"></i>
                                            </a>
                                            <button onclick="javascript:verEncaje('{!! $credito->id !!}');" class="btn btn-sm btn-danger">
                                                <i class="fas fa-print"></i>
                                            </button>
                                            @endif                                            
                                        </td>
                                        <td class="text-center">
                                            
                                            @if($credito->totalLetrasImpagas > 0)
                                            @if($credito->status == 'APROBADO')
                                            <a class="btn btn-xs btn-success" onclick="javascript:entregarDinero('{!! $credito->id !!}', '{!! $credito->retener !!}');" title="Entregar" style="color: white">
                                                <i class="fas fa-hand-holding-usd"></i> Entregar Dinero
                                            </a>
                                            @endif 

                                            @if($credito->status == 'ENTREGADO')
                                            <button onclick="javascript:verCreditDetalle('{!! $credito->id !!}');" class="btn btn-sm btn-danger">
                                                <i class="far fa-money-bill-alt"></i> Pagar Letras
                                            </button>
                                            <button onclick="javascript:liquidarDeuda('{!! $credito->id !!}');" class="btn btn-sm btn-dark">
                                                <i class="fas fa-unlock-alt"></i> Pagar Totalidad
                                            </button>
                                            @if(Auth::user()->admin)
                                            <button onclick="javascript:creditoIncobrable('{!! $credito->id !!}');" class="btn btn-sm btn-info">
                                                <i class="fab fa-gripfire"></i> Incobrable
                                            </button>
                                            @endif
                                            
                                            @endif  
                                            @if($credito->status == 'PENDIENTE')
                                            @if(Auth::user()->admin == true)
                                            <a class="btn btn-xs btn-success" onclick="javascript:verFondos('{!! $credito->id !!}');" title="Aprobar" style="color: white">
                                                <i class="fas fa-user-check"></i> Aprobar
                                            </a>
                                            @endif
                                            <a class="btn btn-xs btn-danger" onclick="javascript:negarCredito('{!! $credito->id !!}');" title="Rechazar" style="color: white">
                                                <i class="far fa-trash-alt"></i> Rechazar
                                            </a>
                                            <a class="btn btn-xs btn-info" onclick="javascript:editarCredito('{!! $credito->id !!}');" title="Editar" style="color: white">
                                                <i class="far fa-trash-alt"></i> Editar
                                            </a>
                                            @endif
                                            @else
                                            <button onclick="javascript:modalFiles('{!! $credito->id !!}');" class="btn btn-sm btn-default">
                                                <i class="fas fa-file-upload"></i> Archivos
                                            </button>
                                            @endif
                                            <button onclick="javascript:modalFiles('{!! $credito->id !!}');" class="btn btn-sm btn-default">
                                                <i class="fas fa-file-upload"></i> Archivos
                                            </button>
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
</div>
@include('credit.modal_vista')
@include('credit.modal_file')
@include('credit.modal_incobrable')
@include('credit.modal_liquidar')
@include('credit.modal_detalles')
@include('credit.modal_ticket')
@include('credit.modal_edit')
@include('credit.modal_encaje')
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
    });
    function verCreditDetalle(id) {
        loading();
        $.ajax({
            url: "{{URL::to('credit/verPagos')}}/" + id,
            type: 'GET',
            success: function (res) {
                stoploading();
                var det = '';
                $.each(res, function (data, value) {
                    det += '<tr id="letra-' + value.id + '" class="odd">';
                    det += '     <td>';
                    if (value.status == 'PENDIENTE') {
                        det += '        <button onclick="javascript:resumenLetra(' + value.id + ');" class="btn btn-primary btn-sm"><i class="fas fa-check"></button></td>';
                    }
                    det += '     </td>';
                    det += '     <td>' + value.numero_cuota + '</td>';
                    det += '     <td><b>' + value.date_vencimiento + '</b></td>';
                    det += '     <td>$' + value.valor_cuota + '</td>';
                    det += '     <td>$' + value.interes_mora + '</td>';
                    det += '     <td>$' + value.pago_general + '</td>';
                    if (value.valor_pagado !== '0.00') {
                        det += '     <td>$' + value.valor_pagado + '</td>';
                    } else {
                        det += '     <td>$ --- </td>';
                    }

                    det += '     <td>';
                    if (value.status == 'PENDIENTE') {
                        det += '     <span class="badge bg-warning">' + value.status + '</span>';
                    } else {
                        det += '     <span class="badge bg-success">' + value.status + '</span>';
                    }
                    det += '     </td>';
                    det += '     <td class="text-center">';
                    if (value.status == 'PAGADA') {
                        det += '        <button onclick="javascript:imprimirTicket(' + value.id + ');" class="btn btn-default btn-sm"><i class="fas fa-print"></button></td>';
                    }
                    det += '</td>';
                    det += '</tr>';
                });
                $('#credit_detalle_table > tbody').html(det);
                $('#frmModalCreditDetalles').modal('show');
            }
        });
    }
    function resumenLetra(id) {
        $.ajax({
            url: "{{URL::to('credit/verCuota')}}/" + id,
            type: 'GET',
            success: function (res) {
                $('#credit_detalle_table > tbody').find('#letra-' + id).addClass('bg-success');
                $('#id_detalle').val(res.id);
                $('#cuota_pago').html('CUOTA ' + res.numero_cuota);
                $('#interes_mora').val(res.interes_mora);
                var total = parseFloat(res.valor_cuota) + parseFloat(res.interes_mora);
                $('#valor_pago').val(total);
                var resumen = '';
                resumen += '<table>';
                resumen += '    <tr>';
                resumen += '        <td>Cuota: <b>$' + res.valor_cuota + '</b></td>';
                
                resumen += '        <td>Interés: <b>$' + res.interes_mora + '</b></td>';
                resumen += '    </tr>';
                resumen += '    <tr>';
                resumen += '        <td>Saldo: <b>$' + res.saldo_anterior_cuota + '</b></td>';
                
                resumen += '        <td>Faltante: <b>$' + res.faltante_anterior_cuota + '</b></td>';
                resumen += '    </tr>';
                resumen += '</table>';
                $('#tableDesc').html(resumen);
            }
        });
    }
    function pagarLetra() {
        Swal.fire({
            title: "Pagar esta Fecha",
            text: "¡Se Procederá a imprimir el Comprobante!",
            type: "info",
            showCancelButton: true,
            confirmButtonText: "Si, Continuar..."
        }).then(function (result) {
            if (result.value) {
                loading();
                var id = $('#id_detalle').val();
                var customer = $('#customer_id').val();
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    method: "POST",
                    url: "{{URL::to('credit/pagarLetra')}}/" + id + '/' + customer,
                    data: $('#frmCuota').serialize(),
                    success: function (res) {
                        stoploading();
                        if (res) {
                            console.log(res);
                            Swal.fire('Transacción Realizada con Exito...');
                            $('#frmModalCreditDetalles').modal('hide');
                            $('#comprobante_id').html(res.customerMovimiento.code);
                            var pdf = '<iframe id="imagen_comprobante" class="img" width="100%" height="500px" src="/uploads/comprobante/' + res.customerMovimiento.code + '.pdf">';
                            $('#cabecera_ticket').html(pdf);
                            $('#frmModalTiket').modal('show');
                        }
                    }
                });
            }
        });
    }
    function verFondos(id) {
        loading();
        $.ajax({
            url: "{{URL::to('credit/fondos')}}/" + id,
            type: 'GET',
            success: function (res) {
                //if (res == true) {
                    aprobarCredito(id);
                //} else {
                //    Swal.fire({
                //        title: "EL CRÉDITO NO PUEDE SER APROBADO EL VALOR SOLICITADO EXCEDE AL CAPITAL",
                 //       text: 'Edite los valores a entregar',
                  //      type: "warning",
//confirmButtonText: "ACEPTAR"
                  //  });
                //}
                stoploading();
            }
        });
    }
    function aprobarCredito(id) {
        Swal.fire({
            title: 'Ingrese la Fecha de aprobación?',
            text: 'Formato: Año-Mes-dia (AAAA-mm-dd)',
            html: "<input id='date_created' class='swal2-input' type='date' value='{!! date('Y-m-d') !!}'>",
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Aprobar',
            showLoaderOnConfirm: true,
            preConfirm: (fecha) => {
                return fetch("{{URL::to('credit/aprobarCredito')}}/" + id + "/" + $('#date_created').val())
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.statusText);
                            }
                            return response.json();
                        }).catch(error => {
                    Swal.showValidationMessage(`Request failed: ${error}`)
                })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.value) {
                location.reload();
            }
        });
    }
    function negarCredito(id) {
        $.ajax({
            url: "{{URL::to('credit/negarCredito')}}/" + id,
            type: 'GET',
            success: function (res) {
                stoploading();
                location.reload();
            }
        });
    }
    function entregarDinero(id, por) {
        $('#prestamo_id').val(id);
        $('#porcentDesgra').val(por);
        $('#frmModalEncaje').modal('show');
    }
    function entregarDineroEncaje(entra) {
        var valor = 0;
        if (entra == '1') {
            if ($('#porcentDesgra').val() == 'N') {
                valor = $('#porcentajeRetenerCreditoCompany').val();
            } else {
                valor = $('#porcentDesgra').val();

            }
        }
        var id = $('#prestamo_id').val();
        Swal.fire({
            title: 'Ingrese la Fecha de Entrega?',
            text: 'Formato: Año-Mes-dia (AAAA-mm-dd)',
            html: "<input id='date_entrega' class='swal2-input' type='date' value='{!! date('Y-m-d') !!}'>",
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Entregar',
            showLoaderOnConfirm: true,
            preConfirm: (fecha) => {
                return fetch("{{URL::to('credit/entregarDinero')}}/" + id + '/' + valor + "/" + $('#date_entrega').val())
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.statusText);
                            }
                            return response.json();
                        }).catch(error => {
                    Swal.showValidationMessage(`Request failed: ${error}`)
                })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.value) {
                location.reload();
            }
        });
    }
    function liquidarDeuda(id) {
        Swal.fire({
            title: "Liquidar Crédito",
            text: "¡Este Proceso, modificará mucha información, continuar?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Si, Continuar..."
        }).then(function (result) {
            if (result.value) {
                loading();
                $.ajax({
                    method: "GET",
                    url: "{{URL::to('credit/liquidarDetalle')}}/" + id,
                }).done(function (res) {
                    console.log('datos', res)
                    $('#capital_liquidar').html('$ ' + res.capital);
                    $('#desgravament_liquidar').html('$ ' + res.desgravamen);
                    $('#interes_liquidar').html('$ ' + res.interes);
                    $('#total_liquidar').html('$ ' + res.total);
                    $('#carpeta_liquidar').val(res.carpeta);
                    stoploading();
                    var det = '';
                    $.each(res.detalle, function (data, value) {
                        det += '<tr id="letra-' + value.id + '">';
                        det += '     <td class="text-center">' + value.numero_cuota + '</td>';
                        det += '     <td class="text-center"><b>$' + value.date_vencimiento + '</b></td>';
                        det += '     <td class="text-center"><b>$' + value.capital_amortizado + '</b></td>';
                        det += '     <td class="text-center"><b>$' + value.fondo_desgravamen + '</b></td>';
                        if (value.date_vencimiento <= res.feacha_actual) {
                            det += '     <td class="text-center"><b>$' + value.interes_periodo + '</b></td>';
                        } else {
                            det += '     <td class="text-center"><span class="badge bg-danger">No Aplica interés</span></td>';
                        }
                        det += '     <td class="text-center">'
                        if (value.status == 'PENDIENTE') {
                            det += '     <span class="badge bg-warning">' + value.status + '</span>';
                        } else {
                            det += '     <span class="badge bg-success">' + value.status + '</span>';
                        }
                        det += '     </td>';
                        det += '</tr>';
                    });
                    $('#credit_detalle_table > tbody').html(det);
                    $('#frmModalCreditLiquidar').modal('show');
                });
            }
        });
    }
    function liquitarCreditoFinal() {
        var id = $('#carpeta_liquidar').val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{URL::to('credit/liquitarCreditoFinal')}}/" + id,
            type: 'POST',
            success: function (res) {
                if (res) {
                    Swal.fire('Transacción Realizada con Exito...');
                    location.reload();
                }
            }
        });
    }
    function creditoIncobrable(id) {
        Swal.fire({
            title: "Crédito Incobrable",
            text: "¡Este Proceso, modificará mucha información, continuar?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Si, Continuar..."
        }).then(function (result) {
            if (result.value) {
                loading();
                $.ajax({
                    method: "GET",
                    url: "{{URL::to('credit/incobrableDetalle')}}/" + id,
                }).done(function (res) {
                    stoploading();
                    var det = '';
                    $.each(res.detalle, function (data, value) {
                        det += '<tr id="letra-' + value.id + '">';
                        det += '     <td class="text-center">' + value.numero_cuota + '</td>';
                        det += '     <td class="text-center"><b>$' + value.date_vencimiento + '</b></td>';
                        det += '     <td class="text-center"><b>$' + value.capital_amortizado + '</b></td>';
                        det += '     <td class="text-center"><b>$' + value.fondo_desgravamen + '</b></td>';
                        if (value.date_vencimiento <= res.feacha_actual) {
                            det += '     <td class="text-center"><b>$' + value.interes_periodo + '</b></td>';
                        } else {
                            det += '     <td class="text-center"><span class="badge bg-danger">No Aplica interés</span></td>';
                        }
                        det += '     <td class="text-center">'
                        if (value.status == 'PENDIENTE') {
                            det += '     <span class="badge bg-warning">' + value.status + '</span>';
                        } else {
                            det += '     <span class="badge bg-success">' + value.status + '</span>';
                        }
                        det += '     </td>';
                        det += '</tr>';
                    });
                    $('#capital').html('$ ' + res.capital);
                    $('#desgravament').html('$ ' + res.desgravamen);
                    $('#interes').html('$ ' + res.interes);
                    $('#total').html('$ ' + res.total);
                    $('#carpeta').val(res.carpeta);
                    $('#credit_detalle_table > tbody').html(det);
                    $('#frmModalCreditIncobrable').modal('show');
                });
            }
        });
    }
    function creditoIncobrableFinal() {
        var id = $('#carpeta').val();
        var parametros = {};
        parametros['incobrable'] = $('#incobrable').val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{URL::to('credit/creditoIncobrableFinal')}}/" + id,
            type: 'POST',
            data: {parametros: parametros},
            success: function (res) {
                if (res) {
                    Swal.fire('Transacción Realizada con Exito...');
                    location.reload();
                }
            }
        });
    }
    function modalFiles(id) {
        $.ajax({
            url: "{{URL::to('credit/showFiles')}}/" + id,
            type: 'GET',
            success: function (res) {
                var serv = '';
                $.each(res, function (key, value) {
                    serv += ' <tr class="success" id="archivo-' + value.id + '">';
                    serv += '     <td>' + value.archivo + '</td>';
                    serv += '     <td>' + value.descripcion + '</td>';
                    serv += '     <td class="text-align-center">';
                    serv += '         <a class="btn btn-danger btn-xs" href="javascript:verDocumento(' + value.id + ');"><i class="far fa-file-pdf"></i></a>';
                    serv += '         <a class="btn btn-primary btn-xs" href="{!! URL::to("/") !!}/' + value.path + '"  target="_blank"><i class="fa fa-download"></i></a>';
                    serv += '         <a class="btn btn-danger btn-xs" onclick="javascript:eliminarArchivo(' + value.id + ');"><i class="fa fa-times"></i></a>';
                    serv += '     </td>';
                    serv += ' </tr>';
                });
                $('#table_files > tbody').html(serv);
                $('#id_credit').val(id);
            }
        });
        $('#frmModalFile').modal('show');
    }
    function verDocumento(id) {
        $.ajax({
            url: "{{URL::to('credit/verPdfView')}}/" + id,
            type: 'GET',
            success: function (res) {
                console.log(res);
                var pdf = '<iframe id="imagen_comprobante" class="img" width="100%" height="500px" src="/uploads/credit/' + res + '">';
                $('#print-document').html(pdf);
                $('#path_invoice').val(res);
                $('#frmPrintDocumentModal').modal('show');
                $('#frmModalFile').modal('hide');
            }
        });
    }
    function eliminarArchivo(id) {
        Swal.fire({
            title: 'Esta Seguro...?',
            text: 'Desea Eliminar el Archivo..?',
            icon: 'question',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, Proceder',
            cancelButtonText: 'No, cancelar!',
            cancelButtonClass: 'btn btn-danger',
            confirmButtonClass: 'btn btn-success',
            showCancelButton: true,
            showCloseButton: true
        }).then((result) => {
            if (result) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{URL::to('credit/destroyArchivo')}}/" + id,
                    type: 'POST',
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Archivo Eliminado Correctamente...',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        $('#archivo-' + id).fadeOut();
                    }
                });
            }
        });
    }
    function anadirArchivos() {
        var id = $('#id_credit').val();
        var descripcion = $('#nombre_archivo').val();
        var archivo = $('#file').val();
        if (descripcion !== '') {
            if (archivo !== '') {
                var data = new FormData();
                data.append('file', $('#file').prop("files")[0]);
                data.append('descripcion', descripcion);
                data.append('pacienteId', $('#paciente_id').val());
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ URL::to('credit/guardarArchivos') }}/" + id,
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    data: data,
                    success: function (res) {
                        if (res) {
                            var detalle = '';
                            detalle += '<tr class="success" id="archivo-' + res.id + '">';
                            detalle += '    <td>' + res.archivo + '</td>';
                            detalle += '    <td>' + res.descripcion + '</td>';
                            detalle += '    <td class="text-align-center">';
                            detalle += '         <a class="btn btn-danger btn-xs" href="javascript:verDocumento(' + res.id + ');"><i class="far fa-file-pdf"></i></a>';
                            detalle += '         <a class="btn btn-primary btn-xs" href="{!! URL::to("/") !!}/' + res.path + '"  target="_blank"><i class="fa fa-download"></i></a>';
                            detalle += '         <a class="btn btn-danger btn-xs" onclick="javascript:eliminarArchivo(' + res.id + ');"><i class="fa fa-times"></i></a>';
                            detalle += '    </td>';
                            detalle += '</tr>';
                            $('#table_files >tbody').prepend(detalle);
                            Swal.fire('Nuevo Archivo Agregado al Producto Correctament...');
                        } else {
                            Swal.fire('Existe algún problema por favor contáctese con sistemas...');
                        }

                    },
                    error: function () {
                        Swal.fire('Transacción Realizada con Exito...');
                    }
                });
            } else {
                Swal.fire({
                    title: 'Debe agregar un archivo',
                    icon: 'info',
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonText: "Aceptar",
                });
            }
        } else {
            Swal.fire({
                title: 'Debe agregar una descripción del archivo',
                icon: 'info',
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: "Aceptar",
            });
        }
    }
    function imprimirTicket(id) {
        $.ajax({
            url: "{{URL::to('credit/pdfCuota')}}/" + id,
            type: 'GET',
            success: function (res) {
                console.log(res);
                $('#frmModalCreditDetalles').modal('hide');
                $('#cabecera_ticket').html(res);
                $('#frmModalTiket').modal('show');
            }
        });
    }
    function editarCredito(id) {
        $('#numeroCredito').val(id);
        $('#modalEditCredito').modal('show');
    }
    function guardarEditCredito() {
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: "POST",
            url: "{{URL::to('credit/creditos/edit')}}",
            data: $('#editarCredito').serialize()
        }).done(function (res) {
            location.reload();
        });
    }
    function verEncaje(id) {
        $.ajax({
            url: "{{URL::to('credit/pdfEncaje')}}/" + id,
            type: 'GET',
            success: function (res) {
                $('#cabecera_ticket').html(res);
                $('#frmModalTiket').modal('show');
            }
        });
    }
    function reiniciarPagina() {
        location.reload();
    }
</script>
@stop
