<div>
    <div>
        <div class="col-3 mt-2 mb-2">
            <button wire:click="abrirModal(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i> Nuevo Denominacion y Billetes
            </button>
        </div>
        <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
            <thead>
                <tr>
                    <th style="width: 1%;">#</th>
                    <th>Nombre</th>
                    <th>Valor</th>
                    <th>Modificar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($billetes as $bill)
                <tr>
                    <td>{{ $bill->id }}</td>
                    <td>{{ $bill->nombre }}</td>
                    <td>{{ $bill->valor }}</td>
                    <td>
                        <button wire:click="editarBilletes({{ $bill->id }})" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button" class="btn bg-light btn-xs"><i class="fa fa-pen"></i></button>
                        <button wire:click="borrarBilletes({{ $bill->id }})" class="btn bg-light btn-xs"><i class="fa fa-trash"></i></button>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $billetes->links() }}
    </div>
    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Denominacion y Billetes </h4>
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
                        <div class="row mb-4">
                            <div class="col-12">
                                <label>Nombre</label>
                                <input type="text" class="form-control" placeholder="Ingrese un nombre" wire:model="nombre" id="nombre">
                                <label>Valor</label>
                                <input type="number" step="any" class="form-control" placeholder="Ingrese un valor" wire:model="valor" id="valor">
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