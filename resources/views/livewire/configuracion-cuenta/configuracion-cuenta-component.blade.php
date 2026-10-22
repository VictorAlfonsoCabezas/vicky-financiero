<div class="configuracion-cuenta-page">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="small text-muted mb-1">CONTABILIDAD / CONFIGURACIÓN</div>
            <h1 class="page-header mb-1">Configuración de asientos</h1>
            <p class="text-muted mb-0">Define las cuentas y los valores que se utilizan al generar los asientos automáticos.</p>
        </div>
        <button type="button" class="btn btn-primary" wire:click="abrirModal(0)"><i class="fa fa-plus me-2" aria-hidden="true"></i>Nueva configuración</button>
    </div>
    <div class="row g-3 mb-4">
        @foreach ([['Configuraciones', $resumen->total, 'fa-sliders-h', 'primary'], ['Operaciones diarias', $resumen->diarias, 'fa-exchange-alt', 'info'], ['Operaciones de bóvedas', $resumen->bovedas, 'fa-university', 'success']] as $indicador)
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3"><span class="cc-stat-icon bg-{{ $indicador[3] }} bg-opacity-10 text-{{ $indicador[3] }}"><i class="fa {{ $indicador[2] }}" aria-hidden="true"></i></span>
                    <div>
                        <div class="text-muted small">{{ $indicador[0] }}</div>
                        <div class="fs-3 fw-bold">{{ number_format($indicador[1] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="panel panel-inverse mb-4">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-filter me-2" aria-hidden="true"></i>Buscar configuraciones</h2><span class="badge bg-white text-dark">{{ $configuraciones->total() }} resultados</span>
        </div>
        <div class="panel-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-4"><label for="cc-buscar" class="form-label">Módulo, concepto o cuenta</label>
                    <div class="input-group"><span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span><input id="cc-buscar" type="search" class="form-control" wire:model.debounce.350ms="buscarConfiguracion" placeholder="Ej. ahorro, diario o 1.01"></div>
                </div>
                <div class="col-6 col-lg-2"><label for="cc-origen" class="form-label">Origen</label><select id="cc-origen" class="form-select" wire:model="filtroOrigen">
                        <option value="">Todos</option>
                        <option value="diarias">Operaciones diarias</option>
                        <option value="bovedas">Bóvedas</option>
                    </select></div>
                <div class="col-6 col-lg-3"><label for="cc-concepto-filtro" class="form-label">Concepto</label><select id="cc-concepto-filtro" class="form-select" wire:model="filtroConcepto">
                        <option value="">Todos los conceptos</option>@foreach ($conceptos as $concepto)<option value="{{ $concepto->id }}">{{ $concepto->nombre }}</option>@endforeach
                    </select></div>
                <div class="col-6 col-lg-2"><label for="cc-pagina" class="form-label">Por página</label><select id="cc-pagina" class="form-select" wire:model="porPagina">
                        <option>12</option>
                        <option>24</option>
                        <option>48</option>
                    </select></div>
                <div class="col-6 col-lg-1"><button type="button" class="btn btn-default w-100" wire:click="limpiarFiltros" aria-label="Limpiar filtros" title="Limpiar filtros"><i class="fa fa-undo" aria-hidden="true"></i></button></div>
            </div>
        </div>
    </div>
    <div class="small text-muted mb-3" role="status" wire:loading wire:target="buscarConfiguracion,filtroOrigen,filtroConcepto,porPagina,gotoPage"><i class="fa fa-spinner fa-spin me-2" aria-hidden="true"></i>Actualizando configuraciones…</div>
    <div class="row g-4">
        @forelse ($configuraciones as $config)
        @php
        $debe = $config->detalles->where('debe_haber', true);
        $haber = $config->detalles->where('debe_haber', false);
        $completa = $debe->isNotEmpty() && $haber->isNotEmpty() && !$config->detalles->contains(function ($fila) { return !$fila->planCuentas; });
        $nombreModulo = optional($config->typeTransaction)->name ?: optional($config->operacionesDescargoBovedas)->nombre ?: 'Módulo no disponible';
        @endphp
        <div class="col-12 col-xl-6" wire:key="configuracion-{{ $config->id }}">
            <div class="panel panel-inverse h-100 mb-0">
                <div class="panel-heading">
                    <h2 class="panel-title text-break">{{ $nombreModulo }}</h2><span class="badge {{ $config->status ? 'bg-success' : 'bg-secondary' }} ms-2">{{ $config->status ? 'Activa' : 'Inactiva' }}</span>
                </div>
                <div class="panel-body p-0">
                    <div class="p-3 border-bottom d-flex flex-wrap justify-content-between gap-2">
                        <div><span class="badge bg-light text-dark border me-1">{{ $config->type_transaction_id !== null ? 'Operación diaria' : 'Bóvedas' }}</span><span class="text-muted small">{{ optional($config->concepto)->nombre ?: 'Sin concepto' }}</span></div><span class="small {{ $completa ? 'text-success' : 'text-danger' }}"><i class="fa {{ $completa ? 'fa-check-circle' : 'fa-exclamation-circle' }} me-1" aria-hidden="true"></i>{{ $completa ? 'Debe y Haber definidos' : 'Revisar cuentas' }}</span>
                    </div>
                    <div class="row g-0">
                        @foreach ([['Debe', $debe, 'primary'], ['Haber', $haber, 'success']] as $lado)
                        <div class="col-12 col-sm-6 {{ $loop->first ? 'cc-debe' : '' }}">
                            <div class="px-3 pt-3 pb-2 d-flex justify-content-between"><strong class="text-{{ $lado[2] }}">{{ $lado[0] }}</strong><span class="badge bg-light text-dark border">{{ $lado[1]->count() }}</span></div>
                            <ul class="list-unstyled px-3 pb-2 mb-0 cc-cuentas">
                                @forelse ($lado[1] as $fila)<li class="py-2 border-bottom"><span class="font-monospace fw-bold d-block">{{ optional($fila->planCuentas)->codigo ?: 'Sin código' }}</span><span class="small text-muted">{{ optional($fila->planCuentas)->nombre ?: 'Cuenta no disponible' }}</span></li>@empty<li class="text-muted small py-3">No hay cuentas en este lado.</li>@endforelse
                            </ul>
                        </div>
                        @endforeach
                    </div>
                    <div class="border-top p-3 d-flex flex-wrap align-items-center justify-content-between gap-2"><small class="text-muted">Configuración #{{ $config->id }}</small>
                        <div class="d-flex flex-wrap gap-2"><button type="button" class="btn btn-sm btn-outline-secondary" wire:click="abrirModalAsiento({{ $config->id }})"><i class="fa fa-cog me-1" aria-hidden="true"></i>Parámetros</button><button type="button" class="btn btn-sm btn-outline-primary" wire:click="abrirModal({{ $config->id }})"><i class="fa fa-pen me-1" aria-hidden="true"></i>Editar</button><button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmarElminarConfiguracion({{ $config->id }})" aria-label="Eliminar configuración {{ $config->id }}" title="Eliminar configuración"><i class="fa fa-trash" aria-hidden="true"></i></button></div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="panel panel-inverse">
                <div class="panel-body text-center py-5"><i class="fa fa-sliders-h fs-1 text-muted mb-3" aria-hidden="true"></i>
                    <h2 class="fs-5">No se encontraron configuraciones</h2>
                    <p class="text-muted">Ajusta los filtros o crea una configuración para tus asientos automáticos.</p><button type="button" class="btn btn-default me-2" wire:click="limpiarFiltros">Limpiar filtros</button><button type="button" class="btn btn-primary" wire:click="abrirModal(0)">Nueva configuración</button>
                </div>
            </div>
        </div>
        @endforelse
    </div>
    <div class="mt-4">
        <p class="text-muted small">Mostrando {{ $configuraciones->firstItem() ?? 0 }}–{{ $configuraciones->lastItem() ?? 0 }} de {{ $configuraciones->total() }} configuraciones</p>{{ $configuraciones->links() }}
    </div>
    @include('livewire.configuracion-cuenta._editor')
    @include('livewire.configuracion-cuenta._parametros')
    <div wire:ignore.self class="modal fade" id="ccEliminarModal" tabindex="-1" aria-labelledby="cc-eliminar-titulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="cc-eliminar-titulo">Eliminar configuración</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                @if ($cuentaEliminar)<div class="modal-body">
                    <p>¿Quieres eliminar esta configuración?</p>
                    <div class="border rounded p-3 mb-3"><strong>{{ optional($cuentaEliminar->typeTransaction)->name ?: optional($cuentaEliminar->operacionesDescargoBovedas)->nombre }}</strong>
                        <div class="text-muted">{{ optional($cuentaEliminar->concepto)->nombre }} · #{{ $cuentaEliminar->id }}</div>
                    </div>
                    <div class="alert alert-warning mb-0">Se eliminarán sus reglas de Debe y Haber. Las configuraciones con asientos asociados se conservan.</div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-danger" wire:click="elminarConfigCompleta({{ $cuentaEliminar->id }})" wire:loading.attr="disabled" wire:target="elminarConfigCompleta">Eliminar configuración</button></div>@endif
            </div>
        </div>
    </div>
</div>