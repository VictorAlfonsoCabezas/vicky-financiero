<div>
    <div>
        <div>
            <div class="col-3 mt-2 mb-2">
                <button wire:click="abrirModal(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i><b> Agregar Recaudación</b></button>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><b>Otros Ingresos</b></h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                                <thead>
                                    <tr>
                                        <th style="width: 1%;">ID</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Valor</th>
                                        <th>Fecha/Hora</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($valores as $value)
                                    <tr>
                                        <td>{{$value->id}}</td>
                                        <td>{{$value->name}}</td>
                                        <td>{{$value->descripcion}}</td>
                                        <td>{{$value->valor}}</td>
                                        <td>{{$value->date_create}}<br>{{$value->hour_create}}</td>
                                        <td>
                                        <button wire:click="borrarValor({{ $value->id }})" class="btn bg-light btn-xs"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        {{-- MODAL --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Nueva Formas de Pago </h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="storeFormas">
                        <div class="modal-body">
                            @if ($errors->any())
                            <div class="callout callout-warning">
                                <h5>Verifica estas observaciones.</h5>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </div>
                            @endif
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label>Razón</label>
                                <input type="text" class="form-control" placeholder="Ingrese una Razon" wire:model="name" id="name">
                            </div>
                            <div class="col-12 mt-3">
                                <label>Valor</label>
                                <input step="0.01" type="number" class="form-control" placeholder="Ingrese el valor" wire:model="valor" id="valor">
                            </div>
                            <div class="col-12 mt-3">
                                <label>Descripción</label>
                                <textarea class="form-control" wire:model="descripcion" rows="3" placeholder="Descripción del ingreso"></textarea>
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
</div>