<div>
    <div class="row mt-3">
        <div class="col-12 col-sm-6 col-md-6 d-flex align-items-stretch flex-column">
            <div class="card bg-light d-flex flex-fill">
                <div class="card-header text-muted border-bottom-0">
                    <b>PERFIL</b>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-7">
                            <h2 class="lead"><b>{{ $user->firstname }} {{ $user->lastname }}</b></h2>
                            <p class="text-muted text-sm"><b>Caja: </b> {!! Auth::user()->company->company_name !!} </p>
                            <ul class="ms-4 mb-0 fa-ul text-muted">
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span>
                                    Usuario: {{ $user->username }}</li>
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span>
                                    Cédula: {{ $user->ruc }}</li>
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-envelope"></i></span>
                                    Email: {{ $user->email }}</li>
                            </ul>
                        </div>
                        <div class="col-5 text-center">
                            <img src="{{ URL::asset('/img/sinusuario.jpg') }}" alt="user-avatar"
                                class="img-circle img-fluid">
                        </div>
                    </div>
                </div>
                <div class="card-footer">

                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-6 d-flex align-items-stretch flex-column">
            <div class="card bg-light d-flex flex-fill">
                <div class="card-header text-muted border-bottom-0">
                    <b>Cambio de contraseña</b>
                </div>
                <div class="card-body pt-0">
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
                            <input type="password" class="form-control" placeholder="Nueva contraseña"
                                wire:model="nueva_clave" required>
                        </div>
                        <div class="col-12 mt-2">
                            <label>Repita Contraseña</label>
                            <input type="password" class="form-control" placeholder="Repita su nueva contraseña"
                                wire:model="nueva_clave_confirmation" required>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a wire:click="restablecerClave()" class="btn btn-primary text-white"><i class="fa fa-key"></i>
                        Cambiar Contraseña </a>
                </div>
            </div>
        </div>
    </div>
</div>
