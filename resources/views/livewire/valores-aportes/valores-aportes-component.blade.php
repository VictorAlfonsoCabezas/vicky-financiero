<div>
    <div class="row col col-sm-12">

        <section class="col col-sm-4">
            <div class="form-group">
                <label for="fecha_fin">Hasta:</label>
                <input type="month" class="form-control text-uppercase" id="mes_fin" name="mes_fin" wire:model="mes_fin" wire:change="obtenerDatos">
            </div>
        </section>
        <div class="col-4">
            <label>Busqueda</label>
            <div class="input-group input-group-sm">
                <input type="text" wire:model="search" id="search" class="form-control" placeholder="Buscar Socio">
                <div class="input-group-append">
                    <div class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row col col-sm-12">
        <div class="row">
            <a class="btn btn-primary btn-xs" style="color: white;" wire:click="export">
                <i class="fas fa-plus"></i> Exportar Excel
            </a>
        </div>
    </div>
    <br>
    <div class="row col col-sm-12">
        <div class="row">
            <a class="btn btn-primary btn-xs" style="color: white;" wire:click="generateTxt">
                <i class="fas fa-plus"></i> Exportar Txt
            </a>
        </div>
    </div>
    <div>
        @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
        @endif

        <form wire:submit.prevent="processFile">
            <div class="custom-file row col col-sm-6" id="inputFile">
                <input type="file" wire:model="uploadedFile" class="custom-file-input" id="exampleInputFile" onchange="updateLabel(this)">
                <label class="custom-file-label" for="exampleInputFile" wire:ignore>Buscar Archivo</label>
            </div>

            @error('uploadedFile')
            <span class="text-danger">{{ $message }}</span>
            @enderror

            <!-- Botón para cargar y procesar el archivo -->
            <button type="submit" class="btn btn-primary mt-3">Procesar Archivo</button>
        </form>
    </div>


    <div class="row col col-sm-12">
        <div class="card-body" style="background-color: #ededf3;">
            <div class="tab-content">
                <div class="card-body table-responsive p-0">

                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th><b>Cuenta N°</b></th>
                                <th><b>Cliente</b></th>
                                <th><b>Valor Aporte</b></th>
                                <th><b>N° Letras Pendientes</b></th>
                                <th><b>Valor Creditos</b></th>
                                <th><b>Detalle</b></th>
                                <th><b>Total</b></th>

                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            @foreach($cuentasClientes as $key => $value)
                            <tr>
                                <td>{{ $value->codigo }}</td>
                                <td>{{ $value->cliente }}</td>
                                <td>{{ $value->valorperiodico }}</td>
                                <td>{{ $value->letrasPendientes }}</td>
                                <td>{{ $value->valorPendiente }}</td>
                                <td>
                                    @foreach($value->letrasVencidas as $vencidas)
                                    Letra <b> {{ $vencidas->numero_cuota}}</b>, crédito <b>{{ $vencidas->code_folder_header}}</b>, Valor <b>{{ $vencidas->valor_cuota}}</b> <br>
                                    @endforeach

                                </td>
                                <td>{{ $value->totalRecaudar }}</td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $cuentasClientes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function updateLabel(input) {
        var fileName = input.files[0].name;
        var label = input.nextElementSibling;
        label.innerText = fileName;
    }
</script>