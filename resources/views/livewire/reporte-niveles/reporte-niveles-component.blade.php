<div>
    <div class="row col col-sm-12">
        <section class="col col-sm-6">
            <div class="col-6">
                <label class="small">Nivel </label>
                <select id="nivel" wire:model="nivel" class="form-control form-control-sm" wire:change="seleccionarNivel" wire:key="nivel">
                    <option value=""> --SELECCIONE--</option>
                    <option value="1">De 1 a 30 días</option>
                    <option value="2">De 31 a 90 días</option>
                    <option value="3">De 91 a 180 días</option>
                    <option value="4">De más de 360 dias</option>
                </select>
            </div>
        </section>
    </div>
    <div class="row col col-sm-12">
        <section class="col col-sm-6">
            <div class="col-6">
                <button type="button" wire:click="decargarReporte" class="btn btn-block btn-primary btn-xs" wire:loading.attr="disabled">Descargar</button>
            </div>
        </section>

    </div>
    <div class="row col col-sm-12">

        <table class="table table-striped table-valign-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>CREDITO</th>
                    <th>CEDULA</th>
                    <th>NOMBRES</th>
                    <th>VALOR</th>
                    <th>FECHA </th>
                    <th>DIAS ATRASO</th>
                    <th>PLAZO EN MESES</th>
                    <th># CUOTA</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th><b><label style="color:rgb(52, 129, 44);">{{$this->totalValores}} $</label></b></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->detalleNiveles as $key => $letras)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{$letras->code_folder_header}}</td>
                    <td>{{$letras->cedulaCliente}}</td>
                    <td>{{$letras->nombresCliente}}</td>
                    <td>{{$letras->valor_cuota}}</td>
                    <td>{{$letras->date_vencimiento}}</td>
                    <td>{{$letras->diasDiferencia}}</td>
                    <td>{{$letras->totalLetras}}</td>
                    <td>{{$letras->numero_cuota}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>