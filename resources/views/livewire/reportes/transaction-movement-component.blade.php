<div>
    <div wire:loading role="status" class="alert alert-info">Actualizando reporte…</div>
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <h2 class="panel-title">Filtros del reporte</h2>
        </div>
        <div class="panel-body">
            @if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
            <div>
                <div class="row g-3 mb-3">
                    <div class="col-md-2"><label for="desde" class="form-label">Desde</label><input id="desde" wire:model="filters.desde" type="date" class="form-control" required></div>
                    <div class="col-md-2"><label for="hasta" class="form-label">Hasta</label><input id="hasta" wire:model="filters.hasta" type="date" class="form-control" required></div>
                    <div class="col-md-3"><label for="tipo" class="form-label">Tipo de transacción</label><select id="tipo" wire:model="filters.tipo" class="form-select">
                            <option value="">Todos los tipos</option>@foreach($types as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach
                        </select></div>
                    <div class="col-md-2"><label for="estado" class="form-label">Estado</label><select id="estado" wire:model="filters.estado" class="form-select">@foreach(['activos' => 'Activos', 'anulados' => 'Anulados', 'todos' => 'Todos'] as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div>
                    <div class="col-md-3"><label for="buscar" class="form-label">Socio o comprobante</label><input id="buscar" wire:model.debounce.400ms="filters.buscar" class="form-control" maxlength="120" placeholder="Nombre, identificación o código"></div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="text-muted align-self-center">Los filtros se aplican automáticamente.</span>
                    <button class="btn btn-default" type="button" wire:click="clearFilters" wire:loading.attr="disabled">Limpiar</button>
                    <button class="btn btn-danger" wire:click="exportPdf" wire:loading.attr="disabled" type="button"><i class="fa fa-file-pdf me-1"></i>Descargar PDF</button>
                    <button class="btn btn-success" wire:click="exportExcel" wire:loading.attr="disabled" type="button"><i class="fa fa-file-excel me-1"></i>Descargar Excel</button>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <h2 class="panel-title">Resumen por tipo de transacción</h2>
        </div>
        <div class="panel-body">
            <p class="text-muted">Los totales incluyen todos los resultados filtrados. Las entradas y salidas corresponden a movimientos activos con acción S y R, respectivamente. El valor registrado incluye los anulados cuando se seleccionan.</p>
            @include('reportes.movimientos-transacciones-resumen')
        </div>
    </div>
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <h2 class="panel-title">Detalle de movimientos · {{ $movimientos->total() }} registros</h2>
        </div>
        <div class="panel-body">
            @include('reportes.movimientos-transacciones-detalle')
            <div class="mt-3">{{ $movimientos->links() }}</div>
        </div>
    </div>
</div>