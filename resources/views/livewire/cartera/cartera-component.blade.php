<div>
    <div class="row">
        <div class="col-md-5">
            <div class="input-group input-group-sm mb-2 mt-2">
                <input type="text" wire:model="search" id="search" class="form-control" placeholder="Buscar Regla">
                <div class="input-group-append">
                    <div class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
            <a href="/cartera-reglas" target="_blank" style="font-size: 12px;">Ver Reglas</a>
            <div class="row">
                <div class="col-6">
                    <select class="form-control mb-2" wire:model="mes" wire:change="vaciarValores()">
                        @foreach ($meses as $mes)
                            <option value="{{ $mes->codigo }}">{{ $mes->mes }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <select class="form-control mb-2" wire:model="anio" wire:change="vaciarValores()">
                        @foreach ($anios as $an)
                            <option value="{{ $an }}">{{ $an }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Dias mora</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reglas as $reg)
                                <tr>
                                    <td>
                                        <img src="dist/img/default-150x150.png" alt="Product 1"
                                            class="img-circle img-size-32 me-2">
                                        {{ $reg->nombre }}
                                    </td>
                                    <td>
                                        <small class="text-success me-1">
                                            <i class="fas fa-arrow-up"></i>
                                            {{ $reg->dias_mora }}dias
                                        </small>
                                    </td>
                                    <td>
                                        @if ($reg->estadoHeader == 'PENDIENTE')
                                            <small class="badge bg-primary"><i class="far fa-clock"></i>
                                                {{ $reg->estadoHeader }}</small>
                                        @else
                                            <small class="badge bg-success"><i class="far fa-clock"></i>
                                                {{ $reg->estadoHeader }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($reg->estadoHeader == 'FINALIZADO')
                                            <a wire:click="ver({{ $reg->id }})" class="text-muted">
                                                <i class="fas fa-search"></i>
                                            </a>
                                        @else
                                            <a wire:click="calcular({{ $reg->id }})" class="text-muted">
                                                <i class="fas fa-calculator"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="row">
                <div class="col-9">
                    <div class="input-group input-group-sm mb-2 mt-2">
                        <input type="text" wire:model="search2" id="search2" class="form-control"
                            placeholder="Buscar Cliente">
                        <div class="input-group-append">
                            <div class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($this->finalizarInteres)
                    <div class="col-3  mb-2 mt-2">
                        <a wire:click="finalizarRegla();" class="btn btn-success btn-sm" style="color: white;"><i
                                class="fa fa-comment"></i> Enviar Whatsapp Todos</a>
                    </div>
                @endif
            </div>
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombres</th>
                                <th>Fecha Vencimiento</th>
                                <th>Dias Mora</th>
                                <th>Mensaje</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detalles as $det)
                                <tr>
                                    <td>
                                        @if ($det->estado == 'FINALIZADO')
                                            <input class="form-check-input" type="checkbox" checked disabled>
                                        @else
                                            <input class="form-check-input" type="checkbox">
                                        @endif
                                    </td>
                                    <td>{{ $det->nombres . ' ' . $det->apellidos }}</td>
                                    <td>{{ $det->date_vencimiento }}</td>
                                    <td>{{ $det->dias_transcurridos }}</td>
                                    <td>{{ $det->mensaje }}</td>
                                    <td>
                                        @if ($det->estado == 'PENDIENTE')
                                            <small class="badge bg-primary"><i class="far fa-clock"></i>
                                                {{ $det->estado }}</small>
                                        @else
                                            <small class="badge bg-success"><i class="far fa-clock"></i>
                                                {{ $det->estado }}</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    {{ $detalles->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
