<div class="savings-module users-module user-editor">
    <div class="savings-heading"><div><div class="text-muted small mb-1">ACCESO / USUARIOS</div><h1>{{ $id_selected > 0 ? 'Editar usuario' : 'Nuevo usuario' }}</h1><p>Configura el perfil, la empresa y los permisos del usuario.</p></div><a href="/user-new" class="btn savings-secondary-action"><i class="fa fa-arrow-left me-2"></i>Volver a usuarios</a></div>
    <div class="row g-4">
        <div class="col-12 col-xl-3">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle" src="/img/sinusuario.jpg"
                            alt="User profile picture">
                    </div>
                    <h3 class="profile-username text-center">{{ $firstname }} {{ $lastname }}</h3>
                    <p class="text-muted text-center">{{ $rol_name ?: 'Sin rol asignado' }}</p><div class="text-center mb-3"><span class="badge {{ $status ? 'bg-success' : 'bg-secondary' }}">{{ $status ? 'Usuario activo' : 'Usuario inactivo' }}</span><div class="small text-muted mt-2">{{ $username }}</div></div>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Inicios de Sesión</b> <a class="float-end">{{ $totalLogs }}</a>
                        </li>
                    </ul>
                    <a class="btn btn-primary btn-block text-white" data-bs-toggle="modal"
                        data-bs-target="#modalGeneral"><i class="fa fa-history me-2"></i>Historial de accesos</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-9">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills user-tabs">
                        <li class="nav-item"><a role="button" tabindex="0" class="nav-link {{$tab == 'settings' ? 'active' : ''}}"
                                wire:click="cambioTab('settings')" wire:keydown.enter="cambioTab('settings')">Configuración</a></li>
                        @if ($id_selected > 0)
                            <li class="nav-item"><a role="button" tabindex="0" class="nav-link {{$tab == 'permisos' ? 'active' : ''}}"
                                    wire:click="cambioTab('permisos')" wire:keydown.enter="cambioTab('permisos')">Permisos</a></li>
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
                            <section class="panel panel-inverse user-settings-panel"><div class="panel-heading"><h2 class="panel-title">Identidad y contacto</h2></div><div class="panel-body"><p class="text-muted small mb-4">Datos personales para identificar al usuario.</p><div class="form-group row">
                                <label for="user-ruc" class="col-sm-2 col-form-label">Identificación</label>
                                <div class="col-sm-10">
                                    <input id="user-ruc" type="number" class="form-control" wire:model="ruc" placeholder="1700000001">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="user-firstname" class="col-sm-2 col-form-label">Nombres</label>
                                <div class="col-sm-10">
                                    <input id="user-firstname" type="text" class="form-control" wire:model="firstname"
                                        placeholder="Nombres">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="user-lastname" class="col-sm-2 col-form-label">Apellidos</label>
                                <div class="col-sm-10">
                                    <input id="user-lastname" type="text" class="form-control" wire:model="lastname"
                                        wire:blur="updateLastname" placeholder="Apellidos">
                                </div>
                            </div>
                            </div></section>
<section class="panel panel-inverse user-settings-panel"><div class="panel-heading"><h2 class="panel-title">Credenciales de acceso</h2></div><div class="panel-body"><p class="text-muted small mb-4">Usuario y clave para ingresar al sistema.</p><div class="form-group row">
                                <label for="user-username" class="col-sm-2 col-form-label">Usuario</label>
                                <div class="col-sm-10">
                                    <input id="user-username" type="text" class="form-control" wire:model="username" placeholder="Usuario">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="user-email" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input id="user-email" type="email" class="form-control" wire:model="email"
                                        placeholder="mail@mail.com">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="user-token" class="col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-10">
                                    <input id="user-token" type="password" autocomplete="new-password" class="form-control" wire:model="token" placeholder="**********">
                                </div>
                            </div>
                            </div></section>
