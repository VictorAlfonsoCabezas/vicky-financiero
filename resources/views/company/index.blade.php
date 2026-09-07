 @extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<div class="row">
   <section class="col-lg-3">
        <div class="small-box bg-dark shadow-lg">
            <div class="inner">
                <h3>{{$cantidad}}</h3>
                <p>Empresas</p>
            </div>
            <div class="icon">
                <i class="far fa-building" style="color: white;"></i>
            </div>
            <a href="company/create" class="small-box-footer">Crear Empresa<i class="fas fa-plus-circle"></i></a>
        </div>
    </section>
    <section class="col-lg-9">
        @include('includes.mensaje')
    </section>
</div>
<div class="card card-success">
    <div class="card-header">
        <h2 class="card-title">Información Empresas </h2>
    </div>
    <!-- /.card-header -->
    <div class="card-body" style="display: block;">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-hover table-bordered" style="width:100%" id="table_sucursales">
                    <thead class="thead-dark">
                        <tr>
                            <th style='width: 30px;'>#</th>
                            <th scope="col">Logo</th>
                            <th scope="col">Empresa</th>
                            <th scope="col">Ruc</th>
                            <th scope="col">Dirección</th>
                            <th scope="col">Teléfono</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($company as $value)
                        <tr id='{{$value->id}}'>
                            <td>
                                <form action="{{URL::to('company/'.$value->id)}}" method="POST">
                                    @csrf
                                    @method("delete")
                                    <a href="{{URL::to('company/'.$value->id.'/edit')}}" method="GET" class="btn btn-primary btn-sm" style="color: white;"><i class="fas fa-edit"></i></a>
                                    <!--<button type="submit" class="btn btn-primary btn-xs" style="color: white;"><i class="far fa-trash-alt"></i> Eliminar</button>-->
                                </form>
                            </td>
                            <td>
                                @if ($value->photo !== '' && $value->photo !== null)
                                <img alt="{{$value->comercial_name}}" src="uploads/companies/{{$value->photo}}" id="logo" title="{{$value->comercial_name}}" class="img-thumbnail img-responsive superbox-img photo" style="width: 55px;"/>
                                @else
                                <img src="{{URL::asset('img/no-disponible.png')}}" alt="{{$value->comercial_name}}" title="{{$value->comercial_name}}" height="50" width="50"/>
                                @endif
                            </td>
                            <td>{{$value->comercial_name}}</td>
                            <td>{{$value->ruc}}</td>
                            <td>{{$value->address}}</td>
                            <td>{{$value->phone}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
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