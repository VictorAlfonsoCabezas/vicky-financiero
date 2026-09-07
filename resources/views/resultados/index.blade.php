@extends('layouts.app')
@section('custom_css_rules')
<script src="https://code.highcharts.com/highcharts.js"></script>
@stop
@section('content')
<div  class="card card-primary shadow-lg bg-white rounded">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Registro Pagos Vencidos</h3>
        </div>
        <div class="card-body">

            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-lg-5">
                                <label>Fecha Inicio:</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="inicio" value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="form-group col-lg-5">
                                <label>Fecha Actual:</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="fin" value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                            <div>
                                <button onclick="javascript:generarResultados();"class="btn btn-primary"style="position: relative;top: 31px;right: -10px;"><i class="fas fa-clipboard-list"></i> Consultar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>

        </div>
    </div>
</div>
<div id="container">

</div>
@endsection
@section('scripts')
<script type="text/javascript">
    function generarResultados() {
        loading();
        var inicio = $('#inicio').val();
        var fin = $('#fin').val();
        $.ajax({
            method: "GET",
            url: "{{URL::to('resultados/generarResultados')}}/" + inicio + '/' + fin
        }).done(function (res) {
            stoploading();
            console.log(res);
            generarGrafica(res);
            location.href = "{{URL::to('resultados/generarPdfResultados') }}/" + inicio + '/' + fin;
        });
    }
    function generarGrafica(res) {
        
        Highcharts.chart('container', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'INFORME DE INGRESOS Y EGRESOS',
                align: 'left'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                         format: '{point.name}: <b>{point.y} $</b>'
                    }
                }
            },
            series: [{
                    name: 'Brands',
                    colorByPoint: true,
                    data: [
                        {
                            name: 'INGRESOS',
                            y: res.ingresos
                        }, 
                        {
                            name: 'EGRESOS',
                            y: res.egresos
                        }
                    ]
                }]
        });
    }

</script>
@stop
