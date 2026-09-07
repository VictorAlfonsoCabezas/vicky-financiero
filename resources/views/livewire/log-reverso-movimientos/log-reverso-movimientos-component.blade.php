<div>
    <div class="row">
        <div class="col-md-12">
            <div class="row mt-2">
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
                <div class="col-4">
                    <label>Fecha Inicio</label>
                    <input type="date" class="form-control form-control-sm" placeholder="Fecha Inicio" wire:model="fechaInicio">
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-valign-middle table-sm">
                    <thead>
                        <tr>

                            <th style="width: 1%;">id</th>
                            <th>Cliente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalle as $key => $det)
                        <tr>

                            <td>
                                {{ $det->id }}
                            </td>
                            <td>
                                {{ $det->customer_name }}
                            </td>
                            <td>
                                <button wire:click="verLogs({{ $det->id }})" data-bs-toggle="modal" data-bs-target="#modalGeneral1" class="btn bg-blue btn-xs">
                                    Ver Log
                                </button>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $detalle->links() }}
            </div>
        </div>
    </div>
    {{-- MODAL LOG--}}
    <div wire:ignore.self class="modal fade" id="modalGeneral1" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Log</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <th>Fecha/Hora</th>
                                                <th>Detalle</th>
                                                <th>Usuario</th>
                                                <th>Observación</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($this->detalleLog as $det)
                                                <tr>
                                                <td>{{$det->date_create}} / {{$det->hour_create}}</td>
                                                <td>{{$det->detalle}} </td>
                                                <td>{{$det->user_name}} </td>
                                                <td>{{$det->observacion}} </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>

</script>