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
            <a href="/int-reglas" target="_blank" style="font-size: 12px;">Ver Reglas</a>
            <div class="row">
                <div class="col-6">
                    <select class="form-control form-control-sm mb-2" wire:model="mes" wire:change="vaciarValores()">
                        @foreach ($meses as $mes)
                            <option value="{{ $mes->codigo }}">{{ $mes->mes }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <select class="form-control form-control-sm mb-2" wire:model="anio" wire:change="vaciarValores()">
                        @foreach ($anios as $an)
                            <option value="{{ $an }}">{{ $an }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-valign-middle table-sm">
                        <thead>
                            <tr>
                                <th style="width: 1%;">#</th>
                                <th>Regla Cálculo</th>
                                <th>Valor</th>
                                <th>Interés</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reglas as $reg)
                                <tr>
                                    <td>
                                        {{ $reg->id }}
                                    </td>
                                    <td>
                                        {{ $reg->nombre }}
                                    </td>
                                    <td>
                                        @if ($reg->operacion == '(+)')
                                            <i class="fas fa-arrow-up"></i>
                                        @else
                                            <i class="fas fa-arrow-down"></i>
                                        @endif
                                        ${{ $reg->valor }}
                                    </td>
                                    <td>
                                        <small class="text-success me-1">
                                            @if ($reg->operacion == '(+)')
                                                <i class="fas fa-arrow-up"></i>
                                            @else
                                                <i class="fas fa-arrow-down"></i>
                                            @endif
                                            {{ $reg->porcentaje }}%
                                        </small>
                                    </td>
                                    <td>
                                        @if ($reg->estadoHeader == 'FINALIZADO')
                                            <small class="badge bg-success"><i class="far fa-clock"></i>
                                                {{ $reg->estadoHeader }}</small>
                                        @else
                                            <small class="badge bg-primary"><i class="far fa-clock"></i>
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
                        <a wire:click="finalizarRegla();" class="btn btn-primary btn-sm"
                            style="color: white;">Finzalizar Todos</a>
                    </div>
                @endif
            </div>
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-valign-middle table-sm">
                        <thead>
                            <tr>
                                <th>Nombres</th>
                                <th>Tipo Cuenta</th>
                                <th>Saldo</th>
                                <th>Interes</th>
                                <th>Total</th>
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
                                        <small>{{ $det->customer->apellidos . ' ' . $det->customer->nombres }}</small>
                                        <br><b># Cuenta: {{ $det->customerTipoAhorros->codigo }}</b>
                                    </td>
                                    <td> <small
                                            class="badge" style="{{ \App\Support\SavingsPalette::legacyStyle($det->customerTipoAhorros->tipoAhorros->class) }}">{{ $det->customerTipoAhorros->tipoAhorros->name }}</small>
                                    </td>
                                    <td>${{ $det->valor_saldo }}</td>
                                    <td>
                                        <small class="text-success me-1">
                                            @if ($det->operacion == '(+)')
                                                <i class="fas fa-arrow-up"></i>
                                            @else
                                                <i class="fas fa-arrow-down"></i>
                                            @endif
                                            {{ $det->valor_interes }}
                                        </small>
                                    </td>
                                    <td>
                                        {{ $det->valor_total }}
                                    </td>
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
