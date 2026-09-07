<div>
    <div>
        <div class="col-3 mt-2 mb-2">
            <button wire:click="abrirModal(0);" type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral"
                class="btn btn-block btn-default btn-flat"><i class="fa fa-plus"></i><b> Nueva Empresa</b></button>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><b>Empresas</b></h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-head-fixed text-nowrap table-hover" style="font-size: 15px;">
                            <thead>
                                <tr>
                                    <th style="width: 1%;">id</th>
                                    <th>Logo</th>
                                    <th>Empresa</th>
                                    <th>Ruc</th>
                                    <th>Direccion</th>
                                    <th>Telefono</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($company as $com)
                                    <tr>
                                        <td>{{ $com->id }}</td>
                                        <td>

                                        </td>
                                        <td>{{ $com->comercial_name }}</td>
                                        <td>{{ $com->ruc }}</td>
                                        <td>{{ $com->address }}</td>
                                        <td>{{ $com->phone }}</td>
                                        <td>{{ $com->email }}</td>
                                        <td>
                                            <button wire:click="editarCompany({{ $com->id }})" type="button"
                                                data-bs-toggle="modal" data-bs-target="#modalGeneral" type="button"
                                                class="btn bg-light btn-xs"><i class="fa fa-pen"></i>
                                            </button>
                                            <button wire:click="borrarCompany({{ $com->id }})"
                                                class="btn bg-light btn-xs"><i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $company->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog" style="max-width: 80%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><b> Nueva Empresa<b></h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="storeCompany">
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
                                <div class="col-3">
                                    <label>Nombre de la empresa</label>
                                    <input type="text" class="form-control" placeholder="Ingrese de la empresa"
                                        wire:model="company_name" id="company_name">
                                </div>
                                <div class="col-3">
                                    <label>Nombre Comercial</label>
                                    <input type="text" class="form-control" placeholder="Escribe Comercial"
                                        wire:model="comercial_name" id="comercial_name">
                                </div>
                                <div class="col-3">
                                    <label>Ruc</label>
                                    <input type="tex" class="form-control" placeholder="Ingrese ruc"wire:model="ruc"
                                        id="ruc">
                                </div>
                                <div class="col-3">
                                    <label>Representante Legal</label>
                                    <input type="tex" class="form-control" placeholder="Representante Legal"
                                        wire:model="legal_representative" id="legal_representative">
                                </div>
                                <div class="col-12">
                                    <label>Direccion</label>
                                    <textarea class="form-control" rows="3" placeholder="Ingrese direccion" wire:model="address" id="address"></textarea>
                                </div>
                                <div class="col-3">
                                    <label>Telefono</label>
                                    <input type="tex" class="form-control" placeholder="0999999999"
                                        wire:model="phone" id="phone">
                                </div>
                                <div class="col-3">
                                    <label>E-mail</label>
                                    <input type="tex" class="form-control" placeholder="Correo Electronico"
                                        wire:model="email" id="email">
                                </div>
                                 <div class="col-3">
                                    <label>Tipo Empresa</label>
                                    <select class="form-control" wire:model="company_type" id="company_type" name="company_type" >
                                        <option value=1>CAJA DE AHORRO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary">Guardar </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
