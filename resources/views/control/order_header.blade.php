@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-bs-dismiss="alert" aria-hidden="true">×</button>
    <h5><i class="icon fas fa-info-circle"></i> Hola, {{Auth::user()->firstname}}!</h5>
    - Aqui podemos agregar, editar e inactivar todo referente a los pedidos realizados.
</div>
<br>
<div class="card card-primary">
    <div class="card-header">
        <h2 class="card-title">LISTA DE PEDIDOS</h2>
        <div class="card-tools">
            <a href="" class="btn btn-m btn-warning"><i class="fas fa-plus"></i> Nuevo Pedido</a>
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
        </div>
    </div>

@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('#table_company thead #busqueda .filtro').each(function () {
            var title = $(this).text();
            $(this).html('<input type="text" placeholder="' + title + '" class="form-control"/>');
        });

        var table = $('#table_company').DataTable({
            bRetrieve: true,
            scrollX: true,
            "language": {
                url: '//cdn.datatables.net/plug-ins/1.10.9/i18n/Spanish.json'
            }
        });

        $("#table_company thead th input[type=text]").on('keyup change', function () {
            table.column($(this).parent().index() + ':visible')
                    .search(this.value)
                    .draw();
        });

    });

    function eliminarCompany(id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: "DELETE",
            url: "{{URL::to('company')}}/" + id
        }).done(function (res) {
            if (res) {
                $('#' + id).remove();
            }
        });
    }
</script>
@stop