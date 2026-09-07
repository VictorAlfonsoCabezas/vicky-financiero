@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 mt-3">

            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <div class="row">
                            <div class="col-5 text-center">
                                <img src="uploads/companies/{{$company->photo}}" alt="user-avatar" class="img-circle img-fluid" style="max-width: 130px; height: auto;">
                            </div>
                            <div class="col-7">
                                <h2 class="lead"><b>{{$company->comercial_name}}</b></h2>
                                <p class="text-muted text-sm"><b>Dirección: </b>{{$company->address}} </p>
                                <p class="text-muted text-sm"><b>Telefono: </b>{{$company->phone}} </p>
                                <p class="text-muted text-sm"><b>Email: </b>{{$company->email}} </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mt-3">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-plus"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" style="color: blue"> <b>Depositos</b></span>
                                <span class="info-box-number">
                                    <a href="{{URL::to('/cuentas/0')}}" class="btn btn-primary btn-xs" style="color: white;">
                                        <i class="fas fa-plus"></i> Agregar
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-minus"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" style="color: red"><b>Retiros</b></span>
                                <span class="info-box-number">
                                    <a href="{{URL::to('/cuentas/0')}}" class="btn btn-danger btn-xs" style="color: white;">
                                        <i class="fas fa-plus"></i> Agregar
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-default elevation-1"><i class="fas fa-money-bill-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" style="color: #28a745"><b>Gastos</b></span>
                                <span class="info-box-number" style="color: #28a745;">
                                    <a href="{{URL::to('gastos')}}" class="btn btn-default btn-xs">
                                        <i class="fas fa-plus"></i> Agregar
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-credit-card"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text" style="color: #ffc107"><b>Nuevo Crédito</b></span>
                                <span class="info-box-number" style="color: #28a745;">
                                    
                                    <a href="{{URL::to('/creditos/0')}}" class="btn btn-warning btn-xs" style="color: white;">
                                        <i class="fas fa-list"></i> Ver
                                    </a>
                                    
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">REPORTE MENSUAL INGRESOS/EGRESOS CLIENTES</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                            </p>
                            <p class="ms-auto d-flex flex-column text-end">
                            </p>
                        </div>
                        <div class="position-relative mb-4">
                            <canvas id="mensual-vista" height="200"></canvas>
                        </div>
                        <div class="d-flex flex-row justify-content-end">
                            <span class="me-2">
                                <i class="fas fa-square text-primary"></i> INGRESOS
                            </span>

                            <span>
                                <i class="fas fa-square text-red"></i> EGRESOS
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header border-0">
                                        <h3 class="card-title">Letras Vencidas entre <b> {{date('Y-m').'-01'}} </b> al <b>{{ date('Y-m-d', strtotime(date('Y-m') . '-01 +1 month -1 day'))}} </b></h3>

                                    </div>
                                    <div class="card-body table-responsive p-0" style="height: 47vh; overflow: auto;">
                                        <table id="mora_table_mes" class="table table-striped table-valign-middle" style="font-size: 13px;">
                                            <thead>
                                                <tr id="busqueda">
                                                    <th>Cliente</th>
                                                    <th>Fecha Vencimiento</th>
                                                    <th>Días Mora</th>
                                                    <th>valor</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header border-0">
                                        <div class="d-flex justify-content-between">
                                            <h3 class="card-title">Dinero entregado</h3>
                                        </div>
                                    </div>
                                    <div class="card-body">

                                        <div class="position-relative mb-4">
                                            <canvas id="visitors-chart" height="200"></canvas>
                                        </div>
                                        <div class="d-flex flex-row justify-content-end">
                                            <span class="me-2">
                                                <i class="fas fa-square text-primary"></i> Dinero Entregado
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header border-0">
                                        <div class="d-flex justify-content-between">
                                            <h3 class="card-title">Intereses Cobrados</h3>
                                        </div>
                                    </div>
                                    <div class="card-body">

                                        <div class="position-relative mb-4">
                                            <canvas id="interes_cobrado" height="200"></canvas>
                                        </div>
                                        <div class="d-flex flex-row justify-content-end">
                                            <span class="me-2">
                                                <i class="fas fa-square text-primary"></i> Intereses Cobrados
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header border-0">
                                        <h3 class="card-title">Todas las letras Vencidas
                                            <a class="btn  btn-xs" href="{{URL::to('cobranza')}}" title="Ver Vencidas"><span class="badge bg-danger"><i class="fas fa-eye"></i></span></a>
                                        </h3>
                                    </div>
                                    <div class="card-body table-responsive p-0">
                                        <table id="tabla_pending_pay" class="table table-striped table-valign-middle" style="font-size: 13px;">
                                            <thead>
                                                <tr id="busqueda">
                                                    <th>Cliente</th>
                                                    <th class="filtre">Fecha Vencimiento</th>
                                                    <th>Días Mora</th>
                                                    <th>valor</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('cobranza/mensajes')
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('.sidebar-mini').addClass('sidebar-collapse');
        datosIniciales();
        ObtenerDatosCeditos();
        atrasosLetrasMes();
        listarTabla();
        $('#buscar_cuenta').keydown(function(event) {
            if (event.shiftKey && event.keyCode === 224) {
                event.preventDefault();
            }
            if (event.keyCode === 13) {
                loading();
                if ($(this).val() !== '') {
                    var dato = $(this).val();
                    console.log('dato a buscar', dato);
                    $.ajax({
                        url: "/customer/buscar/" + dato,
                        type: 'GET',
                        success: function(res) {
                            console.log('respuest', res);
                            stoploading();
                            if (res != '') {
                                if (res.length > 1) {
                                    var customers = '';
                                    $.each(res, function(key, value) {
                                        customers += '<tr>';
                                        customers += '     <td>' + value.code + '</td>';
                                        customers += '     <td>' + value.nombres + '' + value.apellidos + '</td>';
                                        customers += '     <td>' + value.numero_documento + '</td>';
                                        customers += '     <td class="text-center"><a class="btn btn-lg btn-default" onclick="irCuenta(' + value.numero_documento + ');"><i class="fas fa-check text-primary"></i></a></td>';
                                        customers += '</tr>';
                                    });
                                    $('#customer_table >tbody').html(customers);
                                    $('#formCustomerIngresos').modal('show');
                                } else {
                                    var ruc = res[0].numero_documento;
                                    irCuenta(ruc);
                                }

                            } else {
                                Swal.fire({
                                    title: "SIN REGISTROS DE ESTE CLIENTE",
                                    type: "warning",
                                    confirmButtonText: "ACEPTAR"
                                });

                            }
                        }
                    });
                }
            }
        });
    });
    //    Grafica mensual
    function datosIniciales() {
        console.log('obtener datos inciales');
        $.ajax({
            url: "{{URL::to('home/datos')}}",
            type: 'GET',
            success: function(res) {
                obtenerDatostanuales(res);
            }
        });

    }

    function obtenerDatostanuales(meses) {
        'use strict'
        var ticksStyle = {
            fontColor: '#495057',
            fontStyle: 'bold'
        }
        var mode = 'index'
        var intersect = true

        var $salesChart = $('#mensual-vista')
        var salesChart = new Chart($salesChart, {
            type: 'bar',
            data: {
                labels: meses['meses'],
                datasets: [{
                        backgroundColor: '#007bff',
                        borderColor: '#007bff',
                        data: meses['ingresos']
                    },
                    {
                        backgroundColor: 'red',
                        borderColor: '#red',
                        data: meses['retiros']
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    mode: mode,
                    intersect: intersect
                },
                hover: {
                    mode: mode,
                    intersect: intersect
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        // display: false,
                        gridLines: {
                            display: true,
                            lineWidth: '4px',
                            color: 'rgba(0, 0, 0, .2)',
                            zeroLineColor: 'transparent'
                        },
                        ticks: $.extend({
                            beginAtZero: true,

                            // Include a dollar sign in the ticks
                            callback: function(value, index, values) {
                                if (value >= 1000) {
                                    value /= 1000
                                    value += 'k'
                                }
                                return '$' + value
                            }
                        }, ticksStyle)
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: false
                        },
                        ticks: ticksStyle
                    }]
                }
            }
        })

    }

    function ObtenerDatosCeditos() {
        console.log('obtener grafica dos');
        $.ajax({
            url: "{{URL::to('home/credit')}}",
            type: 'GET',
            success: function(res) {
                console.log(res, 'datos para el nuevo fraf');
                dineroEntregado(res);
                interesCobrado(res);
            }
        });
    }

    function dineroEntregado(meses) {
        'use strict'

        var ticksStyle = {
            fontColor: '#495057',
            fontStyle: 'bold'
        }

        var mode = 'index'
        var intersect = true

        var $visitorsChart = $('#visitors-chart')
        var visitorsChart = new Chart($visitorsChart, {
            data: {
                labels: meses['meses'],
                datasets: [{
                    type: 'line',
                    data: meses['entregado'],
                    backgroundColor: 'transparent',
                    borderColor: '#007bff',
                    pointBorderColor: '#007bff',
                    pointBackgroundColor: '#007bff',
                    fill: false
                    // pointHoverBackgroundColor: '#007bff',
                    // pointHoverBorderColor    : '#007bff'
                }]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    mode: mode,
                    intersect: intersect
                },
                hover: {
                    mode: mode,
                    intersect: intersect
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        // display: false,
                        gridLines: {
                            display: true,
                            lineWidth: '4px',
                            color: 'rgba(0, 0, 0, .2)',
                            zeroLineColor: 'transparent'
                        },
                        ticks: $.extend({
                            beginAtZero: true,
                            suggestedMax: 200
                        }, ticksStyle)
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: false
                        },
                        ticks: ticksStyle
                    }]
                }
            }
        })
    }

    function interesCobrado(meses) {
        'use strict'

        var ticksStyle = {
            fontColor: '#495057',
            fontStyle: 'bold'
        }

        var mode = 'index'
        var intersect = true

        var $visitorsChart = $('#interes_cobrado')
        var visitorsChart = new Chart($visitorsChart, {
            data: {
                labels: meses['meses'],
                datasets: [{
                    type: 'line',
                    data: meses['interes'],
                    backgroundColor: 'transparent',
                    borderColor: '#007bff',
                    pointBorderColor: '#007bff',
                    pointBackgroundColor: '#007bff',
                    fill: false
                    // pointHoverBackgroundColor: '#007bff',
                    // pointHoverBorderColor    : '#007bff'
                }]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    mode: mode,
                    intersect: intersect
                },
                hover: {
                    mode: mode,
                    intersect: intersect
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        // display: false,
                        gridLines: {
                            display: true,
                            lineWidth: '4px',
                            color: 'rgba(0, 0, 0, .2)',
                            zeroLineColor: 'transparent'
                        },
                        ticks: $.extend({
                            beginAtZero: true,
                            suggestedMax: 200
                        }, ticksStyle)
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: false
                        },
                        ticks: ticksStyle
                    }]
                }
            }
        })
    }

    function irCuenta(ruc) {
        console.log(ruc);
        window.location.href = "{{ URL::to('historial/movimientos') }}/" + ruc;
    }

    function verMensajes(id) {
        $.ajax({
            url: "/cobranza/notificarLetraMensajes/" + id,
            type: 'GET',
            async: false,
        }).done(function(res) {
            var detalle = '';
            $.each(res.notificaciones, function(key, value) {
                detalle += '<div class="direct-chat-msg">';
                detalle += '    <div class="direct-chat-infos clearfix">';
                detalle += '        <span class="direct-chat-name float-start">Codev</span>';
                detalle += '        <span class="direct-chat-timestamp float-end">' + value.fecha_creacion + '</span>';
                detalle += '    </div>';
                detalle += '    <img class="direct-chat-img" src="../codev/negro.png" alt="Message User Image">';
                detalle += '    <div class="direct-chat-text">' + value.whatsapp + '</div>';
                detalle += '</div>';

            });
            $('#listaMensajesLetras').html(detalle);
            $('#totalEnvios').html(res.total);
            $('#textodesc').html(res.texto);
            $('#modalDetalleNotificacion').modal('show');

        });
    }
</script>
@stop