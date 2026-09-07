@extends('layouts.app')
@section('custom_css_rules')@stop
@section('content')
<div class="col-md-12">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Crear Cliente</h3>
        </div>
        <form id="form-pedido" role="form" action="{{URL::to('customer')}}" method="POST" class="container was-validated" autocomplete="off" enctype="multipart/form-data">
            @csrf
            <div class="card-header" style="background-color: #bccef4;">
                <h3 class="card-title">Datos del Cliente</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Nombres</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fab fa-product-hunt"></i></span>
                            </div>
                            <input type="text" id="nombres" name="nombres" class="form-control text-uppercase" placeholder="Nombres" required value="{{old('nombres')}}">
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Apellidos</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fab fa-product-hunt"></i></span>
                            </div>
                            <input type="text" id="apellidos" name="apellidos" class="form-control text-uppercase" placeholder="Apellidos" required>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Documento</label>
                        <div class="input-group mb-3">
                            <select id="documento" name="documento" class="form-control" required>
                                <option value="03">RUC</option>
                                <option value="04" selected="">CEDULA</option>
                                <option value="05">PASAPORTE</option>
                            </select>                            
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Número de Cédula/o ruc</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fab fa-product-hunt"></i></span>
                            </div>
                            <input type="number" id="numero_documento" name="numero_documento" class="form-control" placeholder="C.I" required onblur="buscarCustomer();">
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <div class="form-group">
                            <label for="porcentaje">Código Pais</label>
                            <select type="text" id="codigo" name="codigo" class="form-control text-uppercase" required>
                                @foreach($country as $count)
                                <option value="{{$count->id}}">{{$count->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Teléfono</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fab fa-product-hunt"></i></span>
                            </div>
                            <input type="number" id="telefono" name="telefono" class="form-control" placeholder="Telefono" required>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Correo</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fab fa-product-hunt"></i></span>
                            </div>
                            <input type="email" id="correo" name="correo" class="form-control" placeholder="Correo" >
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="form-group">
                            <label for="porcentaje">Tarifa</label>
                            <select type="text" id="tarifa" name="tarifa" class="form-control text-uppercase" required>
                                <option value="" > -- Seleccione -- </option>
                                @foreach($tarifas as $tarifa)
                                <option value="{{$tarifa->id}}">{{$tarifa->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="form-group">
                            <label for="porcentaje">Estado Civil</label>
                            <select type="text" id="estado_civil" name="estado_civil" class="form-control text-uppercase" onchange="javascript:cambioEstadoCivil();">
                                @foreach(config('constants.ESTOS_CIVIL') as  $type)
                                <option value="{{$type['code']}}"> {{$type['name']}} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Dirección</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fab fa-product-hunt"></i></span>
                            </div>
                            <textarea type="text" id="direccion" name="direccion" class="form-control text-uppercase" placeholder="Dirección" ></textarea>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="card-header" style="background-color: #b5dcf9;">
                    <h3 class="card-title">Datos del Conyuge</h3>
                </div>
                <div class="row" id="conyugue">


                </div>
                <hr>
                <div class="card-header" style="background-color: #a9e5e3;">
                    <h3 class="card-title">Datos del Parentesco</h3>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <div class="form-group">
                            <label for="porcentaje">Parentesco</label>
                            <select type="text" id="parentesco" name="parentesco" class="form-control" required>
                                <option value="" > -- Seleccione -- </option>
                                @foreach(config('constants.PARENTESCO') as $type)
                                <option value="{{$type}}"> {{$type}} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Nombres Parentesco</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fab fa-product-hunt"></i></span>
                            </div>
                            <input type="text" id="nombres_parentesco" name="nombres_parentesco" class="form-control text-uppercase" placeholder="Nombres Parentesco" required value="{{old('nombres')}}">
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Teléfono Parentesco</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fab fa-product-hunt"></i></span>
                            </div>
                            <input type="number" id="telefono_parentesco" name="telefono_parentesco" class="form-control" placeholder="Telefono" required>
                        </div>
                    </div>
                    <!--                    <div class="form-group col-md-4">
                                            <div class="form-group">
                                                <label for="plazo_anual">Cedula Parentesco</label>
                                                <input type="number" class="form-control text-uppercase" id="identificacion_parentesto" name="identificacion_parentesto"  placeholder="Identificacion del parentesco" required="" onblur="compararDocumento();">
                                            </div>
                                        </div>-->
                </div>
            </div>
            <div class="card-footer">
                <a class="btn btn-warning" href="{{URL::to('customer')}}">Regresar</a>
                <button type="submit" class="btn btn-primary">Crear</button>
            </div>
        </form>
    </div>
</div>

@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('.sidebar-mini').addClass('sidebar-collapse');
        $('#table_order_header thead #busqueda .filtro').each(function () {
            var title = $(this).text();
            $(this).html('<input type="text" placeholder="' + title + '" class="form-control"/>');
        });
        var table = $('#table_order_header').DataTable({
            bRetrieve: true,
            scrollX: true,
            "language": {
                url: '//cdn.datatables.net/plug-ins/1.10.9/i18n/Spanish.json'
            }
        });
        $("#table_order_header thead th input[type=text]").on('keyup change', function () {
            table.column($(this).parent().index() + ':visible')
                    .search(this.value)
                    .draw();
        });
    });
    function eliminarProduct(id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: "DELETE",
            url: "{{URL::to('product')}}/" + id
        }).done(function (res) {
            if (res) {
                $('#' + id).remove();
            }
        });
    }
    function buscarCustomer() {
        var dato = $('#numero_documento').val();
        $.ajax({
            url: "{{URL::to('customer/buscarCedulas')}}/" + dato,
            type: 'GET',
            success: function (res) {
                console.log(res);
                stoploading();
                if (res == false) {
                    Swal.fire({
                        title: "Cliente ya existe",
                        text: "¡Ingrese nuevos Datos!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Aceptar"
                    });
                    $('#nombres').val('');
                    $('#apellidos').val('');
                    $('#numero_documento').val('');

                }else{
                     buscarApiDatos(dato);
                }
            }
        });
    }
    function buscarApiDatos(dato){
    console.log(dato);
    $.ajax({
        url: "/customer/buscarApiDatos/" + dato,
        type: 'GET',
        success: function (res) {
            $('#nombres').val(res.nombres);
            $('#apellidos').val(res.apellidos);
        }
    });
}
    function compararDocumento() {
        var documento_titular = $('#numero_documento').val();
        var documento_parentezco = $('#identificacion_parentesto').val();
        if (documento_titular == documento_parentezco) {
            Swal.fire({
                title: "El cliente no puede ser su Referenaci",
                text: "¡Ingrese nuevos datos!",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar"
            });
            $('#nombres').val('');
            $('#apellidos').val('');
            $('#numero_documento').val('');
            $('#identificacion_parentesto').val('');
            $('#nombres_parentesco').val('');
        }
    }
    function cambioEstadoCivil() {
        var estado = $('#estado_civil').val();
        if (estado != 'SOLTERO/A' && estado != 'VIUDO/A') {
            var campos = "";

            campos += '<div class="form-group col-md-4">';
            campos += '    <label>Nombres Conyugue</label>';
            campos += '    <div class="input-group mb-3">';
            campos += '        <div class="input-group-prepend">';
            campos += '            <span class="input-group-text"><i class="fab fa-product-hunt"></i></span>';
            campos += '        </div>';
            campos += '        <input type="text" id="nombres_conyugue" name="nombres_conyugue" class="form-control text-uppercase" placeholder="Nombres Conyugue" required>';
            campos += '    </div>';
            campos += '</div>';
            campos += '<div class="form-group col-md-4">';
            campos += '    <div class="form-group">';
            campos += '        <label for="plazo_anual">Cédula Conyugue</label>';
            campos += '        <input type="number" class="form-control text-uppercase" id="identificacion_conyugue" name="identificacion_conyugue"  placeholder="Identificacion del parentesco" required>';
            campos += '    </div>';
            campos += '</div>';
            campos += '<div class="form-group col-md-4">';
            campos += '    <div class="form-group">';
            campos += '        <label for="plazo_anual">Telefono Conyugue</label>';
            campos += '        <input type="number" class="form-control text-uppercase" id="telefono_conyugue" name="telefono_conyugue"  placeholder="Telefono Conyugue" required>';
            campos += '    </div>';
            campos += '</div>';
            $("#conyugue").html(campos);

        } else {
            var campos = "";
            $("#conyugue").html(campos);
        }
    }
</script>
@stop