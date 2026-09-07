<div>
    <div class="row justify-content-between">
        <div class="col-3 mt-2 mb-2">
            <a type="button" class="btn btn-primary text-white" href="/user-new/0"><i class="fa fa-plus"></i> </a>
        </div>
        <div class="col-3 mt-2 mb-2">
            <div class="input-group input-group-sm">
                <input type="text" wire:model="search" class="form-control float-end" placeholder="Buscar">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-default">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header" style="padding: 8px;">
                    <h5 class="card-title"><i class="fa fa-user"></i></i> <b>Usuarios</b></h5>
                </div>
                <div class="card-body table-responsive p-2">
                    <div class="row">
                        @foreach ($users as $user)
                        <div class="col-md-4 col-sm-6 col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-{{ $user->status ? 'primary' : 'danger' }}"><i class="fa fa-user"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ $user->username }}</span>
                                    <a href="/user-new/{{ $user->id }}"><i class="fas fa-edit"></i> Editar</a>
                                    <span class="info-box-number">{{ $user->ruc }}</span>
                                    <small>{{ $user->firstname }} {{ $user->lastname }} / <i class="fa fa-envelope"></i> {{ $user->email }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>