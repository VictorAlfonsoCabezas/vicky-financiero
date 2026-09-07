<div>
    <div class="col-3 mt-2 mb-2">
        <button wire:click="abrirModal(0);" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
            class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i><b> Sedes Centro de Costos</b></button>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><b>Sedes Centro de Costos</b></h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm">
                            <input type="text" wire:model="search" class="form-control float-end"
                                placeholder="Buscar">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-head-fixed text-nowrap table-hover table-striped"
                        style="font-size: 15px;">
                        <thead>
                            <tr class="text-center">
                                <th style="width: 1%;">id</th>
                                <th>Sede</th>
                                <th>Centro de Costos</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sedesCentroCostos as $sedesCentro)
                                <tr class="text-center">
                                    <td>{{ $sedesCentro->id }}</td>
                                    <td>{{ $sedesCentro->nombresede }}</td>
                                    <td>{{ $sedesCentro->nombrecentro }}</td>
                                    <td>
                                        <button wire:click="abrirModal({{ $sedesCentro->id }})" type="button"
                                            data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button"
                                            class="btn bg-light btn-xs"><i class="fa fa-pen"></i>
                                        </button>
                                        {{-- <button wire:click="borrarConceptos({{ $sedesCentro->id }})"
                                            class="btn bg-light btn-xs"><i class="fa fa-trash"></i>
                                        </button> --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $sedesCentroCostos->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title"> Sedes Centro de Costos </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeConceptos">
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="callout callout-warning">
                                <h5>Verifica estas observaciones.</h5>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </div>
                        @endif
                        <div class="row mb-2">
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label for="country_id">Sede</label>
                                <select title="Seleccionar" class="form-control" wire:model="sede_id">
                                    <option> - Seleccione uno - </option>
                                    @foreach ($sedes as $sede)
                                        <option value="{{ $sede->id }}">{{ $sede->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label for="country_id">Centro de Costos</label>
                                <select title="Seleccionar" class="form-control" wire:model="centro_costos_id">
                                    <option> - Seleccione uno - </option>
                                    @foreach ($centroCostos as $centro)
                                        <option value="{{ $centro->id }}">{{ $centro->name }}</option>
                                    @endforeach
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
