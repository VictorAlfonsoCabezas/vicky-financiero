<div class="plan-cuentas-page">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="text-muted small mb-1">CONTABILIDAD / CONFIGURACIÓN</div>
            <h1 class="page-header mb-1">Plan de cuentas</h1>
            <p class="text-muted mb-0">Consulta tu catálogo y configura el uso de cada cuenta.</p>
        </div>
        <button type="button" class="btn btn-primary" wire:click="reiniciarNuevo"><i class="fa fa-plus me-2" aria-hidden="true"></i>Nueva cuenta</button>
    </div>
    <div class="row g-3 mb-4">
        @foreach ([['Total de cuentas', $resumen->total, 'fa-book', 'primary'], ['Para préstamos', $resumen->prestamos, 'fa-hand-holding-usd', 'info'], ['Para gastos', $resumen->gastos, 'fa-receipt', 'warning'], ['Para utilidades', $resumen->utilidades, 'fa-chart-line', 'success']] as $indicador)
        <div class="col-6 col-xl-3">
            <div class="card border-0 h-100 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 py-3"><span class="pc-stat-icon bg-{{ $indicador[3] }} bg-opacity-10 text-{{ $indicador[3] }}"><i class="fa {{ $indicador[2] }}" aria-hidden="true"></i></span>
                    <div>
                        <div class="text-muted small">{{ $indicador[0] }}</div>
                        <div class="fs-3 fw-bold">{{ number_format($indicador[1] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="row g-4 align-items-start">
        <div class="col-12 col-xl-7">
            <div class="panel panel-inverse mb-0">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-list me-2" aria-hidden="true"></i>Catálogo de cuentas</h2><span class="badge bg-white text-dark">{{ $cuentas->total() }} resultados</span>
                </div>
                <div class="panel-body p-0">
                    <div class="p-3 border-bottom">
                        <div class="row g-2">
                            <div class="col-12"><label class="form-label small fw-bold" for="pc-buscar">Buscar cuenta</label>
                                <div class="input-group"><span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span><input id="pc-buscar" type="search" class="form-control" wire:model.debounce.350ms="search" placeholder="Código o nombre de la cuenta" autocomplete="off"></div>
                            </div>
                            <div class="col-6 col-md-4"><label for="pc-uso" class="form-label small">Uso contable</label><select id="pc-uso" class="form-select" wire:model="filtroUso">
                                    <option value="">Todos los usos</option>
                                    <option value="prestamo">Préstamos</option>
                                    <option value="gasto">Gastos</option>
                                    <option value="utilidad">Utilidades</option>
                                    <option value="estado_resultados">Estado de resultados</option>
                                </select></div>
                            <div class="col-6 col-md-4"><label for="pc-origen" class="form-label small">Origen</label><select id="pc-origen" class="form-select" wire:model="filtroOrigen">
                                    <option value="">Todos los orígenes</option>
                                    <option value="sistema">Sistema</option>
                                    <option value="personalizada">Personalizadas</option>
                                </select></div>
                            <div class="col-6 col-md-2"><label for="pc-pagina" class="form-label small">Por página</label><select id="pc-pagina" class="form-select" wire:model="porPagina">
                                    <option>25</option>
                                    <option>50</option>
                                    <option>100</option>
                                </select></div>
                            <div class="col-6 col-md-2 d-flex align-items-end"><button type="button" class="btn btn-default w-100" wire:click="limpiarFiltros" title="Limpiar filtros"><i class="fa fa-undo" aria-hidden="true"></i><span class="visually-hidden">Limpiar filtros</span></button></div>
                        </div>
                    </div>
                    <div class="px-3 py-2 small text-muted border-bottom" aria-live="polite">Selecciona una cuenta para editarla. <span wire:loading wire:target="search,filtroUso,filtroOrigen,porPagina"><i class="fa fa-spinner fa-spin ms-2" aria-hidden="true"></i> Actualizando…</span></div>
                    <div class="table-responsive pc-account-list">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-3">Código / cuenta</th>
                                    <th scope="col">Uso contable</th>
                                    <th scope="col" class="text-end pe-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cuentas as $cuen)
                                <tr wire:key="cuenta-{{ $cuen->id }}" class="{{ (int) $id_seleccionado === (int) $cuen->id ? 'table-primary' : '' }}">
                                    <td class="ps-3"><button type="button" class="btn btn-link text-start p-0 text-decoration-none pc-account-button" wire:click="verPlan({{ $cuen->id }})" aria-pressed="{{ (int) $id_seleccionado === (int) $cuen->id ? 'true' : 'false' }}"><span class="d-block fw-bold font-monospace">{{ $cuen->codigo }}</span><span class="d-block text-body small mt-1">{{ $cuen->nombre }}</span></button>
                                        <div class="mt-1 small text-muted">{{ $cuen->creado_usuario ? 'Personalizada' : 'Sistema' }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">@if ($cuen->prestamo)<span class="badge bg-info text-dark">Préstamo</span>@endif @if ($cuen->gasto)<span class="badge bg-warning text-dark">Gasto</span>@endif @if ($cuen->utilidad)<span class="badge bg-success">Utilidad</span>@endif @if ($cuen->estado_resultados)<span class="badge bg-secondary">Resultados</span>@endif @if (!$cuen->prestamo && !$cuen->gasto && !$cuen->utilidad && !$cuen->estado_resultados)<span class="text-muted small">General</span>@endif</div>
                                    </td>
                                    <td class="text-end pe-3 text-nowrap">
                                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="verPlan({{ $cuen->id }})" aria-label="Editar cuenta {{ $cuen->codigo }}" title="Editar cuenta">
                                            <i class="fa fa-pen" aria-hidden="true"></i>
                                        </button>
                                        @if ($cuen->creado_usuario)
                                        <button type="button" class="btn btn-sm btn-outline-danger ms-1" wire:click="confirmarEliminar({{ $cuen->id }})" aria-label="Eliminar cuenta {{ $cuen->codigo }}" title="Eliminar cuenta">
                                            <i class="fa fa-trash" aria-hidden="true"></i></button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5"><i class="fa fa-search fs-2 text-muted mb-3 d-block" aria-hidden="true"></i><strong>No se encontraron cuentas</strong>
                                        <p class="text-muted mt-1 mb-3">Prueba otro código, nombre o combinación de filtros.</p><button type="button" class="btn btn-sm btn-outline-primary" wire:click="limpiarFiltros">Limpiar filtros</button>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top">
                        <div class="small text-muted mb-2">Mostrando {{ $cuentas->firstItem() ?? 0 }}–{{ $cuentas->lastItem() ?? 0 }} de {{ $cuentas->total() }} cuentas</div>{{ $cuentas->links() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-5">
            <div class="panel panel-inverse pc-editor" wire:key="editor-cuenta-{{ $id_seleccionado }}">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa {{ $id_seleccionado ? 'fa-pen' : 'fa-plus-circle' }} me-2" aria-hidden="true"></i>{{ $id_seleccionado ? 'Editar cuenta' : 'Nueva cuenta' }}</h2><span class="badge bg-primary">{{ $id_seleccionado ? 'Edición' : 'Registro' }}</span>
                </div>
                <form wire:submit.prevent="{{ $id_seleccionado ? 'actualizarPlan' : 'nuevoPlan' }}">
                    <div class="panel-body">
                        @if ($cuenta)<div class="bg-light rounded p-3 mb-3">
                            <div class="fw-bold font-monospace text-primary">{{ $cuenta->codigo }}</div>
                            <div class="fw-bold mt-1">{{ $cuenta->nombre }}</div>
                            <div class="text-muted small mt-1">Saldo registrado: {{ number_format((float) $cuenta->saldo, 2) }}</div>
                        </div>@else<p class="text-muted small">Define el código, el nombre y los usos de la nueva cuenta.</p>@endif
                        @if ($errors->any())<div class="alert alert-danger" role="alert"><strong>Revisa los campos indicados.</strong>
                            <ul class="mb-0 mt-1 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>@endif
                        <h3 class="fs-6 fw-bold border-bottom pb-2 mb-3">Información de la cuenta</h3>
                        <div class="row g-3 mb-4">
                            <div class="col-12"><label for="pc-nombre" class="form-label">Nombre <span class="text-danger">*</span></label><input id="pc-nombre" class="form-control @error('nombre') is-invalid @enderror" wire:model.defer="nombre" maxlength="60" placeholder="Ej. CAJA GENERAL" required>@error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-12"><label for="pc-codigo" class="form-label">Código contable <span class="text-danger">*</span></label><input id="pc-codigo" class="form-control font-monospace @error('codigo') is-invalid @enderror" wire:model.defer="codigo" placeholder="Ej. 1.01.02" required aria-describedby="pc-codigo-ayuda">@error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror<div id="pc-codigo-ayuda" class="form-text">Hasta 7 niveles de uno o dos dígitos, separados por puntos.</div>
                            </div>
                        </div>
                        <h3 class="fs-6 fw-bold border-bottom pb-2 mb-2">Uso contable</h3>
                        <p class="text-muted small">Una cuenta puede tener más de un uso.</p>
                        <div class="row g-2 mb-4">
                            @foreach ([['prestamo', 'Préstamos', 'Operaciones de crédito.'], ['gasto', 'Gastos', 'Registro de gastos.'], ['utilidad', 'Utilidades', 'Distribución de utilidades.'], ['estado_resultados', 'Estado de resultados', 'Presentación de resultados.']] as $uso)
                            <div class="col-12 col-sm-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" role="switch" id="pc-{{ $uso[0] }}" wire:model.defer="{{ $uso[0] }}"><label class="form-check-label fw-bold" for="pc-{{ $uso[0] }}">{{ $uso[1] }}</label></div>
                                    <div class="small text-muted mt-1">{{ $uso[2] }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <h3 class="fs-6 fw-bold border-bottom pb-2 mb-3">Plazos y vinculación</h3>
                        <div class="row g-3">
                            <div class="col-6"><label for="pc-desde" class="form-label">Desde (días)</label><input id="pc-desde" type="number" min="0" class="form-control @error('tiempo_inicio') is-invalid @enderror" wire:model.defer="tiempo_inicio" placeholder="Sin límite">@error('tiempo_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-6"><label for="pc-hasta" class="form-label">Hasta (días)</label><input id="pc-hasta" type="number" min="0" class="form-control @error('tiempo_fin') is-invalid @enderror" wire:model.defer="tiempo_fin" placeholder="Sin límite">@error('tiempo_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-12"><label for="pc-banco" class="form-label">Banco asociado</label><select id="pc-banco" class="form-select @error('banco_id') is-invalid @enderror" wire:model.defer="banco_id">
                                    <option value="">Sin banco asociado</option>@foreach ($bancos as $ban)<option value="{{ $ban->id }}">{{ $ban->nombre }} — {{ $ban->numero_cuenta }}</option>@endforeach
                                </select>@error('banco_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-12"><label for="pc-accion" class="form-label">Acción sobre el saldo</label><select id="pc-accion" class="form-select @error('accion_cuenta') is-invalid @enderror" wire:model.defer="accion_cuenta">
                                    <option value="">Sin acción definida</option>
                                    <option value="SUMA">Suma (A +)</option>
                                    <option value="RESTA">Resta (B −)</option>
                                </select>@error('accion_cuenta')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        </div>
                    </div>
                    <div class="border-top p-3 d-flex flex-wrap justify-content-between gap-2 bg-light"><button type="button" class="btn btn-default" wire:click="reiniciarNuevo">{{ $id_seleccionado ? 'Cancelar edición' : 'Limpiar' }}</button><button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="nuevoPlan,actualizarPlan"><span wire:loading.remove wire:target="nuevoPlan,actualizarPlan"><i class="fa fa-save me-2" aria-hidden="true"></i>{{ $id_seleccionado ? 'Guardar cambios' : 'Crear cuenta' }}</span><span wire:loading wire:target="nuevoPlan,actualizarPlan"><i class="fa fa-spinner fa-spin me-2" aria-hidden="true"></i>Guardando…</span></button></div>
                </form>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" id="pcEliminarModal" tabindex="-1" aria-labelledby="pc-eliminar-titulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                @if ($cuentaEliminar)
                <div class="modal-header">
                    <h3 class="modal-title fs-5" id="pc-eliminar-titulo">Eliminar cuenta</h3><button type="button" class="btn-close" aria-label="Cancelar" wire:click="cancelarEliminar" autofocus></button>
                </div>
                <div class="modal-body">
                    <p>¿Quieres eliminar esta cuenta del catálogo?</p>
                    <div class="border rounded p-3 mb-3"><strong class="font-monospace">{{ $cuentaEliminar->codigo }}</strong>
                        <div>{{ $cuentaEliminar->nombre }}</div>
                    </div>
                    <div class="alert alert-warning mb-0">La cuenta solo se eliminará si no tiene operaciones ni configuraciones asociadas.</div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-default" wire:click="cancelarEliminar">Cancelar</button><button type="button" class="btn btn-danger" wire:click="eliminarPlan({{ $cuentaEliminar->id }})" wire:loading.attr="disabled" wire:target="eliminarPlan"><i class="fa fa-trash me-2" aria-hidden="true"></i>Eliminar cuenta</button></div>
                @endif
            </div>
        </div>
    </div>
</div>