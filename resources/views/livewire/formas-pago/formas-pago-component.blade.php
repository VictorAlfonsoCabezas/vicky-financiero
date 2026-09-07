<div>
    <div>
        <div>
            <div class="col-3 mt-2 mb-2">
                <button wire:click="abrirModal(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i><b> Nueva Formas
                        Pago</b></button>
            </div>
            <div class="col-3">
                <label class="small">Clasificación
                    <a href="/clasificacion-formas-pago" target="_blank">
                        <i class="fa fa-plus-circle"></i>
                    </a>
                </label>

            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><b>Formas de Pago</b></h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                </div>
                            </div>
                        </div>

                        <table class="table table-hover">
                            <thead>
                                <td>
                                    Nombre
                                </td>
                                <td>
                                    Código
                                </td>
                                <td>
                                    Descripción
                                </td>
                                <td>
                                    Estado
                                </td>
                                <td>
                                    Credito Descargo Bóveda
                                </td>
                                <td>
                                    Acciones
                                </td>
                            </thead>
                            <tbody>
                                @foreach($formasPago as $detail)
                                <tr>
                                    <td>{{$detail->nombre}}</td>
                                    <td>{{$detail->code}}</td>
                                    <td>{{$detail->descripcion}}</td>
                                    <td>
                                        @if ($detail->status)
                                        <small class="badge bg-primary" wire:click="cambioFormas({{ $detail->id }})"></i>Activo</small>
                                        @else
                                        <small class="badge bg-danger" wire:click="cambioFormas({{ $detail->id }})"></i>Desactivado</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($detail->credito_descargo_boveda)
                                        <small class="badge bg-primary" wire:click="cambioDescargo({{ $detail->id }})"></i>Bóveda</small>
                                        @else
                                        <small class="badge bg-danger" wire:click="cambioDescargo({{ $detail->id }})"></i>Cuenta</small>
                                        @endif
                                    </td>
                                    <td>
                                        <button wire:click="editarFormas({{ $detail->id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button" class="btn bg-light btn-xs"><i class="fa fa-pen"></i></button>
                                        <button wire:click="borrarFormas({{ $detail->id }})" class="btn bg-light btn-xs"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
                                <label>Nombre</label>
                                <input type="text" class="form-control" placeholder="Ingrese un nombre" wire:model="nombre" id="nombre">
                            </div>
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label>Codigo</label>
                                <input type="text" class="form-control" placeholder="Ingrese un código" wire:model="code" id="code">
                            </div>
                            <div class="col-12">
                                <label class="small">Clasificación </label>
                                <select id="clasificacion_forma_pagos_id" wire:model="clasificacion_forma_pagos_id" class="form-control form-control-sm">
                                    <option value=""> --SELECCIONE--</option>
                                    @foreach ($grupos as $opcion)
                                    <option value="{{ $opcion->id }}">{{ $opcion->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <div class="col-12">
                                    <label>Descripción</label>
                                    <textarea class="form-control" rows="3" placeholder="Ingrese una descripción, esto es opcional" wire:model="descripcion" id="descripcion"></textarea>
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
</div>