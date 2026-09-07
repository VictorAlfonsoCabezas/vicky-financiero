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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($company as $com)
                                    <tr>
                                        <td>{{ $com->id }}</td>
                                        <td>
                                            @if ($com->photo !== '' && $com->photo !== null)
                                                <img alt="{{ $com->comercial_name }}"
                                                    src="uploads/companies/{{ $com->photo }}" id="logo"
                                                    title="{{ $com->comercial_name }}"
                                                    class="img-thumbnail img-responsive superbox-img photo"
                                                    style="width: 55px;" />
                                            @else
                                                <img src="{{ URL::asset('img/no-disponible.png') }}"
                                                    alt="{{ $com->comercial_name }}"
                                                    title="{{ $com->comercial_name }}" height="50"
                                                    width="50" />
                                            @endif
                                        </td>
                                        <td>{{ $com->comercial_name }}</td>
                                        <td>{{ $com->ruc }}</td>
                                        <td>{{ $com->address }}</td>
                                        <td>{{ $com->phone }}</td>
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
                                <div class="col-3 espacio-inferior">
                                    <label>Ruc</label>
                                    <input type="tex" class="form-control" placeholder="Ingrese ruc"wire:model="ruc"
                                        id="ruc">
                                </div>
                                <div class="col-3">
                                    <label>Representante Legal</label>
                                    <input type="tex" class="form-control" placeholder="Representante Legal"
                                        wire:model="legal_representative" id="legal_representative">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-2">
                                    <label>Ciudad de la Empresa</label>
                                    <input class="form-control" placeholder="Ciudad" wire:model="ciudad" id="ciudad">
                                </div>
                                <div class="col-3">
                                    <label>Pais Comercial</label>
                                    <input class="form-control" placeholder="Pais" wire:model="pais" name="pais"
                                        id="pais">
                                </div>
                                <div class="form-group">
                                    <label>Obligar selección Garante sin ser Socio</label>
                                    <select class="form-control" wire:model="obligar_garante" name="obligar_garante"
                                        id="obligar_garante">
                                        <option value="">- Seleccione -</option>
                                        <option value="0" selected="">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                </div>
                                <br>
                                <div class="col-3 margin-left: 400px; margin-top: 50px; ">
                                    <label>Porcentajea Retener en Credito</label>
                                    <input type="number" class="form-control text-uppercase"
                                        wire:model="porcentaje_retener_credito" name="porcentaje_retener_credito"
                                        id="porcentaje_retener_credito"placeholder="0.00">
                                </div>
                                <div class="col-12">
                                    <label>Direccion</label>
                                    <textarea class="form-control" rows="3" placeholder="Ingrese direccion" wire:model="address" id="address"></textarea>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-3">
                                    <label>Telefono</label>
                                    <input type="tex" class="form-control" placeholder="0999999999"
                                        wire:model="phone" id="phone">
                                </div>
                                <br>
                                <div class="col-3">
                                    <label>E-mail</label>
                                    <input type="tex" class="form-control" placeholder="Correo Electronico"
                                        wire:model="email" id="email">
                                </div>
                                <div class="col-3">
                                    <label>Tipo Empresa</label>
                                    <select class="form-control" wire:model="company_type" id="company_type"
                                        name="company_type">
                                        <option value="">- Seleccione -</option>
                                        <option value=1>CAJA DE AHORRO</option>
                                    </select>
                                </div>
                            </div>
                            <p style="font-size: 18px; font-weight: bold;">Porcentaje</p>
                            <div class="row mb-4">
                                <div>
                                    <label>Fondo Desgravament</label>
                                    <input type="number" class="form-control text-uppercase"
                                        wire:model="desgravament"
                                        name="desgravament"id="desgravament"placeholder="0.00">
                                </div>
                                <div class="col-4">
                                    <label>Interés por Mora</label>
                                    <input type="tex" class="form-control"
                                        placeholder="interes de mora "value="0.00" wire:model="mora"
                                        id="mora">
                                </div>
                            </div>
                            <div class="form-group col-md-8">
                                <label>Imagen</label>
                                <div class="custom-file">
                                    <input id="photo" type="file" class="custom-file-input"
                                        wire:model="photo" name="photo" accept="image/*">
                                    <label class="custom-file-label" for="photo">Elegir Archivo...</label>
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