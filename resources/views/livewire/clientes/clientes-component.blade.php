<div class="savings-module customers-module">
    <div class="savings-heading"><div><div class="text-muted small mb-1">SOCIOS / CLIENTES</div><h1>Ficha del cliente</h1><p>Administra la informaci&oacute;n personal, referencias y documentos de tus clientes.</p></div><div class="d-flex gap-2 flex-wrap"><button type="button" wire:click="seleccionarCliente(0)" class="btn btn-primary"><i class="fa fa-user-plus me-2"></i>Nuevo cliente</button>@if(Auth::user()->descargar_clientes)<button type="button" wire:click="descargarClientes" class="btn savings-secondary-action"><i class="fa fa-download me-2"></i>Exportar clientes</button>@endif</div></div>
@if ($id_seleccionado > 0)
    <div class="customer-quick-actions mb-3"><strong>{{ $apellidos }} {{ $nombres }}</strong><span class="text-muted">{{ $numero_documento }}</span><a class="btn btn-outline-primary" href="/cuentas/{{ $id_seleccionado }}"><i class="fa fa-wallet me-1"></i>Cuentas ({{ $totalCuentas }})</a><a class="btn btn-outline-primary" href="/creditos/{{ $id_seleccionado }}"><i class="fa fa-hand-holding-usd me-1"></i>Pr&eacute;stamos ({{ $totalPrestamos }})</a><a href="#customer-location" class="btn savings-secondary-action"><i class="fa fa-map-marker-alt me-1"></i>Ubicaci&oacute;n</a><button type="button" data-bs-toggle="modal" data-bs-target="#modalGeneral4" class="btn savings-secondary-action"><i class="fa fa-paperclip me-1"></i>Adjuntar archivos</button></div>
    @endif
    <div class="row g-3 align-items-start customer-columns">
        <aside class="col-12 col-lg-3 savings-sidebar"><div class="card"><div class="card-header"><h2 class="h5 mb-3">Lista de clientes</h2><input type="search" wire:model.debounce.350ms="search" class="form-control" placeholder="Nombre o documento" aria-label="Buscar cliente"></div><div class="card-body p-0"><div wire:loading.delay wire:target="search,seleccionarCliente" class="p-3 text-primary" role="status">Cargando datos...</div><ul class="nav nav-pills flex-column">
        @forelse ($clientes as $cli)
        <li class="nav-item" wire:key="profile-customer-{{ $cli->id }}"><button type="button" wire:click="seleccionarCliente({{ $cli->id }})" class="nav-link savings-customer {{ $id_seleccionado == $cli->id ? 'is-selected' : '' }}" aria-pressed="{{ $id_seleccionado == $cli->id ? 'true' : 'false' }}"><i class="fa fa-user me-1"></i><strong>{{ $cli->apellidos }} {{ $cli->nombres }}</strong>@if($cli->nuevo)<span class="badge bg-success ms-1">Nuevo</span>@endif<br><small>{{ $cli->numero_documento }}</small>@if($id_seleccionado == $cli->id)<span class="savings-customer-selected"><i class="fa fa-check-circle me-1"></i>Cliente seleccionado</span>@endif</button></li>
        @empty<li class="p-4 text-muted">No se encontraron clientes.</li>@endforelse
        </ul>{{ $clientes->links() }}</div></div></aside>
        <div class="col-12 col-lg-6 customer-editor">
            <div class="card">
                <div class="card-header p-2">
                    <h2 class="h5 mb-3">Datos del cliente</h2>
                    <ul class="nav nav-pills customer-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab == 'activity' ? 'active' : '' }}" href="#activity" wire:click.prevent="selectTab('activity')" role="tab" aria-selected="{{ $activeTab == 'activity' ? 'true' : 'false' }}">
                                @if ($this->id_seleccionado !== 0)
                                Datos Personales
                                @else
                                Nuevo Cliente
                                @endif
                            </a>
                        </li>
                        @if ($this->id_seleccionado !== 0)
                        <li class="nav-item"><a class="nav-link {{ $activeTab == 'timeline' ? 'active' : '' }}" href="#timeline" wire:click.prevent="selectTab('timeline')" role="tab" aria-selected="{{ $activeTab == 'timeline' ? 'true' : 'false' }}">Historial</a>
                        </li>
                        <li class="nav-item"><a class="nav-link {{ $activeTab == 'archivos' ? 'active' : '' }}" href="#archivos" wire:click.prevent="selectTab('archivos')" role="tab" aria-selected="{{ $activeTab == 'archivos' ? 'true' : 'false' }}">Archivos</a>
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" wire:key="customer-panel-{{ $id_seleccionado }}">

                        <div class="tab-pane {{ $activeTab == 'activity' ? 'active' : '' }}" id="activity" role="tabpanel">
                            @if ($errors->any())
                            <div class="callout callout-warning">
                                <h5>Verifica estas observaciones.</h5>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </div>
                            @endif
                            <div class="post">
                                @if ($this->id_seleccionado !== 0)
                                <div class="user-block">
                                    <img class="img-circle img-bordered-sm" src="/img/sinusuario.jpg" alt="user image">
                                    <span class="username">
                                        <a>{{ $this->nombres }} {{ $this->apellidos }}</a>
                                    </span>
                                    <span class="description">Fecha creación - {{ $this->created_at }}</span>
                                </div>
                                <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap mb-3">
                                    <a class="btn btn-app bg-warning" data-bs-toggle="modal" data-bs-target="#modalGeneral4"
                                        title="Archivos de esta cuenta">
                                        <i class="fa fa-paperclip"></i> Archivos
                                    </a>
                                    <button type="button" class="btn btn-primary" wire:click="store" wire:loading.attr="disabled" wire:target="store"><i class="fa fa-save me-2"></i>Guardar cambios</button>
                                </div>
                                @endif

                                <details class="panel customer-detail-panel" open wire:ignore.self wire:key="customer-section-personal-{{ $id_seleccionado }}"><summary class="panel-heading"><span>Datos personales</span><i class="fa fa-chevron-down" aria-hidden="true"></i></summary><div class="panel-body"><div style="display: flex; align-items: center; justify-content: space-between;">
                                    <h5>Información Básica <i class="fa fa-edit"></i></h5>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="fundador">
                                        <label class="form-check-label">
                                            <i class="fa fa-{{$this->fundador ? 'user-secret' : 'user'}}"></i>
                                            Socio
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <input type="date" wire:model="date_open_account"
                                            class="form-control form-control-sm"
                                            placeholder="0000-00-00" title="Selecciona la fecha que inicio a ser socio">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-3">
                                        <label class="small">Tipo Identificacion
                                            <a href="/tipo-documento" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="tipo_documento_id" class="form-control form-control-sm">
                                            @foreach ($tipoDocumento as $tipoDoc)
                                            <option value="{{ $tipoDoc['id'] }}">{{ $tipoDoc['nombre'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="small">Identificación</label>
                                        <div class="input-group input-group-sm">
                                            <input type="search" wire:model.defer="numero_documento"
                                                wire:blur="buscarCedula()" class="form-control" placeholder="Ingresa">
                                            @if ($this->id_seleccionado == 0)
                                            <span class="input-group-append">
                                                <button wire:click="buscarCedula()" type="button"
                                                    title="buscar Api Externa" class="btn btn-info btn-flat"><i
                                                        class="fa fa-search"></i></button>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="small">Sexo
                                            <a href="/genero" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="genero_id" class="form-control form-control-sm">
                                            @foreach ($genero as $gen)
                                            <option value="{{ $gen['id'] }}">{{ $gen['nombre'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="small">Estado Civil
                                            <a href="/estado-civil" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="estado_civil_id" class="form-control form-control-sm">
                                            @foreach ($estadoCivil as $estad)
                                            <option value="{{ $estad['id'] }}">{{ $estad['nombre'] }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row  mt-3">
                                    <div class="col-12 col-md-6">
                                        <label class="small">Nombres</label>
                                        <input type="text" wire:model="nombres" class="form-control form-control-sm"
                                            placeholder="Nombres">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="small">Apellidos</label>
                                        <input type="text" wire:model="apellidos" class="form-control form-control-sm"
                                            placeholder="Apellidos">
                                    </div>
                                </div>
                                <div class="row  mt-3">
                                    <div class="col-12 col-md-4">
                                        <label class="small">Nivel Académico
                                            <a href="/nivel-academico" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="nivel_academico_id"
                                            class="form-control form-control-sm selectorNivelAcademico">
                                            <option value="">Seleccione</option>
                                            @foreach ($niveles as $nivel)
                                            <option value="{{ $nivel['id'] }}">{{ $nivel['nombre'] }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Fecha Nacimiento</label>
                                        <input type="date" wire:model="fecha_nacimiento"
                                            wire:change="calcularEdadVista()" class="form-control form-control-sm"
                                            placeholder="0000-00-00">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Edad</label>
                                        <input type="number" wire:model="edad" class="form-control form-control-sm"
                                            placeholder="0" readonly>
                                    </div>
                                </div>
                                <hr>
                                <h5>Contacto <i class="fa fa-user" aria-hidden="true"></i></h5>
                                <div class="row">
                                    <div class="col-12 col-md-3">
                                        <label class="small">Codigo Pais
                                            <a href="/country" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="pais_id"
                                            class="form-control form-control-sm selectorCodigoPais">
                                            @foreach ($paises as $pai)
                                            <option value="{{ $pai['id'] }}">{{ $pai['nombre'] }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="small">Celular</label>
                                        <input type="text" wire:model="telefono" class="form-control form-control-sm"
                                            placeholder="0999999999">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="small">Telefono Fijo</label>
                                        <input type="text" wire:model="telefono_fijo"
                                            class="form-control form-control-sm" placeholder="023383387">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="small">Email</label>
                                        <input type="mail" wire:model="correo" class="form-control form-control-sm"
                                            placeholder="ejemplo@mail.com">
                                    </div>
                                </div></div></details>
<details class="panel customer-detail-panel" open wire:ignore.self wire:key="customer-section-address-{{ $id_seleccionado }}"><summary class="panel-heading"><span>Domicilio</span><i class="fa fa-chevron-down" aria-hidden="true"></i></summary><div class="panel-body"><h5>Domicilio <i class="fa fa-globe" aria-hidden="true"></i></h5>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <label class="small">Tipo Vivienda</label>
                                        <select wire:model="tipo_vivienda" class="form-control form-control-sm">
                                            <option value="">Seleccione</option>
                                            <option value="CASA PROPIA">CASA PROPIA</option>
                                            <option value="ARRENDADA">ARRENDADA</option>
                                            <option value="FAMILIAR">FAMILIAR</option>
                                            <option value="OTROS">OTROS</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="small">Tiempo Residencia</label>
                                        <input type="text" wire:model="tiempo_vivienda"
                                            class="form-control form-control-sm" placeholder="1 año">
                                    </div>
                                    <div class="col-12 mt-3">
                                        <label class="small">Direccción</label>
                                        <textarea class="form-control" wire:model="direccion" rows="3"
                                            placeholder="Escriba la dirección"></textarea>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Provincia
                                            <a href="/provincia" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="provincia_id" wire:change="cambiarProvincia()"
                                            class="form-control form-control-sm selectorProvincia">
                                            @foreach ($provincia as $prov)
                                            <option value="{{ $prov['id'] }}">{{ $prov['nombre'] }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Ciudad
                                            <a href="/ciudad" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="ciudad_id"
                                            class="form-control form-control-sm selectorCiudad">
                                            @foreach ($ciudad as $ciu)
                                            <option value="{{ $ciu['id'] }}">{{ $ciu['nombre'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Parroquia
                                            <a href="/parroquia" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="parroquia_id"
                                            class="form-control form-control-sm selectorParroquia">
                                            @foreach ($parroquia as $parr)
                                            <option value="{{ $parr['id'] }}">{{ $parr['nombre'] }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div></div></details>
<details class="panel customer-detail-panel" open wire:ignore.self wire:key="customer-section-spouse-{{ $id_seleccionado }}"><summary class="panel-heading"><span>Datos del cónyuge</span><i class="fa fa-chevron-down" aria-hidden="true"></i></summary><div class="panel-body"><div style="display: {{ $this->estado_civil_id == 2 ? 'block' : 'none' }};">
                                    <h5>Datos Cónyuge <i class="fa fa-female" aria-hidden="true"></i><i
                                            class="fa fa-male" aria-hidden="true"></i></h5>
                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <label class="small">Separación Bienes</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    wire:model="seperacion_bienes">
                                                <label class="form-check-label">Separación de Bienes</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label class="small">Cargas Familiares</label>
                                            <input type="number" wire:model="cargas_familiares"
                                                class="form-control form-control-sm" placeholder="0">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label class="small">Nivel Académico
                                                <a href="/nivel-academico" target="_blank">
                                                    <i class="fa fa-plus-circle"></i>
                                                </a>
                                            </label>
                                            <select wire:model="conyuge_nivel_academico_id"
                                                class="form-control form-control-sm selectorNivelConyuge">
                                                <option value="">Seleccione</option>
                                                @foreach ($niveles as $nivel)
                                                <option value="{{ $nivel['id'] }}">{{ $nivel['nombre'] }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-3 mt-2">
                                            <label class="small">Nombres Cónyuge</label>
                                            <input type="text" wire:model="conyugue_nombre"
                                                class="form-control form-control-sm" placeholder="Nombres Cónyugue">
                                        </div>
                                        <div class="col-3 mt-2">
                                            <label class="small">Identificacion Cónyuge</label>
                                            <input type="text" wire:model="conyugue_identificacion"
                                                class="form-control form-control-sm"
                                                placeholder="Identificacion Cónyugue">
                                        </div>
                                        <div class="col-3 mt-2">
                                            <label class="small">Teléfono Cónyuge</label>
                                            <input type="text" wire:model="conyugue_telefono"
                                                class="form-control form-control-sm" placeholder="Telefono Cónyugue">
                                        </div>
                                        <div class="col-3 mt-2">
                                            <label class="small">Fecha de Nacimiento</label>
                                            <input type="date" wire:model="conyuge_fecha_nacimiento"
                                                class="form-control form-control-sm" placeholder="0000-00-00">
                                        </div>

                                        <div class="col-4 mt-2">
                                            <label class="small">Ocupación</label>
                                            <input type="text" wire:model="conyuge_ocupacion"
                                                class="form-control form-control-sm" placeholder="Telefono Cónyugue">
                                        </div>
                                        <div class="col-4 mt-2">
                                            <label class="small">Teléfono Empresa</label>
                                            <input type="text" wire:model="conyuge_empresa_nombre"
                                                class="form-control form-control-sm" placeholder="Ocupación">
                                        </div>
                                        <div class="col-4 mt-2">
                                            <label class="small">Empresa</label>
                                            <input type="text" wire:model="conyuge_empresa_direccion"
                                                class="form-control form-control-sm" placeholder="Empresa">
                                        </div>

                                        <div class="col-7 mt-2">
                                            <label class="small">Dirección Empresa</label>
                                            <input type="text" wire:model="conyuge_empresa_telefono"
                                                class="form-control form-control-sm" placeholder="Dirección Empresa">
                                        </div>
                                        <div class="col-4 mt-2">
                                            <label class="small">Cargo</label>
                                            <input type="text" wire:model="conyuge_cargo_empresa"
                                                class="form-control form-control-sm" placeholder="Cargo">
                                        </div>
                                        <div class="col-1 mt-2">
                                            <label class="small">Tiempo</label>
                                            <input type="number" wire:model="conyuge_tiempo_empresa"
                                                class="form-control form-control-sm" placeholder="Tiempo Empresa">
                                        </div>

                                    </div>
                                </div></div></details>
<details class="panel customer-detail-panel" open wire:ignore.self wire:key="customer-section-work-{{ $id_seleccionado }}"><summary class="panel-heading"><span>Actividad laboral</span><i class="fa fa-chevron-down" aria-hidden="true"></i></summary><div class="panel-body"><div class="d-flex justify-content-between align-items-center">
                                    <h5>Datos Ocupacionales <i class="fa fa-building" aria-hidden="true"></i></h5>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default">{{ $this->ocupacion }}</button>
                                        <button type="button" class="btn btn-default dropdown-toggle dropdown-icon"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu" role="menu">
                                            <a class="dropdown-item" wire:click="ocupacion('EMPRESA')">Empresa
                                                @if ($this->ocupacion == 'EMPRESA')
                                                <i class="fa fa-check"></i>
                                                @endif
                                            </a>
                                            <a class="dropdown-item" wire:click="ocupacion('NEGOCIO')">Negocio Propio
                                                @if ($this->ocupacion == 'NEGOCIO')
                                                <i class="fa fa-check"></i>
                                                @endif
                                            </a>
                                            <a class="dropdown-item" wire:click="ocupacion('NINGUNO')">Ninguno
                                                @if ($this->ocupacion == 'NINGUNO')
                                                <i class="fa fa-check"></i>
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Trabaja empresa -->
                                <div class="row"
                                    style="display: {{ $this->ocupacion == 'EMPRESA' ? 'flex' : 'none' }};">
                                    <div class="col-12 col-md-4">
                                        <label class="small">Nombre Empresa</label>
                                        <input type="text" wire:model="empresa_nombre"
                                            class="form-control form-control-sm" placeholder="Nombre de la Empresa">
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <label class="small">Dirección Empresa</label>
                                        <input type="text" wire:model="empresa_direccion"
                                            class="form-control form-control-sm" placeholder="Dirección de la Empresa">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="small">Provincia
                                            <a href="/provincia" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="empresa_provincia_id"
                                            class="form-control form-control-sm selectorEmpresaProvincia">
                                            <option value="">Seleccione</option>
                                            @foreach ($provincia as $prov)
                                            <option value="{{ $prov['id'] }}">{{ $prov['nombre'] }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Ciudad
                                            <a href="/ciudad" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="empresa_canton_id"
                                            class="form-control form-control-sm selectorEmpresaCiudad">
                                            <option value="">Seleccione</option>
                                            @foreach ($ciudad as $ciu)
                                            <option value="{{ $ciu['id'] }}">{{ $ciu['nombre'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Parroquia
                                            <a href="/parroquia" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="empresa_parroquia_id"
                                            class="form-control form-control-sm selectorEmpresaParroquia">
                                            <option value="">Seleccione</option>
                                            @foreach ($parroquia as $parr)
                                            <option value="{{ $parr['id'] }}">{{ $parr['nombre'] }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="small">Teléfono Empresa</label>
                                        <input type="text" wire:model="empresa_telefono"
                                            class="form-control form-control-sm" placeholder="0999999999">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Tiempo Empresa</label>
                                        <input type="number" wire:model="empresa_tiempo"
                                            class="form-control form-control-sm" placeholder="1 año">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Cargo Empresa</label>
                                        <input type="text" wire:model="empresa_cargo"
                                            class="form-control form-control-sm" placeholder="Servicio al cliente">
                                    </div>
                                </div>

                                <!-- Negocio Propio -->
                                <div class="row"
                                    style="display: {{ $this->ocupacion == 'NEGOCIO' ? 'flex' : 'none' }};">
                                    <div class="col-12 col-md-4">
                                        <label class="small">Nombre Negocio</label>
                                        <input type="text" wire:model="negocio_nombre"
                                            class="form-control form-control-sm" placeholder="Nombre del Negocio">
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <label class="small">Dirección Negocio</label>
                                        <input type="text" wire:model="negocio_direccion"
                                            class="form-control form-control-sm" placeholder="Dirección del Negocio">
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="small">Provincia
                                            <a href="/provincia" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="negocio_provincia_id"
                                            class="form-control form-control-sm selectorNegocioProvincia">
                                            <option value="">Seleccione</option>
                                            @foreach ($provincia as $prov)
                                            <option value="{{ $prov['id'] }}">{{ $prov['nombre'] }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Ciudad
                                            <a href="/ciudad" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="negocio_canton_id"
                                            class="form-control form-control-sm selectorNegocioCiudad">
                                            <option value="">Seleccione</option>
                                            @foreach ($ciudad as $ciu)
                                            <option value="{{ $ciu['id'] }}">{{ $ciu['nombre'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Parroquia
                                            <a href="/parroquia" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="negocio_parroquia_id"
                                            class="form-control form-control-sm selectorNegocioParroquia">
                                            <option value="">Seleccione</option>
                                            @foreach ($parroquia as $parr)
                                            <option value="{{ $parr['id'] }}">{{ $parr['nombre'] }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="col-12 col-md-4">
                                        <label class="small">Teléfono Negocio</label>
                                        <input type="text" wire:model="negocio_telefono"
                                            class="form-control form-control-sm" placeholder="0999999999">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Tiempo Negocio</label>
                                        <input type="number" wire:model="negocio_tiempo"
                                            class="form-control form-control-sm" placeholder="1 año">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Actividad Negocio</label>
                                        <input type="text" wire:model="negocio_actividad"
                                            class="form-control form-control-sm" placeholder="Farmacia">
                                    </div>
                                </div></div></details>
<details class="panel customer-detail-panel" open wire:ignore.self wire:key="customer-section-bank-{{ $id_seleccionado }}"><summary class="panel-heading"><span>Datos bancarios</span><i class="fa fa-chevron-down" aria-hidden="true"></i></summary><div class="panel-body"><h5>Datos Bancarios <i class="fa fa-money-bill-alt" aria-hidden="true"></i></h5>
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <label class="small">Banco
                                            <a href="/bancos" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="banco_id" class="form-control form-control-sm">
                                            <option value="">Seleccione</option>
                                            @foreach ($banco as $ban)
                                            <option value="{{ $ban['id'] }}">{{ $ban['nombre'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Tipo de Cuenta</label>
                                        <a href="/tipo-cuenta" target="_blank">
                                            <i class="fa fa-plus-circle"></i>
                                        </a>
                                        </label>
                                        <select wire:model="tipo_cuenta_id" class="form-control form-control-sm">
                                            <option value="">Seleccione</option>
                                            @foreach ($tipo_cuenta as $tip)
                                            <option value="{{ $tip['id'] }}">{{ $tip['nombre'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">No. Cuenta</label>
                                        <input type="text" wire:model="no_cuenta" class="form-control form-control-sm"
                                            placeholder="2200000000">
                                    </div>
                                </div></div></details>
<details class="panel customer-detail-panel" open wire:ignore.self wire:key="customer-section-references-{{ $id_seleccionado }}"><summary class="panel-heading"><span>Referencias</span><i class="fa fa-chevron-down" aria-hidden="true"></i></summary><div class="panel-body"><h5>Referencias <i class="fa fa-users" aria-hidden="true"></i></h5>
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <label class="small">Parentesco
                                            <a href="/parentezco" target="_blank">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </label>
                                        <select wire:model="parentezco_id" class="form-control form-control-sm">
                                            @foreach ($parentezco as $paren)
                                            <option value="{{ $paren['id'] }}">{{ $paren['nombre'] }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Nombres</label>
                                        <input type="text" wire:model="name_parentesco"
                                            class="form-control form-control-sm" placeholder="Nombres y Apellidos">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="small">Teléfono</label>
                                        <input type="text" wire:model="telefono_parentesco"
                                            class="form-control form-control-sm" placeholder="0999999999">
                                    </div>
                                    <div class="col-1 mt-4">
                                        <a wire:click="agregarReferencia();" class="btn btn-primary btn-sm"
                                            style="color: white; position: relative;top: 6px;"><i
                                                class="fa fa-plus"></i></a>
                                    </div>
                                </div>
                                @if (count($valores))
                                @foreach ($valores as $key => $result)
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <label class="small">Parentezco
                                        </label>

                                        <select wire:model.defer="valores.{{ $key }}.parentezco_id"
                                            class="form-control form-control-sm" disabled>
                                            @foreach ($parentezco as $paren)
                                            <option value="{{ $result['parentezco_id'] }}">{{ $result['parentezco_nombre'] }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="small">Nombres</label>
                                        <input type="text" wire:model.defer="valores.{{ $key }}.nombres_apellidos"
                                            class="form-control form-control-sm" placeholder="Nombres y Apellidos">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="small">Teléfono</label>
                                        <input type="text" wire:model.defer="valores.{{ $key }}.celular"
                                            class="form-control form-control-sm" placeholder="0999999999">
                                    </div>
                                    <div class="col-1 mt-4">
                                        <a wire:click="quitarReferencia({{ $result['id'] }});"
                                            class="btn btn-danger btn-sm"
                                            style="color: white; position: relative;top: 6px;"><i
                                                class="fa fa-minus"></i></a>
                                    </div>
                                </div>
                                @endforeach
                                @endif


</div></details>
                            </div>
                            <div class="mt-3">
                                <button type="button" wire:click="store"
                                    class="btn btn-primary" wire:loading.attr="disabled" wire:target="store">Guardar</button>
                            </div>
                        </div>

                        <div class="tab-pane {{ $activeTab == 'timeline' ? 'active' : '' }}" id="timeline" role="tabpanel">
                            <div class="timeline timeline-inverse">

                                <div class="time-label">
                                    <span class="bg-danger">
                                        Información
                                    </span>
                                </div>
                                <div>
                                    <i class="fas fa-clock bg-success"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i>
                                            {{ $this->updated_at }}</span>
                                        <h3 class="timeline-header border-0"><a>{{ $this->nombres }}
                                                {{ $this->apellidos }}</a>
                                            Se edita la información por ultima vez
                                        </h3>
                                    </div>
                                </div>
                                <div>
                                    <i class="fas fa-clock bg-primary"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i>
                                            {{ $this->created_at }}</span>
                                        <h3 class="timeline-header border-0"><a>{{ $this->nombres }}
                                                {{ $this->apellidos }}</a>
                                            Se Crea el cliente
                                        </h3>
                                    </div>
                                </div>
                                <div>
                                    <i class="far fa-clock bg-gray"></i>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane {{ $activeTab == 'archivos' ? 'active' : '' }}" id="archivos" role="tabpanel">
                            <div class="col-12 col-md-12 col-lg-12 order-1 order-md-2">
                                @if ($this->id_seleccionado > 0)
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h3 class="text-primary">
                                            <i class="far fa-fw fa-file-pdf"></i> CREDITOS
                                        </h3>
                                        <br>
                                        <ul class="list-unstyled">
                                            @foreach ($filesCreditos as $fil)
                                            @if ($fil->formato == 'doc' || $fil->formato == 'docx')
                                            <li>
                                                <a href="{{ $fil->path }}" class="btn-link text-secondary"
                                                    target="_blank"><i class="far fa-fw fa-file-word"></i>
                                                    {{ $fil->descripcion }}</a>
                                                <p class="small">{{ $fil->archivo }}</p>
                                                <p class="small"><b>Crédito: </b> {{ $fil->code }}</p>
                                            </li>
                                            @elseif ($fil->formato == 'pdf')
                                            <li>
                                                <a href="{{ $fil->path }}" class="btn-link text-secondary"
                                                    target="_blank"><i class="far fa-fw fa-file-pdf"></i>
                                                    {{ $fil->descripcion }}</a>
                                                <p class="small">{{ $fil->archivo }}</p>
                                                <p class="small"><b>Crédito: </b> {{ $fil->code }}</p>
                                            </li>
                                            @elseif ($fil->formato == 'jpg' || $fil->formato == 'jpeg' || $fil->formato == 'png' || $fil->formato == 'gif')
                                            <li>
                                                <a href="{{ $fil->path }}" class="btn-link text-secondary"
                                                    target="_blank"><i class="far fa-fw fa-image "></i>
                                                    {{ $fil->descripcion }}</a>
                                                <p class="small">{{ $fil->archivo }}</p>
                                                <p class="small"><b>Crédito: </b> {{ $fil->code }}</p>
                                            </li>
                                            @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <h3 class="text-primary">
                                            <i class="far fa-fw fa-file-pdf"></i> CUENTAS
                                        </h3>
                                        <br>
                                        <ul class="list-unstyled">
                                            @foreach ($filesCuentas as $fil)
                                            @if ($fil->formato == 'doc' || $fil->formato == 'docx')
                                            <li>
                                                <a href="{{ $fil->path }}" class="btn-link text-secondary"
                                                    target="_blank"><i class="far fa-fw fa-file-word"></i>
                                                    {{ $fil->descripcion }}</a>
                                                <p class="small">{{ $fil->archivo }}</p>
                                                <p class="small"><b>Cuenta: </b>
                                                    {{ $fil->customer_tipo_ahorros_id }}
                                                </p>
                                            </li>
                                            @elseif ($fil->formato == 'pdf')
                                            <li>
                                                <a href="{{ $fil->path }}" class="btn-link text-secondary"
                                                    target="_blank"><i class="far fa-fw fa-file-pdf"></i>
                                                    {{ $fil->descripcion }}</a>
                                                <p class="small">{{ $fil->archivo }}</p>
                                                <p class="small"><b>Cuenta: </b>
                                                    {{ $fil->customer_tipo_ahorros_id }}
                                                </p>
                                            </li>
                                            @elseif ($fil->formato == 'jpg' || $fil->formato == 'jpeg' || $fil->formato == 'png' || $fil->formato == 'gif')
                                            <li>
                                                <a href="{{ $fil->path }}" class="btn-link text-secondary"
                                                    target="_blank"><i class="far fa-fw fa-image "></i>
                                                    {{ $fil->descripcion }}</a>
                                                <p class="small">{{ $fil->archivo }}</p>
                                                <p class="small"><b>Cuenta: </b>
                                                    {{ $fil->customer_tipo_ahorros_id }}
                                                </p>
                                            </li>
                                            @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>


                                <hr>
                                <h3 class="text-primary"><i class="far fa-fw fa-file-pdf"></i> ARCHIVOS GENERALES
                                </h3>
                                <br>

                                {{-- <img
                                        src="{{ asset('/storage/clientes/gPWruogaRVsfPnpoRhCkMFbvqrpFgOM7UrdyByWZ.png') }}"
                                alt=""> --}}
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Archivos Generales</h3>
                                    </div>

                                    <div class="card-body p-0">
                                        <table class="table table-sm">
                                            <tbody>
                                                @foreach ($filesGeneral as $fil)
                                                @if ($fil->formato == 'doc' || $fil->formato == 'docx')
                                                <tr>
                                                    <td>
                                                        <a href="{{ asset($fil->path) }}"
                                                            class="btn-link text-secondary" target="_blank"><i
                                                                class="far fa-fw fa-file-word"></i>
                                                            {{ $fil->descripcion }} </a>
                                                    </td>
                                                    <td>
                                                        <a href="{{ asset($fil->path) }}" target="_blank">
                                                            <i class="fa fa-download" aria-hidden="true"></i>
                                                        </a>
                                                    </td>
                                                    <td class="text-center"><a class="btn btn-danger btn-xs text-white"
                                                            wire:click="eliminarFile({{ $fil->id }})"><i
                                                                class="fa fa-times" aria-hidden="true"></i> Eliminar</a>
                                                    </td>
                                                </tr>
                                                @elseif ($fil->formato == 'pdf')
                                                <tr>
                                                    <td>
                                                        <a href="{{ asset($fil->path) }}"
                                                            class="btn-link text-secondary" target="_blank"><i
                                                                class="fa fa-file-pdf" aria-hidden="true"></i>
                                                            {{ $fil->descripcion }} </a>
                                                    </td>
                                                    <td>
                                                        <a href="{{ asset($fil->path) }}" target="_blank">
                                                            <i class="fa fa-download" aria-hidden="true"></i>
                                                        </a>
                                                    </td>
                                                    <td class="text-center"><a class="btn btn-danger btn-xs text-white"
                                                            wire:click="eliminarFile({{ $fil->id }})"><i
                                                                class="fa fa-times text-white" aria-hidden="true"></i>
                                                            Eliminar</a></td>
                                                </tr>
                                                @elseif ($fil->formato == 'jpg' || $fil->formato == 'jpeg' || $fil->formato == 'png' || $fil->formato == 'gif')
                                                <tr>
                                                    <td>
                                                        <a href="{{ asset($fil->path) }}""
                                                                                                                                                                                                                        class="
                                                            btn-link text-secondary" target="_blank"><i
                                                                class="far fa-fw fa-image "></i>
                                                            {{ $fil->descripcion }} </a>
                                                    </td>
                                                    <td>
                                                        <a href="{{ asset($fil->path) }}""
                                                                                                                                                                                                                        target="
                                                            _blank">
                                                            <i class="fa fa-download" aria-hidden="true"></i>
                                                        </a>
                                                    </td>
                                                    <td class="text-center"><a class="btn btn-danger btn-xs text-white"
                                                            wire:click="eliminarFile({{ $fil->id }})"><i
                                                                class="fa fa-times text-white" aria-hidden="true"></i>
                                                            Eliminar</a></td>
                                                </tr>
                                                @endif
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                @endif

                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div id="customer-location" class="col-12 col-lg-3 customer-location">
            <div class="card card-primary card-outline">
                <div class="card-header"><h2 class="h5 mb-0">Ubicaci&oacute;n del cliente</h2></div>
                <div class="card-body box-profile">
                    @if ($this->id_seleccionado !== 0)
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle" src="/img/sinusuario.jpg"
                            alt="User profile picture">
                    </div>
                    <h3 class="profile-username text-center">{{ $this->nombres }} {{ $this->apellidos }}</h3>
                    <p class="text-muted text-center">{{ Auth::user()->company->comercial_name }}</p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <a href="/cuentas/{{ $this->id_seleccionado }}" target="_blank">
                            <li class="list-group-item">
                                <b>Cuentas</b> <span class="badge bg-primary float-end">{{ $this->totalCuentas }}</span>
                            </li>
                        </a>
                        <a href="/creditos/{{ $this->id_seleccionado }}" target="_blank">
                            <li class="list-group-item">
                                <b>Prestamos</b> <span class="badge bg-primary float-end">{{ $this->totalPrestamos }}</span>
                            </li>
                        </a>
                    </ul>
                    @endif
                    @if ($this->id_seleccionado == 0)
                    <div id="mapaContenedor" style="display: none;">
                        <div id="mapid" style="height: 400px; width: 100%;"></div>
                    </div>
                    @else
                    <label class="small">Latitud, Longitud</label>
                    <div class="input-group input-group-sm" style="margin-bottom: 15px;">
                        <input type="text" wire:model="latitudlongitud" class="form-control"
                            placeholder="-0.00000, 0.00000">
                        <span class="input-group-append">
                            <button wire:click="storeLatitudlongitud()" type="button" title="Guardar Nueva referencias"
                                class="btn btn-primary btn-flat"><i class="fas fa-save"></i></button>
                        </span>
                    </div>
                    <div id="mapaContenedor" wire:ignore>
                        <div id="mapid" style="height: 400px; width: 100%;"></div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ARCHIVOS --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral4" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Archivos</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form>
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
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Nombre Archivo</label>
                                                <input type="text" class="form-control" wire:model="descrpcion"
                                                    placeholder="Nombre del Archivo">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputFile">Archivo</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input"
                                                            wire:model="archivo" id="exampleInputFile">
                                                        <label class="custom-file-label" for="exampleInputFile">Elegir
                                                            Archivo</label>
                                                    </div>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"
                                                            wire:click="subirArchivo()">Subir</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card-body p-0">
                                    <table class="table table-sm">
                                        <tbody>
                                            @foreach ($filesGeneral as $fil)
                                            @if ($fil->formato == 'doc' || $fil->formato == 'docx')
                                            <tr>
                                                <td>
                                                    <a href="{{ $fil->path }}" class="btn-link text-secondary"
                                                        target="_blank"><i class="far fa-fw fa-file-word"></i>
                                                        {{ $fil->descripcion }} </a>
                                                </td>
                                                <td>
                                                    <a href="{{ $fil->path }}" target="_blank">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                                <td class="text-center"><a class="btn btn-danger btn-xs text-white"
                                                        wire:click="eliminarFile({{ $fil->id }})"><i class="fa fa-times"
                                                            aria-hidden="true"></i>
                                                        Eliminar</a></td>
                                            </tr>
                                            @elseif ($fil->formato == 'pdf')
                                            <tr>
                                                <td>
                                                    <a href="{{ $fil->path }}" class="btn-link text-secondary"
                                                        target="_blank"><i class="fa fa-file-pdf"
                                                            aria-hidden="true"></i>
                                                        {{ $fil->descripcion }} </a>
                                                </td>
                                                <td>
                                                    <a href="{{ $fil->path }}" target="_blank">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                                <td class="text-center"><a class="btn btn-danger btn-xs text-white"
                                                        wire:click="eliminarFile({{ $fil->id }})"><i
                                                            class="fa fa-times text-white" aria-hidden="true"></i>
                                                        Eliminar</a></td>
                                            </tr>
                                            @elseif ($fil->formato == 'jpg' || $fil->formato == 'jpeg' || $fil->formato == 'png' || $fil->formato == 'gif')
                                            <tr>
                                                <td>
                                                    <a href="{{ $fil->path }}" class="btn-link text-secondary"
                                                        target="_blank"><i class="far fa-fw fa-image "></i>
                                                        {{ $fil->descripcion }} </a>
                                                </td>
                                                <td>
                                                    <a href="{{ $fil->path }}" target="_blank">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                                <td class="text-center"><a class="btn btn-danger btn-xs text-white"
                                                        wire:click="eliminarFile({{ $fil->id }})"><i
                                                            class="fa fa-times text-white" aria-hidden="true"></i>
                                                        Eliminar</a></td>
                                            </tr>
                                            @endif
                                            @endforeach
                                        </tbody>
                                    </table>
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
    document.addEventListener('livewire:load', function() {
        let mymap;

        window.addEventListener('locationUpdated', event => {
            if (mymap) {
                mymap.remove();
            }

            mymap = L.map('mapid').setView([@this.latitud, @this.longitud], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(mymap);

            L.marker([@this.latitud, @this.longitud]).addTo(mymap);
        });

        initializeSelect2('.selectorNivelAcademico', 'selectorNivel');
        initializeSelect2('.selectorCodigoPais', 'selectorPais');
        initializeSelect2('.selectorProvincia', 'selectorProvincia');
        initializeSelect2('.selectorCiudad', 'selectorCiudad');
        initializeSelect2('.selectorParroquia', 'selectorParroquia');
        initializeSelect2('.selectorNivelConyuge', 'selectorNivelConyuge');
        initializeSelect2('.selectorEmpresaProvincia', 'selectorEProvincia');
        initializeSelect2('.selectorEmpresaCiudad', 'selectorECiudad');
        initializeSelect2('.selectorEmpresaParroquia', 'selectorEParroquia');
        initializeSelect2('.selectorNegocioProvincia', 'selectorNProvincia');
        initializeSelect2('.selectorNegocioCiudad', 'selectorNCiudad');
        initializeSelect2('.selectorNegocioParroquia', 'selectorNParroquia');

        Livewire.hook('message.processed', (message, component) => {
            initializeSelect2('.selectorNivelAcademico', 'selectorNivel');
            initializeSelect2('.selectorCodigoPais', 'selectorPais');
            initializeSelect2('.selectorProvincia', 'selectorProvincia');
            initializeSelect2('.selectorCiudad', 'selectorCiudad');
            initializeSelect2('.selectorParroquia', 'selectorParroquia');
            initializeSelect2('.selectorNivelConyuge', 'selectorNivelConyuge');
            initializeSelect2('.selectorEmpresaProvincia', 'selectorEProvincia');
            initializeSelect2('.selectorEmpresaCiudad', 'selectorECiudad');
            initializeSelect2('.selectorEmpresaParroquia', 'selectorEParroquia');
            initializeSelect2('.selectorNegocioProvincia', 'selectorNProvincia');
            initializeSelect2('.selectorNegocioCiudad', 'selectorNCiudad');
            initializeSelect2('.selectorNegocioParroquia', 'selectorNParroquia');
        });
    });

    function initializeSelect2(selector, eventName) {
        const selectElement = document.querySelector(selector);
        if (selectElement && !selectElement.classList.contains('select2-hidden-accessible')) {
            $(selectElement).select2({
                theme: "bootstrap4",
                dropdownDirection: 'bottom'
            }).on('change', function(e) {
                Livewire.emit(eventName, e.target.value);
            });
        }
    }
</script>