<div>
    <div class="card mt-3" style="position: relative; left: 0px; top: 0px;">
        <div class="card-header ui-sortable-handle" style="cursor: move;">
            <h3 class="card-title">
                <i class="fas fa-chart-pie me-1"></i>
                Acciones de los Socios
            </h3>
            <div class="card-tools">
                <ul class="nav nav-pills ms-auto">
                    <li class="nav-item">
                        <a wire:click="abrirModal(0)" class="btn btn-primary" style="color: white;" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"><i class="fa fa-plus"></i> Agregar</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content p-0">
                <div class="row">
                    @foreach ($header as $hed)
                    <div class="col-lg-3 col-6">
                        <div class="small-box {{$hed->class}}">
                            <div class="inner" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" wire:click="abrirModal({{$hed->id}})">
                                <h3>{{$hed->anio}}</h3>
                                <p>{{ App\Models\Meses::where('company_id', Auth::user()->company_id)->where('codigo', $hed->mes)->first()->mes}}</p>
                                <p style="font-size: 12px;"><i>{{ $hed->nombre }}</i> <b>$ {{ $hed->capital }}</b></p>
                            </div>
                            <div class="icon" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" wire:click="abrirModal({{$hed->id}})">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <a href="/acciones-detalle/{{$hed->id}}" class="small-box-footer">Detalles <i class="fas fa-arrow-circle-right"></i></a>
                            @if ($hed->estado == 'ACTIVO')
                            <a wire:click="eliminar({{$hed->id}})" class="small-box-footer">Borrar <i class="fas fa-trash"></i></a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Acciones</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeHeader">
                    <div class="modal-body">
                        @if ($errors->any())
                        <div class="callout callout-warning">
                            <h5>Verifica estas observaciones.</h5>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </div>
                        @endif
                        <div class="row mb-4">
                            <div class="col-12">
                                <label>Nombre</label>
                                <input type="text" class="form-control" placeholder="Nombre" wire:model="nombre">
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Descripcion</label>
                                    <textarea class="form-control" rows="3" placeholder="Ingrese una descripciòn" wire:model="descripcion"></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <label>Capital</label>
                                <input type="text" class="form-control" placeholder="Capital" wire:model="capital">
                            </div>
                            <div class="col-6 mt-3">
                                <select class="form-control mb-2" wire:model="anio">
                                    <option value=""> -- ELECCIONE -- </option>
                                    @foreach ($anios as $an)
                                    <option value="{{ $an }}">{{ $an }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mt-3">
                                <select class="form-control mb-2" wire:model="mes">
                                    <option value=""> -- ELECCIONE -- </option>
                                    @foreach ($meses as $mes)
                                    <option value="{{ $mes->codigo }}">{{ $mes->mes }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mt-3">
                                <select class="form-control mb-2" wire:model="certificado">
                                    <option value=""> -- ELECCIONE -- </option>
                                    <option value="1">CERTIFICADO</option>
                                    <option value="0">UTILIDAD</option>
                                   
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>