<div>
    <div class="col-3 mt-2 mb-2">
        <button wire:click="nuevaRegla(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i> Nueva Regla</button>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Regla de Intereses</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" wire:model="search" id="search" class="form-control float-end" placeholder="Buscar">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 600px;">
                    <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                        <thead>
                            <tr>
                                <th style="width: 1%;">#</th>
                                <th>Valores</th>
                                <th>DE 1 A 30</th>
                                <th>DE 31 A 60 </th>
                                <th>DE 61 A 90 </th>
                                <th>DE 91 A 9999</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($intereses as $interes)
                            <tr>
                                <td>{{$interes->id}}</td>
                                <td>{{'DESDE $'.$interes->valor_inicio .' HASTA $'. $interes->valor_fin }}</td>
                                <td>@if(!$interes->porcentaje)$@else % @endif {{$interes->primer_valor}}</td>
                                <td>@if(!$interes->porcentaje)$@else % @endif {{$interes->segundo_valor}}</td>
                                <td>@if(!$interes->porcentaje)$@else % @endif {{$interes->tercer_valor}}</td>
                                <td>@if(!$interes->porcentaje)$@else % @endif {{$interes->cuarto_valor}}</td>
                                <td> @if ($interes->status)
                                    <small class="badge bg-primary" wire:click="cambioEstado({{ $interes->id }})"></i>Activo</small>
                                    @else
                                    <small class="badge bg-danger" wire:click="cambioEstado({{ $interes->id }})"></i>Desactivado</small>
                                    @endif
                                </td>
                                <td>
                                    <button wire:click="nuevaRegla({{ $interes->id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button" class="btn bg-light btn-xs"><i class="fa fa-pen"></i></button>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $intereses->links() }}
                </div>
            </div>
        </div>
    </div>


    {{-- MODAL CREACION EDICION--}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Regla de intereses.</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        @if ($errors->any())
                        <div class="callout callout-warning">
                            <h5>Verifica estas observaciones.</h5>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </div>
                        @endif
                        <H3>Rango de valores</H3>
                        <section class="col col-sm-4">
                            <div class="col-6">
                                <label class="small">Porcentaje/Valor </label>
                                <select id="porcentaje" wire:model="porcentaje" class="form-control text-uppercase">
                                    <option value="0">Valor</option>
                                    <option value="1">Porcentaje</option>
                                </select>
                            </div>
                        </section>
                        <div class="row mb-4">
                            <div class="col-6">
                                <label>Valor Inicial</label>
                                <div class="input-group mb-6">
                                    <input type="number" class="form-control" wire:model="valor_inicio" step="0.01">
                                    <div class="input-group-append">
                                        <span class="input-group-text" placeholder="Valor Máximo">Valor Inicial.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <label>Valor Final</label>
                                <div class="input-group mb-6">
                                    <input type="number" class="form-control" wire:model="valor_fin" step="0.01">
                                    <div class="input-group-append">
                                        <span class="input-group-text" placeholder="Valor Máximo">Valor Final.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <H3>DÍAS DE CALENDARIO DE MORA </H3>
                        <div class="row mb-4">
                            <div class="col-6">
                                <label>DE 1 A 30 DIAS</label>
                                <div class="input-group mb-6">
                                    <input type="number" class="form-control" wire:model="primer_valor" step="0.01">
                                    <div class="input-group-append">
                                        <span class="input-group-text" placeholder="Valor Máximo">DE 1 A 30.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <label>DE 31 A 60 DIAS</label>
                                <div class="input-group mb-6">
                                    <input type="number" class="form-control" wire:model="segundo_valor" step="0.01">
                                    <div class="input-group-append">
                                        <span class="input-group-text" placeholder="Valor Máximo">DE 31 A 60.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-6">
                                <label>DE 61 A 90 DIAS</label>
                                <div class="input-group mb-6">
                                    <input type="number" class="form-control" wire:model="tercer_valor" step="0.01">
                                    <div class="input-group-append">
                                        <span class="input-group-text" placeholder="Valor Máximo">DE 61 A 90.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <label>DE 91 A 99999 DIAS</label>
                                <div class="input-group mb-6">
                                    <input type="number" class="form-control" wire:model="cuarto_valor" step="0.01">
                                    <div class="input-group-append">
                                        <span class="input-group-text" placeholder="Valor Máximo">DE 91 A 99999.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>