<section class="panel panel-inverse user-settings-panel"><div class="panel-heading"><h2 class="panel-title">Empresa, rol y estado</h2></div><div class="panel-body"><p class="text-muted small mb-4">Asigna la empresa, sede y rol del usuario.</p><div class="form-group row">
                                <label for="user-rol_id" class="col-sm-2 col-form-label">Rol</label>
                                <div class="col-sm-10">
                                    <select id="user-rol_id" name="rol_id" class="form-control" wire:model="rol_id">
                                        <option value="">Seleccione un Rol</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="user-company_id" class="col-sm-2 col-form-label">Empresa</label>
                                <div class="col-sm-10">
                                    <select id="user-company_id" name="company_id" class="form-control" wire:model="company_id">
                                        <option value="">Seleccione una Empresa</option>
                                        @foreach($empresas as $empresa)
                                            <option value="{{ $empresa->id }}">{{ $empresa->comercial_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="user-sede_id" class="col-sm-2 col-form-label">Sede</label>
                                <div class="col-sm-10">
                                    <select id="user-sede_id" name="company_id" class="form-control" wire:model="sede_id">
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
                            </div></section>
<div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <button type="button" class="btn btn-primary" wire:click="createUpdateUser" wire:loading.attr="disabled" wire:target="createUpdateUser"><i class="fa fa-save me-2"></i>Guardar usuario</button>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{$tab == 'permisos' ? 'active' : ''}}"><div class="user-permissions-heading"><h2 class="h5">Permisos del usuario</h2><p class="text-muted mb-0">Los cambios en estos permisos se guardan al activar o desactivar cada opci&oacute;n.</p></div>
                            <div class="row">

                                <!-- Permiso Admin -->
                                <div class="col-12 col-lg-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-{{$admin ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$admin ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Permisos Créditos</span>
                                                <input type="checkbox" role="switch" aria-label="Cambiar permiso admin" wire:model="admin" wire:click="cambiaAdmin()">
                                            </div>
                                            <span class="mailbox-read-time float-end" style="font-size: 12px;">Esta
                                                opción es para permisos especiales en créditos, como aprobación de
                                                créditos, novaciones de créditos.</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permiso Caja Valor -->
                                <div class="col-12 col-lg-6">
                                    <div class="info-box">
                                        <span
                                            class="info-box-icon bg-{{$permiso_caja_valor ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$permiso_caja_valor ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Registrar Valores Cajas</span>
                                                <input type="checkbox" role="switch" aria-label="Cambiar permiso permiso_caja_valor" wire:model="permiso_caja_valor" wire:click="cambiaPermisoCajaValor()">
                                            </div>
                                            <span class="mailbox-read-time float-end" style="font-size: 12px;">Esta es
                                                para que el usuario pueda ingresar valores de cajas..</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permiso Credito Aprobar -->
                                <div class="col-12 col-lg-6">
                                    <div class="info-box">
                                        <span
                                            class="info-box-icon bg-{{$permiso_credito_aprobar ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$permiso_credito_aprobar ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Aprobar Créditos</span>
                                                <input type="checkbox" role="switch" aria-label="Cambiar permiso permiso_credito_aprobar" wire:model="permiso_credito_aprobar" wire:click="cambiaPermisoCreditoAprobar()">
                                            </div>
                                            <span class="mailbox-read-time float-end" style="font-size: 12px;">Activa
                                                este permiso para poder aprobar créditos.</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permiso Reversar Cajas -->
                                <div class="col-12 col-lg-6">
                                    <div class="info-box">
                                        <span
                                            class="info-box-icon bg-{{$reversar_cajas ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$reversar_cajas ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Reversar cajas</span>
                                                <input type="checkbox" role="switch" aria-label="Cambiar permiso reversar_cajas" wire:model="reversar_cajas" wire:click="cambiaReversarCajas()">
                                            </div>
                                            <span class="mailbox-read-time float-end" style="font-size: 12px;">Activa
                                                esta opción para reversar el cierre de caja..</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permiso Reversar Movimientos -->
                                <div class="col-12 col-lg-6">
                                    <div class="info-box">
                                        <span
                                            class="info-box-icon bg-{{$reversar_movimientos ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$reversar_movimientos ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Reversar Movimientos</span>
                                                <input type="checkbox" role="switch" aria-label="Cambiar permiso reversar_movimientos" wire:model="reversar_movimientos" wire:click="cambiaReversarMovimientos()">
                                            </div>
                                            <span class="mailbox-read-time float-end" style="font-size: 12px;">Activa
                                                esta opción para que el usuario pueda reversar movimientos como
                                                depositos, retiros, entrega de dinero de préstamos.</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permiso Cierre Caja -->
                                <div class="col-12 col-lg-6">
                                    <div class="info-box">
                                        <span
                                            class="info-box-icon bg-{{$cierre_caja ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$cierre_caja ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Recibir Cierres de caja</span>
                                                <input type="checkbox" role="switch" aria-label="Cambiar permiso cierre_caja" wire:model="cierre_caja" wire:click="cambiaCierreCaja()">
                                            </div>
                                            <span class="mailbox-read-time float-end" style="font-size: 12px;">Activa
                                                esta opción para recibir los cierres de caja..</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permiso Reverar Créditos -->
                                <div class="col-12 col-lg-6">
                                    <div class="info-box">
                                        <span
                                            class="info-box-icon bg-{{$permiso_reversar_creditos ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$permiso_reversar_creditos ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Reversar Créditos</span>
                                                <input type="checkbox" role="switch" aria-label="Cambiar permiso permiso_reversar_creditos" wire:model="permiso_reversar_creditos" wire:click="cambiaReversoCredito()">
                                            </div>
                                            <span class="mailbox-read-time float-end" style="font-size: 12px;">Activa
                                                esta opción para reversar créditos una vez aprobados pero sin entregar
                                                el dinero</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permiso Modificar números de Cuenta -->
                                <div class="col-12 col-lg-6">
                                    <div class="info-box">
                                        <span
                                            class="info-box-icon bg-{{$permiso_numero_cuentas ? 'primary' : 'danger'}} elevation-1"><i
                                                class="fas fa-thumbs-{{$permiso_numero_cuentas ? 'up' : 'down'}}"></i></span>
                                        <div class="info-box-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="info-box-text">Modificar # Cuentas</span>
                                                <input type="checkbox" role="switch" aria-label="Cambiar permiso permiso_numero_cuentas" wire:model="permiso_numero_cuentas" wire:click="cambiaNumeroCuenta()">
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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