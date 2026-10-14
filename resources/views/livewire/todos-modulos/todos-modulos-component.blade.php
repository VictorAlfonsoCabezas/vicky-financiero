<div>
    @include('livewire.todos-modulos._listado')
    {{-- MODAL CREAR ASIENTO MANUAL --}}
    <div wire:ignore.self class="modal fade" id="modalManualAsiento" style="display: none;" aria-hidden="true"
        data-bs-backdrop="static" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-plus-circle text-primary me-2"></i>Crear asiento manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form wire:submit.prevent="storeManual">
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-warning">
                                <h5>Verifica estas observaciones.</h5>
                                <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-3">
                                <label>Movimiento</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $movimiento_manual_id }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label>Fecha contable</label>
                                <input type="datetime-local" class="form-control form-control-sm" wire:model="fecha_manual">
                            </div>
                            <div class="col-md-6">
                                <label>Concepto</label>
                                <select class="form-select form-select-sm" wire:model="concepto_id">
                                    <option value="">- Seleccione -</option>
                                    @foreach ($tipoConceptos as $tipoConcepto)
                                        <optgroup label="{{ $tipoConcepto->nombre }}">
                                            @foreach ($conceptos->where('tipo_concepto_id', $tipoConcepto->id) as $concepto)
                                                <option value="{{ $concepto->id }}">{{ $concepto->nombre }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mt-2">
                                <label>Descripción</label>
                                <textarea class="form-control form-control-sm" rows="2" wire:model="descripcion"></textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group position-relative">
                                    <label>Agregar cuenta contable</label>
                                    <input type="search" wire:model="search" class="form-control form-control-sm"
                                        placeholder="Buscar por codigo o nombre">
                                    @if(!empty($search))
                                        <div class="card card-widget widget-user-2 position-absolute w-100 bg-white shadow"
                                            style="z-index: 1000; top: 100%; left: 0; right: 0; max-height: 260px; overflow-y: auto; border: 1px solid #ddd;">
                                            <div class="card-footer p-0">
                                                <ul class="nav flex-column nav-pills">
                                                    @forelse ($planCuentasBuscador as $plan)
                                                        <li class="nav-item">
                                                            <a wire:click="agregarCuenta({{ $plan->id }})" class="nav-link">
                                                                {{ $plan->nombre }}
                                                                <span class="float-end badge bg-primary">{{ $plan->codigo }}</span>
                                                            </a>
                                                        </li>
                                                    @empty
                                                        <li class="nav-item p-2">No hay resultados</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12 table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Cuenta</th>
                                            <th>Opcion</th>
                                            <th>Centro Costos</th>
                                            <th>Debe</th>
                                            <th>Haber</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($asientos as $key => $asi)
                                            <tr>
                                                <td><b>{{ $asi['cuenta_codigo'] }}</b> {{ $asi['cuenta_nombre'] }}</td>
                                                <td>
                                                    <div class="form-check form-switch">
                                                        <input type="checkbox" class="form-check-input"
                                                            id="manualDebeHaber{{ $key }}"
                                                            wire:click="actualizarDebHaber({{ $key }})"
                                                            {{ $asi['debe_haber'] ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="manualDebeHaber{{ $key }}">
                                                            {{ $asi['debe_haber'] ? 'Debe' : 'Haber' }}
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <select class="form-control form-control-sm"
                                                        wire:change="actualizarCentro({{ $key }}, $event.target.value)">
                                                        <option value="0">Ninguno</option>
                                                        @foreach ($sedes as $se)
                                                            <optgroup label="{{ $se->name }}">
                                                                @foreach ($centro_costos->where('sede_id', $se->id) as $sedeCentro)
                                                                    <option value="{{ $sedeCentro->id }}" {{ $asi['sedes_centro_costos'] == $sedeCentro->id ? 'selected' : '' }}>
                                                                        {{ optional($sedeCentro->sede)->name ?? 'Sin sede' }} -
                                                                        {{ optional($sedeCentro->centroCostos)->name ?? 'Sin centro de costos' }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td style="width: 130px;">
                                                    <input class="form-control form-control-sm text-end" type="number"
                                                        step="0.01" value="{{ $asi['debe'] }}"
                                                        wire:change="actualizarValores({{ $key }}, $event.target.value)"
                                                        {{ $asi['debe_haber'] ? '' : 'disabled' }}>
                                                </td>
                                                <td style="width: 130px;">
                                                    <input class="form-control form-control-sm text-end" type="number"
                                                        step="0.01" value="{{ $asi['haber'] }}"
                                                        wire:change="actualizarValores({{ $key }}, $event.target.value)"
                                                        {{ $asi['debe_haber'] ? 'disabled' : '' }}>
                                                </td>
                                                <td class="text-center">
                                                    <a class="text-danger" wire:click="elminarCuenta({{ $key }})">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-center" colspan="6">Sin cuentas</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @php
                        $totalManualDebe = collect($asientos)->sum('debe');
                        $totalManualHaber = collect($asientos)->sum('haber');
                        $diferenciaManual = abs(round($totalManualDebe - $totalManualHaber, 2));
                    @endphp
                    <div class="px-3 pb-3">
                        <div class="d-flex flex-wrap gap-3 justify-content-end rounded bg-light p-3">
                            <span>Debe <strong>$ {{ number_format($totalManualDebe, 2) }}</strong></span>
                            <span>Haber <strong>$ {{ number_format($totalManualHaber, 2) }}</strong></span>
                            <span class="{{ $diferenciaManual > 0 ? 'text-danger' : 'text-success' }}">Diferencia <strong>$ {{ number_format($diferenciaManual, 2) }}</strong></span>
                        </div>
                        @if ($diferenciaManual > 0)
                            <small class="text-danger d-block mt-2">Puedes guardar el asiento con esta diferencia; quedará pendiente de revisión.</small>
                        @endif
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="storeManual"><span wire:loading.remove wire:target="storeManual">Guardar asiento</span><span wire:loading wire:target="storeManual">Guardando…</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('livewire.todos-modulos._detalle')
</div>
