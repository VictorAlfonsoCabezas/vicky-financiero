@extends('portal.layout')
@section('content')
<h1 class="page-header">Simulador de préstamos</h1>
<p>Esta simulación utiliza las condiciones del producto de la caja. No crea un crédito ni constituye una aprobación.</p>
<div class="card mb-3"><form class="card-body row g-3" method="POST" action="{{ route('portal.simulator') }}">@csrf
<div class="col-md-6"><label class="form-label">Producto</label><select class="form-select" name="producto" required><option value="">Selecciona un producto</option>@foreach($products as $product)<option value="{{ $product->id }}" {{ old('producto', request('producto')) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Monto</label><input class="form-control" name="valor" type="number" min="1" max="99999" step="0.01" value="{{ old('valor', request('valor')) }}" required></div>
<div class="col-md-6"><label class="form-label">Número de cuotas</label><input class="form-control" name="cuotas" type="number" min="1" max="360" value="{{ old('cuotas', request('cuotas')) }}" required></div>
<div class="col-md-6"><label class="form-label">Fecha de inicio</label><input class="form-control" name="fecha" type="date" value="{{ old('fecha', request('fecha', date('Y-m-d'))) }}" required></div>
<div class="col-12"><button class="btn btn-primary">Simular</button></div></form></div>
@if(count($rows))<div class="card"><div class="card-body table-responsive"><button class="btn btn-outline-secondary mb-3" onclick="window.print()">Imprimir simulación</button><table class="table"><thead><tr><th>Cuota</th><th>Fecha</th><th>Capital</th><th>Interés</th><th>Desgravamen</th><th>Total cuota</th></tr></thead><tbody>@foreach($rows as $row)<tr><td>{{ $row['cuotas'] }}</td><td>{{ $row['fechas'] }}</td><td>{{ $row['amoritizado'] }}</td><td>{{ $row['interes'] }}</td><td>{{ $row['desgravamen'] }}</td><td>{{ $row['cuotaPago'] }}</td></tr>@endforeach</tbody></table></div></div>@endif
@endsection
