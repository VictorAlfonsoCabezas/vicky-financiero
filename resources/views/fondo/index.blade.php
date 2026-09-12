@extends('layouts.app')
@section('title', 'Fondos')
@section('custom_css_rules')<link href="{{ asset('css/savings.css') }}" rel="stylesheet">@endsection
@section('content')
<div class="savings-module"><div class="savings-heading"><div><div class="text-muted small mb-1">TESORER&Iacute;A / FONDOS</div><h1>Administraci&oacute;n de fondos</h1><p>Consulta saldos, registra ingresos y egresos y revisa sus movimientos.</p></div></div>
<div class="row g-3 mb-4">@foreach(['valor_inicial' => 'Saldo inicial', 'valor_ingreso' => 'Ingresos acumulados', 'valor_egreso' => 'Egresos acumulados', 'valor_total' => 'Saldo total'] as $field => $label)<div class="col-12 col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body"><div class="text-muted mb-2">{{ $label }}</div><strong class="fs-3">$ {{ number_format($fondos->sum($field), 2) }}</strong></div></div></div>@endforeach</div>
<div class="card"><div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2"><h2 class="h5 mb-0">Fondos activos</h2><div id="fund-exports"></div></div><div class="card-body"><div class="table-responsive"><table id="table_fondos" class="table table-hover align-middle w-100"><thead><tr><th>C&oacute;digo</th><th>Inicial</th><th>Ingresos</th><th>Egresos</th><th>Saldo</th><th>Creaci&oacute;n</th><th>Acciones</th></tr></thead><tbody>
@foreach($fondos as $fondo)<tr><td><strong>{{ $fondo->code }}</strong></td>@foreach(['valor_inicial','valor_ingreso','valor_egreso','valor_total'] as $field)<td data-order="{{ $fondo->$field }}" class="text-nowrap">$ {{ number_format($fondo->$field, 2) }}</td>@endforeach<td>{{ $fondo->date_created }}<div class="small text-muted">{{ $fondo->hour_created }}</div></td><td><div class="d-flex gap-1 flex-wrap"><button type="button" class="btn btn-sm btn-outline-success" data-fund="{{ $fondo->id }}" data-code="{{ $fondo->code }}" data-type="IN">Ingreso</button><button type="button" class="btn btn-sm btn-outline-danger" data-fund="{{ $fondo->id }}" data-code="{{ $fondo->code }}" data-type="EG">Egreso</button><button type="button" class="btn btn-sm savings-secondary-action" data-details="{{ route('fondo.verMovimientos', $fondo->id) }}" data-code="{{ $fondo->code }}">Movimientos</button></div></td></tr>@endforeach
</tbody></table></div></div></div>
@include('fondo.modal_fondo')
@include('fondo.modal_detalles')
</div>@endsection
@section('scripts')<script src="{{ asset('js/fondo.js') }}" defer></script>@endsection
