<div>
    <div class="row">
        <div class="col-md-12">
            <div class="row mt-2">
                <div class="col-3">
                    <label>Busqueda</label>
                    <div class="input-group input-group-sm">
                        <input type="text" wire:model="search" id="search" class="form-control"
                            placeholder="Buscar Socio">
                        <div class="input-group-append">
                            <div class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-3">
                    <label>Fecha</label>
                    <input type="date" class="form-control form-control-sm" placeholder="Fecha Fin"
                        wire:model="fechaFin">
                </div>
            </div>
        </div>
    </div>
    <div class="row col col-sm-12">
        <div class="card-body" style="background-color: #ededf3;">
            <div class="tab-content">
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th><b>Codigo</b></th>
                                <th><b>Comprobante</b></th>
                                <th><b>Nombre</b></th>
                                <th><b>Tipo Transacción</b></th>
                                <th><b>Valor</b></th>
                                <th><b>Observación</b></th>
                                <th><b>Usuario</b></th>
                                <th><b>Fecha de Creación</b></th>

                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                            @foreach($movimientos as $val)
                            <tr>
                                <td>{{$val->code}}</td>
                                <td>{{$val->comprobante}}</td>
                                <td>{{$val->customer_name}}</td>
                                <td>{{$val->type_transaction_name}}</td>
                                <td>{{$val->valor_movimiento}}</td>
                                <td>{{$val->observation}}</td>
                                <td>{{$val->usuarioCreo}}</td>
                                <td>
                                    <input type="date" wire:change="cambiarfecha($event.target.value, {{ $val->id }})" value="{{$val->date_created}}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $movimientos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>