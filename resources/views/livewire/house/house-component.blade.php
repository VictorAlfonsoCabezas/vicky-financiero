<div>
    <div class="row mt-3">
        <div class="col-12 col-sm-6 col-md-6 d-flex align-items-stretch flex-column">
            <div class="card bg-light d-flex flex-fill">
                <div class="card-header text-muted border-bottom-0">
                    Hola, Bienvenido a la <b>CAJA WEB</b>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-7">
                            <h2 class="lead"><b>{{ $customer->nombres }} {{ $customer->apellidos }}</b></h2>
                            <p class="text-muted text-sm"><b>Caja: </b> {!! Auth::user()->company->company_name !!} </p>
                            <ul class="ms-4 mb-0 fa-ul text-muted">
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span>
                                    Dirección: {{ $customer->direccion }}</li>
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span>
                                    Teléfono: {{ $customer->telefono }}</li>
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-envelope"></i></span>
                                    Email: {{ $customer->correo }}</li>
                            </ul>
                        </div>
                        <div class="col-5 text-center">

                            @if (Auth::user()->company->photo !== '' && Auth::user()->company->photo !== null)
                            <img alt="{{ Auth::user()->company->comercial_name }}" src="uploads/companies/{{ Auth::user()->company->photo }}" title="{{ Auth::user()->company->comercial_name }}" alt="user-avatar" class="img-circle img-fluid" />
                            @else
                            <img src="{{ URL::asset('img/no-disponible.png') }}" alt="{{ Auth::user()->company->comercial_name }}" title="{{ Auth::user()->company->comercial_name }}" alt="user-avatar" class="img-circle img-fluid" />
                            @endif


                            {{-- <img src="{{ URL::asset('/img/Auth::user()->company->photo.jpg') }}" alt="user-avatar"
                            class="img-circle img-fluid"> --}}
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-end">
                        <a wire:click="abrirModal()" class="btn btn-sm btn-primary text-white" data-bs-toggle="modal" data-bs-target="#modalGeneral">
                            <i class="fas fa-key"></i> Cambio Contraseña
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-6">
            <a href="/customer-movimiento-solicitud">
                <div class="col-12 col-sm-12">
                    <div class="info-box bg-light">
                        <div class="info-box-content">
                            <span class="info-box-text text-center text-muted">Solicitar Acreditacion</span>
                            <span class="info-box-number text-center text-muted mb-0"><i class="fas fa-exchange-alt" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
            </a>
            <a href="/customer-transferencias">
                <div class="col-12 col-sm-12">
                    <div class="info-box bg-light">
                        <div class="info-box-content">
                            <span class="info-box-text text-center text-muted">Transferencias</span>
                            <span class="info-box-number text-center text-muted mb-0"><i class="fas fa-hand-holding" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
            </a>
            <!-- 
            <a href="/customer-movimientos">
                <div class="col-12 col-sm-12">
                    <div class="info-box bg-light">
                        <div class="info-box-content">
                            <span class="info-box-text text-center text-muted">Cuentas</span>
                            <span class="info-box-number text-center text-muted mb-0"><i class="fas fa-file-invoice" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
            </a>
-->
            <a href="/customer-cuentas">
                <div class="col-12 col-sm-12">
                    <div class="info-box bg-light">
                        <div class="info-box-content">
                            <span class="info-box-text text-center text-muted">Movimientos</span>
                            <span class="info-box-number text-center text-muted mb-0"><i class="fas fa-file-invoice" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
            </a>
            <a href="/customer-prestamos">
                <div class="col-12 col-sm-12">
                    <div class="info-box bg-light">
                        <div class="info-box-content">
                            <span class="info-box-text text-center text-muted">Préstamos</span>
                            <span class="info-box-number text-center text-muted mb-0"><i class="fas fa-money-bill" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
            </a>
            <a href="/customer-simulador">
                <div class="col-12 col-sm-12">
                    <div class="info-box bg-light">
                        <div class="info-box-content">
                            <span class="info-box-text text-center text-muted">Simulador</span>
                            <span class="info-box-number text-center text-muted mb-0"><i class="fas fa-calculator" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- MODAL CAMBIO CONTRASEÑA --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral" style="display: none;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title"> Cambio de Constraseña </h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form wire:submit.prevent="restablecerClave">
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
                                <label>Nueva Contraseña</label>
                                <input type="password" class="form-control" placeholder="Nueva contraseña" wire:model="nueva_clave" required>
                            </div>
                            <div class="col-12 mt-2">
                                <label>Repita Contraseña</label>
                                <input type="password" class="form-control" placeholder="Repita su nueva contraseña" wire:model="nueva_clave_confirmation" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- MODAL TERMINOS --}}
    <div wire:ignore.self class="modal fade" id="modalGeneral2" style="display: blck;" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title"> Terminos y condiciones</h4>

                </div>
                <form wire:submit.prevent="aceptarTerminosUso">
                    <div class="modal-body">
                        @if($existeAceptar)
                        <div class="row mb-4">
                            <div class="col-12">
                                @if(isset($terminoAceptar->description))
                                {!! html_entity_decode($terminoAceptar->description) !!}
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="submit" class="btn btn-primary">Guardar </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@if($existeAceptar)
<script>
    document.addEventListener('livewire:load', function() {
        // Abre el modal cuando Livewire ha cargado completamente
        $('#modalGeneral2').modal('show');
    });
</script>
@endif