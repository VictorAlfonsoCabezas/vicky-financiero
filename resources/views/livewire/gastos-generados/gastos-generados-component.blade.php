<div>
    <div>
        <div>
            <div class="col-3 mt-2 mb-2">
                <button wire:click="abrirModal(0)" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
                    class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i><b> Nuevo Gastos
                        Generales</b></button>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><b>Gastos Generales</b></h3>
                            <div class="card-tools">
                                <div class="input-group input-group-lg" style="width:400 px;">
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0" style="height: 480px;">
                            <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                                <thead>
                                    <tr class="text-center">
                                        <th>PRIN</th>
                                        <th>CODIGO</th>
                                        <th>NOMBRE</th>
                                        <th>TIPO TRANSACCION</th>
                                        <th>OBSERVACION</th>
                                        <th>VALOR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($gastos as $gas)
                                        <td>
                                            <a class="btn btn-info btn-sm"
                                                href="{{ URL::to('gastos/tcket/' . $gas->id) }}"
                                                title="Tabla de Amortización">
                                                <i class="far fa-file-pdf"></i>
                                            </a>
                                        </td>
                                        <td>{{ $gas->code }}</td>
                                        <td>{{ $gas->customer_name }}</td>
                                        <td>{{ $gas->type_transaction_name }}</td>
                                        <td>{{ $gas->observation }}</td>
                                        <td>{{ $gas->saldo_general }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $gastos->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- MODAL --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><b>Nuevo Gastos Generados</b> </h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="storeGastos">
                        <div class="modal-body">
                            @if ($errors->any())
                                <div class="callout callout-warning">
                                    <h5>Verifica estas observaciones.</h5>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </div>
                            @endif
                            <div class="row mb-6">
                                <div class="col-12"style="margin-bottom: 10px;">
                                    <label>Valor</label>
                                    <input type="number" step="any" class="form-control"
                                        placeholder="Ingrese un valor" wire:model="saldo_general" id="saldo_general">
                                </div>
                            </div>
                            <div class="row mb-6">
                                <div class="col-12">
                                    <label>Observation</label>
                                    <textarea class="form-control" rows="3" placeholder="Ingrese una descripción" wire:model="observation"
                                        id="observation"></textarea>
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
