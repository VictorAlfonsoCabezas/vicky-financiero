@extends('layouts.app')
@section('title', 'Recurrencia de cartera')
@section('custom_css_rules')
<link href="{{ asset('css/savings.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="savings-module">
<div class="savings-heading"><div><div class="small text-muted mb-1">CONFIGURACI&Oacute;N / CARTERA</div><h1>Recurrencia de cartera</h1><p>Configura los rangos de d&iacute;as y su orden para organizar la cartera.</p></div><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formRecurrenciaNew"><i class="fa fa-plus me-2"></i>Nueva recurrencia</button></div>
@if(session('mensaje'))<div class="alert alert-success" role="status">{{ session('mensaje') }}</div>@endif
<div class="card"><div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2"><div><h2 class="h5 mb-1">Lista de Recurrencias</h2><span class="text-muted">{{ $datos->count() }} rangos configurados</span></div><div id="recurrence-exports" class="d-flex gap-2 flex-wrap"></div></div>
<div class="card-body"><div class="table-responsive"><table id="table_prestamos" class="table table-hover align-middle w-100"><thead><tr><th>Orden</th><th>Desde</th><th>Hasta</th><th>Creaci&oacute;n</th><th>Creado por</th><th>D&iacute;as</th><th>Acciones</th></tr></thead><tbody>
@foreach($datos as $dato)
<tr><td><span class="badge bg-primary">{{ $dato->orden }}</span></td><td>{{ $dato->desde }}</td><td>{{ $dato->hasta === null ? 'Sin límite' : $dato->hasta }}</td><td>{{ $dato->date_created }}<div class="text-muted small">{{ $dato->hour_created }}</div></td><td>{{ $dato->user_created_name }}</td><td>{{ $dato->dias === null ? 'Abierto' : $dato->dias }}</td><td><form action="{{ url('recurrenciaCartera/'.$dato->id) }}" method="POST" data-confirm-delete>@csrf @method("delete")<button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash-alt me-1"></i>Eliminar</button></form></td></tr>
@endforeach
</tbody></table></div></div></div>
@include('recurrencia-cartera.modal_recurrencia')
</div>
@endsection
@section('scripts')<script src="{{ asset('js/recurrencia-cartera.js') }}" defer></script>@endsection
