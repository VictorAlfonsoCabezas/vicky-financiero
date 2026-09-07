@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<div class="row col col-sm-12">
    <section class="col col-sm-6">
        <div class="form-group">
            <label for="valor_prestamo">Desde:</label>
            <input type="date" class="form-control text-uppercase" id="fecha_inicio" name="fecha_inicio" required="" value="{{date('Y-m-d')}}">
        </div>
    </section>
    <section class="col col-sm-6">
        <div class="form-group">
            <label for="valor_prestamo">Hasta:</label>
            <input type="date" class="form-control text-uppercase" id="fecha_fin" name="fecha_fin" required="" value="{{date('Y-m-d')}}">
        </div>
    </section>

</div>


<div class="botonesSuperiores" style="margin-bottom: 5px;">
    <a class="btn btn-labeled btn-info header-btn" href="javascript:descargarCreditos();">
        <span class="btn-label"><i class="fa fa-download"></i></span>
        Creditos
    </a>
    <a class="btn btn-labeled btn-danger header-btn" href="javascript:descargarVencidos();" style="display: none;">
        <span class="btn-label"><i class="fa fa-download"></i></span>
        Vencidos
    </a>
    <a class="btn btn-labeled btn-success header-btn" href="javascript:descargarPendientes();">
        <span class="btn-label"><i class="fa fa-download"></i></span>
        Pendientes
    </a>
</div>



@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('.sidebar-mini').addClass('sidebar-collapse');
    });
    function descargarCreditos() {
        location.href = "{{URL('credit/download/creditos')}}/" + $('#fecha_inicio').val() + "/" + $('#fecha_fin').val();
    }
    function descargarVencidos() {
        location.href = "{{URL('credit/download/vencidos')}}/" + $('#fecha_inicio').val() + "/" + $('#fecha_fin').val();
    }
    function descargarPendientes() {
        location.href = "{{URL('credit/download/pendientes')}}/" + $('#fecha_inicio').val() + "/" + $('#fecha_fin').val();
    }
</script>
@stop