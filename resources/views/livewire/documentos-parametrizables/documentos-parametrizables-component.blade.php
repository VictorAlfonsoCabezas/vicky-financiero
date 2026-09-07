<div>
    <div>
        <div class="card-body p-0" style="height: 100vh; overflow: auto;">
            <div>
                <div class="col-3 mt-2 mb-2">
                    <a href="{{ URL::to('/formatoCreado/' . 0 . '/' . 0) }}" class="btn btn-block btn-default btn-flat">
                        <i class="fa fa-plus"></i><b> Nuevo Formato</b>
                    </a>
                </div>
                <div class="col-3 mt-2 mb-2">

                    <a class="btn btn-primary btn-sm" title="Entregar Valores"
                        data-bs-toggle="modal"
                        data-bs-target="#modalGeneral">
                        <i class="fa fa-plus"></i><b> Nuevo Formato</b>
                    </a>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><b>Formato</b></h3>
                                <div class="card-tools">
                                    <div class="input-group input-group-sm" style="width: 250px;">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive p-0" style="height: 300px;">
                                <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Formato</th>
                                            <th>Estado</th>
                                            <th>Accion</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($formatos as $formato)
                                        <tr class="text-center">
                                            <td>{{ $formato->formato }}</td>
                                            <td>{{ $formato->status }}</td>
                                            <td><a class="btn btn-outline-primary btn-sm" href="{{ route('formatoCreado.ver', [$formato->id, 0]) }}">Editar</a></td>

                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL Seccion Formato --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Formato</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                @if ($errors->any())
                <div class="callout callout-warning">
                    <h5>Verifica estas observaciones.</h5>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </div>
                @endif
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="card card-success card-outline direct-chat direct-chat-success shadow-sm">


                            <div class="card-body" style="background-color: #ededf3;">
                                <div class="tab-content">
                                    <div class="row col col-sm-12">

                                        <section class="col col-sm-12">
                                            <div class="col-12">
                                                <label class="small">Variables </label>
                                                <select id="tipo_formato" wire:model="tipo_formato" class="form-control text-uppercase">
                                                    <option value=""> --SELECCIONE--</option>
                                                    <option value="1"> PAGARE </option>

                                                </select>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                    <a class="btn btn-danger" title="Pagar Letra" wire:click="generarDocumento()">
                    <i class="fas fa-print" style="color: white;"></i>Guardar</i>
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>
