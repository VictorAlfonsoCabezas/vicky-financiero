@extends('portal.layout')
@section('content')
<h1 class="page-header">Cambiar contraseña</h1>
<div class="card"><form class="card-body" method="POST" action="{{ route('portal.password') }}">@csrf
<div class="mb-3"><label class="form-label" for="actual">Contraseña actual</label><input class="form-control" type="password" id="actual" name="actual" autocomplete="current-password" required></div>
<div class="mb-3"><label class="form-label" for="password">Nueva contraseña</label><input class="form-control" type="password" id="password" name="password" autocomplete="new-password" minlength="8" maxlength="100" required></div>
<div class="mb-3"><label class="form-label" for="password_confirmation">Repite la nueva contraseña</label><input class="form-control" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" minlength="8" maxlength="100" required></div>
<button class="btn btn-primary">Guardar contraseña</button></form></div>
@endsection
