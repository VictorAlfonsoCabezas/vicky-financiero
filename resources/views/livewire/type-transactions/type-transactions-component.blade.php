<div class="transactions-module">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h1 class="page-header mb-1"><i class="fa fa-exchange-alt text-primary me-2" aria-hidden="true"></i> Tipos de transacción</h1><p class="text-muted mb-0">Consulta y configura los tipos de movimiento.</p></div>
        <button type="button" class="btn btn-primary" wire:click="abrirModal(0)" data-bs-toggle="modal" data-bs-target="#modalGeneral"><i class="fa fa-plus me-1" aria-hidden="true"></i> Nuevo tipo de transacción</button>
    </div>
    <div class="panel panel-inverse mb-4">
        <div class="panel-heading"><h4 class="panel-title"><i class="fa fa-filter me-2" aria-hidden="true"></i> Buscar y filtrar</h4><button type="button" class="btn btn-white btn-sm" wire:click="limpiarFiltros"><i class="fa fa-eraser me-1" aria-hidden="true"></i> Limpiar filtros</button></div>
        <div class="panel-body">
            <div class="row g-3">
                <div class="col-12 col-lg-6"><label class="form-label" for="tipo-search">Buscar</label><div class="input-group"><span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span><input id="tipo-search" type="search" class="form-control" wire:model.debounce.350ms="search" placeholder="Nombre, abreviatura, cartola o descripción"></div></div>
                <div class="col-12 col-sm-4 col-lg-2"><label class="form-label" for="tipo-action">Acción</label><select id="tipo-action" class="form-select" wire:model="actionFiltro"><option value="">Todas</option><option value="S">S</option><option value="R">R</option></select></div>
                <div class="col-12 col-sm-4 col-lg-2"><label class="form-label" for="tipo-afecta">Afecta</label><select id="tipo-afecta" class="form-select" wire:model="afectaFiltro"><option value="">Todos</option><option value="C">C</option><option value="E">E</option></select></div>
                <div class="col-12 col-sm-4 col-lg-2"><label class="form-label" for="tipo-estado">Estado</label><select id="tipo-estado" class="form-select" wire:model="estadoFiltro"><option value="">Todos</option><option value="1">Activo</option><option value="0">Inactivo</option></select></div>
            </div>
        </div>
    </div>
    <div class="panel panel-inverse">
        <div class="panel-heading"><h4 class="panel-title"><i class="fa fa-list me-2" aria-hidden="true"></i> Tipos registrados <span class="badge bg-primary ms-2">{{ $typetransaction->total() }}</span></h4><span wire:loading wire:target="search,actionFiltro,afectaFiltro,estadoFiltro,limpiarFiltros,page" role="status"><i class="fa fa-spinner fa-spin me-1"></i> Buscando...</span></div>
        <div class="panel-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 transactions-table">
                    <thead><tr><th>ID</th><th>Nombre</th><th>Abreviatura</th><th>Nombre en cartola</th><th>Descripción</th><th>Acción</th><th>Afecta</th><th>Estado</th><th>Acciones</th></tr></thead>
                    <tbody>
                        @forelse ($typetransaction as $type)
                        <tr wire:key="tipo-transaccion-{{ $type->id }}">
                            <td class="text-muted">{{ $type->id }}</td><td class="fw-bold">{{ $type->name }}</td><td><span class="badge bg-light text-dark">{{ $type->name_corto ?: '—' }}</span></td><td>{{ $type->nombre_cartola ?: '—' }}</td><td class="transactions-description">{{ $type->description }}</td><td><span class="badge bg-light text-dark">{{ $type->action }}</span></td><td><span class="badge bg-light text-dark">{{ $type->afecta }}</span></td>
                            <td><span class="badge {{ $type->status ? 'bg-success' : 'bg-secondary' }}">{{ $type->status ? 'Activo' : 'Inactivo' }}</span></td>
                            <td><button type="button" class="btn btn-outline-primary btn-sm" title="Editar {{ $type->name }}" wire:click="consultarDatosEdit({{ $type->id }})" data-bs-toggle="modal" data-bs-target="#modalGeneral"><i class="fa fa-pen me-1" aria-hidden="true"></i> Editar</button></td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center py-5"><i class="fa fa-search fa-2x text-muted mb-3 d-block" aria-hidden="true"></i><h5>No se encontraron tipos de transacción</h5><p class="text-muted mb-0">Prueba con otro texto o limpia los filtros.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 border-top"><small class="text-muted">Mostrando {{ $typetransaction->firstItem() ?? 0 }}–{{ $typetransaction->lastItem() ?? 0 }} de {{ $typetransaction->total() }} tipos</small><div>{{ $typetransaction->links() }}</div></div>
        </div>
    </div>
        {{-- MODAL --}}
        <div wire:ignore.self class="modal fade" id="modalGeneral" tabindex="-1" aria-labelledby="type-modal-title" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="type-modal-title"><i class="fa fa-exchange-alt text-primary me-2"></i> {{ $id_seleccionado > 0 ? "Editar tipo de transacción" : "Nuevo tipo de transacción" }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <form wire:submit.prevent="storeTransaciones">
                        <div class="modal-body">
                            @if ($errors->any())
                            <div class="alert alert-warning" role="alert">
                                <h5>Verifica estas observaciones.</h5>
                                @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                                @endforeach
                            </div>
                            @endif
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" placeholder="Ingrese el nombre" wire:model="name" id="name">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Nombre Corto (Abrev)</label>
                                    <input type="text" class="form-control" placeholder="Ingrese el nombre" wire:model="name_corto" id="name_corto">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Nombre Cartola</label>
                                    <input type="text" class="form-control" placeholder="Ingrese el nombre" wire:model="nombre_cartola" id="nombre_cartola">
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" rows="3" placeholder="Ingrese una descripción" wire:model="description" id="description"></textarea>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Accion</label>
                                    <select class="form-select" wire:model="action" id="action">
                                        <option value="">Seleccione una opción</option>
                                        <option value="S">S</option>
                                        <option value="R">R</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Afecta</label>
                                    <select class="form-select" wire:model="afecta" id="afecta">
                                        <option value="">Seleccione una opción</option>
                                        <option value="C">C</option>
                                        <option value="E">E</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

</div>
