<div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Usuario</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/user-new"><i class="fas fa-home"></i> Regresar</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <div class="row">
        <div class="col-md-3">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle" src="/img/sinusuario.jpg"
                            alt="User profile picture">
                    </div>
                    <h3 class="profile-username text-center">{{ $firstname }} {{ $lastname }}</h3>
                    <p class="text-muted text-center">{{ $rol_name }}</p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Inicios de Sesión</b> <a class="float-end">{{ $totalLogs }}</a>
                        </li>
                    </ul>
                    <a class="btn btn-primary btn-block text-white" data-bs-toggle="modal"
                        data-bs-target="#modalGeneral"><b>Logs</b></a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link {{$tab == 'settings' ? 'active' : ''}}"
                                wire:click="cambioTab('settings')">Configuración</a></li>
                        @if ($id_selected > 0)
                            <li class="nav-item"><a class="nav-link {{$tab == 'permisos' ? 'active' : ''}}"
                                    wire:click="cambioTab('permisos')">Permisos</a></li>
                        @endif
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        @if ($errors->any())
                            <div class="callout callout-warning">
                                <h5>Verifica estas observaciones.</h5>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </div>
                        @endif
                        <div class="tab-pane {{$tab == 'settings' ? 'active' : ''}}">
                            <div class="form-group row">
                                <label for="inputName" class="col-sm-2 col-form-label">Identificación</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" wire:model="ruc" placeholder="1700000001">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputName" class="col-sm-2 col-form-label">Nombres</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" wire:model="firstname"
                                        placeholder="Nombres">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail" class="col-sm-2 col-form-label">Apellidos</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" wire:model="lastname"
                                        wire:blur="updateLastname" placeholder="Apellidos">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputExperience" class="col-sm-2 col-form-label">Usuario</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" wire:model="username" placeholder="Usuario">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputName2" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" wire:model="email"
                                        placeholder="mail@mail.com">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputSkills" class="col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" wire:model="token" placeholder="**********">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputSkills" class="col-sm-2 col-form-label">Rol</label>
                                <div class="col-sm-10">
                                    <select name="rol_id" class="form-control" wire:model="rol_id">
                                        <option value="">Seleccione un Rol</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputSkills" class="col-sm-2 col-form-label">Empresa</label>
                                <div class="col-sm-10">
                                    <select name="company_id" class="form-control" wire:model="company_id">
                                        <option value="">Seleccione una Empresa</option>
                                        @foreach($empresas as $empresa)
                                            <option value="{{ $empresa->id }}">{{ $empresa->comercial_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputSkills" class="col-sm-2 col-form-label">Sede</label>
                                <div class="col-sm-10">
                                    <select name="company_id" class="form-control" wire:model="sede_id">
                                        <option value="">Seleccione una Sede</option>
                                        @foreach($sedes as $sed)
                                            <option value="{{ $sed->id }}">{{ $sed->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" wire:model="status"> Estado Usuario
                                        </label>
                                    </div>
                                </div>

                                <!-- TODO programar ley te proeccion modal y registros -->
                                <!-- <div class="offset-sm-2 col-sm-10">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" wire:model="status"> Yo acepto la <a href="#">ley de proteccion de datos</a>
                                        </label>
                                    </div>
                                </div> -->
                            </div>
                            <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <a class="btn btn-primary text-white" wire:click="createUpdateUser"><i
                                            class="fas fa-save"></i> Guardar</a>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{$tab == 'permisos' ? 'active' : ''}}">
                            <div class="row">

                                <!-- Permiso Admin -->
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-{{$admin ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$admin ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Permisos Créditos</span>
                                                <input type="checkbox" wire:model="admin" wire:click="cambiaAdmin()">
                                            </div>
                                            <span class="mailbox-read-time float-end" style="font-size: 12px;">Esta
                                                opción es para permisos especiales en créditos, como aprobación de
                                                créditos, novaciones de créditos.</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permiso Caja Valor -->
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-box">
                                        <span
                                            class="info-box-icon bg-{{$permiso_caja_valor ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$permiso_caja_valor ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Registrar Valores Cajas</span>
                                                <input type="checkbox" wire:model="permiso_caja_valor"
                                                    wire:click="cambiaPermisoCajaValor()">
                                            </div>
                                            <span class="mailbox-read-time float-end" style="font-size: 12px;">Esta es
                                                para que el usuario pueda ingresar valores de cajas..</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permiso Credito Aprobar -->
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-box">
                                        <span
                                            class="info-box-icon bg-{{$permiso_credito_aprobar ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$permiso_credito_aprobar ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Aprobar Créditos</span>
                                                <input type="checkbox" wire:model="permiso_credito_aprobar"
                                                    wire:click="cambiaPermisoCreditoAprobar()">
                                            </div>
                                            <span class="mailbox-read-time float-end" style="font-size: 12px;">Activa
                                                este permiso para poder aprobar créditos.</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permiso Reversar Cajas -->
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-box">
                                        <span
                                            class="info-box-icon bg-{{$reversar_cajas ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$reversar_cajas ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Reversar cajas</span>
                                                <input type="checkbox" wire:model="reversar_cajas"
                                                    wire:click="cambiaReversarCajas()">
                                            </div>
                                            <span class="mailbox-read-time float-end" style="font-size: 12px;">Activa
                                                esta opción para reversar el cierre de caja..</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permiso Reversar Movimientos -->
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-box">
                                        <span
                                            class="info-box-icon bg-{{$reversar_movimientos ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$reversar_movimientos ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Reversar Movimientos</span>
                                                <input type="checkbox" wire:model="reversar_movimientos"
                                                    wire:click="cambiaReversarMovimientos()">
                                            </div>
                                            <span class="mailbox-read-time float-end" style="font-size: 12px;">Activa
                                                esta opción para que el usuario pueda reversar movimientos como
                                                depositos, retiros, entrega de dinero de préstamos.</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permiso Cierre Caja -->
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-box">
                                        <span
                                            class="info-box-icon bg-{{$cierre_caja ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$cierre_caja ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Recibir Cierres de caja</span>
                                                <input type="checkbox" wire:model="cierre_caja"
                                                    wire:click="cambiaCierreCaja()">
                                            </div>
                                            <span class="mailbox-read-time float-end" style="font-size: 12px;">Activa
                                                esta opción para recibir los cierres de caja..</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permiso Reverar Créditos -->
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-box">
                                        <span
                                            class="info-box-icon bg-{{$permiso_reversar_creditos ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$permiso_reversar_creditos ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Reversar Créditos</span>
                                                <input type="checkbox" wire:model="permiso_reversar_creditos"
                                                    wire:click="cambiaReversoCredito()">
                                            </div>
                                            <span class="mailbox-read-time float-end" style="font-size: 12px;">Activa
                                                esta opción para reversar créditos una vez aprobados pero sin entregar
                                                el dinero</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permiso Modificar números de Cuenta -->
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="info-box">
                                        <span
                                            class="info-box-icon bg-{{$permiso_numero_cuentas ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$permiso_numero_cuentas ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Modificar # Cuentas</span>
                                                <input type="checkbox" wire:model="permiso_numero_cuentas"
                                                    wire:click="cambiaNumeroCuenta()">
                                            </div>
                                            <span class="mailbox-read-time float-end" style="font-size: 12px;">Activa
                                                esta opción para poder modificar números de Cuentas.</span>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>

    {{-- MODAL LOGS--}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> Logs Inicio de Sesión</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Información</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logSession as $log)
                                <tr>
                                    <td>
                                        <div class="user-block">
                                            <img class="img-circle" src="/img/sinusuario.jpg" alt="User Image">
                                            <span class="username"><a href="#">{{ $log->user->username }}</a></span>
                                            <span class="description">{{ $log->description }} -
                                                {{ $log->fecha_creacion }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $log->ip_address }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>