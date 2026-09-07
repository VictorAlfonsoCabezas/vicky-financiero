<div>
    <div class="row justify-content-between">
        <div class="col-3 mt-2 mb-2">
            <button type="button" class="btn btn-primary btn-block" wire:click="abrirModal(0);" data-bs-toggle="modal" data-bs-target="#modalGeneral"><i class="fa fa-plus"></i> Agregar Proveedor</button>
        </div>
        <div class="col-3 mt-2 mb-2">
            <div class="input-group input-group-sm">
                <input type="text" wire:model="search" class="form-control float-end" placeholder="Buscar">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-default">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header" style="padding: 8px;">
                    <h5 class="card-title"><i class="fa fa-truck"></i></i> <b>Proveedores</b></h5>
                </div>
                <div class="card-body table-responsive p-2">
                    <div class="row">
                        @foreach ($proveedores as $prov)
                        <div class="col-md-4 col-sm-6 col-12" wire:click="abrirModal({{ $prov->id }})" data-bs-toggle="modal" data-bs-target="#modalGeneral">
                            <div class="info-box">
                                <span class="info-box-icon bg-{{ $prov->status ? 'primary' : 'danger' }}"><i class="fa fa-truck"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ $prov->nombre }}</span>
                                    <span class="info-box-number">{{ $prov->ruc }}</span>
                                    <small>{{ $prov->telefono }} / {{ $prov->email }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    {{ $proveedores->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title"><i class="fa fa-truck"></i> Proveedores </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white;">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="storeProveedores">
                    <div class="modal-body">
                        @if ($errors->any())
                        <div class="col-12">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text text-danger"><b>Revisa la siguiente información</b></span>
                                    @foreach ($errors->all() as $error)
                                    <small class="text-danger">{{ $error }}</small>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row mb-2">
                            <div class="col-6" style="margin-bottom: 10px;">
                                <label>Ruc</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-hashtag"></i></span>
                                    </div>
                                    <input type="number" class="form-control" placeholder="Ruc Proveedor" wire:model="ruc">
                                </div>
                            </div>
                            <div class="col-6" style="margin-bottom: 10px;">
                                <label>Nombre</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Nombre Proveedor" wire:model="nombre">
                                </div>
                            </div>
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label>Descripcion</label>
                                <textarea type="text" class="form-control" placeholder="Descripcion" wire:model="descripcion"></textarea>
                            </div>
                            <div class="col-12" style="margin-bottom: 10px;">
                                <label>Dirección</label>
                                <textarea type="text" class="form-control" placeholder="Direccion" wire:model="direccion"></textarea>
                            </div>
                            <div class="col-4" style="margin-bottom: 10px;">
                                <label>Teléfono</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="number" class="form-control" placeholder="0999999999" wire:model="telefono">
                                </div>
                            </div>
                            <div class="col-4" style="margin-bottom: 10px;">
                                <label>Correo Electrónico</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="mail" class="form-control" placeholder="ejemplo@mail.com" wire:model="email">
                                </div>
                            </div>
                            <div class="col-2" style="margin-bottom: 10px;">
                                <label>Estado</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="status">
                                    <label class="form-check-label">{{ $this->status ? 'Activo' : 'Inactivo' }}</label>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="row mt-2">
                            <div class="col-sm-6">

                                <div class="form-group">
                                    <label>Cuenta Gasto</label>
                                    <select class="form-control" wire:model="plan_cuenta_id">
                                        <option value="">Seleccione una opción</option>
                                        @foreach ($planGastos as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Centro Costo</label>
                                    <select class="form-control" wire:model="centro_costos_id">
                                        <option value="">Seleccione una opción</option>
                                        @foreach ($centroCostos as $centro)
                                        <option value="{{ $centro->id }}">{{ $centro->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Tipo de Factura</label>
                                    <select class="form-control" wire:model="tipo_factura">
                                        <option value="">Seleccione una opción</option>
                                        <option value="fisica">Factura Fisica</option>
                                        <option value="electronica">Factura Electrónica</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if ($tipo_factura == 'fisica')
                        <hr>
                        <div class="row mt-2">

                            <div class="col-sm-6">
                                <label># Factura</label>
                                <input type="text" wire:model="numero_autorizacion" placeholder="Ingresar los 10 dígitos" class="form-control">
                            </div>

                            <div class="col-sm-6">
                                <label>Fecha Caducidad</label>
                                <input type="date"
                                    wire:model="fecha_caducidad" class="form-control">
                            </div>

                        </div>
                        
                        @endif

                        <!-- @if ($tipo_factura)
                        <hr>
                        <div class="row mt-2">

                            <div class="col-sm-6">
                                <label># Factura</label>
                                <input type="text" wire:model="numero_autorizacion" placeholder="{{ $tipo_factura == 'fisica' ? 'Ingresar los 10 dígitos' : 'Ingresar los 49 dígitos' }}" class="form-control">
                            </div>

                            <div class="col-sm-6">
                                <label>Fecha Caducidad</label>
                                <input type="date"
                                    wire:model="fecha_caducidad" class="form-control">
                            </div>

                        </div>
                        
                        @endif-->



                        @if ($this->id_seleccionado !== 0)
                        <hr>
                        <h4>Contactos Proveedor</h4>
                        <div class="row">
                            <div class="col-4" style="margin-bottom: 10px;">
                                <input type="text" class="form-control form-control-border" wire:model="nombre_contacto" placeholder="Nombres">
                            </div>
                            <div class="col-4" style="margin-bottom: 10px;">
                                <input type="number" class="form-control form-control-border" wire:model="telefono_contacto" placeholder="Telefono">
                            </div>
                            <div class="col-3" style="margin-bottom: 10px;">
                                <input type="mail" class="form-control form-control-border" wire:model="email_contacto" placeholder="Email">
                            </div>
                            <div class="col-1 mb-2">
                                <a wire:click="agregarContacto();" class="btn btn-warning btn-sm text-white"><b>
                                        Agregar</b></a>
                            </div>
                        </div>

                        <div class="row mt-2">
                            @foreach ($contactos as $contac)
                            <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
                                <div class="card bg-light d-flex flex-fill">
                                    <div class="card-header text-muted border-bottom-0">
                                        Contactos
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="row">
                                            <div class="col-12">
                                                <h2 class="lead"><b>{{ $contac->nombre_contacto }}</b></h2>
                                                <ul class="ms-4 mb-0 fa-ul text-muted">
                                                    <li class="small">
                                                        <span class="fa-li">
                                                            <i class="fas fa-lg fa-phone"></i>
                                                        </span> Teléfono: {{ $contac->telefono }}
                                                    </li>
                                                    <li class="small">
                                                        <span class="fa-li">
                                                            <i class="fa fa-envelope"></i>
                                                        </span> Email: {{ $contac->email }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="text-end">
                                            <a wire:click="quitarContacto({{ $contac->id }})" class="btn btn-sm btn-primary text-white">
                                                <i class="fas fa-times"></i> Quitar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
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