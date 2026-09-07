<div>

    <section class="content mt-5 ms-5 me-5">

        <div class="card">
            <div class="card-body row">
                <div class="col-5 text-center d-flex align-items-center justify-content-center">
                    <div class="">
                        @if ($this->fin == 0)
                            <h2>Transferencias <strong>CODEV</strong></h2>
                            <p class="lead mb-5">Al alcance de tus manos
                            </p>
                        @else
                            <img class="animation__shake" src="{{ URL::asset('/img/money.gif') }}">
                        @endif
                    </div>
                </div>
                <div class="col-7">
                    <div class="form-group">
                        <label for="inputEmail">De:</label>
                        <select class="custom-select" wire:model="cuenta_origen" wire:change="obtenerDatosSaldo"
                            wire:key="cuenta_origen">
                            <option value="">-Seleccione una Cuenta-</option>
                            @foreach ($customer_cuenta_origen as $customer_origenes)
                                <option value="{{ $customer_origenes->id }}">
                                    {{ $customer_origenes->codigo . ' (' . $customer_origen->nombres . ' ' . $customer_origen->apellidos . ')' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="inputSubject">Valor:</label>
                        <input type="number" class="form-control form-control-border" wire:model="valor"
                            placeholder="0.00"
                            style="font-size:48px; text-align:center; font-weight:bold; border:2px solid; border-radius:12px; padding:12px; width:250px; margin:0 auto; display:block;"
                            wire:change="obtenerDatosSaldo" wire:key="valor">
                    </div>



                    <div class="form-group">
                        <label for="inputSubject">Para:</label>
                        <input type="search" wire:model="search" class="form-control">
                    </div>

                    @if (!empty($search) && isset($customer_cuenta_destino['id']))
                        <div class="col-12 col-sm-6 col-md-12 d-flex align-items-stretch flex-column">
                            <div class="card bg-light d-flex flex-fill">
                                <div class="card-header text-muted border-bottom-0">
                                    {{ Auth::user()->company->comercial_name }}
                                </div>
                                <div class="card-body pt-0">
                                    <div class="row">
                                        <div class="col-7">
                                            <h2 class="lead">
                                                <b>{{ $customer_cuenta_destino['apellidos'] . ' ' . $customer_cuenta_destino['nombres'] }}</b>
                                            </h2>
                                            <p class="text-muted text-sm"><b>Identificación: </b>
                                                {{ $customer_cuenta_destino['numero_documento'] }} </p>
                                            <ul class="ms-4 mb-0 fa-ul text-muted">
                                                <li class="small"><span class="fa-li"><i
                                                            class="fas fa-lg fa-building"></i></span> Dirección:
                                                    {{ $customer_cuenta_destino['direccion'] }}
                                                </li>
                                                <li class="small"><span class="fa-li"><i
                                                            class="fas fa-lg fa-phone"></i></span> Telefono:
                                                    {{ $customer_cuenta_destino['telefono'] }}
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-5 text-center">
                                            <img src="../../dist/img/user1-128x128.jpg" alt="user-avatar"
                                                class="img-circle img-fluid">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a wire:click="seleccionarCliente({{ $customer_cuenta_destino->customer_id }})"
                                        class="btn btn-sm btn-primary" style="color: white;">
                                        <i class="fas fa-user"></i> Seleccionar cuenta
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif



                    <div class="form-group">
                        <label for="inputMessage">Agregar motivo</label>
                        <textarea wire:model="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                    @if ($this->customer_destino > 0)
                        <div class="form-group">
                            <button type="button" class="btn btn-block btn-primary btn-lg"
                                wire:click="confirmarTransferencia()">Confirmar</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>


</div>