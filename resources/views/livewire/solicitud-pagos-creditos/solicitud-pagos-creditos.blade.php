<div class="credit-payments-module">
    <div class="mb-4">
        <h1 class="page-header mb-1"><i class="fa fa-file-invoice-dollar text-primary me-2" aria-hidden="true"></i> Solicitudes de pagos de créditos</h1>
        <p class="text-muted mb-0">Revisa las transferencias y las formas de pago antes de aprobar una cuota.</p>
    </div>
    <div class="panel panel-inverse mb-4">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="fa fa-filter me-2" aria-hidden="true"></i> Buscar solicitudes</h4>
        </div>
        <div class="panel-body">
            <div class="row g-3">
                <div class="col-12 col-md-8"><label for="credit-payments-search" class="form-label">Cliente</label>
                    <div class="input-group"><span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span><input id="credit-payments-search" type="search" class="form-control" wire:model.debounce.350ms="buscarCedula" placeholder="Identificación, nombres o apellidos"></div>
                </div>
                <div class="col-12 col-md-4"><label for="credit-payments-state" class="form-label">Estado del pago</label><select id="credit-payments-state" class="form-select" wire:model="estadoFiltro">
                        <option value="3">Pendiente de revisión</option>
                        <option value="1">Aprobado</option>
                        <option value="2">Rechazado</option>
                    </select></div>
            </div>
        </div>
    </div>
    @error('solicitud')<div class="alert alert-warning" role="alert">{{ $message }}</div>@enderror
    @if($errorPago)<div class="alert alert-danger" role="alert">{{ $errorPago }}</div>@endif
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="fa fa-list me-2" aria-hidden="true"></i> Pagos registrados <span class="badge bg-primary ms-2">{{ $letras->total() }}</span></h4><span wire:loading wire:target="buscarCedula,estadoFiltro,page" role="status"><i class="fa fa-spinner fa-spin me-1" aria-hidden="true"></i> Buscando...</span>
        </div>
        <div class="panel-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 credit-payments-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Crédito / cuota</th>
                            <th>Fecha de registro</th>
                            <th>Registrado por</th>
                            <th class="text-end">Transferencia</th>
                            <th class="text-end">Otros pagos</th>
                            <th class="text-end">Total recibido</th>
                            <th>Estado</th>
                            <th>Revisado por</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($letras as $letra)
                        <tr wire:key="solicitud-pago-{{ $letra->letra_id }}-{{ $letra->prestamo_id }}">
                            <td class="fw-bold">{{ $letra->apellidos }} {{ $letra->nombres }}<small class="d-block text-muted fw-normal">{{ $letra->numero_documento }}</small></td>
                            <td class="text-nowrap">#{{ $letra->codeFolderHeader }}<small class="d-block text-muted">Cuota {{ $letra->numeroCuota }}</small></td>
                            <td class="text-nowrap">{{ $letra->fecha }}<small class="d-block text-muted">{{ $letra->hora }}</small></td>
                            <td>{{ $letra->usuario }}</td>
                            <td class="text-end credit-payments-money">$ {{ number_format((float) $letra->valor_transferencia, 2, '.', ',') }}</td>
                            <td class="text-end credit-payments-money">$ {{ number_format((float) $letra->valor_otros, 2, '.', ',') }}</td>
                            <td class="text-end credit-payments-money fw-bold">$ {{ number_format((float) $letra->valor_total, 2, '.', ',') }}</td>
                            <td><span class="badge {{ $estadoFiltro === '3' ? 'bg-warning text-dark' : ($estadoFiltro === '1' ? 'bg-success' : 'bg-danger') }}">{{ $estadoFiltro === '3' ? 'Pendiente' : ($estadoFiltro === '1' ? 'Aprobado' : 'Rechazado') }}</span></td>
                            <td>{{ $letra->aprobado_por ?: 'Sin revisar' }}</td>
                            <td><button type="button" class="btn btn-outline-primary btn-sm text-nowrap" wire:click="cargarDatosPago({{ $letra->letra_id }})" data-bs-toggle="modal" data-bs-target="#modalPagos"><i class="fa fa-eye me-1" aria-hidden="true"></i> Revisar pago</button></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5"><i class="fa fa-file-invoice-dollar fa-2x text-muted d-block mb-3" aria-hidden="true"></i>
                                <h5>No hay solicitudes para estos filtros</h5>
                                <p class="text-muted mb-0">Prueba con otro cliente o estado.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 border-top"><small class="text-muted">Mostrando {{ $letras->firstItem() ?? 0 }}–{{ $letras->lastItem() ?? 0 }} de {{ $letras->total() }} solicitudes</small>
                <div>{{ $letras->links() }}</div>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" id="modalPagos" tabindex="-1" aria-labelledby="credit-payment-title" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="credit-payment-title"><i class="fa fa-file-invoice-dollar text-primary me-2" aria-hidden="true"></i> Revisión del pago</h4><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div wire:loading wire:target="cargarDatosPago" class="alert alert-info" role="status">Cargando detalle...</div>
                    <div wire:loading.remove wire:target="cargarDatosPago">
                        @error('solicitud')<div class="alert alert-warning" role="alert">{{ $message }}</div>@enderror
                        @if($errorPago)<div class="alert alert-danger" role="alert">{{ $errorPago }}</div>@endif
                        @if($registro)
                        <div class="row g-4 mb-4">
                            <div class="col-12 col-lg-6">
                                <h5 class="credit-payments-section"><i class="fa fa-user me-2" aria-hidden="true"></i> Cliente y crédito</h5>
                                <dl class="row g-2">
                                    <dt class="col-sm-4">Cliente</dt>
                                    <dd class="col-sm-8">{{ $registro->apellidos }} {{ $registro->nombres }}</dd>
                                    <dt class="col-sm-4">Identificación</dt>
                                    <dd class="col-sm-8">{{ $registro->numero_documento }}</dd>
                                    <dt class="col-sm-4">Crédito</dt>
                                    <dd class="col-sm-8">#{{ $registro->codeFolderHeader }}</dd>
                                    <dt class="col-sm-4">Cuota</dt>
                                    <dd class="col-sm-8">{{ $registro->numeroCuota }}</dd>
                                </dl>
                            </div>
                            <div class="col-12 col-lg-6">
                                <h5 class="credit-payments-section"><i class="fa fa-calendar-check me-2" aria-hidden="true"></i> Registro y revisión</h5>
                                <dl class="row g-2">
                                    <dt class="col-sm-4">Registrado por</dt>
                                    <dd class="col-sm-8">{{ $registro->user_name ?: 'No disponible' }}</dd>
                                    <dt class="col-sm-4">Fecha</dt>
                                    <dd class="col-sm-8">{{ $registro->date_create }} {{ $registro->hour_create }}</dd>
                                    <dt class="col-sm-4">Revisado por</dt>
                                    <dd class="col-sm-8">{{ $registro->usuario_solicitud ?: 'Sin revisar' }}</dd>
                                    <dt class="col-sm-4">Fecha de revisión</dt>
                                    <dd class="col-sm-8">{{ $registro->fecha_solicitud ?: 'Sin revisar' }}</dd>
                                </dl>
                            </div>
                        </div>
                        @if((int) $registro->status === 3)
                        <section class="credit-payments-summary border mb-4" aria-labelledby="calculoCuotaTitulo">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                <h5 id="calculoCuotaTitulo" class="mb-0">Cálculo de la cuota</h5>
                                <div class="form-check form-switch">
                                    <input id="chkInteresMora" class="form-check-input" type="checkbox" role="switch" wire:model="aplicarInteresMora" wire:loading.attr="disabled" wire:target="aplicarInteresMora,aprobarSolicitud,rechazarSolicitud" aria-controls="estadoInteresMora">
                                    <label class="form-check-label" for="chkInteresMora">Aplicar interés de mora</label>
                                </div>
                            </div>
                            <div class="row g-3" id="estadoInteresMora" aria-live="polite">
                                <div class="col-12 col-md-6">
                                    <dl class="row g-2 mb-0">
                                        <dt class="col-7 fw-normal">Cuota base</dt>
                                        <dd class="col-5 text-end mb-0 credit-payments-money">$ {{ number_format($calculoCuota['base'], 2) }}</dd>
                                        <dt class="col-7 fw-normal">Faltante anterior</dt>
                                        <dd class="col-5 text-end mb-0 credit-payments-money">+ $ {{ number_format($calculoCuota['faltante'], 2) }}</dd>
                                        <dt class="col-7 fw-normal">Abono anterior</dt>
                                        <dd class="col-5 text-end mb-0 credit-payments-money">− $ {{ number_format($calculoCuota['abono'], 2) }}</dd>
                                        <dt class="col-7 fw-normal">Mora aplicada</dt>
                                        <dd class="col-5 text-end mb-0 credit-payments-money">+ $ {{ number_format($calculoCuota['mora'], 2) }}</dd>
                                        <dt class="col-7 border-top pt-2">Cuota a cubrir</dt>
                                        <dd class="col-5 border-top pt-2 text-end mb-0 fw-bold credit-payments-money">$ {{ number_format($calculoCuota['total'], 2) }}</dd>
                                    </dl>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <span class="badge {{ $aplicarInteresMora ? 'bg-warning text-dark' : 'bg-light text-dark' }}">Con mora · $ {{ number_format($calculoCuota['conMora'], 2) }}</span>
                                        <span class="badge {{ $aplicarInteresMora ? 'bg-light text-dark' : 'bg-primary' }}">Sin mora · $ {{ number_format($calculoCuota['sinMora'], 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between gap-2 mb-2"><span>Total recibido</span><strong class="credit-payments-money">$ {{ number_format((float) $totalAprobar, 2) }}</strong></div>
                                    @php($diferencia = $calculoCuota['diferencia'])
                                    <div class="alert {{ $diferencia < 0 ? 'alert-warning' : 'alert-success' }} py-2 mb-0">
                                        @if($diferencia === 0.0)
                                        El pago cubre exactamente la cuota.
                                        @else
                                        {{ $diferencia < 0 ? 'Faltante' : 'Excedente' }} <strong class="credit-payments-money">$ {{ number_format(abs($diferencia), 2) }}</strong>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <small wire:loading wire:target="aplicarInteresMora" class="text-muted mt-2" role="status">Actualizando cálculo…</small>
                        </section>
                        @else
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <div class="credit-payments-summary"><span class="text-muted">Total recibido</span>
                                    <h3 class="mb-0 credit-payments-money">$ {{ number_format((float) $totalRecibido, 2, '.', ',') }}</h3>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="credit-payments-summary"><span class="text-muted">Mora registrada en la cuota</span>
                                    <h3 class="mb-0 credit-payments-money">$ {{ number_format((float) $interesMoraOriginal, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        @endif
                        <h5 class="credit-payments-section"><i class="fa fa-wallet me-2" aria-hidden="true"></i> Formas de pago</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle credit-payments-table">
                                <thead>
                                    <tr>
                                        <th>Forma de pago</th>
                                        <th class="text-end">Valor</th>
                                        <th>Banco</th>
                                        <th>Comprobante</th>
                                        <th>Fecha del comprobante</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detallePagos as $pago)<tr wire:key="detalle-pago-{{ $pago->id }}">
                                        <td>{{ $pago->forma_pago }}</td>
                                        <td class="text-end credit-payments-money">$ {{ number_format((float) $pago->valor, 2, '.', ',') }}</td>
                                        <td>{{ $pago->banco->nombre ?? 'No aplica' }}</td>
                                        <td>{{ $pago->numero_comprobante ?: 'No registrado' }}</td>
                                        <td>{{ $pago->fecha_comprobante }} {{ $pago->hora_comprobante }}</td>
                                    </tr>@endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($detallePagos->contains(function ($pago) { return !empty($pago->path); }))
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('solicitud-pagos-creditos.comprobante', $letraSeleccionada) }}" target="_blank" rel="noopener"><i class="fa fa-paperclip me-1" aria-hidden="true"></i> Abrir comprobante</a>
                        @else<div class="alert alert-light mb-0">No hay un comprobante adjunto.</div>@endif
                        @else<div class="text-muted py-4 text-center">Selecciona una solicitud para revisar sus pagos.</div>@endif
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-white me-auto" data-bs-dismiss="modal">Cerrar</button>
                    @if($registro && (int) $registro->status === 3)
                    <button type="button" class="btn btn-outline-danger" data-payment-action="rechazarSolicitud" data-letra="{{ $letraSeleccionada }}" wire:loading.attr="disabled" wire:target="cargarDatosPago,aprobarSolicitud,rechazarSolicitud,aplicarInteresMora"><i class="fa fa-times-circle me-1" aria-hidden="true"></i> Rechazar pagos no efectivos</button>
                    <button type="button" class="btn btn-success" data-payment-action="aprobarSolicitud" data-letra="{{ $letraSeleccionada }}" data-total="{{ number_format((float) $totalAprobar, 2, '.', ',') }}" wire:loading.attr="disabled" wire:target="cargarDatosPago,aprobarSolicitud,rechazarSolicitud,aplicarInteresMora"><i class="fa fa-check-circle me-1" aria-hidden="true"></i> Aprobar pago</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>